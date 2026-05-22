<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Boutiques</title>

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Leaflet (Maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- SheetJS (Excel) -->
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    
    <style>
        #map, #edit_map { height: 300px; width: 100%; border-radius: 8px; margin-bottom: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Boutiques</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Boutiques</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_boutique">
                                <i class="fa fa-plus"></i> Ajouter une boutique
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="datatables table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nom de la boutique</th>
                                                <th>Téléphone</th>
                                                <th>Localisation</th>
                                                <th>Produits (JSON)</th>
                                                <th>État</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $b) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($b['code_boutique']) ?></td>
                                                <td><strong><?= htmlspecialchars($b['nom_boutique']) ?></strong></td>
                                                <td><?= htmlspecialchars($b['telephone_boutique'] ?? '-') ?></td>
                                                <td>
                                                    <?php if ($b['latitude'] && $b['longitude']) { ?>
                                                        <small>
                                                            <i class="fa fa-map-marker-alt text-danger"></i>
                                                            <?= number_format($b['latitude'], 6) ?>, <?= number_format($b['longitude'], 6) ?>
                                                        </small>
                                                    <?php } else { echo '<em class="text-muted">Non définie</em>'; } ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($b['produit_json']) {
                                                        $produits = json_decode($b['produit_json'], true);
                                                        if (is_array($produits) && count($produits) > 0) {
                                                            echo '<span class="badge badge-info">'.count($produits).' produit(s)</span>';
                                                        } else {
                                                            echo '<em class="text-muted">Aucun</em>';
                                                        }
                                                    } else {
                                                        echo '<em class="text-muted">Aucun</em>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $b['etat_boutique'] == 'active' ? 'success' : 'secondary' ?>">
                                                        <?= $b['etat_boutique'] == 'active' ? 'Active' : 'Inactive' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="#" class="text-info mr-3 edit-boutique"
                                                       data-code="<?= $b['code_boutique'] ?>"
                                                       data-nom="<?= htmlspecialchars($b['nom_boutique']) ?>"
                                                       data-telephone="<?= htmlspecialchars($b['telephone_boutique'] ?? '') ?>"
                                                       data-latitude="<?= $b['latitude'] ?>"
                                                       data-longitude="<?= $b['longitude'] ?>"
                                                       data-produits="<?= htmlspecialchars($b['produit_json'] ?? '') ?>"
                                                       data-etat="<?= $b['etat_boutique'] ?>">
                                                        <i class="la la-edit"></i> Modifier
                                                    </a>
                                                    <br>
                                                    <a href="#" class="text-danger delete-boutique"
                                                       data-code="<?= $b['code_boutique'] ?>"
                                                       data-nom="<?= htmlspecialchars($b['nom_boutique']) ?>">
                                                        <i class="la la-trash"></i> Supprimer
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUT BOUTIQUE -->
    <div class="modal fade" id="add_boutique" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une boutique</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nom de la boutique <span class="text-danger">*</span></label>
                                    <input type="text" name="sai_nom_boutique" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="text" name="sai_telephone_boutique" class="form-control" placeholder="Ex: +225 000 000 000">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <input type="text" step="any" name="sai_latitude" id="add_lat" class="form-control" placeholder="Sélectionnez sur la carte">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="text" step="any" name="sai_longitude" id="add_lng" class="form-control" placeholder="Sélectionnez sur la carte">
                                </div>
                            </div>
                        </div>

                        <div id="map"></div>

                        <div class="form-group">
                            <label>Importer des produits via Excel</label>
                            <div class="custom-file">
                                <input type="file" id="excel_import" class="custom-file-input" accept=".xlsx, .xls">
                                <label class="custom-file-label">Choisir un fichier...</label>
                            </div>
                            <small class="text-muted">Format: Colonne A (Désignation), Colonne B (Prix de vente)</small>
                        </div>

                        <div class="form-group">
                            <label>Produits associés (JSON) <small class="text-muted">facultatif</small></label>
                            <textarea name="sai_produit_json" id="add_produit_json" class="form-control" rows="4" placeholder='[{"designation":"Produit A","prix_vente":5000},{"designation":"Produit B","prix_vente":3000}]'></textarea>
                            <small class="form-text text-muted">Format JSON attendu : tableau d'objets avec designation et prix_vente</small>
                        </div>

                        <div class="form-group">
                            <label>État</label>
                            <select name="sai_etat_boutique" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-ajouter-boutique" class="btn btn-success">
                            <i class="fa fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MODIFICATION BOUTIQUE -->
    <div class="modal fade" id="editBoutiqueModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST">
                    <input type="hidden" name="sai_code_boutique" id="edit_code">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier la boutique</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nom</label>
                                    <input type="text" name="sai_nom_boutique" id="edit_nom" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="text" name="sai_telephone_boutique" id="edit_telephone" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div id="edit_map"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <label>Latitude</label>
                                <input type="text" step="any" name="sai_latitude" id="edit_latitude" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Longitude</label>
                                <input type="text" step="any" name="sai_longitude" id="edit_longitude" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Importer/Mettre à jour via Excel</label>
                            <div class="custom-file">
                                <input type="file" id="edit_excel_import" class="custom-file-input" accept=".xlsx, .xls">
                                <label class="custom-file-label">Choisir un fichier...</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Produits (JSON)</label>
                            <textarea name="sai_produit_json" id="edit_produits" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <label>État</label>
                            <select name="sai_etat_boutique" id="edit_etat" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-modifier-boutique" class="btn btn-primary">
                            <i class="fa fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SUPPRESSION -->
    <div class="modal fade" id="deleteBoutiqueModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="POST">
                    <input type="hidden" name="sai_code_boutique" id="delete_code">
                    <div class="modal-body text-center py-4">
                        <h5>Confirmer la suppression</h5>
                        <p>Voulez-vous vraiment supprimer la boutique <strong id="delete_nom"></strong> ?</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-supprimer-boutique" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
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

    <script>
        $(document).ready(function () {
            $('.datatables').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' }
            });

            // === GESTION DES CARTES ===
            let addMap, addMarker;
            let editMap, editMarker;

            function initMap(mapId, latInputId, lngInputId, lat = 5.3484, lng = -4.0305) {
                const map = L.map(mapId).setView([lat, lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    $(`#${latInputId}`).val(e.latlng.lat.toFixed(6));
                    $(`#${lngInputId}`).val(e.latlng.lng.toFixed(6));
                });

                marker.on('dragend', function(e) {
                    const pos = marker.getLatLng();
                    $(`#${latInputId}`).val(pos.lat.toFixed(6));
                    $(`#${lngInputId}`).val(pos.lng.toFixed(6));
                });

                return { map, marker };
            }

            // Init Map Ajout
            $('#add_boutique').on('shown.bs.modal', function () {
                if (!addMap) {
                    const obj = initMap('map', 'add_lat', 'add_lng');
                    addMap = obj.map;
                    addMarker = obj.marker;
                } else {
                    addMap.invalidateSize();
                }
            });

            // Init Map Modification
            $('#editBoutiqueModal').on('shown.bs.modal', function () {
                const lat = parseFloat($('#edit_latitude').val()) || 5.3484;
                const lng = parseFloat($('#edit_longitude').val()) || -4.0305;
                
                if (!editMap) {
                    const obj = initMap('edit_map', 'edit_latitude', 'edit_longitude', lat, lng);
                    editMap = obj.map;
                    editMarker = obj.marker;
                } else {
                    editMap.setView([lat, lng], 13);
                    editMarker.setLatLng([lat, lng]);
                    editMap.invalidateSize();
                }
            });

            // === GESTION EXCEL ===
            function handleExcelImport(fileInputId, textareaId) {
                $(`#${fileInputId}`).on('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    $(this).next('.custom-file-label').html(file.name);

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                        const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                        const products = [];
                        jsonData.forEach((row, index) => {
                            if (index === 0 && (row[0] === 'Désignation' || row[0] === 'Designation' || row[0] === 'Nom')) return; // Skip header
                            if (row[0] && row[1]) {
                                products.push({ designation: row[0].toString(), prix_vente: parseFloat(row[1]) });
                            }
                        });

                        $(`#${textareaId}`).val(JSON.stringify(products, null, 2));
                    };
                    reader.readAsArrayBuffer(file);
                });
            }

            handleExcelImport('excel_import', 'add_produit_json');
            handleExcelImport('edit_excel_import', 'edit_produits');

            // === MODIFICATION ===
            $('.edit-boutique').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#edit_code').val(btn.data('code'));
                $('#edit_nom').val(btn.data('nom'));
                $('#edit_telephone').val(btn.data('telephone'));
                $('#edit_latitude').val(btn.data('latitude'));
                $('#edit_longitude').val(btn.data('longitude'));
                $('#edit_produits').val(btn.data('produits'));
                $('#edit_etat').val(btn.data('etat'));

                $('#editBoutiqueModal').modal('show');
            });

            // === SUPPRESSION ===
            $('.delete-boutique').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#delete_code').val(btn.data('code'));
                $('#delete_nom').text(btn.data('nom'));

                $('#deleteBoutiqueModal').modal('show');
            });
        });
    </script>
</body>
</html>