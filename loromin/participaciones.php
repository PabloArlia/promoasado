<?php
require_once __DIR__ . '/init.php';

if (empty($_SESSION['admin'])) {
    header('Location: ' . urladmin . 'index.php');
    exit;
}

$statement = db()->prepare("
    SELECT
        part.id,
        CAST(AES_DECRYPT(u.nombre, :key1) AS CHAR) AS nombre,
        CAST(AES_DECRYPT(u.apellido, :key2) AS CHAR) AS apellido,
        CAST(AES_DECRYPT(u.email, :key3) AS CHAR) AS email,
        CAST(AES_DECRYPT(u.celular, :key4) AS CHAR) AS celular,
        CAST(AES_DECRYPT(u.dni, :key5) AS CHAR) AS dni,
        b.nombre as bar,
        c.nombre as cadena,
        part.fecha_participacion,
        part.preguntas_aprobadas,
        part.gano_juego,
        part.latitud,
        part.longitud,
        part.distancia_km,
        part.fecha_respondio,
        p.nombre as premio_nombre,
        p.ganaste as premio_mensaje,
        p.imagen as premio_imagen,
        GROUP_CONCAT(CONCAT(ib.numero_boton, ':', ib.resultado) ORDER BY ib.numero_boton SEPARATOR ',') as botones_resultados
    FROM participacion part
    JOIN usuarios u ON u.id = part.usuario_id
    LEFT JOIN bares b ON b.id = part.bar_id
    LEFT JOIN cadenas c ON c.id = b.cadena_id
    LEFT JOIN semillas_horarias sh ON sh.participante_ganador_id = part.id
    LEFT JOIN premio p ON p.id = sh.premio
    LEFT JOIN intentos_botones ib ON ib.participante_id = part.id
    GROUP BY part.id
    ORDER BY part.fecha_participacion DESC
");

$statement->execute([
    ':key1' => AES_KEY,
    ':key2' => AES_KEY,
    ':key3' => AES_KEY,
    ':key4' => AES_KEY,
    ':key5' => AES_KEY,
]);

$participaciones = $statement->fetchAll();

// Función para parsear botones del GROUP_CONCAT
function parseButtonResults(string $botones_string): array {
    $resultados = [];
    if (empty($botones_string)) {
        return $resultados;
    }
    foreach (explode(',', $botones_string) as $item) {
        [$numero, $resultado] = explode(':', $item);
        $resultados[(int)$numero] = (bool)(int)$resultado;
    }
    return $resultados;
}

if (isset($_GET['xls']) && $_GET['xls'] === '1') {
    $filename = 'participaciones_' . date('Ymd_His') . '.xls';

    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo "<table border=\"1\">";
    echo '<tr>';
    echo '<th>#</th>';
    echo '<th>Nombre</th>';
    echo '<th>Apellido</th>';
    echo '<th>Email</th>';
    echo '<th>Celular</th>';
    echo '<th>DNI</th>';
    echo '<th>Bar</th>';
    echo '<th>Fecha Participación</th>';
    echo '<th>Preguntas Aprobadas</th>';
    echo '<th>Ganó Juego</th>';
    echo '<th>Botón 1</th>';
    echo '<th>Botón 2</th>';
    echo '<th>Botón 3</th>';
    echo '<th>Botón 4</th>';
    echo '<th>Distancia (km)</th>';
    echo '<th>Fecha Respuesta</th>';
    echo '<th>Premio</th>';
    echo '</tr>';

    foreach ($participaciones as $part) {
        $resultados_botones = parseButtonResults($part['botones_resultados'] ?? '');
        
        echo '<tr>';
        echo '<td>' . (int)$part['id'] . '</td>';
        echo '<td>' . htmlspecialchars($part['nombre'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($part['apellido'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($part['email'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($part['celular'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($part['dni'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($part['bar'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($part['fecha_participacion'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . ($part['preguntas_aprobadas'] ? 'Sí' : 'No') . '</td>';
        echo '<td>' . ($part['gano_juego'] ? 'Sí' : 'No') . '</td>';
        echo '<td>' . ($resultados_botones[1] ?? '-' ? 'Ganó' : 'Perdió') . '</td>';
        echo '<td>' . ($resultados_botones[2] ?? '-' ? 'Ganó' : 'Perdió') . '</td>';
        echo '<td>' . ($resultados_botones[3] ?? '-' ? 'Ganó' : 'Perdió') . '</td>';
        echo '<td>' . ($resultados_botones[4] ?? '-' ? 'Ganó' : 'Perdió') . '</td>';
        echo '<td>' . (float)($part['distancia_km'] ?? 0) . '</td>';
        echo '<td>' . htmlspecialchars($part['fecha_respondio'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($part['premio_nombre'] ?? 'Sin premio', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    exit;
}

include 'header.php';
?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Participaciones</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="?xls=1" class="btn btn-success me-2">Descargar XLS</a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped card-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Celular</th>
                                <th>Bar</th>
                                <th>Fecha</th>
                                <th>Gano</th>
                                <th>Botón 1</th>
                                <th>Botón 2</th>
                                <th>Botón 3</th>
                                <th>Botón 4</th>
                                <th>Preguntas</th>
                                <th>Distancia</th>
                                <th>Premio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($participaciones): ?>
                                <?php foreach ($participaciones as $part): ?>
                                <tr>
                                    <td>
                                        <span class="text-secondary"><?=(int)$part['id']?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex py-1 align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="mt-0 mb-2 fw-bold"><?=htmlspecialchars($part['nombre'] ?? '', ENT_QUOTES, 'UTF-8')?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?=htmlspecialchars($part['apellido'] ?? '', ENT_QUOTES, 'UTF-8')?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?=htmlspecialchars($part['email'], ENT_QUOTES, 'UTF-8')?>">
                                            <?=htmlspecialchars($part['email'] ?? '', ENT_QUOTES, 'UTF-8')?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:<?=htmlspecialchars($part['celular'], ENT_QUOTES, 'UTF-8')?>">
                                            <?=htmlspecialchars($part['celular'] ?? '', ENT_QUOTES, 'UTF-8')?>
                                        </a>
                                    </td>
                                    <td>
                                        <?=htmlspecialchars($part['bar'] ?? 'N/A', ENT_QUOTES, 'UTF-8')?>
                                    </td>
                                    <td>
                                        <?=htmlspecialchars($part['fecha_participacion'], ENT_QUOTES, 'UTF-8')?>
                                    </td>
                                    <td>
                                        <span class="text-dark-fg badge bg-<?=$part['gano_juego'] ? 'success' : 'warning'?>">
                                            <?=$part['gano_juego'] ? 'Ganó' : 'Perdió'?>
                                        </span>
                                    </td>
                                    <?php 
                                        $resultados_botones = parseButtonResults($part['botones_resultados'] ?? '');
                                    ?>
                                    <td>
                                        <?php if (isset($resultados_botones[1])): ?>
                                            <span class="text-dark-fg badge bg-<?=$resultados_botones[1] ? 'success' : 'danger'?>">
                                                <?=$resultados_botones[1] ? 'Ganó' : 'Perdió'?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($resultados_botones[2])): ?>
                                            <span class="text-dark-fg badge bg-<?=$resultados_botones[2] ? 'success' : 'danger'?>">
                                                <?=$resultados_botones[2] ? 'Ganó' : 'Perdió'?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($resultados_botones[3])): ?>
                                            <span class="text-dark-fg badge bg-<?=$resultados_botones[3] ? 'success' : 'danger'?>">
                                                <?=$resultados_botones[3] ? 'Ganó' : 'Perdió'?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($resultados_botones[4])): ?>
                                            <span class="text-dark-fg badge bg-<?=$resultados_botones[4] ? 'success' : 'danger'?>">
                                                <?=$resultados_botones[4] ? 'Ganó' : 'Perdió'?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-dark-fg badge bg-<?=$part['preguntas_aprobadas'] ? 'success' : 'danger'?>">
                                            <?=$part['preguntas_aprobadas'] ? 'Ok' : 'Mal'?>
                                        </span>
                                    </td>
                                    <td>
                                        <?=number_format((float)($part['distancia_km'] ?? 0), 2)?>
                                    </td>
                                    <td>
                                        <?php if ($part['premio_nombre']): ?>
                                            <span class="text-success">
                                                <?=htmlspecialchars($part['premio_nombre'], ENT_QUOTES, 'UTF-8')?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin premio</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">
                                        No hay participaciones registradas
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
