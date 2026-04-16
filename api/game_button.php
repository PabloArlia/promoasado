<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $participant = currentParticipant();
    if ($participant === null) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'La sesión expiró. Volvé a registrarte para seguir.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $participantId = (int) $participant['id'];

    if (isGameComplete($participantId)) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'El juego ya fue resuelto para este participante.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $payload = json_decode(file_get_contents('php://input') ?: '{}', true, 512, JSON_THROW_ON_ERROR);
    $button = (int) ($payload['button'] ?? 0);

    if (!in_array($button, [1, 2, 3, 4], true)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Botón inválido.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $statuses = getButtonStatuses($participantId);
    if ($statuses[$button] !== null) {
        $latestParticipant = getParticipant($participantId) ?? $participant;
        $completed = isGameComplete($participantId);

        echo json_encode([
            'success' => true,
            'result' => $statuses[$button],
            'message' => 'Ese botón ya había sido jugado.',
            'completed' => $completed,
            'redirect' => $completed
                ? ((int) $latestParticipant['gano_juego'] === 1 ? 'preguntas.php' : 'cierre.php')
                : null,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = $button === 4
        ? evaluateLastButton($participantId)
        : randomChance(BUTTON_SUCCESS_RATES[$button]);

    recordButtonResult($participantId, $button, $result);
    $game = finalizeGame($participantId);

    echo json_encode([
        'success' => true,
        'result' => $result,
        'message' => $result
            ? 'El webservice respondió TRUE para este botón.'
            : 'El webservice respondió FALSE para este botón.',
        'completed' => $game['completed'],
        'won' => $game['won'],
        'redirect' => $game['completed'] ? ($game['won'] ? 'preguntas.php' : 'cierre.php') : null,
        'statuses' => $game['statuses'],
    ], JSON_UNESCAPED_UNICODE);
} catch (JsonException $exception) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'El payload enviado no es válido.',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno al resolver el botón.',
    ], JSON_UNESCAPED_UNICODE);
}