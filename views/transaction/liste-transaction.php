<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, produit-scalable=0">
    <title>Transaction</title>
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
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Transactions</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://localhost/gestion_commerciale/utilisateur/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Transactions</li>
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
                                                <th>Date & Heure</th>
                                                <th>Montant</th>
                                                <th>Mode Règlement</th>
                                                <th>Facture</th>
                                                <th>Utilisateur</th>
                                                <th>État</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $key => $c) { ?>
                                            <tr>
                                                <td><span class="badge badge-light"><?= htmlspecialchars($c['numero_transaction']) ?></span></td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($c['date_transaction'])) ?><br>
                                                    <small class="text-muted"><?= htmlspecialchars($c['heure_transaction']) ?></small>
                                                </td>
                                                <td><strong><?= number_format($c['montant_transaction'], 0, ',', ' ') ?> F</strong></td>
                                                <td><?= htmlspecialchars($c['mode_reglement']) ?></td>
                                                <td><?= htmlspecialchars($c['numero_facture']) ?></td>
                                                <td><span class="text-info"><?= htmlspecialchars($c['nom_utilisateur'] ?? 'Inconnu') ?></span></td>
                                                <td><span class="badge badge-success"><?= htmlspecialchars($c['etat_transaction']) ?></span></td>
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