<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration API DGI e-MECeF</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .step-card {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        .step-number {
            background: #0066cc;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            margin-right: 15px;
        }
    </style>
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
                                <i class="la la-shield-alt"></i> Configuration API DGI e-MECeF
                            </h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= url('/societe/parametres') ?>">Paramètres</a></li>
                                <li class="breadcrumb-item active">Configuration DGI</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-12">

                <!-- Avertissement important -->
                <div class="warning-box">
                    <h5><i class="la la-exclamation-triangle"></i> Important</h5>
                    <p><strong>Chaque société DOIT s'enregistrer auprès de la DGI pour obtenir SES propres credentials.</strong></p>
                    <p>Ces credentials sont <strong>uniques à votre entreprise</strong> et ne peuvent pas être partagés.</p>
                </div>

                <!-- Explication du processus -->
                <div class="info-box">
                    <h5><i class="la la-info-circle"></i> Qu'est-ce que e-MECeF ?</h5>
                    <p>
                        <strong>e-MECeF</strong> (Mécanisme Électronique de Contrôle et d'Enregistrement des Factures)
                        est le système officiel de la DGI de Côte d'Ivoire pour certifier les factures.
                    </p>
                    <p>
                        Une fois configuré, chaque facture sera <strong>automatiquement certifiée</strong> par la DGI
                        et recevra un cachet fiscal électronique.
                    </p>
                </div>

                <!-- Étapes pour obtenir les credentials -->
                <h4 class="mt-5 mb-3">📋 Comment obtenir vos credentials DGI ?</h4>

                <div class="step-card">
                    <div class="d-flex align-items-start">
                        <span class="step-number">1</span>
                        <div>
                            <h5>S'enregistrer sur le portail e-MECeF</h5>
                            <p>Visitez : <a href="https://emecef.ci" target="_blank">https://emecef.ci</a></p>
                            <p>Cliquez sur "Inscription" et remplissez le formulaire avec les informations de votre entreprise.</p>
                        </div>
                    </div>
                </div>

                <div class="step-card">
                    <div class="d-flex align-items-start">
                        <span class="step-number">2</span>
                        <div>
                            <h5>Fournir les documents requis</h5>
                            <ul>
                                <li>Registre de Commerce et du Crédit Mobilier (RCCM)</li>
                                <li>Compte Contribuable</li>
                                <li>Attestation d'immatriculation DGI</li>
                                <li>Pièce d'identité du dirigeant</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="step-card">
                    <div class="d-flex align-items-start">
                        <span class="step-number">3</span>
                        <div>
                            <h5>Passer l'audit technique</h5>
                            <p>La DGI vérifiera que votre logiciel (ce système) est conforme aux normes FNE.</p>
                            <p>Vous devrez fournir des captures d'écran de factures générées en mode simulation.</p>
                        </div>
                    </div>
                </div>

                <div class="step-card">
                    <div class="d-flex align-items-start">
                        <span class="step-number">4</span>
                        <div>
                            <h5>Recevoir vos credentials</h5>
                            <p>Une fois approuvé, la DGI vous enverra :</p>
                            <ul>
                                <li><strong>IFU</strong> : Identifiant Fiscal Unique</li>
                                <li><strong>NIM</strong> : Numéro MECeF</li>
                                <li><strong>API Key</strong> : Clé d'authentification</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="step-card">
                    <div class="d-flex align-items-start">
                        <span class="step-number">5</span>
                        <div>
                            <h5>Configurer dans ce système</h5>
                            <p>Entrez vos credentials dans le formulaire ci-dessous.</p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de configuration -->
                <div class="card mt-5">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="la la-cog"></i> Configuration de vos credentials DGI</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($data['success'])): ?>
                            <div class="alert alert-success">
                                <i class="la la-check-circle"></i> <?= $data['success'] ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($data['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="la la-times-circle"></i> <?= $data['error'] ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= url('/societe/sauvegarder_config_dgi') ?>">
                            <?= $csrf_field ?? '' ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <strong>IFU (Identifiant Fiscal Unique)</strong>
                                            <i class="la la-question-circle" title="Fourni par la DGI"></i>
                                        </label>
                                        <input type="text"
                                               name="dgi_ifu"
                                               class="form-control"
                                               placeholder="Ex: CI0123456789"
                                               value="<?= htmlspecialchars($data['config']['dgi_ifu'] ?? '') ?>">
                                        <small class="text-muted">Format : CI suivi de 10 chiffres</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <strong>NIM (Numéro MECeF)</strong>
                                            <i class="la la-question-circle" title="Numéro d'identification MECeF"></i>
                                        </label>
                                        <input type="text"
                                               name="dgi_nim"
                                               class="form-control"
                                               placeholder="Ex: NIM123456"
                                               value="<?= htmlspecialchars($data['config']['dgi_nim'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <strong>Clé API (API Key)</strong>
                                    <i class="la la-question-circle" title="Clé secrète fournie par la DGI"></i>
                                </label>
                                <input type="password"
                                       name="dgi_api_key"
                                       class="form-control"
                                       placeholder="••••••••••••••••••••"
                                       value="<?= htmlspecialchars($data['config']['dgi_api_key'] ?? '') ?>">
                                <small class="text-muted">⚠️ Ne partagez JAMAIS cette clé</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Mode</strong></label>
                                        <select name="dgi_mode" class="form-select">
                                            <option value="test" <?= ($data['config']['dgi_mode'] ?? 'test') === 'test' ? 'selected' : '' ?>>
                                                🧪 Test (api-test.emecef.ci)
                                            </option>
                                            <option value="production" <?= ($data['config']['dgi_mode'] ?? '') === 'production' ? 'selected' : '' ?>>
                                                🚀 Production (api.emecef.ci)
                                            </option>
                                        </select>
                                        <small class="text-muted">Commencez en mode TEST</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Activer la certification DGI</strong></label>
                                        <div class="form-check form-switch" style="padding-top: 10px;">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="dgi_actif"
                                                   value="1"
                                                   <?= ($data['config']['dgi_actif'] ?? false) ? 'checked' : '' ?>>
                                            <label class="form-check-label">
                                                Activer la certification automatique
                                            </label>
                                        </div>
                                        <small class="text-muted">Si désactivé : mode simulation uniquement</small>
                                    </div>
                                </div>
                            </div>

                            <div class="success-box">
                                <h6><i class="la la-lightbulb"></i> Conseil</h6>
                                <p>
                                    Si vous n'avez pas encore vos credentials DGI, laissez ces champs vides.
                                    Le système fonctionnera en <strong>mode simulation</strong> en attendant.
                                </p>
                            </div>

                            <div class="text-end mt-4">
                                <a href="<?= url('/societe/parametres') ?>" class="btn btn-secondary">
                                    <i class="la la-arrow-left"></i> Retour
                                </a>
                                <button type="submit" name="btn-sauvegarder-dgi" class="btn btn-primary">
                                    <i class="la la-save"></i> Enregistrer la configuration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contact DGI -->
                <div class="card mt-4 mb-5">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="la la-phone"></i> Besoin d'aide ?</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Direction Générale des Impôts - Service e-MECeF</strong></p>
                        <ul>
                            <li>📧 Email : <a href="mailto:emecef@dgi.gouv.ci">emecef@dgi.gouv.ci</a></li>
                            <li>🌐 Site web : <a href="https://emecef.ci" target="_blank">https://emecef.ci</a></li>
                            <li>📞 Téléphone : (+225) 27 20 33 86 00</li>
                            <li>📍 Adresse : Abidjan - Plateau, Immeuble SCIAM</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>
