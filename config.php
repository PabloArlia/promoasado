<?php

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
const AES_KEY = 'promo2026asadorulz12345678901234'; // 32 bytes para AES-256
const SEED_MINUTE = 45;
const PRESEED_WINDOW_MINUTES = 20;
const BUTTON_SUCCESS_RATES = [
    1 => 0.88,
    2 => 0.78,
    3 => 0.68,
];
const QUESTIONS = [
    'q1' => [
        'label' => 'En el futbol, ¿cual es el unico jugador que puede tocar la pelota con las manos?',
        'options' => [
            'a' => 'El arquero',
            'b' => 'El delantero',
        ],
        'correct' => 'a',
    ],
    'q2' => [
        'label' => 'En el futbol, ¿de que color es la tarjeta que saca el arbitro para expulsar a un jugador?',
        'options' => [
            'a' => 'Roja',
            'b' => 'Violeta',
        ],
        'correct' => 'a',
    ],
    'q3' => [
        'label' => 'En el futbol, ¿cuanto tiempo dura un partido de futbol 11 normal?',
        'options' => [
            'a' => '90 minutos',
            'b' => '20 minutos',
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