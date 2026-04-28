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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id     = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $nombre = trim($_POST['nombre'] ?? '');
        $imagen = trim($_POST['imagen'] ?? '') ?: null;

        $errs = [];
        if ($nombre === '') $errs[] = 'El nombre es obligatorio.';

        if ($errs) { http_response_code(422); echo json_encode(['errors' => $errs]); exit; }

        if ($id) {
            $db->prepare("UPDATE premio SET nombre=?, imagen=? WHERE id=?")->execute([$nombre, $imagen, $id]);
        } else {
            $db->prepare("INSERT INTO premio (nombre, imagen) VALUES (?,?)")->execute([$nombre, $imagen]);
            $id = (int)$db->lastInsertId();
        }
        echo json_encode(['ok' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $db->prepare("DELETE FROM premio WHERE id=?")->execute([$id]);
        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['errors' => ['Accion desconocida.']]); exit;
}

$premios = $db->query("SELECT * FROM premio ORDER BY id ASC")->fetchAll();
$title = 'Premios';
include 'header.php';
?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">Premios</h2></div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create">
                        <svg class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Nuevo Premio
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div id="flash" class="alert alert-success mb-3" style="display:none"></div>
        <div id="errbox" class="alert alert-danger mb-3" style="display:none"></div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nombre</th>
                                <th>Imagen</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($premios as $premio): ?>
                            <tr>
                                <td><?php echo $premio['id']; ?></td>
                                <td><?php echo esc($premio['nombre']); ?></td>
                                <td><?php if ($premio['imagen']): ?><img src="<?php echo esc($premio['imagen']); ?>" alt="" style="max-width:100px; max-height:50px;"> <?php echo esc($premio['imagen']); ?><?php endif; ?></td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-<?php echo $premio['id']; ?>">
                                            Editar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(<?php echo $premio['id']; ?>)">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal modal-blur fade" id="modal-create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Premio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-create">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Imagen</label>
                                <input type="text" class="form-control" name="imagen" placeholder="Ruta de la imagen">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals Edit -->
<?php foreach ($premios as $premio): ?>
<div class="modal modal-blur fade" id="modal-edit-<?php echo $premio['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Premio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-edit" data-id="<?php echo $premio['id']; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" value="<?php echo esc($premio['nombre']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Imagen</label>
                                <input type="text" class="form-control" name="imagen" value="<?php echo esc($premio['imagen'] ?? ''); ?>" placeholder="Ruta de la imagen">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function deleteItem(id) {
    if (confirm('¿Eliminar este premio?')) {
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=delete&id=' + id
        }).then(r => r.json()).then(data => {
            if (data.ok) location.reload();
            else alert('Error');
        });
    }
}

document.getElementById('form-create').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'save');
    fetch('', {method: 'POST', body: formData}).then(r => r.json()).then(data => {
        if (data.ok) location.reload();
        else {
            document.getElementById('errbox').innerHTML = data.errors.join('<br>');
            document.getElementById('errbox').style.display = 'block';
        }
    });
});

document.querySelectorAll('.form-edit').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        formData.append('id', this.dataset.id);
        fetch('', {method: 'POST', body: formData}).then(r => r.json()).then(data => {
            if (data.ok) location.reload();
            else {
                document.getElementById('errbox').innerHTML = data.errors.join('<br>');
                document.getElementById('errbox').style.display = 'block';
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>