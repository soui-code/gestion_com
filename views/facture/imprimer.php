<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= "FACT-" . date('Ymd', strtotime($facture['date_facture'])) . "-" . str_pad($facture['numero_facture'], 4, '0', STR_PAD_LEFT) ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            background: #fff;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(n+3) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 35px;
            line-height: 45px;
            color: #333;
            font-weight: bold;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #f8f9fa;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .totals-section {
            width: 50%;
            float: right;
            margin-top: 20px;
        }
        .totals-section table {
            width: 100%;
        }
        .totals-section table tr td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
        }
        .totals-section table tr:last-child td {
            border-bottom: none;
            font-size: 1.2em;
            font-weight: bold;
            color: #000;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .invoice-box {
                box-shadow: none;
                border: none;
                max-width: 100%;
                padding: 0;
            }
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .badge-status {
            display: inline-block;
            padding: 5px 10px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 0.9em;
            text-transform: uppercase;
            font-weight: bold;
        }
        .status-payee { background: #d4edda; color: #155724; }
        .status-impayee { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="5">
                    <table>
                        <tr>
                            <td class="title">
                                <!-- Logo Placeholder -->
                                <div style="font-size:24px; text-transform:uppercase; color:#2c3e50; letter-spacing: 2px;">MANDIGO</div>
                                <div style="font-size:12px; font-weight:normal; color:#7f8c8d;">GESTION COMMERCIALE</div>
                            </td>
                            <td class="text-right">
                                <strong>FACTURE N°: <?= htmlspecialchars($facture['numero_facture']) ?></strong><br>
                                Date: <?= date('d/m/Y', strtotime($facture['date_facture'])) ?><br>
                                <?php
                                $statusClass = (strtolower($facture['etat_facture']) === 'payée' || strtolower($facture['etat_facture']) === 'payee') ? 'status-payee' : 'status-impayee';
                                ?>
                                Statut: <span class="badge-status <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($facture['etat_facture'])) ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="5">
                    <table>
                        <tr>
                            <td>
                                <strong>ÉMETTEUR</strong><br>
                                MANDIGO<br>
                                123 Rue du Commerce<br>
                                Abidjan, Côte d'Ivoire<br>
                                Tél: +225 01 23 45 67 89<br>
                                Email: contact@mandigo.ci
                            </td>
                            <td class="text-right">
                                <strong>CLIENT FACTURÉ</strong><br>
                                <?= htmlspecialchars($facture['nom_client'] ?: 'Client Particulier') ?><br>
                                <?php if($facture['adresse_client']): ?>
                                    <?= nl2br(htmlspecialchars($facture['adresse_client'])) ?><br>
                                <?php endif; ?>
                                <?php if($facture['telephone_client']): ?>
                                    Tél: <?= htmlspecialchars($facture['telephone_client']) ?><br>
                                <?php endif; ?>
                                <?php if($facture['email_client']): ?>
                                    Email: <?= htmlspecialchars($facture['email_client']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td style="width:15%">Référence</td>
                <td style="width:40%">Désignation</td>
                <td class="text-center" style="width:15%">Qté</td>
                <td class="text-right" style="width:15%">Prix U.</td>
                <td class="text-right" style="width:15%">Montant</td>
            </tr>

            <?php 
            $calcul_total_ht = 0;
            if (!empty($items)):
                foreach ($items as $index => $item): 
                    $designation = $item['designation'] ?: 'Produit ref: ' . $item['reference'];
                    $montantLigne = $item['quantite_commande'] * $item['prix_vente'];
                    $calcul_total_ht += $montantLigne;
                    $isLast = ($index === count($items) - 1) ? 'last' : '';
            ?>
                <tr class="item <?= $isLast ?>">
                    <td><?= htmlspecialchars($item['reference']) ?></td>
                    <td><?= htmlspecialchars($designation) ?></td>
                    <td class="text-center"><?= htmlspecialchars($item['quantite_commande']) ?></td>
                    <td class="text-right"><?= number_format($item['prix_vente'], 0, ',', ' ') ?> F</td>
                    <td class="text-right"><?= number_format($montantLigne, 0, ',', ' ') ?> F</td>
                </tr>
            <?php 
                endforeach; 
            else: 
                // Fallback pour les factures sans détails
                $calcul_total_ht = $facture['montant_ht'];
            ?>
                <tr class="item last">
                    <td>1</td>
                    <td><?= htmlspecialchars($facture['titre_facture']) ?></td>
                    <td class="text-center">1</td>
                    <td class="text-right"><?= number_format($facture['montant_ht'], 0, ',', ' ') ?> F</td>
                    <td class="text-right"><?= number_format($facture['montant_ht'], 0, ',', ' ') ?> F</td>
                </tr>
            <?php endif; ?>
        </table>
        
        <?php
        $montant_taxe = $facture['montant_ttc'] - $facture['montant_ht'];
        // Note: The database doesn't seem to store the discount (remise) separately in the invoice table, 
        // but we can calculate it if we had the original HT. For now, we'll show it as 0 or a placeholder.
        ?>

        <div class="clearfix">
            <div class="totals-section">
                <table>
                    <tr>
                        <td><strong>Total HT</strong></td>
                        <td class="text-right"><strong><?= number_format($facture['montant_ht'], 0, ',', ' ') ?> F</strong></td>
                    </tr>
                    <?php if ($montant_taxe != 0): ?>
                    <tr>
                        <td>
                            Taxe
                            <?php if($taxe): ?>
                                (<?= htmlspecialchars($taxe['titre_taxe']) ?> <?= floatval($taxe['taux_taxe']) ?>%)
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?= number_format($montant_taxe, 0, ',', ' ') ?> F</td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Net à payer</td>
                        <td class="text-right" style="font-size: 1.2em; color: #ff9b44; font-weight: bold;"><?= number_format($facture['montant_ttc'], 0, ',', ' ') ?> FCFA</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="margin-top: 50px; text-align: center; color: #7f8c8d; font-size: 13px; border-top: 1px solid #eee; padding-top: 20px;">
            <p>Merci pour votre confiance.</p>
            <p><strong>MANDIGO</strong> - NINEA: 1234567890 - RCCM: CI-ABJ-2023-B-12345</p>
            <p>Société au capital de 1 000 000 FCFA</p>
        </div>
    </div>
</body>
</html>
