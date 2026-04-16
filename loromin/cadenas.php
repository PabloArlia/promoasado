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
        $id            = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $nombre        = trim($_POST['nombre'] ?? '');
        $identificador = trim($_POST['identificador'] ?? '');
        $logo          = trim($_POST['logo_actual'] ?? '') ?: null;

        $errs = [];
        if ($nombre === '')        $errs[] = 'El nombre es obligatorio.';
        if ($identificador === '') $errs[] = 'El identificador es obligatorio.';

        if (empty($errs) && !empty($_FILES['logo']['tmp_name'])) {
            $ext     = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowed = ['png','jpg','jpeg','gif','webp','svg'];
            if (!in_array($ext, $allowed, true)) {
                $errs[] = 'Formato de imagen no permitido.';
            } else {
                $slug     = preg_replace('/[^a-z0-9\-]/', '', strtolower($identificador));
                $filename = $slug . '.' . $ext;
                $dir      = __DIR__ . '/../img/cadenas/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $dir . $filename)) {
                    $logo = 'img/cadenas/' . $filename;
                } else {
                    $errs[] = 'No se pudo guardar la imagen.';
                }
            }
        }

        if ($errs) { http_response_code(422); echo json_encode(['errors' => $errs]); exit; }

        if ($id) {
            $db->prepare("UPDATE cadenas SET nombre=?, identificador=?, logo=? WHERE id=?")->execute([$nombre, $identificador, $logo, $id]);
        } else {
            $db->prepare("INSERT INTO cadenas (nombre, identificador, logo) VALUES (?,?,?)")->execute([$nombre, $identificador, $logo]);
            $id = (int)$db->lastInsertId();
        }
        echo json_encode(['ok' => true, 'id' => $id, 'logo' => $logo]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $db->prepare("DELETE FROM cadenas WHERE id=?")->execute([$id]);
        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['errors' => ['Accion desconocida.']]); exit;
}

$cadenas = $db->query("SELECT * FROM cadenas ORDER BY id ASC")->fetchAll();
$title = 'Cadenas';
include 'header.php';
?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">Cadenas</h2></div>
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
                                <th>Identificador</th>
                                <th style="width:240px">Logo</th>
                                <th style="width:110px">Bares</th>
                                <th style="width:165px"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cadenas as $c): ?>
                        <tr data-id="<?= (int)$c['id'] ?>">
                            <td class="text-muted"><?= (int)$c['id'] ?></td>
                            <td><input type="text" class="form-control f-nombre" value="<?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>"></td>
                            <td><input type="text" class="form-control f-identificador" value="<?= htmlspecialchars($c['identificador'], ENT_QUOTES, 'UTF-8') ?>"></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($c['logo']): ?>
                                    <img class="logo-preview" src="<?= htmlspecialchars('../' . $c['logo'], ENT_QUOTES, 'UTF-8') ?>" style="height:40px;object-fit:contain;flex-shrink:0">
                                    <?php else: ?>
                                    <span class="logo-preview text-muted" style="flex-shrink:0">—</span>
                                    <?php endif; ?>
                                    <input type="file" class="form-control f-logo-file" accept="image/*">
                                    <input type="hidden" class="f-logo-actual" value="<?= htmlspecialchars($c['logo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </td>
                            <td><a href="bares.php?cadena=<?= (int)$c['id'] ?>" class="btn btn-ghost-secondary">Ver bares</a></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-save">&#10003; Guardar</button>
                                    <button class="btn btn-outline-danger btn-delete">&#x2715;</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr data-id="" id="row-new">
                            <td class="text-muted">—</td>
                            <td><input type="text" class="form-control f-nombre" placeholder="Nombre"></td>
                            <td><input type="text" class="form-control f-identificador" placeholder="identificador-slug"></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="file" class="form-control f-logo-file" accept="image/*">
                                    <input type="hidden" class="f-logo-actual" value="">
                                </div>
                            </td>
                            <td></td>
                            <td>
                                <button class="btn btn-primary btn-save">+ Agregar</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function showFlash(msg) {
    var el = document.getElementById('flash');
    el.textContent = msg; el.style.display = '';
    setTimeout(function(){ el.style.display = 'none'; }, 3000);
}
function showErr(msgs) {
    var el = document.getElementById('errbox');
    el.innerHTML = msgs.map(function(m){ return '<div>'+m+'</div>'; }).join('');
    el.style.display = '';
}
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-save');
    if (btn) {
        var row    = btn.closest('tr');
        var nombre = row.querySelector('.f-nombre').value.trim();
        var ident  = row.querySelector('.f-identificador').value.trim();
        if (!nombre || !ident) { showErr(['Nombre e identificador son obligatorios.']); return; }
        var fd = new FormData();
        fd.append('action', 'save');
        fd.append('id', row.dataset.id || '');
        fd.append('nombre', nombre);
        fd.append('identificador', ident);
        fd.append('logo_actual', row.querySelector('.f-logo-actual').value);
        var fi = row.querySelector('.f-logo-file');
        if (fi && fi.files.length) fd.append('logo', fi.files[0]);
        btn.disabled = true;
        fetch('cadenas.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                btn.disabled = false;
                if (data.errors) { showErr(data.errors); return; }
                document.getElementById('errbox').style.display = 'none';
                var isNew = !row.dataset.id;
                if (isNew && data.id) {
                    row.dataset.id = data.id;
                    row.cells[0].textContent = data.id;
                    row.cells[4].innerHTML = '<a href="bares.php?cadena=' + data.id + '" class="btn btn-ghost-secondary">Ver bares</a>';
                    var nr = document.getElementById('row-new');
                    if (nr) { nr.querySelector('.f-nombre').value=''; nr.querySelector('.f-identificador').value=''; var nf=nr.querySelector('.f-logo-file'); if(nf) nf.value=''; }
                }
                if (data.logo) {
                    var prev = row.querySelector('.logo-preview');
                    if (prev && prev.tagName === 'IMG') {
                        prev.src = '../' + data.logo + '?t=' + Date.now();
                    } else if (prev) {
                        var img = document.createElement('img');
                        img.className = 'logo-preview';
                        img.style.cssText = 'height:40px;object-fit:contain;flex-shrink:0';
                        img.src = '../' + data.logo + '?t=' + Date.now();
                        prev.replaceWith(img);
                    }
                    row.querySelector('.f-logo-actual').value = data.logo;
                    if (fi) fi.value = '';
                }
                showFlash('Guardado correctamente.');
            })
            .catch(function(){ btn.disabled = false; showErr(['Error de red.']); });
    }
    var bdel = e.target.closest('.btn-delete');
    if (bdel) {
        if (!confirm('Eliminar cadena y todos sus bares?')) return;
        var fd2 = new FormData();
        fd2.append('action', 'delete');
        fd2.append('id', bdel.closest('tr').dataset.id);
        fetch('cadenas.php', { method: 'POST', body: fd2 })
            .then(function(r){ return r.json(); })
            .then(function(d){ if (d.ok) bdel.closest('tr').remove(); });
    }
});
</script>

<?php include 'footer.php'; ?>
