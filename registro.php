<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$participant = currentParticipant();
if ($participant !== null) {
    redirect(nextStepPath($participant));
}

$values = [
    'nombre' => '',
    'apellido' => '',
    'email' => '',
    'celular' => '',
    'dni' => '',
];
$errors = [];
$fieldErrors = [
    'nombre' => '',
    'apellido' => '',
    'email' => '',
    'celular' => '',
    'dni' => '',
    'acepta_bases' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    if ($values['nombre'] === '') {
        $fieldErrors['nombre'] = 'Ingresá el nombre.';
        $errors[] = $fieldErrors['nombre'];
    }

    if ($values['apellido'] === '') {
        $fieldErrors['apellido'] = 'Ingresá el apellido.';
        $errors[] = $fieldErrors['apellido'];
    }

    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $fieldErrors['email'] = 'Ingresá un email válido.';
        $errors[] = $fieldErrors['email'];
    }

    if ($values['celular'] === '') {
        $fieldErrors['celular'] = 'Ingresá el celular.';
        $errors[] = $fieldErrors['celular'];
    }

    if ($values['dni'] === '' || !ctype_digit($values['dni'])) {
        $fieldErrors['dni'] = 'Ingresá un DNI numérico.';
        $errors[] = $fieldErrors['dni'];
    }

    if (empty($_POST['acepta_bases'])) {
        $fieldErrors['acepta_bases'] = 'Tenés que aceptar bases y condiciones.';
        $errors[] = $fieldErrors['acepta_bases'];
    }

    if ($fieldErrors['email'] === '') {
        $statement = db()->prepare('SELECT 1 FROM participantes WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $values['email']]);

        if ($statement->fetchColumn()) {
            $fieldErrors['email'] = 'Ya existe un participante con ese email.';
            $errors[] = $fieldErrors['email'];
        }
    }

    if ($fieldErrors['dni'] === '') {
        $statement = db()->prepare('SELECT 1 FROM participantes WHERE dni = :dni LIMIT 1');
        $statement->execute(['dni' => $values['dni']]);

        if ($statement->fetchColumn()) {
            $fieldErrors['dni'] = 'Ya existe un participante con ese DNI.';
            $errors[] = $fieldErrors['dni'];
        }
    }

    if (!$errors) {
        try {
            $statement = db()->prepare(
                'INSERT INTO participantes
                    (nombre, apellido, email, celular, dni, acepta_terminos, acepta_bases, registrado_en)
                 VALUES
                    (:nombre, :apellido, :email, :celular, :dni, 1, 1, NOW())'
            );
            $statement->execute($values);

            $_SESSION['participante_id'] = (int) db()->lastInsertId();
            setFlash('success', 'Registro completo. Ya podés avanzar a la mecánica.');
            redirect('mecanica');
        } catch (PDOException $exception) {
            $errors[] = 'No se pudo guardar el registro. Revisá la conexión a MySQL.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="content-card narrow-card registro-card">
    <img src="img/promo-arma.png" alt="Promo Arma tu Asado" class="arma-img" />
    <p class="card-intro">Dejanos tus datos para poder contactarte en caso de ser ganador</p>
    <form class="form-grid" method="post">
        <label>
            <span class="sr-only">Nombre</span>
            <input type="text" name="nombre" placeholder="Nombre" value="<?= esc($values['nombre']) ?>" required<?= $fieldErrors['nombre'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['nombre']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">Apellido</span>
            <input type="text" name="apellido" placeholder="Apellido" value="<?= esc($values['apellido']) ?>" required<?= $fieldErrors['apellido'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['apellido']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">Email</span>
            <input type="email" name="email" placeholder="Email" value="<?= esc($values['email']) ?>" required<?= $fieldErrors['email'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['email']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">Celular</span>
            <input type="text" name="celular" placeholder="Celular" value="<?= esc($values['celular']) ?>" required<?= $fieldErrors['celular'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['celular']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">DNI</span>
            <input type="text" name="dni" placeholder="DNI" value="<?= esc($values['dni']) ?>" required<?= $fieldErrors['dni'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['dni']) . '" aria-invalid="true"' : '' ?>>
        </label>

        <label class="checkbox-row full-width">
            <input type="checkbox" name="acepta_bases" value="1" <?= !empty($_POST['acepta_bases']) ? 'checked' : '' ?><?= $fieldErrors['acepta_bases'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['acepta_bases']) . '" aria-invalid="true"' : '' ?>>
            <span>ACEPTO <a href="bases-y-condiciones" target="_blank">BASES Y CONDICIONES</a>.</span>
        </label>

        <div class="full-width actions-row">
            <button class="btn" type="submit">Jugar</button>
        </div>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>