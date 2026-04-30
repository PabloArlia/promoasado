<?php
require_once __DIR__ . '/init.php';

if (empty($_SESSION['admin'])) {
    header('Location: ' . urladmin . 'index.php');
    exit;
}

$db = db();

// --- Filtro por Cadena ---
$cadenas = $db->query("SELECT id, nombre FROM cadenas ORDER BY nombre ASC")->fetchAll();
$filterCadenaId = isset($_GET['cadena_id']) && $_GET['cadena_id'] !== '' ? (int)$_GET['cadena_id'] : null;

$whereClause = '';
$params = [];
if ($filterCadenaId) {
    $whereClause = ' WHERE c.id = :cadena_id ';
    $params[':cadena_id'] = $filterCadenaId;
}

// --- Consultas para las estadísticas ---

// 1. Participaciones por día
$queryParticipacionesDia = "
    SELECT DATE(p.fecha_participacion) AS dia, COUNT(p.id) AS total
    FROM participacion p
    LEFT JOIN bares b ON p.bar_id = b.id
    LEFT JOIN cadenas c ON b.cadena_id = c.id
    " . ($filterCadenaId ? "WHERE c.id = ?" : "") . "
    GROUP BY dia
    ORDER BY dia DESC
";
$stmt = $db->prepare($queryParticipacionesDia);
$stmt->execute($filterCadenaId ? [$filterCadenaId] : []);
$participacionesPorDia = $stmt->fetchAll();

// 2. Bares con más participaciones (Top 10)
$queryBares = "
    SELECT
        b.nombre as bar_nombre,
        c.nombre as cadena_nombre,
        COUNT(p.id) AS total
    FROM participacion p
    INNER JOIN bares b ON p.bar_id = b.id
    INNER JOIN cadenas c ON b.cadena_id = c.id
    WHERE p.bar_id IS NOT NULL
    " . ($filterCadenaId ? "AND c.id = ?" : "") . "
    GROUP BY b.id, b.nombre, c.nombre
    ORDER BY total DESC
    LIMIT 10
";
$stmt = $db->prepare($queryBares);
$stmt->execute($filterCadenaId ? [$filterCadenaId] : []);
$baresMasParticipaciones = $stmt->fetchAll();

// 3. Participaciones por hora del día
$queryParticipacionesHora = "
    SELECT HOUR(p.fecha_participacion) AS hora, COUNT(p.id) AS total
    FROM participacion p
    LEFT JOIN bares b ON p.bar_id = b.id
    LEFT JOIN cadenas c ON b.cadena_id = c.id
    " . ($filterCadenaId ? "WHERE c.id = ?" : "") . "
    GROUP BY hora
    ORDER BY hora ASC
";
$stmt = $db->prepare($queryParticipacionesHora);
$stmt->execute($filterCadenaId ? [$filterCadenaId] : []);
$participacionesPorHora = $stmt->fetchAll();

// 4. Top 10 usuarios con más participaciones
$queryUsuarios = "
    SELECT
        u.id,
        CAST(AES_DECRYPT(u.nombre, '" . AES_KEY . "') AS CHAR) AS nombre,
        CAST(AES_DECRYPT(u.apellido, '" . AES_KEY . "') AS CHAR) AS apellido,
        COUNT(p.id) AS total_participaciones
    FROM participacion p
    INNER JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN bares b ON p.bar_id = b.id
    LEFT JOIN cadenas c ON b.cadena_id = c.id
    " . ($filterCadenaId ? "WHERE c.id = ?" : "") . "
    GROUP BY u.id, nombre, apellido
    ORDER BY total_participaciones DESC
    LIMIT 10
";
$stmt = $db->prepare($queryUsuarios);
$stmt->execute($filterCadenaId ? [$filterCadenaId] : []);
$usuariosMasActivos = $stmt->fetchAll();

// 5. Estadísticas generales (algunas filtradas, otras no)
$queryStatsGenerales = "
    SELECT
        (SELECT COUNT(*) FROM usuarios) AS total_usuarios,
        (SELECT COUNT(p.id) FROM participacion p
            LEFT JOIN bares b ON p.bar_id = b.id
            LEFT JOIN cadenas c ON b.cadena_id = c.id
            " . ($filterCadenaId ? "WHERE c.id = ?" : "") . "
        ) AS total_participaciones,
        (SELECT COUNT(p.id) FROM participacion p
            LEFT JOIN bares b ON p.bar_id = b.id
            LEFT JOIN cadenas c ON b.cadena_id = c.id
            WHERE p.gano_juego = 1 " . ($filterCadenaId ? "AND c.id = ?" : "") . "
        ) AS total_ganadores_juego,
        (SELECT COUNT(*) FROM semillas_horarias sh
            WHERE sh.participante_ganador_id IS NOT NULL " . ($filterCadenaId ? "AND sh.cadena = ?" : "") . "
        ) AS total_premios_ganados
";
$stmt = $db->prepare($queryStatsGenerales);
$stmt->execute($filterCadenaId ? [$filterCadenaId, $filterCadenaId, $filterCadenaId] : []);
$statsGenerales = $stmt->fetch();


$title = 'Estadísticas';
include 'header.php';
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Estadísticas de la Promoción</h2>
            </div>
            <div class="col-auto ms-auto">
                <form method="get" class="d-flex align-items-center gap-2">
                    <select name="cadena_id" class="form-select" onchange="this.form.submit()" style="min-width: 200px;">
                        <option value="">Todas las cadenas</option>
                        <?php foreach ($cadenas as $cadena): ?>
                            <option value="<?= $cadena['id'] ?>" <?= $filterCadenaId === (int)$cadena['id'] ? 'selected' : '' ?>>
                                <?= esc($cadena['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Fila de Stats Generales -->
        <div class="row row-deck row-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Usuarios Registrados</div>
                        </div>
                        <div class="h1 mb-3"><?= $statsGenerales['total_usuarios'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Participaciones Totales</div>
                        </div>
                        <div class="h1 mb-3"><?= $statsGenerales['total_participaciones'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Ganadores del Juego</div>
                        </div>
                        <div class="h1 mb-3"><?= $statsGenerales['total_ganadores_juego'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Premios Entregados</div>
                        </div>
                        <div class="h1 mb-3"><?= $statsGenerales['total_premios_ganados'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila de Tablas -->
        <div class="row row-cards mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Participaciones por Día</h3></div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Fecha</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php foreach ($participacionesPorDia as $row): ?>
                                <tr><td><?= esc(date('d/m/Y', strtotime($row['dia']))) ?></td><td><?= $row['total'] ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Participaciones por Hora</h3></div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Hora</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php foreach ($participacionesPorHora as $row): ?>
                                <tr><td><?= esc(str_pad((string)$row['hora'], 2, '0', STR_PAD_LEFT) . ':00') ?></td><td><?= $row['total'] ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Top 10 Usuarios más Activos</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Usuario</th><th>Participaciones</th></tr></thead>
                            <tbody>
                                <?php foreach ($usuariosMasActivos as $row): ?>
                                <tr><td><?= esc($row['nombre'] . ' ' . $row['apellido']) ?></td><td><?= $row['total_participaciones'] ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Top 10 Bares con más Participaciones</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>Bar</th><th>Cadena</th><th>Participaciones</th></tr></thead>
                            <tbody>
                                <?php foreach ($baresMasParticipaciones as $row): ?>
                                <tr><td><?= esc($row['bar_nombre']) ?></td><td><?= esc($row['cadena_nombre']) ?></td><td><?= $row['total'] ?></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>