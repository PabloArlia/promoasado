<?php
require_once __DIR__ . '/init.php';

if (empty($_SESSION['admin'])) {
    header('Location: ' . urladmin . 'index.php');
    exit;
}

$db = db();
$limpieza_completada = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'confirmar_limpiar') {
        try {
            $db->exec("TRUNCATE TABLE intentos_botones");
            $db->exec("TRUNCATE TABLE participacion");
            $db->exec("TRUNCATE TABLE usuarios");
            $db->exec("UPDATE semillas_horarias SET participante_ganador_id = NULL, ganado_en = NULL");
            
            $limpieza_completada = true;
        } catch (Exception $e) {
            $error = 'Error al limpiar la base de datos: ' . $e->getMessage();
        }
    }
}

$title = 'Limpiar Base de Datos';
include 'header.php';
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">Limpiar Base de Datos</h2></div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <?php if ($limpieza_completada): ?>
        <div class="alert alert-success">
            <h4>✓ Base de datos limpiada correctamente</h4>
            <p>Se han eliminado:</p>
            <ul>
                <li>Todos los usuarios registrados</li>
                <li>Todas las participaciones</li>
                <li>Todos los intentos de botones</li>
                <li>Se desasignaron todos los premios sembrados</li>
            </ul>
        </div>
        <?php elseif (isset($error)): ?>
        <div class="alert alert-danger">
            <strong>Error:</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php endif; ?>

        <div class="card border-danger">
            <div class="card-body">
                <h3 class="card-title">⚠️ ADVERTENCIA</h3>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                
                <p>Al confirmar, se eliminará:</p>
                <ul>
                    <li><strong>Todos los usuarios registrados</strong></li>
                    <li><strong>Todas las participaciones del juego</strong></li>
                    <li><strong>Todos los intentos de botones</strong></li>
                    <li><strong>Se desasignarán todos los premios sembrados</strong></li>
                </ul>
                
                <p>Los datos de <strong>cadenas</strong> y <strong>bares</strong> se mantienen intactos.</p>
                
                <?php if (!$limpieza_completada): ?>
                <form method="post" class="mt-4">
                    <div class="alert alert-warning">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" id="confirmar_check" required>
                            <span class="form-check-label">He leído la advertencia y confirmo que deseo continuar</span>
                        </label>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <input type="hidden" name="accion" value="confirmar_limpiar">
                        <button type="submit" class="btn btn-danger" id="btn-limpiar" disabled>
                            🗑️ Limpiar Todo
                        </button>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
                
                <script>
                document.getElementById('confirmar_check').addEventListener('change', function() {
                    document.getElementById('btn-limpiar').disabled = !this.checked;
                });
                </script>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
