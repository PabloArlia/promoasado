<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Mexico_City');

const APP_NAME = 'Promo Asado';
const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'promoasado';
const DB_USER = 'root';
const DB_PASS = '';
const SEED_MINUTE = 45;
const PRESEED_WINDOW_MINUTES = 20;
const BUTTON_SUCCESS_RATES = [
    1 => 0.88,
    2 => 0.78,
    3 => 0.68,
];
const QUESTIONS = [
    'q1' => [
        'label' => '¿Cuál es el punto ideal para servir un asado clásico?',
        'options' => [
            'a' => 'Cuando la carne está dorada por fuera y jugosa por dentro.',
            'b' => 'Cuando toda la carne queda completamente seca.',
        ],
        'correct' => 'a',
    ],
    'q2' => [
        'label' => '¿Qué conviene hacer antes de prender el fuego?',
        'options' => [
            'a' => 'Improvisar sin revisar nada.',
            'b' => 'Tener listos carbón, utensilios y cortes.',
        ],
        'correct' => 'b',
    ],
    'q3' => [
        'label' => '¿Cuál es una buena práctica para una promo responsable?',
        'options' => [
            'a' => 'Leer bases y condiciones antes de participar.',
            'b' => 'Participar con datos de terceros.',
        ],
        'correct' => 'a',
    ],
];

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}