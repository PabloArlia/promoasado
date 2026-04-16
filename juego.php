<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$participant = requireParticipant();
$participantId = (int) $participant['id'];

if ($participant['preguntas_aprobadas'] !== null) {
    redirect('cierre.php');
}

if (isGameComplete($participantId)) {
    redirect((int) $participant['gano_juego'] === 1 ? 'preguntas.php' : 'cierre.php');
}

$statuses = getButtonStatuses($participantId);

require_once __DIR__ . '/includes/header.php';
?>
<section class="content-card juego-card">
    <div id="cancha">
        <div class="linea linea-arquero-malo">
            <div class="jugador arquero-malo">
                <span class="circulo"></span>
                <img src="img/arquero-malo.png" alt="Arquero malo" />
                <span class="circulo"></span>
            </div>
        </div>

        <div class="linea linea-3-jugadores">
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
        </div>

        <div class="linea linea-3-jugadores">
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
        </div>

        <div class="linea linea-4-jugadores patea">
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo"></span></div>
        </div>

        <div class="linea linea-arquero">
            <div class="jugador arquero">
                <span class="circulo activo"></span>
                <img src="img/arquero.png" alt="Arquero" />
            </div>
        </div>
    </div>
    <h2>1 PASES</h2>
    <p>AHORA DE LA DEFENSA AL MEDICOAMPO, PENSALO BIEN!</p>
</section>

<script>
const feedback = document.getElementById('game-feedback');

document.querySelectorAll('.game-button').forEach((button) => {
    button.addEventListener('click', async () => {
        const buttonNumber = button.dataset.button;
        button.disabled = true;
        feedback.textContent = `Consultando botón ${buttonNumber}...`;
        feedback.className = 'flash flash-info';

        try {
            const response = await fetch('api/game_button.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ button: Number(buttonNumber) })
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'No se pudo resolver el botón.');
            }

            button.classList.toggle('is-success', payload.result === true);
            button.classList.toggle('is-fail', payload.result === false);
            button.querySelector('strong').textContent = payload.result ? 'TRUE' : 'FALSE';
            feedback.textContent = payload.message;
            feedback.className = `flash ${payload.result ? 'flash-success' : 'flash-error'}`;

            if (payload.completed) {
                window.location.href = payload.redirect;
            }
        } catch (error) {
            button.disabled = false;
            feedback.textContent = error.message;
            feedback.className = 'flash flash-error';
        }
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>