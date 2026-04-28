<?php
require_once __DIR__ . '/init.php';

if (empty($_SESSION['admin'])) {
    header('Location: ' . urladmin . 'index.php');
    exit;
}

$db = db();

// Obtener eventos para el calendario
$events = [];
$stmt = $db->query("
    SELECT sh.franja_semilla, p.nombre as premio, c.nombre as cadena, sh.id
    FROM semillas_horarias sh
    LEFT JOIN premio p ON sh.premio = p.id
    LEFT JOIN cadenas c ON sh.cadena = c.id
    ORDER BY sh.franja_semilla
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['franja_semilla'];
    $title = ($row['premio'] ? $row['premio'] : 'Sin premio') . ' - ' . $row['cadena'];
    $events[] = [
        'id' => $row['id'],
        'title' => $title,
        'start' => $start,
        'allDay' => false
    ];
}

$title = 'Calendario Sembrados';
require_once __DIR__ . '/header.php';
?>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Calendario de Sembrados Horarios</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?= json_encode($events) ?>,
        eventClick: function(info) {
            alert('Evento: ' + info.event.title + '\nFecha: ' + info.event.start.toISOString().slice(0, 16).replace('T', ' '));
        },
        locale: 'es'
    });
    calendar.render();
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>