<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer l'Abonnement</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">
    <!-- Version corrigée CSRF -->
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
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
                    <h3 class="page-title">Gérer l'Abonnement : <?= htmlspecialchars($societe['nom_societe']) ?></h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/abonnement/dashboard">Abonnements</a></li>
                        <li class="breadcrumb-item active">Attribuer</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <!-- Formulaire Attribution -->
            <div class="col-md-8">
                <!-- Abonnement Actuel -->
                <?php if ($abonnement_actuel): ?>
                <div class="card mb-4" style="border-left: 5px solid <?= htmlspecialchars($abonnement_actuel['couleur_plan']) ?>;">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Abonnement Actuel</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Plan :</strong> <span class="badge" style="background: <?= htmlspecialchars($abonnement_actuel['couleur_plan']) ?>; color: white;"><?= htmlspecialchars($abonnement_actuel['nom_plan']) ?></span></p>
                                <p><strong>Prix :</strong> <?= number_format($abonnement_actuel['prix_mensuel'], 0, ',', ' ') ?> FCFA/mois</p>
                                <p><strong>Boutiques :</strong> <?= $nb_boutiques ?> / <?= $abonnement_actuel['nb_boutiques_max'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Date début :</strong> <?= date('d/m/Y', strtotime($abonnement_actuel['date_debut'])) ?></p>
                                <p><strong>Date fin :</strong> <?= date('d/m/Y', strtotime($abonnement_actuel['date_fin'])) ?></p>
                                <p><strong>Jours restants :</strong>
                                    <?php if ($abonnement_actuel['jours_restants'] < 0): ?>
                                        <span class="badge bg-inverse-danger">Expiré</span>
                                    <?php elseif ($abonnement_actuel['jours_restants'] <= 7): ?>
                                        <span class="badge bg-inverse-warning"><?= $abonnement_actuel['jours_restants'] ?> jour(s)</span>
                                    <?php else: ?>
                                        <span class="badge bg-inverse-success"><?= $abonnement_actuel['jours_restants'] ?> jour(s)</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="la la-exclamation-triangle"></i> Cette société n'a pas encore d'abonnement actif.
                </div>
                <?php endif; ?>

                <!-- Formulaire Nouveau/Renouvellement -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <?= $abonnement_actuel ? 'Renouveler / Changer l\'Abonnement' : 'Attribuer un Nouvel Abonnement' ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>/abonnement/enregistrer" id="formAbonnement">
                            <?= $csrf_field ?? '' ?>
                            <input type="hidden" name="id_societe" value="<?= $societe['id_societe'] ?>">

                            <div class="form-group">
                                <label>Choisir un Plan <span class="text-danger">*</span></label>
                                <div class="row">
                                    <?php foreach ($plans as $plan): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="plan-option <?= ($abonnement_actuel && $abonnement_actuel['id_plan'] == $plan['id_plan']) ? 'active' : '' ?>"
                                             style="border: 2px solid <?= htmlspecialchars($plan['couleur']) ?>; border-radius: 10px; padding: 20px; cursor: pointer; text-align: center; transition: all 0.3s;"
                                             onclick="selectPlan(<?= $plan['id_plan'] ?>, <?= $plan['nb_boutiques_max'] ?>, <?= $plan['prix_mensuel'] ?>, '<?= htmlspecialchars($plan['nom_plan']) ?>')">
                                            <input type="radio" name="id_plan" value="<?= $plan['id_plan'] ?>" id="plan_<?= $plan['id_plan'] ?>"
                                                   <?= ($abonnement_actuel && $abonnement_actuel['id_plan'] == $plan['id_plan']) ? 'checked' : '' ?> required style="display: none;">
                                            <div style="font-size: 24px; color: <?= htmlspecialchars($plan['couleur']) ?>; margin-bottom: 10px;">
                                                <i class="la la-award"></i>
                                            </div>
                                            <h5 class="mb-2"><?= htmlspecialchars($plan['nom_plan']) ?></h5>
                                            <p class="mb-1"><strong><?= $plan['nb_boutiques_max'] ?></strong> boutique(s)</p>
                                            <h4 style="color: <?= htmlspecialchars($plan['couleur']) ?>; font-weight: 700;"><?= number_format($plan['prix_mensuel'], 0, ',', ' ') ?> FCFA</h4>
                                            <small class="text-muted">par mois</small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($nb_boutiques > 0): ?>
                                <small class="text-info">
                                    <i class="la la-info-circle"></i> Cette société a actuellement <strong><?= $nb_boutiques ?></strong> boutique(s) enregistrée(s).
                                </small>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date Début <span class="text-danger">*</span></label>
                                        <input type="date" name="date_debut" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Durée (Mois) <span class="text-danger">*</span></label>
                                        <input type="number" name="duree_mois" class="form-control" value="1" min="1" max="12" required>
                                        <small class="text-muted">Date de fin calculée automatiquement</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Montant Payé (FCFA)</label>
                                        <input type="number" name="prix_paye" id="prix_paye" class="form-control" value="0" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mode de Paiement</label>
                                        <select name="mode_paiement" class="form-control">
                                            <option value="Non spécifié">Non spécifié</option>
                                            <option value="Mobile Money">Mobile Money</option>
                                            <option value="Espèces">Espèces</option>
                                            <option value="Virement bancaire">Virement bancaire</option>
                                            <option value="Chèque">Chèque</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Notes / Remarques</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Notes internes..."></textarea>
                            </div>

                            <hr class="my-4">

                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?= BASE_URL ?>/abonnement/dashboard" class="btn btn-secondary">
                                        <i class="la la-arrow-left"></i> Retour
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="la la-check-circle"></i> Attribuer l'Abonnement
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Historique -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Historique des Abonnements</h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($historique) > 0): ?>
                            <div class="timeline">
                                <?php foreach ($historique as $h): ?>
                                <div class="timeline-item mb-3 pb-3" style="border-bottom: 1px solid #e0e0e0;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge" style="background: <?= htmlspecialchars($h['couleur_plan']) ?>; color: white;">
                                            <?= htmlspecialchars($h['nom_plan']) ?>
                                        </span>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($h['created_at'])) ?></small>
                                    </div>
                                    <p class="mb-1" style="font-size: 13px;">
                                        Du <?= date('d/m/Y', strtotime($h['date_debut'])) ?> au <?= date('d/m/Y', strtotime($h['date_fin'])) ?>
                                    </p>
                                    <p class="mb-1" style="font-size: 13px;">
                                        <strong><?= number_format($h['prix_paye'], 0, ',', ' ') ?> FCFA</strong>
                                        <?php if ($h['mode_paiement'] !== 'Non spécifié'): ?>
                                            (<?= htmlspecialchars($h['mode_paiement']) ?>)
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($h['statut'] === 'actif'): ?>
                                        <span class="badge bg-inverse-success">Actif</span>
                                    <?php elseif ($h['statut'] === 'expire'): ?>
                                        <span class="badge bg-inverse-danger">Expiré</span>
                                    <?php else: ?>
                                        <span class="badge bg-inverse-secondary">Annulé</span>
                                    <?php endif; ?>
                                    <?php if ($h['created_by_nom']): ?>
                                        <p class="mb-0 mt-1" style="font-size: 11px; color: #999;">Par : <?= htmlspecialchars($h['created_by_nom']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">Aucun historique</p>
                        <?php endif; ?>
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

    <script>
    function selectPlan(idPlan, nbBoutiques, prix, nomPlan) {
        // Désélectionner tous
        $('.plan-option').removeClass('active').css('box-shadow', 'none');
        $('input[name="id_plan"]').prop('checked', false);

        // Sélectionner celui-ci
        $('#plan_' + idPlan).prop('checked', true);
        $('#plan_' + idPlan).closest('.plan-option').addClass('active').css('box-shadow', '0 5px 20px rgba(0,0,0,0.15)');

        // Mettre à jour le prix
        $('#prix_paye').val(prix);

        // Alerte si nombre boutiques dépasse
        var nbBoutiquesActuelles = <?= $nb_boutiques ?>;
        if (nbBoutiquesActuelles > nbBoutiques) {
            alert('⚠️ Attention : Cette société a déjà ' + nbBoutiquesActuelles + ' boutique(s). Le plan ' + nomPlan + ' est limité à ' + nbBoutiques + ' boutique(s). Nouvelles créations seront bloquées.');
        }
    }

    // Activer visuellement le plan sélectionné au chargement
    $(document).ready(function() {
        var selectedPlan = $('input[name="id_plan"]:checked');
        if (selectedPlan.length) {
            selectedPlan.closest('.plan-option').css('box-shadow', '0 5px 20px rgba(0,0,0,0.15)');
        }
    });
    </script>
</body>
</html>
