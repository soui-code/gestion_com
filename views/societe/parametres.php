<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres de la Société - FNE</title>
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

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Paramètres de la Société (FNE)</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Paramètres FNE</li>
                            </ul>
                        </div>
                        <div class="col-auto">
                            <a href="<?= url('/societe/configuration_dgi') ?>" class="btn btn-primary">
                                <i class="la la-shield-alt"></i> Configuration API DGI
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Formulaire -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fa fa-building"></i> Informations pour Facture Normalisée Électronique
                                </h4>
                                <p class="text-muted small mt-2">Ces informations sont obligatoires pour générer des factures conformes à la DGI de Côte d'Ivoire</p>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <?= $csrf_field ?? '' ?>

                                    <!-- Informations Générales -->
                                    <h5 class="mb-3 text-primary"><i class="fa fa-info-circle"></i> Informations Générales</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nom de la Société <span class="text-danger">*</span></label>
                                                <input type="text" name="nom_societe" class="form-control"
                                                       value="<?= htmlspecialchars($societe['nom_societe'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Service des Impôts</label>
                                                <input type="text" name="service_impots" class="form-control"
                                                       value="<?= htmlspecialchars($societe['service_impots'] ?? '') ?>"
                                                       placeholder="Ex: Bouaké, Abidjan-Plateau">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Adresse Complète <span class="text-danger">*</span></label>
                                        <textarea name="adresse_complete" class="form-control" rows="2" required
                                                  placeholder="Ex: 01 BP 1234, Bouaké, Côte d'Ivoire"><?= htmlspecialchars($societe['adresse_complete'] ?? '') ?></textarea>
                                    </div>

                                    <!-- Informations Fiscales -->
                                    <hr>
                                    <h5 class="mb-3 text-primary"><i class="fa fa-file-text"></i> Informations Fiscales</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Compte Contribuable <span class="text-danger">*</span></label>
                                                <input type="text" name="compte_contribuable" class="form-control"
                                                       value="<?= htmlspecialchars($societe['compte_contribuable'] ?? '') ?>"
                                                       placeholder="Ex: CI123456789" required>
                                                <small class="form-text text-muted">Format: CI + numéro unique</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Numéro RCC</label>
                                                <input type="text" name="numero_rcc" class="form-control"
                                                       value="<?= htmlspecialchars($societe['numero_rcc'] ?? '') ?>"
                                                       placeholder="Ex: A1234567">
                                                <small class="form-text text-muted">Registre du Commerce et du Crédit Mobilier</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Régime d'Imposition <span class="text-danger">*</span></label>
                                        <select name="regime_imposition" class="form-control" required>
                                            <option value="Réel Normal" <?= ($societe['regime_imposition'] ?? '') === 'Réel Normal' ? 'selected' : '' ?>>Réel Normal</option>
                                            <option value="Réel Simplifié" <?= ($societe['regime_imposition'] ?? '') === 'Réel Simplifié' ? 'selected' : '' ?>>Réel Simplifié</option>
                                            <option value="Micro-entreprise" <?= ($societe['regime_imposition'] ?? '') === 'Micro-entreprise' ? 'selected' : '' ?>>Micro-entreprise</option>
                                        </select>
                                    </div>

                                    <!-- Informations Bancaires -->
                                    <hr>
                                    <h5 class="mb-3 text-primary"><i class="fa fa-bank"></i> Informations Bancaires</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nom de la Banque</label>
                                                <input type="text" name="nom_banque" class="form-control"
                                                       value="<?= htmlspecialchars($societe['nom_banque'] ?? '') ?>"
                                                       placeholder="Ex: BACI, SGCI, BOA">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Code Guichet</label>
                                                <input type="text" name="code_guichet" class="form-control"
                                                       value="<?= htmlspecialchars($societe['code_guichet'] ?? '') ?>"
                                                       placeholder="Ex: 0123" maxlength="10">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Clé RIB</label>
                                                <input type="text" name="cle_rib" class="form-control"
                                                       value="<?= htmlspecialchars($societe['cle_rib'] ?? '') ?>"
                                                       placeholder="Ex: 56" maxlength="5">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Numéro de Compte</label>
                                                <input type="text" name="numero_compte" class="form-control"
                                                       value="<?= htmlspecialchars($societe['numero_compte'] ?? '') ?>"
                                                       placeholder="Ex: 12345678901234">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Code Swift/BIC</label>
                                                <input type="text" name="code_swift" class="form-control"
                                                       value="<?= htmlspecialchars($societe['code_swift'] ?? '') ?>"
                                                       placeholder="Ex: BACICI">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RIB Complet (Calculé) -->
                                    <?php if (!empty($societe['code_guichet']) && !empty($societe['numero_compte']) && !empty($societe['cle_rib'])): ?>
                                    <div class="alert alert-info">
                                        <strong><i class="fa fa-info-circle"></i> RIB Complet :</strong>
                                        CI<?= htmlspecialchars($societe['code_guichet']) ?>-<?= htmlspecialchars($societe['numero_compte']) ?>-<?= htmlspecialchars($societe['cle_rib']) ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="text-right mt-4">
                                        <button type="submit" name="btn-update-societe" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i> Enregistrer les Paramètres
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
