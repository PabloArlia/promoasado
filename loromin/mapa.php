<?php
require_once __DIR__ . '/init.php';

if (empty($_SESSION['admin'])) {
    header('Location: ' . urladmin . 'index.php');
    exit;
}

$db = db();

// --- Filtro por Cadena ---
$cadenas = $db->query("SELECT id, nombre FROM cadenas ORDER BY nombre ASC")->fetchAll();
$filterCadenaId = isset($_GET['cadena_id']) && $_GET['cadena_id'] !== '' ? (int)$_GET['cadena_id'] : null;

// --- Consulta de participaciones con geolocalización ---
$query = "
    SELECT
        p.latitud,
        p.longitud,
        p.fecha_participacion,
        CAST(AES_DECRYPT(u.nombre, '" . AES_KEY . "') AS CHAR) AS nombre,
        CAST(AES_DECRYPT(u.apellido, '" . AES_KEY . "') AS CHAR) AS apellido,
        b.nombre as bar_nombre
    FROM participacion p
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN bares b ON p.bar_id = b.id
    LEFT JOIN cadenas c ON b.cadena_id = c.id
    WHERE p.latitud IS NOT NULL AND p.longitud IS NOT NULL
    " . ($filterCadenaId ? "AND c.id = ?" : "") . "
    ORDER BY p.id DESC
    LIMIT 2000
";
$stmt = $db->prepare($query);
$stmt->execute($filterCadenaId ? [$filterCadenaId] : []);
$participaciones = $stmt->fetchAll();

// --- Consulta de bares con geolocalización ---
$queryBares = "
    SELECT nombre, direccion, latitud, longitud
    FROM bares
    WHERE latitud IS NOT NULL AND longitud IS NOT NULL
    " . ($filterCadenaId ? "AND cadena_id = ?" : "");
$stmtBares = $db->prepare($queryBares);
$stmtBares->execute($filterCadenaId ? [$filterCadenaId] : []);
$bares = $stmtBares->fetchAll();


$title = 'Mapa de Participaciones';
include 'header.php';
?>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Mapa de Participaciones</h2>
            </div>
            <div class="col-auto ms-auto">
                <form method="get" class="d-flex align-items-center gap-2">
                    <select name="cadena_id" class="form-select" onchange="this.form.submit()" style="min-width: 200px;">
                        <option value="">Todas las cadenas</option>
                        <?php foreach ($cadenas as $cadena): ?>
                            <option value="<?= $cadena['id'] ?>" <?= $filterCadenaId === (int)$cadena['id'] ? 'selected' : '' ?>>
                                <?= esc($cadena['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="map-legend mb-3" style="font-size: 0.9rem; color: #fff;">
                    <span style="display: inline-block; width: 12px; height: 12px; background-color: #d9534f; border-radius: 50%; margin-right: 5px;"></span> Bares (rojo)
                    <span style="display: inline-block; width: 12px; height: 12px; background-color: #337ab7; border-radius: 50%; margin-left: 15px; margin-right: 5px;"></span> Participaciones (azul)
                </div>
                <div id="map" style="height: 600px; border-radius: 4px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Coordenadas de Argentina para centrar el mapa
    const map = L.map('map').setView([-34.6037, -58.3816], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // --- Marcadores para Bares (ícono rojo) ---
    const bares = <?= json_encode($bares, JSON_NUMERIC_CHECK) ?>;
    const barIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    bares.forEach(bar => {
        L.marker([bar.latitud, bar.longitud], { icon: barIcon })
            .addTo(map)
            .bindPopup(`<b>${bar.nombre}</b><br>${bar.direccion}`);
    });

    const participaciones = <?= json_encode($participaciones, JSON_NUMERIC_CHECK) ?>;
    
    if (participaciones.length > 0) {
        const markers = L.markerClusterGroup();

        participaciones.forEach(p => {
            if (p.latitud && p.longitud) {
                const marker = L.marker([p.latitud, p.longitud]);
                let popupContent = `<b>${p.nombre} ${p.apellido}</b><br>`;
                popupContent += `Fecha: ${new Date(p.fecha_participacion).toLocaleDateString()}<br>`;
                if (p.bar_nombre) {
                    popupContent += `Bar cercano: ${p.bar_nombre}`;
                }
                marker.bindPopup(popupContent);
                markers.addLayer(marker);
            }
        });

        map.addLayer(markers);
        map.fitBounds(markers.getBounds().pad(0.1));
    }
});
</script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<?php include 'footer.php'; ?>