<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Plan</title>
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
                    <h3 class="page-title">Modifier le Plan : <?= htmlspecialchars($plan['nom_plan']) ?></h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/plan/liste">Plans</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Informations du Plan</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>/plan/update">
                            <?= $csrf_field ?? '' ?>
                            <input type="hidden" name="id_plan" value="<?= $plan['id_plan'] ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nom du Plan <span class="text-danger">*</span></label>
                                        <input type="text" name="nom_plan" class="form-control" value="<?= htmlspecialchars($plan['nom_plan']) ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Couleur</label>
                                        <div class="input-group">
                                            <input type="color" name="couleur" class="form-control" value="<?= htmlspecialchars($plan['couleur']) ?>" style="height: 45px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><?= htmlspecialchars($plan['couleur']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre de Boutiques Max <span class="text-danger">*</span></label>
                                        <input type="number" name="nb_boutiques_max" class="form-control" value="<?= $plan['nb_boutiques_max'] ?>" min="1" required>
                                        <small class="text-muted">Limite de boutiques pour ce plan</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Prix Mensuel (FCFA) <span class="text-danger">*</span></label>
                                        <input type="number" name="prix_mensuel" class="form-control" value="<?= $plan['prix_mensuel'] ?>" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($plan['description'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="actif" class="custom-control-input" id="actif" <?= $plan['actif'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="actif">Plan actif (visible aux utilisateurs)</label>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?= BASE_URL ?>/plan/liste" class="btn btn-secondary">
                                        <i class="la la-arrow-left"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="la la-save"></i> Enregistrer les Modifications
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info">
                    <i class="la la-info-circle"></i> <strong>Note :</strong> La modification d'un plan n'affecte pas les abonnements en cours. Les nouvelles limites s'appliquent aux futurs abonnements.
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
    $('input[name="couleur"]').on('input', function() {
        $(this).closest('.input-group').find('.input-group-text').text($(this).val());
    });
    </script>
</body>
</html>
