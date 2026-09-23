<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement Expiré</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .expire-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 90%;
            padding: 50px 40px;
            text-align: center;
            animation: slideDown 0.5s ease;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .expire-icon {
            font-size: 100px;
            color: #e74c3c;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .expire-title {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .expire-message {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .expire-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #e74c3c;
        }
        .expire-details p {
            margin: 8px 0;
            font-size: 14px;
            color: #34495e;
        }
        .expire-details strong {
            color: #2c3e50;
        }
        .btn-retour {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-retour:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .contact-info {
            margin-top: 25px;
            padding: 15px;
            background: #fff3cd;
            border-radius: 10px;
            border-left: 5px solid #ffc107;
        }
        .contact-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #856404;
        }
        .contact-info a {
            color: #856404;
            font-weight: 600;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="expire-container">
        <div class="expire-icon">
            <i class="la la-exclamation-triangle"></i>
        </div>

        <h1 class="expire-title">Abonnement Expiré</h1>

        <p class="expire-message">
            Votre accès à l'application est temporairement suspendu car l'abonnement de votre société a expiré.
        </p>

        <?php if (isset($societe) && $societe): ?>
        <div class="expire-details">
            <p><strong>Société :</strong> <?= htmlspecialchars($societe['nom_societe']) ?></p>
            <?php if ($societe['abonnement_expire_le']): ?>
                <p><strong>Date d'expiration :</strong> <?= date('d/m/Y', strtotime($societe['abonnement_expire_le'])) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="contact-info">
            <p><i class="la la-phone"></i> <strong>Pour renouveler, contactez l'administrateur :</strong></p>
            <p><a href="tel:+22507000000">+225 07 00 00 00 00</a></p>
            <p style="margin-top: 10px; font-size: 12px;">
                L'administrateur pourra renouveler votre abonnement et réactiver votre accès immédiatement.
            </p>
        </div>

        <a href="<?= BASE_URL ?>/utilisateur/connexion" class="btn-retour">
            <i class="la la-arrow-left"></i> Retour à la connexion
        </a>
    </div>
</body>
</html>
