<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, produit-scalable=0">
    <title>Commande</title>

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
                            <h3 class="page-title">Commandes</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="http://localhost/gestion_commerciale/utilisateur/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Commandes</li>
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
                                                <th>#</th>
                                                <th>Date & Heure</th>
                                                <th>Reference Produit</th>
                                                <th>Prix Vente</th>
                                                <th>Quantité</th>
                                                <th>Total</th>
                                                <th>Client</th>
                                                <th>Vendeur</th>
                                                <th>Statut</th>
                                                <th>État</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $key => $c) { ?>
                                            <tr>
                                                <td><span class="badge badge-light"><?= htmlspecialchars($c['numero_commande']) ?></span></td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($c['date_commande'])) ?><br>
                                                    <small class="text-muted"><?= htmlspecialchars($c['heure_commande']) ?></small>
                                                </td>
                                                <td><strong><?= htmlspecialchars($c['reference']) ?></strong></td>
                                                <td><?= number_format($c['prix_vente'], 0, ',', ' ') ?> F</td>
                                                <td><?= htmlspecialchars($c['quantite_commande']) ?></td>
                                                <td><strong><?= number_format($c['montant_commande'], 0, ',', ' ') ?> F</strong></td>
                                                <td><?= htmlspecialchars($c['nom_prenom_client'] ?? 'Client occasionnel') ?></td>
                                                <td><span class="text-info"><?= htmlspecialchars($c['nom_vendeur'] ?? 'Inconnu') ?></span></td>
                                                <td><span class="badge badge-info"><?= htmlspecialchars($c['statut_commande']) ?></span></td>
                                                <td><span class="badge badge-success"><?= htmlspecialchars($c['etat_commande']) ?></span></td>
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