<?php
require_once __DIR__ . '/init.php';

if (empty($_SESSION['admin'])) {
    header('Location: ' . urladmin . 'index.php');
    exit;
}

$db = db();
$flash  = $_SESSION['admin_flash'] ?? null;
$errors = $_SESSION['admin_errors'] ?? null;
unset($_SESSION['admin_flash'], $_SESSION['admin_errors']);

// Filtro por cadena
$filterCadena = isset($_GET['cadena']) && $_GET['cadena'] !== '' ? (int)$_GET['cadena'] : 0;

// --- Acciones POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id        = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $cadena_id = (int)($_POST['cadena_id'] ?? 0);
        $nombre    = trim($_POST['nombre'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $horario   = trim($_POST['horario'] ?? '') ?: null;
        $latitud   = trim($_POST['latitud'] ?? '') ?: null;
        $longitud  = trim($_POST['longitud'] ?? '') ?: null;

        $errs = [];
        if (!$cadena_id) $errs[] = 'Seleccione una cadena.';
        if ($nombre === '') $errs[] = 'El nombre es obligatorio.';
        if ($direccion === '') $errs[] = 'La dirección es obligatoria.';

        if ($errs) {
            $_SESSION['admin_errors'] = $errs;
        } else {
            if ($id) {
                $st = $db->prepare("UPDATE bares SET cadena_id=?, nombre=?, direccion=?, horario=?, latitud=?, longitud=? WHERE id=?");
                $st->execute([$cadena_id, $nombre, $direccion, $horario, $latitud, $longitud, $id]);
                $_SESSION['admin_flash'] = 'Bar actualizado.';
            } else {
                $st = $db->prepare("INSERT INTO bares (cadena_id, nombre, direccion, horario, latitud, longitud) VALUES (?,?,?,?,?,?)");
                $st->execute([$cadena_id, $nombre, $direccion, $horario, $latitud, $longitud]);
                $_SESSION['admin_flash'] = 'Bar creado.';
            }
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $db->prepare("DELETE FROM bares WHERE id=?")->execute([$id]);
            $_SESSION['admin_flash'] = 'Bar eliminado.';
        }
    }

    $qs = $filterCadena ? '?cadena=' . $filterCadena : '';
    header('Location: bares.php' . $qs);
    exit;
}

// --- Edición ---
$editing = null;
if (isset($_GET['edit'])) {
    $st = $db->prepare("SELECT * FROM bares WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editing = $st->fetch();
    if ($editing && !$filterCadena) $filterCadena = (int)$editing['cadena_id'];
}

// Listado de cadenas para el select
$cadenas = $db->query("SELECT id, nombre FROM cadenas ORDER BY nombre ASC")->fetchAll();

// Bares (filtrados o todos)
if ($filterCadena) {
    $st = $db->prepare("
        SELECT b.*, c.nombre AS cadena_nombre
        FROM bares b
        JOIN cadenas c ON c.id = b.cadena_id
        WHERE b.cadena_id = ?
        ORDER BY b.nombre ASC
    ");
    $st->execute([$filterCadena]);
} else {
    $st = $db->query("
        SELECT b.*, c.nombre AS cadena_nombre
        FROM bares b
        JOIN cadenas c ON c.id = b.cadena_id
        ORDER BY c.nombre ASC, b.nombre ASC
    ");
}
$bares = $st->fetchAll();

// Cadena activa para el filtro
$cadenaActiva = null;
if ($filterCadena) {
    foreach ($cadenas as $c) {
        if ((int)$c['id'] === $filterCadena) { $cadenaActiva = $c; break; }
    }
}

$title = 'Bares';
include 'header.php';
?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Bares<?= $cadenaActiva ? ' — ' . htmlspecialchars($cadenaActiva['nombre'], ENT_QUOTES, 'UTF-8') : '' ?>
                </h2>
            </div>
            <div class="col-auto ms-auto d-flex gap-2 align-items-center">
                <!-- Filtro rápido por cadena -->
                <form method="get" class="d-flex align-items-center gap-2">
                    <select name="cadena" class="form-select " onchange="this.form.submit()" style="min-width:160px">
                        <option value="">Todas las cadenas</option>
                        <?php foreach ($cadenas as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (int)$c['id'] === $filterCadena ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBar">
                    + Nuevo bar
                </button>
                <a href="cadenas.php" class="btn btn-outline-secondary">Cadenas</a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <?php if ($flash): ?>
        <div class="alert alert-success mb-3"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
        <div class="alert alert-danger mb-3">
            <ul class="mb-0"><?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped card-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cadena</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Horario</th>
                                <th>Lat / Lon</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bares)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Sin bares aún.</td></tr>
                            <?php else: foreach ($bares as $b): ?>
                            <tr>
                                <td class="text-muted"><?= (int)$b['id'] ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($b['cadena_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><strong><?= htmlspecialchars($b['nombre'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($b['direccion'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($b['horario'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="small text-muted">
                                    <?php if ($b['latitud'] && $b['longitud']): ?>
                                        <?= htmlspecialchars($b['latitud'], ENT_QUOTES, 'UTF-8') ?>,
                                        <?= htmlspecialchars($b['longitud'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php else: ?>
                                        <span class="text-danger">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="bares.php?edit=<?= (int)$b['id'] ?><?= $filterCadena ? '&cadena='.$filterCadena : '' ?>"
                                       class="btn btn-sm btn-outline-secondary me-1">Editar</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este bar?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal nuevo/editar bar -->
<div class="modal modal-blur fade <?= $editing ? 'show' : '' ?>" id="modalBar" tabindex="-1"
     style="<?= $editing ? 'display:block' : '' ?>" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="action" value="save">
                <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?= $editing ? 'Editar bar' : 'Nuevo bar' ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Cadena</label>
                        <select name="cadena_id" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            <?php foreach ($cadenas as $c):
                                $sel = ($editing && (int)$editing['cadena_id'] === (int)$c['id'])
                                    || (!$editing && $filterCadena === (int)$c['id']); ?>
                            <option value="<?= (int)$c['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Nombre / sucursal</label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($editing['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="<?= htmlspecialchars($editing['direccion'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horario</label>
                        <input type="text" name="horario" class="form-control"
                               value="<?= htmlspecialchars($editing['horario'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Latitud</label>
                            <input type="number" name="latitud" step="any" class="form-control"
                                   value="<?= htmlspecialchars($editing['latitud'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Longitud</label>
                            <input type="number" name="longitud" step="any" class="form-control"
                                   value="<?= htmlspecialchars($editing['longitud'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="bares.php<?= $filterCadena ? '?cadena='.$filterCadena : '' ?>" class="btn btn-link me-auto">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if ($editing): ?>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<?php include 'footer.php'; ?>
