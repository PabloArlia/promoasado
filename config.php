<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Mexico_City');

// ===== MEDIDAS DE SEGURIDAD HTTP HEADERS =====
// Prevenir clickjacking
header('X-Frame-Options: DENY');

// Prevenir MIME-sniffing
header('X-Content-Type-Options: nosniff');

// Protección contra XSS
header('X-XSS-Protection: 1; mode=block');

// HSTS (forzar HTTPS) - comentar si no está en HTTPS
// header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Content Security Policy - prevenir inyección de scripts
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com; img-src 'self' data: https:; font-src 'self' cdnjs.cloudflare.com; connect-src 'self' nominatim.openstreetmap.org;");

// Referrer Policy
header('Referrer-Policy: strict-origin-when-cross-origin');

// Evitar que el navegador cache datos sensibles
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

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