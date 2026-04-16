<?php
require_once 'config.php';
require_once 'functions.php';

if (array_key_exists('salir', $_GET)) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
    redirect('index');
}

require_once 'includes/header.php';
?>
<section class="home-stack">
    <img src="img/promo-arma.png" alt="Promo Arma tu Asado" class="arma-img" />
    <div class="tronco-wrap">
        <img src="img/tronco.png" alt="Promo Arma tu Asado" class="tronco-img" />
        <div class="sobretronco">
            <h2>Registrate y participá por fabulosos premios.</h2>
            <a class="btn" href="registro">ENTRAR</a>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>