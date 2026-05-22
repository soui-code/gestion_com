<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Boutiques</title>

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
<?php include 'config/dashboard.php'; ?>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <!-- Page Content -->
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Historique des Mouvements de Stock</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Mouvements de Stock</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table datatable">
                        <thead>
                            <tr>
                                <th>Date & Heure</th>
                                <th>Produit</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Motif</th>
                                <th>Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mouvements as $mouv) { 
                                $date_m = date('d/m/Y H:i', strtotime($mouv['date_mouvement']));
                                $designation = $mouv['designation'] ? htmlspecialchars($mouv['designation']) : 'Ref: '.$mouv['reference_produit'];
                                $user = $mouv['nom_utilisateur'] ? htmlspecialchars($mouv['nom_utilisateur']) : 'Système';
                            ?>
                            <tr>
                                <td><?= $date_m ?></td>
                                <td><strong><?= $designation ?></strong></td>
                                <td>
                                    <?php if ($mouv['type_mouvement'] == 'entree') { ?>
                                        <span class="badge bg-inverse-success"><i class="fa fa-arrow-down mr-1"></i> Entrée</span>
                                    <?php } else { ?>
                                        <span class="badge bg-inverse-danger"><i class="fa fa-arrow-up mr-1"></i> Sortie</span>
                                    <?php } ?>
                                </td>
                                <td><strong><?= $mouv['quantite'] ?></strong></td>
                                <td><?= htmlspecialchars($mouv['motif']) ?></td>
                                <td><?= $user ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

</div>
<!-- /Page Wrapper -->
<?php // require 'config/pieds.php'; ?>
</div>

    <!-- Scripts -->
    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/js/app.js"></script>?>
</body>
</html>
