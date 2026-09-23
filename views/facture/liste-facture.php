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
    <!-- Favicon / CSS (inchangés) -->
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
                            <h3 class="page-title">
                                <i class="la la-file-invoice"></i> Factures Normalisées Électroniques (FNE)
                            </h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Factures FNE</li>
                            </ul>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-primary" style="font-size: 12px; padding: 8px 12px;">
                                <i class="la la-shield-alt"></i> Conforme DGI Côte d'Ivoire
                            </span>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <!-- Statistiques rapides -->
                                <?php
                                $total = count($result);
                                $validees = 0;
                                $en_attente = 0;
                                $rejetees = 0;
                                foreach ($result as $f) {
                                    $statut = $f['statut_dgi'] ?? 'En attente';
                                    if ($statut === 'Validée') $validees++;
                                    elseif ($statut === 'Rejetée') $rejetees++;
                                    else $en_attente++;
                                }
                                ?>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <div class="stats-info">
                                            <h6>Total Factures</h6>
                                            <h4><?= $total ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-info">
                                            <h6>✅ Certifiées DGI</h6>
                                            <h4 style="color: #28a745;"><?= $validees ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-info">
                                            <h6>⏳ En attente</h6>
                                            <h4 style="color: #ffc107;"><?= $en_attente ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-info">
                                            <h6>❌ Rejetées</h6>
                                            <h4 style="color: #dc3545;"><?= $rejetees ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="table-responsive">
                                    <table class="datatables table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>N° Facture</th>
                                                <th>N° Fiscal</th>
                                                <th>Date Émission</th>
                                                <th>Client</th>
                                                <th>Montant HT</th>
                                                <th>TVA</th>
                                                <th>Montant TTC</th>
                                                <th>Statut DGI</th>
                                                <th>État</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $key => $f) {
                                                    // Assurer compatibilité si colonnes FNE manquantes
                                                    $f['numero_fiscal'] = $f['numero_fiscal'] ?? null;
                                                    $f['date_emission'] = $f['date_emission'] ?? $f['date_facture'];
                                                    $f['montant_tva'] = $f['montant_tva'] ?? 0;
                                                    $f['statut_dgi'] = $f['statut_dgi'] ?? 'En attente';
                                                ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($f['numero_facture']) ?></strong>
                                                    <?php if ($f['type_facture']): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars(ucfirst($f['type_facture'])) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($f['numero_fiscal'])): ?>
                                                        <span class="badge bg-primary" style="font-size: 10px;">
                                                            <?= htmlspecialchars($f['numero_fiscal']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($f['date_emission'])): ?>
                                                        <?= date('d/m/Y H:i', strtotime($f['date_emission'])) ?>
                                                    <?php else: ?>
                                                        <?= date('d/m/Y', strtotime($f['date_facture'])) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($f['client_nom'] ?: 'Client de passage') ?>
                                                </td>
                                                <td><?= number_format($f['montant_ht'], 0, ',', ' ') ?> F</td>
                                                <td><?= number_format($f['montant_tva'], 0, ',', ' ') ?> F</td>
                                                <td><strong><?= number_format($f['montant_ttc'], 0, ',', ' ') ?> F</strong></td>
                                                <td>
                                                    <?php
                                                    $statut = $f['statut_dgi'] ?? 'En attente';
                                                    if ($statut === 'Validée'):
                                                        $badgeClass = 'success';
                                                        $icon = '✅';
                                                    elseif ($statut === 'Rejetée'):
                                                        $badgeClass = 'danger';
                                                        $icon = '❌';
                                                    else:
                                                        $badgeClass = 'warning';
                                                        $icon = '⏳';
                                                    endif;
                                                    ?>
                                                    <span class="badge badge-<?= $badgeClass ?>">
                                                        <?= $icon ?> <?= htmlspecialchars($statut) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if(strtolower($f['etat_facture']) == 'payée' || strtolower($f['etat_facture']) == 'payee'): ?>
                                                        <span class="badge badge-success">Payée</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary"><?= htmlspecialchars(ucfirst($f['etat_facture'])) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?= url('/facture/imprimer_fne?id=' . $f['numero_facture']) ?>"
                                                       target="_blank"
                                                       class="btn btn-primary btn-sm"
                                                       title="Imprimer Facture Normalisée Électronique">
                                                        <i class="la la-file-invoice"></i> FNE
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            } else { ?>
                                            <tr>
                                                <td colspan="10" class="text-center py-5">
                                                    <i class="la la-file-invoice la-3x text-muted mb-3" style="display: block;"></i>
                                                    <p class="text-muted">Aucune facture trouvée</p>
                                                    <a href="<?= url('/vente/effectuer') ?>" class="btn btn-primary btn-sm">
                                                        <i class="la la-plus"></i> Créer une vente
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php } ?>
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
        })
    </script>

</body>
</html>