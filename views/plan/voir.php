<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Plans d'Abonnement</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .pricing-card {
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            transition: all 0.3s;
            height: 100%;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.current-plan {
            border-color: #ff9b44;
            box-shadow: 0 10px 40px rgba(255, 155, 68, 0.3);
        }

        .pricing-header {
            padding: 30px;
            border-radius: 15px 15px 0 0;
            color: white;
            text-align: center;
        }

        .pricing-price {
            font-size: 48px;
            font-weight: 700;
            margin: 20px 0 10px;
        }

        .pricing-body {
            padding: 30px;
        }

        .pricing-feature {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .pricing-feature:last-child {
            border-bottom: none;
        }

        .badge-current {
            position: absolute;
            top: -15px;
            right: 20px;
            background: #ff9b44;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 155, 68, 0.4);
        }
    </style>
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
                            <h3 class="page-title">Plans d'Abonnement Disponibles</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Tableau de
                                        bord</a></li>
                                <li class="breadcrumb-item active">Plans</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Mon Plan Actuel -->
                <?php if ($abonnement_actif): ?>
                    <div class="alert"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2" style="color: white;"><i class="la la-check-circle"></i> Votre Plan Actuel
                                    : <strong><?= htmlspecialchars($abonnement_actif['nom_plan']) ?></strong></h4>
                                <p class="mb-0">
                                    <i class="la la-store"></i> <?= $abonnement_actif['nb_boutiques_max'] ?> boutique(s)
                                    disponibles
                                    &nbsp;|&nbsp;
                                    <i class="la la-calendar"></i> Expire le :
                                    <strong><?= date('d/m/Y', strtotime($abonnement_actif['date_fin'])) ?></strong>
                                    <?php if ($abonnement_actif['jours_restants'] <= 7): ?>
                                        &nbsp;<span class="badge badge-warning"><i class="la la-exclamation-triangle"></i>
                                            <?= $abonnement_actif['jours_restants'] ?> jour(s) restant(s)</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <h2 class="mb-0" style="color: white;">
                                    <?= number_format($abonnement_actif['prix_mensuel'], 0, ',', ' ') ?>
                                    FCFA<small>/mois</small></h2>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Plans Disponibles -->
                <div class="row">
                    <?php foreach ($plans as $plan): ?>
                        <?php
                        $is_current = ($abonnement_actif && $abonnement_actif['id_plan'] == $plan['id_plan']);
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="pricing-card <?= $is_current ? 'current-plan' : '' ?>" style="position: relative;">
                                <?php if ($is_current): ?>
                                    <span class="badge-current"><i class="la la-star"></i> Actuel</span>
                                <?php endif; ?>

                                <div class="pricing-header" style="background: <?= htmlspecialchars($plan['couleur']) ?>;">
                                    <h3 class="mb-0"><?= htmlspecialchars($plan['nom_plan']) ?></h3>
                                    <div class="pricing-price">
                                        <?= number_format($plan['prix_mensuel'], 0, ',', ' ') ?>
                                        <small style="font-size: 20px;">FCFA</small>
                                    </div>
                                    <p class="mb-0" style="opacity: 0.9;">par mois</p>
                                </div>

                                <div class="pricing-body">
                                    <div class="pricing-feature">
                                        <i class="la la-check text-success"></i>
                                        <strong><?= $plan['nb_boutiques_max'] ?> boutique(s)</strong> maximum
                                    </div>
                                    <div class="pricing-feature">
                                        <i class="la la-check text-success"></i>
                                        Gestion complète des stocks
                                    </div>
                                    <div class="pricing-feature">
                                        <i class="la la-check text-success"></i>
                                        Point de vente (POS)
                                    </div>
                                    <div class="pricing-feature">
                                        <i class="la la-check text-success"></i>
                                        Factures Normalisées (FNE)
                                    </div>
                                    <div class="pricing-feature">
                                        <i class="la la-check text-success"></i>
                                        Utilisateurs illimités
                                    </div>
                                    <div class="pricing-feature">
                                        <i class="la la-check text-success"></i>
                                        Support technique
                                    </div>
                                    <?php if ($plan['description']): ?>
                                        <div class="mt-3">
                                            <small class="text-muted"><?= htmlspecialchars($plan['description']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Contact pour Upgrade -->
                <div class="card" style="border-left: 5px solid #ff9b44;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2"><i class="la la-phone-volume"></i> Besoin de changer de plan ?</h4>
                                <p class="mb-0 text-muted">
                                    Contactez l'administrateur pour upgrade ou renouvellement de votre abonnement.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="tel:+2250778638987" class="btn btn-primary btn-lg">
                                    <i class="la la-phone"></i> +225 07 78 63 89 87
                                </a>
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