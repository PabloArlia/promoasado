<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$participant = requireParticipant();
$participantId = (int) $participant['id'];

if (!isGameComplete($participantId)) {
    redirect('juego.php');
}

if ((int) $participant['gano_juego'] === 1 && $participant['preguntas_aprobadas'] === null) {
    redirect('preguntas.php');
}

$result = finalResult($participant);

require_once __DIR__ . '/includes/header.php';
?>
<section class="content-card cierre-card <?= $result === 'win' ? 'result-win' : 'result-lose' ?>">
    <img src="img/promo-arma.png" alt="Promo Arma tu Asado" class="arma-img" />
    <?php if ($result === 'win'): ?>
        <img src="img/camiseta.png" alt="Interceptaron tu pase" class="gano-img" />
        <h2>¡CORRECTO! <br/>GANASTE UNA CAMISETA!</h2>
        <p>En unos días nos pondremos en contacto con vos para entregarte tu premio. Gracias por participar.</p>
    <?php elseif ((int) $participant['gano_juego'] === 1): ?>
        <div class="perdio-wrap">
            <img src="img/perdio.png" alt="Interceptaron tu pase" class="perdio-img" />
            <div id="prdbox">
                <h2>Uhh, lo siento!<br/>Interceptaron tu pase.</h2>
                <p>Pero no te preocupes,<br>podés seguir participando mañana.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="perdio-wrap">
            <img src="img/perdio.png" alt="Interceptaron tu pase" class="perdio-img" />
            <div id="prdbox">
                <h2>Uhh, lo siento!<br/>Interceptaron tu pase.</h2>
                <p>Pero no te preocupes,<br>podés seguir participando mañana.</p>
            </div>
        </div>
    <?php endif; ?>    
    <div class="full-width actions-row">
        <a class="btn" href="index">Finalizar</a>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>