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
        r.id,
        r.nombre,
        r.correo,
        r.telefono,
        r.fecha_nacimiento,
        r.estado,
        r.concesionaria,
        r.kia_fidelity,
        r.modelo,
        r.vin,
        r.created_at,
        jr.numero_ingresado
    FROM registros r
    LEFT JOIN juego_resultados jr ON jr.registro_id = r.id
    ORDER BY jr.numero_ingresado ASC, r.id ASC
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
    echo '<th>Numero ingresado</th>';
    echo '<th>Nombre</th>';
    echo '<th>Correo</th>';
    echo '<th>Telefono</th>';
    echo '<th>Fecha nac.</th>';
    echo '<th>Estado</th>';
    echo '<th>Concesionaria</th>';
    echo '<th>Fidelity</th>';
    echo '<th>Modelo</th>';
    echo '<th>VIN</th>';
    echo '<th>Registro</th>';
    echo '</tr>';

    foreach ($usuarios as $u) {
        echo '<tr>';
        echo '<td>' . (int)$u['id'] . '</td>';
        echo '<td>' . ($u['numero_ingresado'] !== null ? (int)$u['numero_ingresado'] : '') . '</td>';
        echo '<td>' . htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['telefono'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['fecha_nacimiento'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['estado'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['concesionaria'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . ($u['kia_fidelity'] ? 'Si' : 'No') . '</td>';
        echo '<td>' . htmlspecialchars($u['modelo'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['vin'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($u['created_at'], ENT_QUOTES, 'UTF-8') . '</td>';
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
                                <th>Número ingresado</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Fecha nac.</th>
                                <th>Estado</th>
                                <th>Concesionaria</th>
                                <th>Fidelity</th>
                                <th>Modelo</th>
                                <th>VIN</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">Sin registros aún.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td class="text-muted"><?= (int)$u['id'] ?></td>
                                <td>
                                    <?php if ($u['numero_ingresado'] !== null): ?>
                                        <strong><?= (int)$u['numero_ingresado'] ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['fecha_nacimiento'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['estado'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($u['concesionaria'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if ($u['kia_fidelity']): ?>
                                        <span class="badge bg-green-lt">Sí</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-lt">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($u['modelo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><code><?= htmlspecialchars($u['vin'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td class="text-muted small"><?= htmlspecialchars($u['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
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
