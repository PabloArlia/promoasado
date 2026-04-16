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
<section class="content-card narrow-card <?= $result === 'win' ? 'result-win' : 'result-lose' ?>">
    <span class="eyebrow">Paso 5</span>
    <h1><?= $result === 'win' ? 'Ganaste' : 'Perdiste' ?></h1>
    <p class="lead">
        <?php if ($result === 'win'): ?>
            Cumpliste con los cuatro botones y respondiste correctamente las tres preguntas.
        <?php elseif ((int) $participant['gano_juego'] === 1): ?>
            El juego fue ganador, pero las respuestas no alcanzaron para validar el premio final.
        <?php else: ?>
            No se completó una combinación ganadora en el juego.
        <?php endif; ?>
    </p>

    <div class="summary-box">
        <p><strong>Participante:</strong> <?= esc($participant['nombre'] . ' ' . $participant['apellido']) ?></p>
        <p><strong>Email:</strong> <?= esc($participant['email']) ?></p>
        <p><strong>Estado del juego:</strong> <?= (int) $participant['gano_juego'] === 1 ? 'Ganador' : 'Perdedor' ?></p>
        <p><strong>Estado final:</strong> <?= $result === 'win' ? 'Premio confirmado' : 'Sin premio' ?></p>
    </div>

    <div class="actions-row">
        <a class="button button-primary" href="index.php">Volver al home</a>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>