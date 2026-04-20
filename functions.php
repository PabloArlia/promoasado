<?php
declare(strict_types=1);


// Guardar cadena en sesión si viene en URL (sobreescribir si existe)
if (!empty($_SERVER['QUERY_STRING'])) {
    // Obtener el primer parámetro del query string sin el '='
    $queryString = $_SERVER['QUERY_STRING'];
    // Si no tiene '=', es el identificador directo
    if (strpos($queryString, '=') === false) {
        $_SESSION['cadena'] = (string) $queryString;
    }
}

// Si no hay cadena, redirigir a error (excepto si ya estamos en error_cadena)
if (!isset($_SESSION['cadena']) && basename($_SERVER['SCRIPT_FILENAME']) !== 'error_cadena.php') {
    header('Location: error_cadena.php');
    exit;
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function esc(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function regenerateCaptcha(): void
{
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';

    for ($index = 0; $index < 5; $index++) {
        $captcha .= $characters[random_int(0, strlen($characters) - 1)];
    }

    $_SESSION['captcha_code'] = $captcha;
}

function currentCaptcha(): string
{
    if (empty($_SESSION['captcha_code'])) {
        regenerateCaptcha();
    }

    return (string) $_SESSION['captcha_code'];
}

function validateCaptcha(string $value): bool
{
    return strtoupper(trim($value)) === currentCaptcha();
}

function currentParticipantId(): ?int
{
    if (isset($_SESSION['participante_id'])) {
        return (int) $_SESSION['participante_id'];
    }

    return isset($_SESSION['participant_id']) ? (int) $_SESSION['participant_id'] : null;
}

function clearParticipantSession(): void
{
    unset($_SESSION['participante_id'], $_SESSION['participant_id']);
}

function getParticipant(int $participantId): ?array
{
    $statement = db()->prepare(
        'SELECT
            p.id,
            p.usuario_id,
            p.gano_juego,
            p.preguntas_aprobadas,
            p.respuestas_json,
            p.fecha_respondio,
            p.fecha_participacion,
            u.nombre,
            u.apellido,
            u.email,
            u.celular,
            u.dni,
            u.acepta_bases,
            u.fecha_registro
         FROM participacion p
         INNER JOIN usuarios u ON u.id = p.usuario_id
         WHERE p.id = :id
         LIMIT 1'
    );
    $statement->execute(['id' => $participantId]);
    $participant = $statement->fetch();

    return $participant ?: null;
}

function currentParticipant(): ?array
{
    $participantId = currentParticipantId();

    if ($participantId === null) {
        if (isset($_SESSION['usuario_id'])) {
            $todayParticipationId = loginUserForToday((int) $_SESSION['usuario_id']);
            $_SESSION['participante_id'] = $todayParticipationId;
            return getParticipant($todayParticipationId);
        }

        return null;
    }

    $participant = getParticipant($participantId);
    if ($participant === null) {
        clearParticipantSession();
        return null;
    }

    $_SESSION['usuario_id'] = (int) $participant['usuario_id'];

    $statement = db()->prepare(
        'SELECT 1
         FROM participacion
         WHERE id = :id AND fecha_participacion = CURDATE()
         LIMIT 1'
    );
    $statement->execute(['id' => $participantId]);

    if (!$statement->fetchColumn()) {
        $todayParticipationId = loginUserForToday((int) $participant['usuario_id']);
        $_SESSION['participante_id'] = $todayParticipationId;
        $_SESSION['participant_id'] = $todayParticipationId;

        return getParticipant($todayParticipationId);
    }

    return $participant;
}

function requireParticipant(): array
{
    $participant = currentParticipant();

    if ($participant === null) {
        setFlash('error', 'Primero tenés que completar el registro.');
        redirect('registro.php');
    }

    return $participant;
}

function getButtonStatuses(int $participantId): array
{
    $statuses = [1 => null, 2 => null, 3 => null, 4 => null];
    $statement = db()->prepare('SELECT numero_boton, resultado FROM intentos_botones WHERE participante_id = :participante_id');
    $statement->execute(['participante_id' => $participantId]);

    foreach ($statement->fetchAll() as $row) {
        $statuses[(int) $row['numero_boton']] = (bool) $row['resultado'];
    }

    return $statuses;
}

function hasStartedGame(int $participantId): bool
{
    $statement = db()->prepare('SELECT COUNT(*) FROM intentos_botones WHERE participante_id = :participante_id');
    $statement->execute(['participante_id' => $participantId]);

    return (int) $statement->fetchColumn() > 0;
}

function hasFailedButton(int $participantId): bool
{
    $statement = db()->prepare(
        'SELECT COUNT(*)
         FROM intentos_botones
         WHERE participante_id = :participante_id AND resultado = 0'
    );
    $statement->execute(['participante_id' => $participantId]);

    return (int) $statement->fetchColumn() > 0;
}

function isGameComplete(int $participantId): bool
{
    if (hasFailedButton($participantId)) {
        return true;
    }

    foreach (getButtonStatuses($participantId) as $status) {
        if ($status === null) {
            return false;
        }
    }

    return true;
}

function recordButtonResult(int $participantId, int $buttonNumber, bool $result): void
{
    $statement = db()->prepare(
        'INSERT INTO intentos_botones (participante_id, numero_boton, resultado, creado_en)
         VALUES (:participante_id, :numero_boton, :resultado, NOW())'
    );

    $statement->execute([
        'participante_id' => $participantId,
        'numero_boton' => $buttonNumber,
        'resultado' => $result ? 1 : 0,
    ]);
}

function finalizeGame(int $participantId): array
{
    $statuses = getButtonStatuses($participantId);
    $completed = false;
    $won = false;

    if (in_array(false, $statuses, true)) {
        $completed = true;
        $won = false;
    } else {
        $completed = true;
        $won = true;

        foreach ($statuses as $status) {
            if ($status === null) {
                $completed = false;
                $won = false;
                break;
            }
        }
    }

    if ($completed) {
        $statement = db()->prepare('UPDATE participacion SET gano_juego = :gano_juego WHERE id = :id');
        $statement->execute([
            'gano_juego' => $won ? 1 : 0,
            'id' => $participantId,
        ]);
    }

    return [
        'statuses' => $statuses,
        'completed' => $completed,
        'won' => $won,
    ];
}

function resetParticipantProgress(int $participantId): void
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $deleteAttempts = $pdo->prepare('DELETE FROM intentos_botones WHERE participante_id = :participante_id');
        $deleteAttempts->execute(['participante_id' => $participantId]);

        $updateParticipant = $pdo->prepare(
            'UPDATE participacion
             SET gano_juego = 0,
                 preguntas_aprobadas = NULL,
                 respuestas_json = NULL,
                 fecha_respondio = NULL
             WHERE id = :id'
        );
        $updateParticipant->execute(['id' => $participantId]);

        $pdo->commit();
    } catch (Throwable $throwable) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $throwable;
    }
}

function randomChance(float $probability): bool
{
    $draw = random_int(1, 10000) / 10000;

    return $draw <= $probability;
}

function seedSlotString(DateTimeImmutable $slot): string
{
    return $slot->format('Y-m-d H:i:00');
}

function ensureSeedSlot(DateTimeImmutable $slot): void
{
    $statement = db()->prepare('INSERT IGNORE INTO semillas_horarias (franja_semilla) VALUES (:franja_semilla)');
    $statement->execute(['franja_semilla' => seedSlotString($slot)]);
}

function claimSeedWinner(int $participantId): bool
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $select = $pdo->prepare(
            'SELECT id
             FROM semillas_horarias
             WHERE franja_semilla <= NOW() AND participante_ganador_id IS NULL
             ORDER BY franja_semilla ASC
             LIMIT 1
             FOR UPDATE'
        );
        $select->execute();
        $row = $select->fetch();

        if (!$row) {
            $pdo->commit();
            return false;
        }

        $update = $pdo->prepare(
            'UPDATE semillas_horarias
             SET participante_ganador_id = :participante_id, ganado_en = NOW()
             WHERE id = :id AND participante_ganador_id IS NULL'
        );
        $update->execute([
            'participante_id' => $participantId,
            'id' => $row['id'],
        ]);

        $claimed = $update->rowCount() === 1;
        $pdo->commit();

        return $claimed;
    } catch (Throwable $throwable) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $throwable;
    }
}

function secondsUntilNextSeed(): int
{
    $statement = db()->query(
        'SELECT TIMESTAMPDIFF(SECOND, NOW(), franja_semilla) AS segundos
         FROM semillas_horarias
         WHERE participante_ganador_id IS NULL
         ORDER BY franja_semilla ASC
         LIMIT 1'
    );
    $result = $statement->fetchColumn();

    return $result !== false ? (int) $result : 3000000;
}

function evaluateButton(int $participantId, int $buttonNumber): bool
{
    if ($buttonNumber === 4) {
        return claimSeedWinner($participantId);
    }

    $secondsLeft = secondsUntilNextSeed();
    switch ($buttonNumber) {
        case 1:
            return $secondsLeft < 60 || randomChance(0.60);
        case 2:
            return $secondsLeft < 20 || randomChance(0.40);
        case 3:
            return $secondsLeft < 10 || randomChance(0.10);
        default:
            return false;
    }
}

function otherPosition(int $buttonNumber, int $sentPosition): int
{
    $maxPositions = [1 => 4, 2 => 3, 3 => 3, 4 => 2];
    $others = array_values(array_filter(
        range(1, $maxPositions[$buttonNumber]),
        fn($p) => $p !== $sentPosition
    ));

    return $others[array_rand($others)];
}

function evaluateLastButton(int $participantId): bool
{
    return evaluateButton($participantId, 4);
}

function saveQuestionResults(int $participantId, array $answers, bool $passed): void
{
    $statement = db()->prepare(
        'UPDATE participacion
         SET respuestas_json = :respuestas_json, preguntas_aprobadas = :preguntas_aprobadas, fecha_respondio = NOW()
         WHERE id = :id'
    );

    $statement->execute([
        'respuestas_json' => json_encode($answers, JSON_UNESCAPED_UNICODE),
        'preguntas_aprobadas' => $passed ? 1 : 0,
        'id' => $participantId,
    ]);
}

function nextStepPath(array $participant): string
{
    $participantId = (int) $participant['id'];

    if ($participant['preguntas_aprobadas'] !== null) {
        return 'cierre';
    }

    if (isGameComplete($participantId)) {
        return (int) $participant['gano_juego'] === 1 ? 'preguntas.php' : 'cierre.php';
    }

    if (hasStartedGame($participantId)) {
        return 'juego';
    }

    return 'mecanica';
}

function finalResult(array $participant): string
{
    if ((int) $participant['gano_juego'] === 1 && (int) $participant['preguntas_aprobadas'] === 1) {
        return 'win';
    }

    return 'lose';
}

function getUserByEmail(string $email): ?array
{
    $statement = db()->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    return $user ?: null;
}

function getUserByDni(string $dni): ?array
{
    $statement = db()->prepare('SELECT * FROM usuarios WHERE dni = :dni LIMIT 1');
    $statement->execute(['dni' => $dni]);
    $user = $statement->fetch();

    return $user ?: null;
}

function getTodayParticipationByUserId(int $userId): ?array
{
    $statement = db()->prepare(
        'SELECT *
         FROM participacion
         WHERE usuario_id = :usuario_id AND fecha_participacion = CURDATE()
         LIMIT 1'
    );
    $statement->execute(['usuario_id' => $userId]);
    $participation = $statement->fetch();

    return $participation ?: null;
}

function createParticipation(int $userId): int
{
    $statement = db()->prepare(
        'INSERT INTO participacion (usuario_id, gano_juego, fecha_participacion)
         VALUES (:usuario_id, 0, CURDATE())'
    );
    $statement->execute(['usuario_id' => $userId]);

    return (int) db()->lastInsertId();
}

function loginUserForToday(int $userId): int
{
    $todayParticipation = getTodayParticipationByUserId($userId);
    if ($todayParticipation !== null) {
        return (int) $todayParticipation['id'];
    }

    try {
        return createParticipation($userId);
    } catch (PDOException $exception) {
        $todayParticipation = getTodayParticipationByUserId($userId);
        if ($todayParticipation !== null) {
            return (int) $todayParticipation['id'];
        }

        throw $exception;
    }
}

// Calcular distancia entre dos puntos geográficos usando fórmula de Haversine (en km)
function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $earthRadiusKm = 6371;

    $latFromRad = deg2rad($lat1);
    $latToRad = deg2rad($lat2);
    $deltaLat = deg2rad($lat2 - $lat1);
    $deltaLng = deg2rad($lng2 - $lng1);

    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
         sin($deltaLng / 2) * sin($deltaLng / 2) * cos($latFromRad) * cos($latToRad);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadiusKm * $c;
}

// Obtener el bar más cercano de una cadena dado lat/lng del usuario
function getClosestBar(int $cadenaId, float $latitud, float $longitud): ?array
{
    // Primero: usar aproximación pitagórica (como si la tierra fuera plana) para filtrar rápido los 5 más cercanos
    $sql = 'SELECT id, nombre, latitud, longitud,
                POWER(latitud - :latitud, 2) + POWER(longitud - :longitud, 2) as aprox_distancia
         FROM bares
         WHERE cadena_id = :cadena_id AND latitud IS NOT NULL AND longitud IS NOT NULL
         ORDER BY aprox_distancia ASC
         LIMIT 5';
    
    $params = [
        'cadena_id' => $cadenaId,
        'latitud' => $latitud,
        'longitud' => $longitud,
    ];
    
    $statement = db()->prepare($sql);
    $statement->execute($params);
    $bares = $statement->fetchAll();   

    if (empty($bares)) {
        return null;
    }

    // Segundo: calcular distancia exacta con Haversine solo en los 5 candidatos
    $closestBar = null;
    $closestDistance = PHP_FLOAT_MAX;

    foreach ($bares as $bar) {
        $distance = haversineDistance(
            $latitud,
            $longitud,
            (float) $bar['latitud'],
            (float) $bar['longitud']
        );

        if ($distance < $closestDistance) {
            $closestDistance = $distance;
            $closestBar = [
                'id' => (int) $bar['id'],
                'nombre' => $bar['nombre'],
                'distancia_km' => $closestDistance,
            ];
        }
    }

    return $closestBar;
}
