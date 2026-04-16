<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$participant = requireParticipant();
$participantId = (int) $participant['id'];

if (!isGameComplete($participantId) || (int) $participant['gano_juego'] !== 1) {
    redirect('cierre.php');
}

if ($participant['preguntas_aprobadas'] !== null) {
    redirect('cierre.php');
}

$questions = QUESTIONS;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = [];

    foreach ($questions as $key => $question) {
        $answer = (string) ($_POST[$key] ?? '');

        if (!array_key_exists($answer, $question['options'])) {
            $errors[] = 'Tenés que responder todas las preguntas.';
            break;
        }

        $answers[$key] = $answer;
    }

    if (!$errors) {
        $passed = true;

        foreach ($questions as $key => $question) {
            if ($answers[$key] !== $question['correct']) {
                $passed = false;
                break;
            }
        }

        saveQuestionResults($participantId, $answers, $passed);
        redirect('cierre.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="content-card mecanica-card preguntas-card">
    <img src="img/promo-arma.png" alt="Promo Arma tu Asado" class="arma-img" />
    
    
    <h2>¡Excelente, sos un potencial ganador!</h2>
    <p class="meca-intro">Sólo un pasito más, respondé correctamente estas preguntas:</p>

    <?php if ($errors): ?>
        <div class="flash flash-error"><?= esc(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" class="question-list">
        <?php foreach ($questions as $key => $question): ?>
            <fieldset class="question-card">
                <legend class="question-title"><?= esc($question['label']) ?></legend>
                <?php foreach ($question['options'] as $optionKey => $label): ?>
                    <label class="option-row checkbox-row">
                        <input type="radio" name="<?= esc($key) ?>" value="<?= esc($optionKey) ?>" <?= (($_POST[$key] ?? '') === $optionKey) ? 'checked' : '' ?>>
                        <span><?= esc($label) ?></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>

        <div class="actions-row">
            <button class="btn" type="submit">Responder</button>
        </div>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>