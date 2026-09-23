<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Plans d'Abonnement</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
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
                    <h3 class="page-title">Plans d'Abonnement</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Plans</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="<?= BASE_URL ?>/plan/nouveau" class="btn btn-primary">
                        <i class="la la-plus"></i> Ajouter un Plan
                    </a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Statistiques -->
        <div class="row mb-4">
            <?php foreach ($plans as $plan): ?>
                <?php
                $nb_societes = 0;
                foreach ($stats as $stat) {
                    if ($stat['id_plan'] == $plan['id_plan']) {
                        $nb_societes = $stat['nb_societes'];
                        break;
                    }
                }
                ?>
                <div class="col-md-4">
                    <div class="card" style="border-left: 5px solid <?= htmlspecialchars($plan['couleur']) ?>;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><?= htmlspecialchars($plan['nom_plan']) ?></h4>
                                    <p class="text-muted mb-0" style="font-size: 13px;"><?= $nb_societes ?> société(s) active(s)</p>
                                </div>
                                <div class="text-right">
                                    <h3 class="mb-0" style="color: <?= htmlspecialchars($plan['couleur']) ?>;"><?= number_format($plan['prix_mensuel'], 0, ',', ' ') ?></h3>
                                    <small class="text-muted">FCFA/mois</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Liste des Plans -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Tous les Plans</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped custom-table" id="table-plans">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>Nom du Plan</th>
                                        <th>Boutiques Max</th>
                                        <th>Prix Mensuel</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($plans as $plan): ?>
                                    <tr>
                                        <td><div style="width: 4px; height: 40px; background: <?= htmlspecialchars($plan['couleur']) ?>; border-radius: 3px;"></div></td>
                                        <td><strong><?= htmlspecialchars($plan['nom_plan']) ?></strong></td>
                                        <td>
                                            <span class="badge badge-primary" style="font-size: 14px; padding: 6px 12px;">
                                                <i class="la la-store"></i> <?= $plan['nb_boutiques_max'] ?> boutique(s)
                                            </span>
                                        </td>
                                        <td><strong><?= number_format($plan['prix_mensuel'], 0, ',', ' ') ?> FCFA</strong></td>
                                        <td><small class="text-muted"><?= htmlspecialchars($plan['description'] ?? 'N/A') ?></small></td>
                                        <td>
                                            <?php if ($plan['actif']): ?>
                                                <span class="badge bg-inverse-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-inverse-danger">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= BASE_URL ?>/plan/modifier?id=<?= $plan['id_plan'] ?>" class="btn btn-sm btn-white text-primary">
                                                <i class="la la-edit"></i> Modifier
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/js/app.js"></script>

    <script>
    $(document).ready(function() {
        $('#table-plans').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    });
    </script>
</body>
</html>
