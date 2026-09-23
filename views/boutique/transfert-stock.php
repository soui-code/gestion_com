<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert de Stock</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/select2.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .transfer-card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .transfer-arrow { text-align: center; font-size: 40px; color: #007bff; margin: 20px 0; }
        .stock-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .stock-info strong { font-size: 18px; color: #28a745; }
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
                            <h3 class="page-title">Transfert de Stock entre Boutiques</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= url('/boutique/enregistrement') ?>">Boutiques</a></li>
                                <li class="breadcrumb-item active">Transfert Stock</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="transfer-card">
                            <form id="transferForm">
                                <div class="row">
                                    <!-- Boutique Source -->
                                    <div class="col-md-5">
                                        <div class="card bg-light">
                                            <div class="card-header bg-danger text-white">
                                                <i class="fa fa-minus-circle"></i> Source (Départ)
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Boutique Source <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="boutique_source" name="boutique_source" required>
                                                        <option value="">-- Sélectionner --</option>
                                                        <?php foreach ($boutiques as $b): ?>
                                                        <option value="<?= $b['code_boutique'] ?>"><?= htmlspecialchars($b['nom_boutique']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Produit <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="produit" name="reference" required disabled>
                                                        <option value="">-- Choisir boutique d'abord --</option>
                                                    </select>
                                                </div>

                                                <div id="stock_source_info" class="stock-info" style="display:none;">
                                                    <div>Stock disponible : <strong id="stock_dispo">0</strong></div>
                                                    <div class="text-muted">Référence : <span id="ref_display"></span></div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Quantité à transférer <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="quantite" name="quantite" min="1" required disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Flèche -->
                                    <div class="col-md-2">
                                        <div class="transfer-arrow">
                                            <i class="fa fa-arrow-right"></i>
                                        </div>
                                    </div>

                                    <!-- Boutique Destination -->
                                    <div class="col-md-5">
                                        <div class="card bg-light">
                                            <div class="card-header bg-success text-white">
                                                <i class="fa fa-plus-circle"></i> Destination (Arrivée)
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Boutique Destination <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="boutique_destination" name="boutique_destination" required disabled>
                                                        <option value="">-- Sélectionner --</option>
                                                        <?php foreach ($boutiques as $b): ?>
                                                        <option value="<?= $b['code_boutique'] ?>"><?= htmlspecialchars($b['nom_boutique']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="alert alert-info mt-4">
                                                    <i class="fa fa-info-circle"></i> Le produit sera automatiquement créé dans la boutique destination s'il n'existe pas.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg" id="btnTransferer" disabled>
                                            <i class="fa fa-exchange"></i> Transférer le Stock
                                        </button>
                                        <a href="<?= url('/boutique/enregistrement') ?>" class="btn btn-secondary btn-lg">
                                            <i class="fa fa-times"></i> Annuler
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Historique récent -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title mb-0"><i class="fa fa-history"></i> Transferts Récents</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Produit</th>
                                                <th>De</th>
                                                <th>Vers</th>
                                                <th>Quantité</th>
                                                <th>Par</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($historique)): ?>
                                                <?php foreach ($historique as $h): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y H:i', strtotime($h['date_mouvement'])) ?></td>
                                                    <td><?= htmlspecialchars($h['produit']) ?></td>
                                                    <td><span class="badge badge-danger"><?= htmlspecialchars($h['boutique_source']) ?></span></td>
                                                    <td><span class="badge badge-success"><?= htmlspecialchars($h['boutique_dest']) ?></span></td>
                                                    <td><?= $h['quantite'] ?></td>
                                                    <td><?= htmlspecialchars($h['utilisateur']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr><td colspan="6" class="text-center text-muted">Aucun transfert récent</td></tr>
                                            <?php endif; ?>
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

    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/select2.min.js"></script>
    <script src="../assets/js/app.js"></script>

    <script>
    $(document).ready(function() {
        $('.select2').select2();
        let stockDisponible = 0;

        // Charger produits quand boutique source change
        $('#boutique_source').on('change', function() {
            const boutiqueId = $(this).val();
            $('#produit').prop('disabled', true).html('<option value="">Chargement...</option>');
            $('#boutique_destination').prop('disabled', true);
            $('#quantite').prop('disabled', true);
            $('#stock_source_info').hide();
            $('#btnTransferer').prop('disabled', true);

            if (!boutiqueId) return;

            $.get('<?= url('/boutique/produits') ?>', { id_boutique: boutiqueId }, function(data) {
                let options = '<option value="">-- Sélectionner produit --</option>';
                if (data.success && data.produits.length > 0) {
                    data.produits.forEach(p => {
                        options += `<option value="${p.reference}" data-stock="${p.stock}">${p.designation} (Stock: ${p.stock})</option>`;
                    });
                } else {
                    options = '<option value="">Aucun produit dans cette boutique</option>';
                }
                $('#produit').html(options).prop('disabled', false);
            }).fail(function() {
                $('#produit').html('<option value="">Erreur de chargement</option>');
            });
        });

        // Quand produit sélectionné
        $('#produit').on('change', function() {
            const stock = $(this).find(':selected').data('stock');
            const ref = $(this).val();

            if (ref && stock > 0) {
                stockDisponible = stock;
                $('#stock_dispo').text(stock);
                $('#ref_display').text(ref);
                $('#stock_source_info').show();
                $('#quantite').prop('disabled', false).attr('max', stock).val(1);
                $('#boutique_destination').prop('disabled', false);
                validateForm();
            } else {
                $('#stock_source_info').hide();
                $('#quantite').prop('disabled', true);
                $('#boutique_destination').prop('disabled', true);
                $('#btnTransferer').prop('disabled', true);
            }
        });

        // Validation quantité
        $('#quantite').on('input', function() {
            const val = parseInt($(this).val());
            if (val > stockDisponible) {
                $(this).val(stockDisponible);
            }
            validateForm();
        });

        // Validation boutique destination
        $('#boutique_destination').on('change', validateForm);

        function validateForm() {
            const source = $('#boutique_source').val();
            const dest = $('#boutique_destination').val();
            const produit = $('#produit').val();
            const qte = parseInt($('#quantite').val()) || 0;

            const valid = source && dest && produit && qte > 0 && source !== dest;
            $('#btnTransferer').prop('disabled', !valid);

            if (source === dest && dest) {
                alert('Les boutiques source et destination doivent être différentes !');
                $('#boutique_destination').val('');
            }
        }

        // Soumission formulaire
        $('#transferForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                reference: $('#produit').val(),
                boutique_source: $('#boutique_source').val(),
                boutique_destination: $('#boutique_destination').val(),
                quantite: $('#quantite').val()
            };

            $('#btnTransferer').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Transfert en cours...');

            $.post('<?= url('/boutique/transferer') ?>', formData, function(response) {
                if (response.success) {
                    alert('✅ ' + response.message);
                    location.reload();
                } else {
                    alert('❌ Erreur: ' + response.error);
                    $('#btnTransferer').prop('disabled', false).html('<i class="fa fa-exchange"></i> Transférer le Stock');
                }
            }).fail(function() {
                alert('❌ Erreur de communication avec le serveur');
                $('#btnTransferer').prop('disabled', false).html('<i class="fa fa-exchange"></i> Transférer le Stock');
            });
        });
    });
    </script>
</body>
</html>
