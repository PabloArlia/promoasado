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
        <div id="sale4" class="linea linea-arquero-malo">
            <div class="jugador arquero-malo">
                <span class="circulo" pos="41"></span>
                <img src="img/arquero-malo.png" alt="Arquero malo" />
                <span class="circulo" pos="42"></span>
            </div>
        </div>

        <div id="sale3" class="linea linea-3-jugadores">
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="31"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="32"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="33"></span></div>
        </div>

        <div id="sale2" class="linea linea-3-jugadores">
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="21"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="22"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="23"></span></div>
        </div>

        <div id="sale1" class="linea linea-4-jugadores patea">
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="11"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="12"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="13"></span></div>
            <div class="jugador"><img src="img/jugador.png" alt="Jugador" /><span class="circulo" pos="14"></span></div>
        </div>

        <div id="sale0" class="linea linea-arquero">
            <div class="jugador arquero">
                <span class="circulo activo"></span>
                <img src="img/arquero.png" alt="Arquero" />
            </div>
        </div>
    </div>
    <h2 id="pases-titulo">0 PASES</h2>
    <p id="pases-subtitulo">DEL ARQUERO A LA DEFENSA, ELEGÍ BIEN Y SEGUIRÁS JUGANDO</p>
</section>

<script>
const REDIRECT_DELAY_MS = 1000;
const LOSE_REDIRECT_PATH = 'cierre.php';

var cargando = false;
var gameLocked = false;
const allPlayableCircles = document.querySelectorAll('.circulo[pos]');

function disableAllCircles() {
    gameLocked = true;
    allPlayableCircles.forEach((circle) => {
        circle.classList.add('bloqueado');
    });
}

function redirectToLoseWithDelay() {
    disableAllCircles();
    setTimeout(() => {
        window.location.href = LOSE_REDIRECT_PATH;
    }, REDIRECT_DELAY_MS);
}

const PASS_MESSAGES = {
    1: { titulo: '0 PASES', subtitulo: 'DEL ARQUERO A LA DEFENSA, ELEGÍ BIEN Y SEGUIRÁS JUGANDO' },
    2: { titulo: '1 PASES', subtitulo: 'AHORA DE LA DEFENSA AL MEDIOCAMPO, PENSALO BIEN!' },
    3: { titulo: '2 PASES', subtitulo: 'DEL MEDIO CAMPO AL DELANTERO, APROVECHÁ LA OPORTUNIDAD' },
    4: { titulo: '3 PASES', subtitulo: 'EL DELANTERO TIRA AL ARCO Y...' },
};

function updatePassText(lineNumber) {
    const msg = PASS_MESSAGES[lineNumber];
    if (!msg) {
        return;
    }

    document.getElementById('pases-titulo').textContent = msg.titulo;
    document.getElementById('pases-subtitulo').textContent = msg.subtitulo;
}

function movePateaToNextLine(currentLine) {
    document.querySelectorAll('#sale1, #sale2, #sale3, #sale4').forEach((line) => {
        line.classList.remove('patea');
    });

    if (currentLine < 4) {
        const nextLine = document.getElementById(`sale${currentLine + 1}`);
        if (nextLine) {
            nextLine.classList.add('patea');
        }

        updatePassText(currentLine + 1);
    }
}

function getCurrentPlayableLine() {
    const activeLine = document.querySelector('#sale1.patea, #sale2.patea, #sale3.patea, #sale4.patea');
    if (!activeLine) {
        return null;
    }

    return Number(activeLine.id.replace('sale', ''));
}

allPlayableCircles.forEach((button) => {
    button.addEventListener('click', async () => {
        if (cargando || gameLocked) {
            return;
        }

        const buttonCode = (button.getAttribute('pos') || '').trim();
        if (buttonCode.length !== 2) {
            return;
        }

        const lineNumber = Number(buttonCode[0]);
        const sentPosition = Number(buttonCode[1]);

        const currentPlayableLine = getCurrentPlayableLine();
        if (currentPlayableLine === null || lineNumber !== currentPlayableLine) {
            return;
        }

        cargando = true;
        button.classList.add('cargando');

        try {
            const response = await fetch('api/game_button.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ button: Number(buttonCode) })
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
                if (response.status === 409 && payload.message && payload.message.includes('Primero tenés que jugar el botón')) {
                    return;
                }

                throw new Error(payload.message || 'No se pudo resolver el botón.');
            }

            const returnedPosition = Number(payload.pos);
            const samePosition = returnedPosition === sentPosition;

            document.querySelectorAll(`.circulo[pos^="${lineNumber}"]`).forEach((circle) => {
                circle.classList.remove('activo', 'error');
            });

            if (samePosition) {
                button.classList.add('activo');
                movePateaToNextLine(lineNumber);
            } else {
                button.classList.add('error');
                const returnedCircle = document.querySelector(`.circulo[pos="${lineNumber}${returnedPosition}"]`);
                if (returnedCircle) {
                    returnedCircle.classList.add('activo');
                }
                redirectToLoseWithDelay();
                return;
            }

            if (payload.completed) {
                disableAllCircles();
                setTimeout(() => {
                    window.location.href = payload.redirect;
                }, REDIRECT_DELAY_MS);
            }
        } catch (error) {
            console.error(error);
            redirectToLoseWithDelay();
            return;
        } finally {
            button.classList.remove('cargando');
            cargando = false;
        }
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>