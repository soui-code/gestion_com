<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélectionner une boutique</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .boutique-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .boutique-card:hover {
            transform: translateY(-3px);
            border-color: #ff9b44;
            box-shadow: 0 5px 20px rgba(255, 155, 68, 0.2);
        }

        .boutique-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #ff9b44 0%, #fc6075 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 26px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .boutique-info h4 {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f1f1f;
        }

        .boutique-info p {
            margin: 0;
            color: #777;
            font-size: 13px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 80px;
            color: #e9ecef;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #777;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 30px;
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
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title"><i class="la la-store"></i> Sélectionner une boutique</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Sélection boutique</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="<?= url('/boutique/enregistrement') ?>" class="btn add-btn">
                                <i class="fa fa-plus"></i> Créer une boutique
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <?php if (!empty($boutiques)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="la la-shopping-bag text-primary"></i>
                                        Choisissez la boutique que vous souhaitez gérer
                                    </h5>

                                    <div class="row">
                                        <?php foreach ($boutiques as $b): ?>
                                            <div class="col-md-6 col-lg-4">
                                                <a href="<?= url('/boutique/activer?id=' . $b['code_boutique']) ?>" style="text-decoration: none; color: inherit;">
                                                    <div class="boutique-card">
                                                        <div class="d-flex align-items-center">
                                                            <div class="boutique-icon">
                                                                <i class="la la-store"></i>
                                                            </div>
                                                            <div class="boutique-info flex-grow-1">
                                                                <h4><?= htmlspecialchars($b['nom_boutique']) ?></h4>
                                                                <p>
                                                                    <i class="fa fa-phone text-muted"></i>
                                                                    <?= htmlspecialchars($b['telephone_boutique'] ?? 'Non renseigné') ?>
                                                                </p>
                                                                <?php if ($b['latitude'] && $b['longitude']): ?>
                                                                    <p class="mt-1">
                                                                        <i class="fa fa-map-marker text-danger"></i>
                                                                        Localisée
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div>
                                                                <?php if ($b['etat_boutique'] === 'actif' || $b['etat_boutique'] === 'active'): ?>
                                                                    <span class="badge badge-success">Actif</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">Inactif</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="empty-state">
                                        <i class="la la-store"></i>
                                        <h3>Aucune boutique enregistrée</h3>
                                        <p>Créez votre première boutique pour commencer à gérer vos ventes</p>
                                        <a href="<?= url('/boutique/enregistrement') ?>" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Créer une boutique
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?= url('') ?>/assets/js/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="<?= url('') ?>/assets/js/popper.min.js"></script>
    <script src="<?= url('') ?>/assets/js/bootstrap.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= url('') ?>/assets/js/app.js"></script>
</body>
</html>
