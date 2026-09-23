<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="<?= url('/manifest.json') ?>">
     <meta name="theme-color" content="#1e293b">
     <link rel="apple-touch-icon" href="<?= url('/assets/icons/icon-192.png') ?>">
     

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-card {
            border-left: 4px solid #ff9b44;
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .societe-item {
            border-left: 3px solid #4CAF50;
            background: #f9f9f9;
        }
        .societe-item:hover {
            background: #f0f0f0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Header -->
                <div class="page-header mb-4">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="page-title">
                                <i class="la la-tachometer"></i> Dashboard Administrateur
                            </h2>
                            <p class="text-muted">Vue d'ensemble de toutes les sociétés du système</p>
                        </div>
                    </div>
                </div>

                <!-- Statistiques Globales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-primary">
                                        <i class="la la-building"></i>
                                    </span>
                                    <div class="dash-count">
                                        <h3><?= count($societes) ?></h3>
                                    </div>
                                </div>
                                <div class="dash-widget-info">
                                    <h6 class="text-muted">Sociétés</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-success">
                                        <i class="la la-store"></i>
                                    </span>
                                    <div class="dash-count">
                                        <h3><?= $stats['total_boutiques'] ?></h3>
                                    </div>
                                </div>
                                <div class="dash-widget-info">
                                    <h6 class="text-muted">Boutiques</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-info">
                                        <i class="fa fa-users"></i>
                                    </span>
                                    <div class="dash-count">
                                        <h3><?= $stats['total_utilisateurs'] ?></h3>
                                    </div>
                                </div>
                                <div class="dash-widget-info">
                                    <h6 class="text-muted">Utilisateurs</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-warning">
                                        <i class="fa fa-cube"></i>
                                    </span>
                                    <div class="dash-count">
                                        <h3><?= $stats['total_produits'] ?></h3>
                                    </div>
                                </div>
                                <div class="dash-widget-info">
                                    <h6 class="text-muted">Produits</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des Sociétés -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title mb-0">
                                            <i class="la la-building"></i> Toutes les Sociétés
                                        </h4>
                                    </div>
                                    <div class="col-auto">
                                        <a href="<?= url('/societe/liste') ?>" class="btn btn-primary btn-sm">
                                            <i class="fa fa-cog"></i> Gérer les sociétés
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($societes)): ?>
                                    <?php foreach ($societes as $societe): ?>
                                    <div class="societe-item p-3 mb-3 rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center">
                                                <?php if ($societe['logo_societe']): ?>
                                                    <img src="../<?= e($societe['logo_societe']) ?>"
                                                         width="50" height="50"
                                                         class="rounded shadow-sm"
                                                         style="object-fit: cover;"
                                                         alt="Logo">
                                                <?php else: ?>
                                                    <div class="avatar avatar-lg">
                                                        <span class="avatar-title rounded-circle bg-secondary">
                                                            <?= strtoupper(substr($societe['nom_societe'], 0, 2)) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="col-md-3">
                                                <h5 class="mb-1"><?= e($societe['nom_societe']) ?></h5>
                                                <small class="text-muted">ID: #<?= $societe['id_societe'] ?></small>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <span class="badge badge-info p-2">
                                                            <i class="la la-store"></i> <?= (int)$societe['nb_boutiques'] ?> Boutiques
                                                        </span>
                                                    </div>
                                                    <div class="col-4">
                                                        <span class="badge badge-primary p-2">
                                                            <i class="la la-users"></i> <?= (int)$societe['nb_utilisateurs'] ?> Utilisateurs
                                                        </span>
                                                    </div>
                                                    <div class="col-4">
                                                        <span class="badge badge-success p-2">
                                                            <i class="la la-cube"></i> <?= (int)$societe['nb_produits'] ?> Produits
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2 text-right">
                                                <a href="<?= url('/utilisateur/switchSociete?id=' . $societe['id_societe']) ?>"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="la la-eye"></i> Consulter
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="la la-inbox la-3x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune société enregistrée</p>
                                        <a href="<?= url('/societe/liste') ?>" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Créer la première société
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Rapides -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0"><i class="la la-bolt"></i> Actions Rapides</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <a href="<?= url('/societe/liste') ?>" class="btn btn-block btn-lg btn-primary">
                                            <i class="fa fa-plus-circle"></i><br>
                                            Nouvelle Société
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="<?= url('/utilisateur/enregistrement') ?>" class="btn btn-block btn-lg btn-info">
                                            <i class="fa fa-user-plus"></i><br>
                                            Nouveau Manager
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="<?= url('/utilisateur/logs') ?>" class="btn btn-block btn-lg btn-secondary">
                                            <i class="la la-file-text"></i><br>
                                            Voir les Logs
                                        </a>
                                    </div>
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
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>
