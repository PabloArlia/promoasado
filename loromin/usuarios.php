<?php
require_once __DIR__ . '/init.php';

if (empty($_SESSION['admin'])) {
    header('Location: ' . urladmin . 'index.php');
    exit;
}

$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

$usuarios = db()->query("
    SELECT
        u.id,
        u.nombre,
        u.apellido,
        u.email,
        u.celular,
        u.dni,
        u.acepta_bases,
        u.fecha_registro,
        COUNT(p.id) as participaciones,
        SUM(CASE WHEN p.gano_juego = 1 THEN 1 ELSE 0 END) as juegos_ganados
    FROM usuarios u
    LEFT JOIN participacion p ON p.usuario_id = u.id
    GROUP BY u.id
    ORDER BY u.fecha_registro DESC
")->fetchAll();

if (isset($_GET['xls']) && $_GET['xls'] === '1') {
    $filename = 'usuarios_' . date('Ymd_His') . '.xls';

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
    echo '<th>Particip.</th>';
    echo '<th>Ganados</th>';
    echo '<th>Bases</th>';
    echo '<th>Registro</th>';
    echo '</tr>';

    foreach ($usuarios as $u) {
        echo '<tr>';
        echo '<td>' . (int)$u['id'] . '</td>';
        echo '<td>' . htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['apellido'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['celular'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['dni'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . (int)$u['participaciones'] . '</td>';
        echo '<td>' . (int)$u['juegos_ganados'] . '</td>';
        echo '<td>' . ($u['acepta_bases'] ? 'Sí' : 'No') . '</td>';
        echo '<td>' . htmlspecialchars($u['fecha_registro'], ENT_QUOTES, 'UTF-8') . '</td>';
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
                <h2 class="page-title">Usuarios registrados</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="?xls=1" class="btn btn-success me-2">Descargar XLS</a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <?php if ($flash): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php endif; ?>
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
                                <th>DNI</th>
                                <th>Particip.</th>
                                <th>Ganados</th>
                                <th>Bases</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Sin registros aún.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td class="text-muted"><?= (int)$u['id'] ?></td>
                                <td><strong><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($u['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['celular'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><code><?= htmlspecialchars($u['dni'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td class="text-center">
                                    <span class="badge bg-blue-lt"><?= (int)$u['participaciones'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ((int)$u['juegos_ganados'] > 0): ?>
                                        <span class="badge bg-green-lt"><?= (int)$u['juegos_ganados'] ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u['acepta_bases']): ?>
                                        <span class="badge bg-green-lt">Sí</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-lt">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($u['fecha_registro'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
