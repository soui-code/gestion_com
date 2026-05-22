<?php
require_once './database/database.php';

$sql = "
    SELECT 
        COALESCE(t.mode_reglement, 'Non spécifié') AS mode,
        COUNT(*) AS nombre_transactions,
        COALESCE(SUM(t.montant_transaction), 0) AS total_montant
    FROM transactions t
    WHERE EXISTS (
        SELECT 1 FROM commandes c 
        WHERE c.numero_facture = t.numero_facture
          AND c.statut_commande = 'Vente'
          AND c.etat_commande = 'validée'
    )
    AND t.etat_transaction = 'validée'
    GROUP BY t.mode_reglement
    ORDER BY total_montant DESC
";
$pdo = getDatabaseConnection();
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($results)) {
    $sql = "
        SELECT 
            COALESCE(mode_reglement, 'Non spécifié') AS mode,
            COUNT(*) AS nombre,
            COALESCE(SUM(montant_transaction), 0) AS total_montant
        FROM transactions 
        WHERE etat_transaction = 'validée'
        GROUP BY mode_reglement
        ORDER BY total_montant DESC
    ";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$modes = [];
$montants = [];
$couleurs = ['#28a745', '#007bff', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#17a2b8'];

foreach ($results as $i => $row) {
    $label = $row['mode'];
    switch ($label) {
        case 'Espèces': $label = 'Espèces'; break;
        case 'Mobile Money': $label = 'Mobile Money'; break;
        case 'Carte bancaire': $label = 'Carte Bancaire'; break;
        case 'Virement': $label = 'Virement'; break;
        case 'Chèque': $label = 'Chèque'; break;
        default: $label = 'Autres'; break;
    }
    $modes[] = $label;
    $montants[] = (float)$row['total_montant'];
}

header('Content-Type: application/json');
echo json_encode([
    'modes' => $modes,
    'montants' => $montants,
    'couleurs' => array_slice($couleurs, 0, count($modes))
]);