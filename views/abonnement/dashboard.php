<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Abonnements</title>
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
                    <h3 class="page-title">Gestion des Abonnements</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Abonnements</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="<?= BASE_URL ?>/plan/liste" class="btn btn-white">
                        <i class="la la-cog"></i> Gérer les Plans
                    </a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card" style="border-left: 5px solid #28a745;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1"><?= $stats['total_actifs'] ?></h4>
                                <p class="text-muted mb-0">Abonnements Actifs</p>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title rounded-circle bg-success font-20">
                                    <i class="la la-check-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card" style="border-left: 5px solid #ffc107;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1"><?= $stats['expire_bientot'] ?></h4>
                                <p class="text-muted mb-0">Expire < 7 jours</p>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title rounded-circle bg-warning font-20">
                                    <i class="la la-exclamation-triangle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card" style="border-left: 5px solid #e74c3c;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1"><?= $stats['expires'] ?></h4>
                                <p class="text-muted mb-0">Abonnements Expirés</p>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title rounded-circle bg-danger font-20">
                                    <i class="la la-times-circle"></i>
                                </span>
                            </div>
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
                        <h4 class="card-title mb-0">Liste des Sociétés</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table" id="table-abonnements">
                                <thead>
                                    <tr>
                                        <th>Société</th>
                                        <th>Plan Actuel</th>
                                        <th>Boutiques</th>
                                        <th>Date Expiration</th>
                                        <th>Jours Restants</th>
                                        <th>Statut</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($abonnements as $abon): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($abon['nom_societe']) ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($abon['nom_plan']): ?>
                                                <span class="badge" style="background: <?= htmlspecialchars($abon['couleur_plan']) ?>; color: white; font-size: 13px; padding: 6px 12px;">
                                                    <?= htmlspecialchars($abon['nom_plan']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Aucun</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <?= $abon['nb_boutiques_utilisees'] ?> / <?= $abon['nb_boutiques_max'] ?? '?' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($abon['abonnement_expire_le']): ?>
                                                <?= date('d/m/Y', strtotime($abon['abonnement_expire_le'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($abon['jours_restants'] !== null): ?>
                                                <?php if ($abon['jours_restants'] < 0): ?>
                                                    <span class="badge bg-inverse-danger">Expiré</span>
                                                <?php elseif ($abon['jours_restants'] <= 7): ?>
                                                    <span class="badge bg-inverse-warning"><?= $abon['jours_restants'] ?> jour(s)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-inverse-success"><?= $abon['jours_restants'] ?> jour(s)</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($abon['statut_abonnement'] === 'actif'): ?>
                                                <span class="badge bg-inverse-success"><i class="la la-check"></i> Actif</span>
                                            <?php elseif ($abon['statut_abonnement'] === 'expire'): ?>
                                                <span class="badge bg-inverse-danger"><i class="la la-times"></i> Expiré</span>
                                            <?php else: ?>
                                                <span class="badge bg-inverse-secondary"><?= ucfirst($abon['statut_abonnement']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= BASE_URL ?>/abonnement/attribuer?id=<?= $abon['id_societe'] ?>" class="btn btn-sm btn-white text-success">
                                                <i class="la la-sync-alt"></i> Gérer
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
        // Tri par défaut : jours restants (croissant)
        $('#table-abonnements').DataTable({
            "order": [[4, "asc"]], // Colonne "Jours Restants"
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    });
    </script>
</body>
</html>
