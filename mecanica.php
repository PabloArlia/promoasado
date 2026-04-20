<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$participant = currentParticipant();
if ($participant !== null && hasStartedGame((int) $participant['id'])) {
    redirect(nextStepPath($participant));
}

// Procesar formulario con ubicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $participant !== null) {
    $latitud = isset($_POST['latitud']) && $_POST['latitud'] !== '' ? (float) $_POST['latitud'] : null;
    $longitud = isset($_POST['longitud']) && $_POST['longitud'] !== '' ? (float) $_POST['longitud'] : null;

    if ($latitud !== null && $longitud !== null) {
        $participationId = (int) $participant['id'];
        $barId = null;
        $distanciaKm = null;

        // Buscar bar más cercano si hay cadena en sesión
        if (isset($_SESSION['cadena'])) {
            $statement = db()->prepare('SELECT id FROM cadenas WHERE identificador = :identificador LIMIT 1');
            $statement->execute(['identificador' => $_SESSION['cadena']]);
            $cadenaRow = $statement->fetch();

            if ($cadenaRow) {
                $closestBar = getClosestBar((int) $cadenaRow['id'], $latitud, $longitud);
                if ($closestBar !== null) {
                    $barId = $closestBar['id'];
                    $distanciaKm = $closestBar['distancia_km'];
                }
            }
        }

        $statement = db()->prepare(
            'UPDATE participacion SET latitud = :latitud, longitud = :longitud, bar_id = :bar_id, distancia_km = :distancia_km WHERE id = :id'
        );
        $statement->execute([
            'latitud' => $latitud,
            'longitud' => $longitud,
            'bar_id' => $barId,
            'distancia_km' => $distanciaKm,
            'id' => $participationId,
        ]);

        redirect('juego');
    }

}

require_once __DIR__ . '/includes/header.php';
?>
<section class="content-card mecanica-card">
    <img src="img/promo-arma.png" alt="Promo Arma tu Asado" class="arma-img" />
    <h2>MECÁNICA</h2>
    <p class="meca-intro">El juego es muy simple, tenés que ir descubriendo la pelota en cada linea hasta llegar al gol.  Cada jugador que elegís debe tener la pelota en sus pies.</p>
    <img src="img/preview.png" alt="Promo Arma tu Asado" class="poreview-img" />
    
    <form method="post" class="full-width">
        <input type="hidden" name="latitud" id="latitud" value="">
        <input type="hidden" name="longitud" id="longitud" value="">
        <div class="full-width actions-row">
            <button class="btn" type="submit" id="btn-comenzar">Comenzar</button>
        </div>
    </form>

    <script>
    // Capturar ubicación al cargar
    function captureLocation() {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitud').value = position.coords.latitude;
                    document.getElementById('longitud').value = position.coords.longitude;
                },
                function(error) {
                    console.error('Error de geolocalización:', error);
                }
            );
        }
    }

    window.addEventListener('load', captureLocation);
    </script>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>