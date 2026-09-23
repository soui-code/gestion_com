<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, produit-scalable=0">
    <title>Factures</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <style>
        #map {
    height: calc(100vh - 150px); 
    width: 100%;
    border-radius: 10px;
}
        .emoji-icon {
    font-size: 40px;   
    line-height: 40px; 
    text-align: center;
}

    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Localisation</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Des boutiques</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                <div id="map"></div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Initialisation Map
    var map = L.map('map').setView([5.345317, -4.024429], 5); 

    // OpenStreetMap Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);



    // Boutiques passées par le Controller en JSON
    var boutiques = <?=
        json_encode($boutiques, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    ?>;

    let hasMarkers = false;
    let bounds = [];

    boutiques.forEach(function(b) {
        if (!b.latitude || !b.longitude || b.latitude == 0 || b.longitude == 0) return;

        hasMarkers = true;
        bounds.push([parseFloat(b.latitude), parseFloat(b.longitude)]);

        // Contenu popup
        let popupContent = `
            <div style="min-width:200px;">
                <h5 style="margin:0 0 10px 0;color:#007bff;">${b.nom_boutique || 'Boutique'}</h5>
                <p style="margin:5px 0;"><i class="fa fa-phone"></i> ${b.telephone_boutique || 'N/A'}</p>
                <p style="margin:5px 0;"><i class="fa fa-map-marker"></i> ${parseFloat(b.latitude).toFixed(6)}, ${parseFloat(b.longitude).toFixed(6)}</p>
            </div>
        `;

        var boutiqueIcon = L.divIcon({
            html: "🏪",
            className: "emoji-icon",
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        // Marqueur
        L.marker([parseFloat(b.latitude), parseFloat(b.longitude)], {icon: boutiqueIcon})
            .addTo(map)
            .bindPopup(popupContent);
    });

    // Ajuster la vue pour inclure tous les marqueurs
    if (hasMarkers && bounds.length > 0) {
        if (bounds.length === 1) {
            map.setView(bounds[0], 13);
        } else {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
</script>

</body>
</html>