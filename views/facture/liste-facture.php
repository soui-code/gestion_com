<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, produit-scalable=0">
    <title>Factures</title>

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
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Factures</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Factures</li>
                            </ul>
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
                                                <th>Numéro</th>
                                                <th>Titre</th>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Montant HT</th>
                                                <th>Taxe</th>
                                                <th>Montant TTC</th>
                                                <th>Client</th>
                                                <th>Etat</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $key => $f) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($f['numero_facture']) ?></td>
                                                <td><?= htmlspecialchars($f['titre_facture']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($f['date_facture'])) ?></td>
                                                <td><?= htmlspecialchars(ucfirst($f['type_facture'])) ?></td>
                                                <td><?= number_format($f['montant_ht'], 0, ',', ' ') ?> FCFA</td>
                                                <td><?= $f['taxe_titre'] ? htmlspecialchars($f['taxe_titre'] . ' (' . $f['taxe_taux'] . '%)') : 'Aucune' ?></td>
                                                <td><strong><?= number_format($f['montant_ttc'], 0, ',', ' ') ?> FCFA</strong></td>
                                                <td><?= htmlspecialchars($f['client_nom'] ?: 'Particulier') ?></td>
                                                <td>
                                                    <?php if(strtolower($f['etat_facture']) == 'payée' || strtolower($f['etat_facture']) == 'payee'): ?>
                                                        <span class="badge badge-success">Payée</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning"><?= htmlspecialchars(ucfirst($f['etat_facture'])) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>                                               
                                                    <a href="imprimer?id=<?= $f['numero_facture'] ?>" 
                                                    target="_blank" 
                                                    class="btn btn-success btn-sm">
                                                    <i class="la la-print"></i> Imprimer
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