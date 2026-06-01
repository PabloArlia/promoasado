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
const PUBLICO = true;

if (array_key_exists('preview', $_GET)) {
    $_SESSION['promo_preview'] = true;
}

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isAdminArea = str_contains($scriptName, '/loromin/');
$canAccessPromo = PUBLICO || !empty($_SESSION['promo_preview']) || $isAdminArea;

if (!$canAccessPromo) {
    http_response_code(403);
    echo '<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>' . htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') . '</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #000;
        }

        .coming-soon-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            color: #fff;
        }

        .coming-soon-shell {
            width: 100%;
            max-width: 500px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2vh;
            text-align: center;
            background: url("img/fondo.png") top center / 100% auto no-repeat, url("img/fondotile.jpg") top center / 100% auto repeat;
        }

        .coming-soon-logos {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
        }

        .coming-soon-logos img {
            display: block;
            max-width: 35%;
            height: auto;
        }

        .coming-soon-content {
            margin: auto 0;
            padding: 2rem 0;
        }

        .coming-soon-promo {
            display: block;
            width: 74%;
            max-width: 330px;
            height: auto;
            margin: 0 auto 2rem;
        }

        .coming-soon-content h1 {
            margin: 0;
            color: #fff;
            font-family: "DollopSerifCondensed", Arial, sans-serif;
            font-size: clamp(3rem, 16vw, 5.4rem);
            line-height: 0.9;
        }

        .coming-soon-content p {
            max-width: 22rem;
            margin: 1rem auto 0;
            color: #fca559;
            font-family: "DollopSerifCondensed", Arial, sans-serif;
            font-size: 1.45rem;
            line-height: 1.2;
        }

        .coming-soon-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            color: #fdb574;
            font-family: "DollopSerifCondensed", Arial, sans-serif;
            font-size: 0.82rem;
            line-height: 1.3;
        }

        .coming-soon-footer img {
            width: 54px;
            height: auto;
            flex: 0 0 auto;
        }

        .coming-soon-footer span {
            text-align: left;
        }
    </style>
</head>
<body>
    <main class="coming-soon-page">
        <section class="coming-soon-shell">
            <div class="coming-soon-logos">
                <img src="img/logo-hellmans.png" alt="Hellmanns">
                <span></span>
            </div>
            <div class="coming-soon-content">
                <img src="img/promo-arma.png" alt="Promo Arma tu Asado" class="coming-soon-promo">
                <h1>Muy pronto</h1>
            </div>
            <div class="coming-soon-footer">
                <img src="img/logo-unilever.png" alt="Unilever">
                <span>Esta pagina web esta dirigida unicamente a consumidores de productos y servicios de Unilever Argentina.</span>
            </div>
        </section>
    </main>
</body>
</html>';
    exit;
}

$host = $_SERVER['HTTP_HOST'] ?? '';
$serverName = $_SERVER['SERVER_NAME'] ?? '';
$serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
$isLocal = in_array($host, ['localhost', '127.0.0.1'], true)
    || in_array($serverName, ['localhost', '127.0.0.1'], true)
    || in_array($serverAddr, ['127.0.0.1', '::1'], true);

if ($isLocal) {
    $dbConfig = [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'promoasado',
        'user' => 'root',
        'pass' => '',
    ];    
    define('urladmin', '/promoasado/loromin/');
    define('debug', true);
} else {
    $dbConfig = [
        'host' => 'localhost',
        'port' => '3306',
        'name' => 'u770236810_promoasado',
        'user' => 'u770236810_promoasado',
        'pass' => '@7rXQh*mOlN',
    ];
    define('urladmin', 'https://promohellmannsasado.com.ar/loromin/');
    define('debug', true);

} 

define('DB_HOST', $dbConfig['host']);
define('DB_PORT', $dbConfig['port']);
define('DB_NAME', $dbConfig['name']);
define('DB_USER', $dbConfig['user']);
define('DB_PASS', $dbConfig['pass']);

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
