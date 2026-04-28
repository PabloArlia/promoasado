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
        $id             = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $franja_semilla = trim($_POST['franja_semilla'] ?? '');
        $premio         = isset($_POST['premio']) && $_POST['premio'] !== '' ? (int)$_POST['premio'] : null;
        $cadena         = isset($_POST['cadena']) && $_POST['cadena'] !== '' ? (int)$_POST['cadena'] : null;

        $errs = [];
        if ($franja_semilla === '') $errs[] = 'La franja de semilla es obligatoria.';
        if (!$cadena) $errs[] = 'Seleccione una cadena.';

        if ($errs) {
            $_SESSION['admin_errors'] = $errs;
        } else {
            if ($id) {
                $st = $db->prepare("UPDATE semillas_horarias SET franja_semilla=?, premio=?, cadena=? WHERE id=?");
                $st->execute([$franja_semilla, $premio, $cadena, $id]);
                $_SESSION['admin_flash'] = 'Semilla horaria actualizada.';
            } else {
                $st = $db->prepare("INSERT INTO semillas_horarias (franja_semilla, premio, cadena) VALUES (?,?,?)");
                $st->execute([$franja_semilla, $premio, $cadena]);
                $_SESSION['admin_flash'] = 'Semilla horaria creada.';
            }
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $db->prepare("DELETE FROM semillas_horarias WHERE id=?")->execute([$id]);
            $_SESSION['admin_flash'] = 'Semilla horaria eliminada.';
        }
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Listado
$where = [];
$params = [];
if ($filterCadena) {
    $where[] = 'sh.cadena = ?';
    $params[] = $filterCadena;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT sh.*, c.nombre AS cadena_nombre, p.nombre AS premio_nombre
          FROM semillas_horarias sh
          LEFT JOIN cadenas c ON sh.cadena = c.id
          LEFT JOIN premio p ON sh.premio = p.id
          $whereSql
          ORDER BY sh.franja_semilla ASC";
$st = $db->prepare($query);
$st->execute($params);
$semillas = $st->fetchAll();

// Listado de cadenas para el select
$cadenas = $db->query("SELECT id, nombre FROM cadenas ORDER BY nombre ASC")->fetchAll();

// Listado de premios para el select
$premios = $db->query("SELECT id, nombre FROM premio ORDER BY nombre ASC")->fetchAll();

$editing = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $st = $db->prepare("SELECT * FROM semillas_horarias WHERE id=?");
        $st->execute([$editId]);
        $editing = $st->fetch();
    } else {
        // Nuevo
        $editing = ['id' => 0, 'franja_semilla' => '', 'premio' => null, 'cadena' => $filterCadena ?: null];
    }
    if ($editing && !$filterCadena && isset($editing['cadena'])) $filterCadena = (int)$editing['cadena'];
}

$title = 'Sembrados Horarios';
include 'header.php';
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Sembrados Horarios</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="?<?php echo $filterCadena ? 'cadena=' . $filterCadena . '&' : ''; ?>edit=0" class="btn btn-primary">
                        <svg class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Nuevo Sembrado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <?php if ($editing !== null): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title"><?php echo $editing['id'] ? 'Editar' : 'Nuevo'; ?> Sembrado Horario</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="save">
                    <?php if ($editing['id']): ?>
                    <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Franja Semilla</label>
                                <input type="datetime-local" class="form-control" name="franja_semilla" value="<?php echo esc($editing['franja_semilla'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Premio</label>
                                <select name="premio" class="form-select">
                                    <option value="">Sin premio</option>
                                    <?php foreach ($premios as $premio): ?>
                                    <option value="<?php echo $premio['id']; ?>" <?php echo ($editing['premio'] ?? null) == $premio['id'] ? 'selected' : ''; ?>><?php echo esc($premio['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Cadena</label>
                                <select name="cadena" class="form-select" required>
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($cadenas as $cadena): ?>
                                    <option value="<?php echo $cadena['id']; ?>" <?php echo ($editing['cadena'] ?? null) == $cadena['id'] ? 'selected' : ''; ?>><?php echo esc($cadena['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="?<?php echo $filterCadena ? 'cadena=' . $filterCadena : ''; ?>" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($flash): ?>
        <div class="alert alert-success"><?php echo esc($flash); ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET">
                    <label class="form-label">Filtrar por Cadena</label>
                    <select name="cadena" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <?php foreach ($cadenas as $cadena): ?>
                        <option value="<?php echo $cadena['id']; ?>" <?php echo $filterCadena === (int)$cadena['id'] ? 'selected' : ''; ?>><?php echo esc($cadena['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Franja Semilla</th>
                            <th>Premio</th>
                            <th>Cadena</th>
                            <th>Ganador</th>
                            <th>Ganado En</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semillas as $semilla): ?>
                        <tr>
                            <td><?php echo esc($semilla['franja_semilla']); ?></td>
                            <td><?php echo esc($semilla['premio_nombre'] ?? 'N/A'); ?></td>
                            <td><?php echo esc($semilla['cadena_nombre'] ?? 'N/A'); ?></td>
                            <td><?php echo $semilla['participante_ganador_id'] ? 'Sí' : 'No'; ?></td>
                            <td><?php echo esc($semilla['ganado_en'] ?? ''); ?></td>
                            <td>
                                <a href="?<?php echo $filterCadena ? 'cadena=' . $filterCadena . '&' : ''; ?>edit=<?php echo $semilla['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $semilla['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>