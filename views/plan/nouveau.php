<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Plan</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
<?php include 'config/dashboard.php'; ?>

<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Créer un Nouveau Plan</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/plan/liste">Plans</a></li>
                        <li class="breadcrumb-item active">Nouveau</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Informations du Nouveau Plan</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>/plan/creer">
                            <?= $csrf_field ?? '' ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nom du Plan <span class="text-danger">*</span></label>
                                        <input type="text" name="nom_plan" class="form-control" placeholder="Ex: Starter, Pro, Enterprise..." required>
                                        <small class="text-muted">Nom qui sera affiché aux utilisateurs</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Couleur</label>
                                        <div class="input-group">
                                            <input type="color" name="couleur" class="form-control" value="#ff9b44" style="height: 45px;" id="couleurPicker">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="couleurLabel">#ff9b44</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">Couleur d'affichage du plan</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre de Boutiques Max <span class="text-danger">*</span></label>
                                        <input type="number" name="nb_boutiques_max" class="form-control" value="1" min="1" max="100" required>
                                        <small class="text-muted">Limite de boutiques pour ce plan</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Prix Mensuel (FCFA) <span class="text-danger">*</span></label>
                                        <input type="number" name="prix_mensuel" class="form-control" value="0" min="0" step="100" required>
                                        <small class="text-muted">Prix par mois en Francs CFA</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Description du plan pour les utilisateurs..."></textarea>
                                <small class="text-muted">Texte descriptif affiché sur la page des plans</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="actif" class="custom-control-input" id="actif" checked>
                                    <label class="custom-control-label" for="actif">Plan actif (visible aux utilisateurs)</label>
                                </div>
                                <small class="text-muted">Si décoché, le plan ne sera pas visible dans la liste des plans disponibles</small>
                            </div>

                            <hr class="my-4">

                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?= BASE_URL ?>/plan/liste" class="btn btn-secondary">
                                        <i class="la la-arrow-left"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="la la-plus-circle"></i> Créer le Plan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info">
                    <i class="la la-info-circle"></i> <strong>Conseil :</strong>
                    Créez des plans adaptés à vos clients : petit commerce (1-2 boutiques), moyen (3-5 boutiques), grande entreprise (10+ boutiques).
                </div>

                <!-- Exemple de Plans -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">💡 Exemples de Plans Classiques</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>Basic</strong> : 1 boutique - 5 000 FCFA/mois - <span style="color: #17a2b8;">●</span> Bleu</li>
                            <li><strong>Standard</strong> : 3 boutiques - 12 000 FCFA/mois - <span style="color: #28a745;">●</span> Vert</li>
                            <li><strong>Premium</strong> : 7 boutiques - 25 000 FCFA/mois - <span style="color: #ff9b44;">●</span> Orange</li>
                            <li><strong>Entreprise</strong> : 15 boutiques - 50 000 FCFA/mois - <span style="color: #6f42c1;">●</span> Violet</li>
                        </ul>
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
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/app.js"></script>

    <script>
    // Mise à jour dynamique du label couleur
    $('#couleurPicker').on('input', function() {
        $('#couleurLabel').text($(this).val());
    });
    </script>
</body>
</html>
