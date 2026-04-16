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
<section class="content-card narrow-card">
    <span class="eyebrow">Paso 4</span>
    <h1>Preguntas</h1>
    <p class="lead">Solo llegás a esta instancia si el juego fue ganador. Respondé correctamente las tres preguntas para confirmar el premio.</p>

    <?php if ($errors): ?>
        <div class="flash flash-error"><?= esc(implode(' ', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" class="question-list">
        <?php foreach ($questions as $key => $question): ?>
            <fieldset class="question-card">
                <legend><?= esc($question['label']) ?></legend>
                <?php foreach ($question['options'] as $optionKey => $label): ?>
                    <label class="option-row">
                        <input type="radio" name="<?= esc($key) ?>" value="<?= esc($optionKey) ?>" <?= (($_POST[$key] ?? '') === $optionKey) ? 'checked' : '' ?>>
                        <span><?= esc($label) ?></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>

        <div class="actions-row">
            <button class="button button-primary" type="submit">Enviar respuestas</button>
        </div>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>