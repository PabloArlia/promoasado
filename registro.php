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

    $shouldTryLogin = ($values['email'] !== '' || $values['dni'] !== '');
    $hasExtraData = ($values['nombre'] !== '' || $values['apellido'] !== '' || $values['celular'] !== '');

    if ($shouldTryLogin) {
        if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $fieldErrors['email'] = 'Ingresá un email válido.';
            $errors[] = $fieldErrors['email'];
        }

        if ($values['dni'] !== '' && !ctype_digit($values['dni'])) {
            $fieldErrors['dni'] = 'Ingresá un DNI numérico.';
            $errors[] = $fieldErrors['dni'];
        }

        if (!$errors) {
            $userByEmail = $values['email'] !== '' ? getUserByEmail($values['email']) : null;
            $userByDni = $values['dni'] !== '' ? getUserByDni($values['dni']) : null;

            if ($userByEmail !== null && $userByDni !== null && (int) $userByEmail['id'] !== (int) $userByDni['id']) {
                $fieldErrors['email'] = 'El email y el DNI corresponden a usuarios distintos.';
                $fieldErrors['dni'] = 'El email y el DNI corresponden a usuarios distintos.';
                $errors[] = $fieldErrors['email'];
            } else {
                $user = $userByEmail ?? $userByDni;

                if ($user === null) {
                    if (!$hasExtraData) {
                        if ($values['email'] !== '') {
                            $fieldErrors['email'] = 'No existe un usuario con ese email.';
                            $errors[] = $fieldErrors['email'];
                        }

                        if ($values['dni'] !== '') {
                            $fieldErrors['dni'] = 'No existe un usuario con ese DNI.';
                            $errors[] = $fieldErrors['dni'];
                        }
                    }
                } else {
                    try {
                        $participationId = loginUserForToday((int) $user['id']);
                        $_SESSION['participante_id'] = $participationId;
                        $currentParticipation = getParticipant($participationId);

                        if ($currentParticipation === null) {
                            if ($values['email'] !== '') {
                                $fieldErrors['email'] = 'No se pudo recuperar tu participación.';
                                $errors[] = $fieldErrors['email'];
                            } else {
                                $fieldErrors['dni'] = 'No se pudo recuperar tu participación.';
                                $errors[] = $fieldErrors['dni'];
                            }
                        } else {
                            redirect(nextStepPath($currentParticipation));
                        }
                    } catch (PDOException $exception) {
                        if ($values['email'] !== '') {
                            $fieldErrors['email'] = 'No se pudo iniciar la participación de hoy. Intentá de nuevo.';
                            $errors[] = $fieldErrors['email'];
                        } else {
                            $fieldErrors['dni'] = 'No se pudo iniciar la participación de hoy. Intentá de nuevo.';
                            $errors[] = $fieldErrors['dni'];
                        }
                    }
                }
            }
        }

        if (!$errors && !$hasExtraData) {
            if ($values['email'] !== '') {
                $fieldErrors['email'] = 'No existe un usuario con ese email.';
                $errors[] = $fieldErrors['email'];
            }
            if ($values['dni'] !== '') {
                $fieldErrors['dni'] = 'No existe un usuario con ese DNI.';
                $errors[] = $fieldErrors['dni'];
            }
        }
    }

    if ($hasExtraData) {
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

        if ($fieldErrors['email'] === '' && getUserByEmail($values['email']) !== null) {
            $fieldErrors['email'] = 'Ya existe un usuario con ese email.';
            $errors[] = $fieldErrors['email'];
        }

        if ($fieldErrors['dni'] === '' && getUserByDni($values['dni']) !== null) {
            $fieldErrors['dni'] = 'Ya existe un usuario con ese DNI.';
            $errors[] = $fieldErrors['dni'];
        }

        if (!$errors) {
            $pdo = db();
            $pdo->beginTransaction();

            try {
                $statement = $pdo->prepare(
                    'INSERT INTO usuarios
                        (nombre, apellido, email, celular, dni, acepta_bases, fecha_registro)
                     VALUES
                        (:nombre, :apellido, :email, :celular, :dni, 1, NOW())'
                );
                $statement->execute($values);

                $userId = (int) $pdo->lastInsertId();

                $statement = $pdo->prepare(
                    'INSERT INTO participacion (usuario_id, gano_juego, fecha_participacion)
                     VALUES (:usuario_id, 0, CURDATE())'
                );
                $statement->execute(['usuario_id' => $userId]);

                $participationId = (int) $pdo->lastInsertId();
                $pdo->commit();

                $_SESSION['participante_id'] = $participationId;
                setFlash('success', 'Registro completo. Ya podés avanzar a la mecánica.');
                redirect('mecanica');
            } catch (PDOException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $errors[] = 'No se pudo guardar el registro. Revisá la conexión a MySQL.';
            }
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
            <input type="text" name="nombre" placeholder="Nombre" value="<?= esc($values['nombre']) ?>"<?= $fieldErrors['nombre'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['nombre']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">Apellido</span>
            <input type="text" name="apellido" placeholder="Apellido" value="<?= esc($values['apellido']) ?>"<?= $fieldErrors['apellido'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['apellido']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">Email</span>
            <input type="email" name="email" placeholder="Email" value="<?= esc($values['email']) ?>"<?= $fieldErrors['email'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['email']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">Celular</span>
            <input type="text" name="celular" placeholder="Celular" value="<?= esc($values['celular']) ?>"<?= $fieldErrors['celular'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['celular']) . '" aria-invalid="true"' : '' ?>>
        </label>
        <label>
            <span class="sr-only">DNI</span>
            <input type="text" name="dni" placeholder="DNI" value="<?= esc($values['dni']) ?>"<?= $fieldErrors['dni'] !== '' ? ' class="input-error" title="' . esc($fieldErrors['dni']) . '" aria-invalid="true"' : '' ?>>
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