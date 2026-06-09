<!doctype html>
<html lang="es">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XLBLYRLLR2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-XLBLYRLLR2');
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(APP_NAME) ?></title>
    <link rel="stylesheet" href="styles.css?<?=time()?>">
</head>
<body>
<header class="header">
    <div class="container">
        <div class="header-top">
            <img src="img/logo-hellmans.png" alt="Hellmans">
            <?php            
            if (isset($_SESSION['cadena'])) {
                $cadena = $_SESSION['cadena'];
                
                $stmt = db()->prepare('SELECT logo FROM cadenas WHERE identificador = :id LIMIT 1');
                $stmt->execute(['id' => $cadena]);
                $row = $stmt->fetch();
                
                if ($row && $row['logo']) {
                    echo '<img src="' . esc($row['logo']) . '" alt="' . esc($cadena) . '">';
                } else {
                    echo '<img src="img/logo-' . esc($cadena) . '.png" alt="' . esc($cadena) . '">';
                }
            } else {
                echo '&nbsp;';
            }
            ?>
        </div>
    </div>
</header>
<main class="page">
    <div class="container">