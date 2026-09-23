<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture Normalisée Électronique - <?= htmlspecialchars($facture['numero_fiscal'] ?? 'N/A') ?></title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            padding: 20px;
            background: #fff;
        }
        .container { max-width: 800px; margin: 0 auto; }

        /* En-tête */
        .header {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        .header h1 {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }
        .numero-fiscal {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }

        /* Bloc émetteur/client */
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        .partie {
            flex: 1;
            border: 1px solid #333;
            padding: 10px;
            background: #fafafa;
        }
        .partie-titre {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 8px;
            text-decoration: underline;
            color: #000;
        }
        .partie-ligne {
            margin-bottom: 4px;
            font-size: 10pt;
        }
        .partie-ligne strong {
            display: inline-block;
            width: 150px;
        }

        /* Tableau articles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #333;
            color: #fff;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #000;
            font-size: 10pt;
        }
        table td {
            padding: 8px;
            border: 1px solid #999;
            font-size: 10pt;
        }
        table tbody tr:nth-child(even) {
            background: #f5f5f5;
        }
        table .align-right {
            text-align: right;
        }
        table .align-center {
            text-align: center;
        }

        /* Totaux */
        .totaux {
            float: right;
            width: 350px;
            margin-top: 10px;
        }
        .totaux-ligne {
            display: flex;
            justify-content: space-between;
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
        }
        .totaux-ligne.final {
            background: #333;
            color: #fff;
            font-weight: bold;
            font-size: 13pt;
            border: 2px solid #000;
        }
        .totaux-label {
            font-weight: bold;
        }

        /* Mentions obligatoires */
        .mentions {
            clear: both;
            margin-top: 30px;
            border: 1px solid #333;
            padding: 10px;
            background: #f9f9f9;
        }
        .mentions h3 {
            font-size: 11pt;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .mentions table {
            margin: 0;
        }
        .mentions td {
            padding: 5px;
            font-size: 10pt;
            border: none;
        }
        .mentions td:first-child {
            font-weight: bold;
            width: 200px;
        }

        /* Montant en lettres */
        .montant-lettres {
            margin-top: 15px;
            padding: 10px;
            background: #ffffcc;
            border: 1px solid #cc9900;
            font-weight: bold;
            font-size: 11pt;
            text-align: center;
        }

        /* QR Code et cachets */
        .certification {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px dashed #666;
            padding: 15px;
            background: #f0f0f0;
        }
        .qr-code img {
            width: 150px;
            height: 150px;
            border: 2px solid #000;
        }
        .cachets {
            text-align: right;
        }
        .cachet-item {
            margin-bottom: 8px;
            font-size: 10pt;
        }
        .cachet-item strong {
            color: #0066cc;
        }

        /* Print */
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .container { max-width: 100%; }

            /* Supprimer l'URL et autres infos du navigateur */
            @page {
                margin: 0;
                size: auto;
            }
        }

        /* Masquer l'URL en bas de page (pour tous les navigateurs) */
        @page {
            margin-top: 0;
            margin-bottom: 0;
        }

        /* Boutons */
        .action-buttons {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
        }
        .btn-print { background: #007bff; }
        .btn-close { background: #6c757d; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Boutons d'action -->
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn btn-print">🖨️ Imprimer</button>
            <button onclick="window.close()" class="btn btn-close">✖️ Fermer</button>
        </div>

        <!-- Message d'aide impression -->
        <div class="no-print" style="background:#fffbcc; padding:10px; text-align:center; margin-bottom:15px; border:1px solid #cc9900; border-radius:5px;">
            <small>
                💡 <strong>Astuce :</strong> Pour supprimer l'URL en bas de page,
                dans les options d'impression, <strong>décochez "En-têtes et pieds de page"</strong>
            </small>
        </div>

        <!-- En-tête FNE -->
        <div class="header">
            <h1>FACTURE NORMALISÉE ÉLECTRONIQUE (FNE)</h1>
            <div class="numero-fiscal">
                Numéro fiscal : <?= htmlspecialchars($facture['numero_fiscal'] ?? 'N/A') ?>
            </div>
            <div style="text-align:center; font-size:9pt; color:#666;">
                (Format : compte-contribuable + année + séquence)
            </div>
        </div>

        <!-- Émetteur et Client -->
        <div class="parties">
            <!-- ÉMETTEUR -->
            <div class="partie">
                <div class="partie-titre">ÉMETTEUR (Vendeur)</div>
                <div class="partie-ligne">
                    <strong>Raison sociale :</strong>
                    <?= htmlspecialchars($facture['nom_societe'] ?? 'N/A') ?>
                </div>
                <div class="partie-ligne">
                    <strong>Adresse :</strong>
                    <?= htmlspecialchars($facture['adresse_complete'] ?? 'N/A') ?>
                </div>
                <div class="partie-ligne">
                    <strong>N° RCC :</strong>
                    <?= htmlspecialchars($facture['numero_rcc'] ?? 'N/A') ?>
                </div>
                <div class="partie-ligne">
                    <strong>Compte-contribuable :</strong>
                    <?= htmlspecialchars($facture['compte_contribuable'] ?? 'N/A') ?>
                </div>
                <div class="partie-ligne">
                    <strong>Régime d'imposition :</strong>
                    <?= htmlspecialchars($facture['regime_imposition'] ?? 'N/A') ?>
                </div>
                <div class="partie-ligne">
                    <strong>Service des Impôts :</strong>
                    <?= htmlspecialchars($facture['service_impots'] ?? 'N/A') ?>
                </div>
                <?php if ($facture['nom_banque']): ?>
                <div class="partie-ligne">
                    <strong>Banque :</strong>
                    <?= htmlspecialchars($facture['nom_banque']) ?>
                </div>
                <div class="partie-ligne">
                    <strong>Guichet :</strong>
                    <?= htmlspecialchars($facture['code_guichet'] ?? '') ?>
                </div>
                <div class="partie-ligne">
                    <strong>N° Compte :</strong>
                    <?= htmlspecialchars($facture['numero_compte'] ?? '') ?>
                </div>
                <div class="partie-ligne">
                    <strong>RIB :</strong>
                    CI<?= htmlspecialchars($facture['code_guichet'] ?? '') ?>-<?= htmlspecialchars($facture['numero_compte'] ?? '') ?>-<?= htmlspecialchars($facture['cle_rib'] ?? '') ?>
                </div>
                <div class="partie-ligne">
                    <strong>Code Swift :</strong>
                    <?= htmlspecialchars($facture['code_swift'] ?? '') ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- CLIENT -->
            <div class="partie">
                <div class="partie-titre">CLIENT</div>
                <div class="partie-ligne">
                    <strong>Nom/Raison sociale :</strong>
                    <?php
                    if ($facture['type_client'] === 'Personne morale' && !empty($facture['raison_sociale'])) {
                        echo htmlspecialchars($facture['raison_sociale']);
                    } else {
                        echo htmlspecialchars($facture['nom_prenom_client'] ?? 'Client de passage');
                    }
                    ?>
                </div>
                <div class="partie-ligne">
                    <strong>Adresse :</strong>
                    <?= htmlspecialchars($facture['adresse_client'] ?? 'N/A') ?>
                </div>
                <?php if ($facture['type_client'] === 'Personne morale'): ?>
                <div class="partie-ligne">
                    <strong>Compte-contribuable :</strong>
                    <?= htmlspecialchars($facture['client_compte_contribuable'] ?? 'N/A') ?>
                </div>
                <div class="partie-ligne">
                    <strong>N° RCC :</strong>
                    <?= htmlspecialchars($facture['client_numero_rcc'] ?? 'N/A') ?>
                </div>
                <?php endif; ?>
                <div class="partie-ligne">
                    <strong>Téléphone :</strong>
                    <?= htmlspecialchars($facture['telephone_client'] ?? 'N/A') ?>
                </div>
                <div class="partie-ligne">
                    <strong>Email :</strong>
                    <?= htmlspecialchars($facture['email_client'] ?? 'N/A') ?>
                </div>
            </div>
        </div>

        <!-- Articles -->
        <h3 style="margin-bottom:10px; font-size:12pt;">DÉSIGNATION DES ARTICLE(S)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">N°</th>
                    <th>Désignation</th>
                    <th style="width:80px;" class="align-center">Quantité</th>
                    <th style="width:120px;" class="align-right">Prix Unitaire HT (FCFA)</th>
                    <th style="width:140px;" class="align-right">Total HT (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_general_ht = 0;
                foreach ($facture['details'] as $detail):
                    $total_general_ht += $detail['total_ht'];
                ?>
                <tr>
                    <td class="align-center"><?= $detail['numero'] ?></td>
                    <td><?= htmlspecialchars($detail['designation']) ?></td>
                    <td class="align-center"><?= number_format($detail['quantite'], 0, ',', ' ') ?></td>
                    <td class="align-right"><?= number_format($detail['prix_unitaire_ht'], 0, ',', ' ') ?></td>
                    <td class="align-right"><?= number_format($detail['total_ht'], 0, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totaux -->
        <div class="totaux">
            <div class="totaux-ligne">
                <span class="totaux-label">Total HT :</span>
                <span><?= number_format($facture['montant_ht'], 0, ',', ' ') ?> FCFA</span>
            </div>
            <div class="totaux-ligne">
                <span class="totaux-label">TVA (<?= round(($facture['montant_tva'] / $facture['montant_ht']) * 100) ?>%) :</span>
                <span><?= number_format($facture['montant_tva'], 0, ',', ' ') ?> FCFA</span>
            </div>
            <div class="totaux-ligne final">
                <span class="totaux-label">TOTAL TTC :</span>
                <span><?= number_format($facture['montant_ttc'], 0, ',', ' ') ?> FCFA</span>
            </div>
        </div>

        <!-- Montant en lettres -->
        <div class="montant-lettres">
            Arrêté à la somme de : <?= htmlspecialchars($facture['montant_en_lettres'] ?? montantEnLettres($facture['montant_ttc'])) ?>
        </div>

        <!-- Mentions obligatoires -->
        <div class="mentions">
            <h3>MENTIONS OBLIGATOIRES SUPPLÉMENTAIRES</h3>
            <table>
                <tr>
                    <td>Date et heure d'émission</td>
                    <td><?= date('d/m/Y à H:i', strtotime($facture['date_emission'])) ?></td>
                </tr>
                <tr>
                    <td>Mode de règlement</td>
                    <td><?= htmlspecialchars($facture['mode_paiement'] ?? 'Espèces') ?></td>
                </tr>
                <tr>
                    <td>Boutique</td>
                    <td><?= htmlspecialchars($facture['nom_boutique'] ?? 'N/A') ?></td>
                </tr>
            </table>
        </div>

        <!-- Certification DGI -->
        <div class="certification">
            <div class="qr-code">
                <?php if (!empty($facture['qr_code'])): ?>
                    <img src="<?= htmlspecialchars($facture['qr_code']) ?>" alt="QR Code FNE">
                <?php else: ?>
                    <div style="width:150px;height:150px;border:2px solid #000;display:flex;align-items:center;justify-content:center;background:#f0f0f0;">
                        <span style="font-size:10pt;text-align:center;">QR Code<br>à générer</span>
                    </div>
                <?php endif; ?>
                <div style="text-align:center; font-size:9pt; margin-top:5px;">
                    <?php if ($facture['cachet_fiscal']): ?>
                        ✅ Certifié DGI
                    <?php else: ?>
                        📋 Mode Simulation
                    <?php endif; ?>
                </div>
            </div>

            <div class="cachets">
                <?php if ($facture['cachet_fiscal']): ?>
                    <!-- Mode Certification DGI active -->
                    <div class="cachet-item">
                        <strong>Cachet fiscal :</strong>
                        ✅ <?= substr($facture['cachet_fiscal'], 0, 20) ?>...
                    </div>
                    <div class="cachet-item">
                        <strong>Signature :</strong>
                        ✅ Certifiée le <?= date('d/m/Y H:i', strtotime($facture['date_certification_dgi'])) ?>
                    </div>
                    <div class="cachet-item">
                        <strong>Statut DGI :</strong>
                        <span style="color:green;">✅ Validée</span>
                    </div>
                <?php else: ?>
                    <!-- Mode Simulation -->
                    <div class="cachet-item" style="background:#fffbcc; padding:8px; border-radius:4px;">
                        <strong>⚠️ Mode Simulation</strong><br>
                        <small style="color:#666;">
                            Facture en attente de certification DGI.<br>
                            Pour activer : Configurer API e-MECeF dans les paramètres.
                        </small>
                    </div>
                    <div class="cachet-item" style="margin-top:10px;">
                        <strong>Statut DGI :</strong>
                        <span style="color:orange;">⏳ <?= htmlspecialchars($facture['statut_dgi']) ?></span>
                    </div>
                <?php endif; ?>
                <div style="margin-top:15px; padding:10px; background:#fff; border:2px solid #0066cc; text-align:center;">
                    <strong style="color:#0066cc; font-size:14pt;">FNE</strong><br>
                    <span style="font-size:8pt;">Facture Normalisée<br>Côte d'Ivoire</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print après chargement
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
