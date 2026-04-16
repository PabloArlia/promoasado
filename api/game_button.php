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
    $combined      = (int) ($payload['button'] ?? 0);
    $buttonNumber  = intdiv($combined, 10);
    $sentPosition  = $combined % 10;

    $validPositions = [1 => 4, 2 => 3, 3 => 3, 4 => 2];

    if (!isset($validPositions[$buttonNumber]) || $sentPosition < 1 || $sentPosition > $validPositions[$buttonNumber]) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Botón o posición inválidos.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $statuses = getButtonStatuses($participantId);

    for ($requiredButton = 1; $requiredButton < $buttonNumber; $requiredButton++) {
        if ($statuses[$requiredButton] === null) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => sprintf('Primero tenés que jugar el botón %d.', $requiredButton),
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    if ($statuses[$buttonNumber] !== null) {
        $latestParticipant = getParticipant($participantId) ?? $participant;
        $completed = isGameComplete($participantId);
        $prevResult = $statuses[$buttonNumber];
        $returnedPos = $prevResult ? $sentPosition : otherPosition($buttonNumber, $sentPosition);

        echo json_encode([
            'success'   => true,
            'result'    => $prevResult,
            'linea'     => $buttonNumber,
            'pos'       => $returnedPos,
            'message'   => 'Ese botón ya había sido jugado.',
            'completed' => $completed,
            'redirect'  => $completed
                ? ((int) $latestParticipant['gano_juego'] === 1 ? 'preguntas.php' : 'cierre.php')
                : null,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = evaluateButton($participantId, $buttonNumber);
    $returnedPos = $result ? $sentPosition : otherPosition($buttonNumber, $sentPosition);

    recordButtonResult($participantId, $buttonNumber, $result);
    $game = finalizeGame($participantId);

    echo json_encode([
        'success'   => true,
        'result'    => $result,
        'pos'       => $returnedPos,
        'message'   => $result
            ? 'El webservice respondió TRUE para este botón.'
            : 'El webservice respondió FALSE para este botón.',
        'completed' => $game['completed'],
        'won'       => $game['won'],
        'redirect'  => $game['completed'] ? ($game['won'] ? 'preguntas.php' : 'cierre.php') : null,
        'statuses'  => $game['statuses'],
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