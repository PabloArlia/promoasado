<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$participant = currentParticipant();
if ($participant !== null && hasStartedGame((int) $participant['id'])) {
    redirect(nextStepPath($participant));
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="content-card mecanica-card">
    <img src="img/promo-arma.png" alt="Promo Arma tu Asado" class="arma-img" />
    <h2>MECÁNICA</h2>
    <p class="meca-intro">El juego es muy simple, tenés que ir descubriendo la pelota en cada linea hasta llegar al gol.  Cada jugador que elegís debe tener la pelota en sus pies.</p>
    <img src="img/preview.png" alt="Promo Arma tu Asado" class="poreview-img" />
    <div class="full-width actions-row">
        <a class="btn" href="juego">Comenzar</a>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>