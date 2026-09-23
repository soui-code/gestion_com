<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer mot de passe</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Changer mot de passe</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Mot de passe</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="POST">
                                    <?= $csrf_field ?? '' ?>
                                    <div class="form-group">
                                        <label>Mot de passe actuel <span class="text-danger">*</span></label>
                                        <input type="password" name="sai_ancien_mdp" class="form-control" required minlength="6">
                                    </div>
                                    <div class="form-group">
                                        <label>Nouveau mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" name="sai_nouveau_mdp" class="form-control" required minlength="6">
                                    </div>
                                    <div class="form-group">
                                        <label>Confirmer le mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" name="sai_confirmation_mdp" class="form-control" required minlength="6">
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" name="btn-changer-mdp" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Changer le mot de passe
                                        </button>
                                    </div>
                                </form>
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
