<?php
declare(strict_types=1);

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

function getParticipant(int $participantId): ?array
{
    $statement = db()->prepare('SELECT * FROM participantes WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $participantId]);
    $participant = $statement->fetch();

    return $participant ?: null;
}

function currentParticipant(): ?array
{
    $participantId = currentParticipantId();

    if ($participantId === null) {
        return null;
    }

    return getParticipant($participantId);
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

function isGameComplete(int $participantId): bool
{
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
    $completed = true;
    $won = true;

    foreach ($statuses as $status) {
        if ($status === null) {
            $completed = false;
            $won = false;
            break;
        }

        if ($status !== true) {
            $won = false;
        }
    }

    if ($completed) {
        $statement = db()->prepare('UPDATE participantes SET gano_juego = :gano_juego WHERE id = :id');
        $statement->execute([
            'gano_juego' => $won ? 1 : 0,
            'id' => $participantId,
        ]);
    }

    return [
        'statuses' => $statuses,
        'completed' => $completed,
        'won' => $completed && $won,
    ];
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

function getActiveSeedSlot(DateTimeImmutable $now): DateTimeImmutable
{
    $slot = $now->setTime((int) $now->format('H'), SEED_MINUTE, 0);

    if ($now < $slot) {
        $slot = $slot->modify('-1 hour');
    }

    return $slot;
}

function ensureSeedSlot(DateTimeImmutable $slot): void
{
    $statement = db()->prepare('INSERT IGNORE INTO semillas_horarias (franja_semilla) VALUES (:franja_semilla)');
    $statement->execute(['franja_semilla' => seedSlotString($slot)]);
}

function claimSeedWinner(DateTimeImmutable $slot, int $participantId): bool
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        ensureSeedSlot($slot);

        $select = $pdo->prepare('SELECT id, participante_ganador_id FROM semillas_horarias WHERE franja_semilla = :franja_semilla LIMIT 1 FOR UPDATE');
        $select->execute(['franja_semilla' => seedSlotString($slot)]);
        $row = $select->fetch();

        if (!$row || $row['participante_ganador_id'] !== null) {
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

function evaluateLastButton(int $participantId): bool
{
    $now = new DateTimeImmutable();
    $activeSlot = getActiveSeedSlot($now);

    if (claimSeedWinner($activeSlot, $participantId)) {
        return true;
    }

    $nextSlot = $activeSlot->modify('+1 hour');
    $minutesToNextSeed = max(0, ($nextSlot->getTimestamp() - $now->getTimestamp()) / 60);

    if ($minutesToNextSeed > PRESEED_WINDOW_MINUTES) {
        return false;
    }

    $progress = 1 - ($minutesToNextSeed / PRESEED_WINDOW_MINUTES);
    $probability = 0.10 + ($progress * 0.55);

    return randomChance($probability);
}

function saveQuestionResults(int $participantId, array $answers, bool $passed): void
{
    $statement = db()->prepare(
        'UPDATE participantes
         SET respuestas_json = :respuestas_json, preguntas_aprobadas = :preguntas_aprobadas, respondido_en = NOW()
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
