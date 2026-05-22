<?php
// session_start();
require_once './database/database.php'; 

$annee = date('Y');

$sql = "
    SELECT 
        MONTH(date_commande) AS mois_num,
        statut_commande,
        COALESCE(SUM(montant_commande), 0) AS total
    FROM commandes 
    WHERE YEAR(date_commande) = ? 
      AND etat_commande = 'validée'
      AND statut_commande IN ('Vente', 'Approvisionnement', 'Achat')
    GROUP BY MONTH(date_commande), statut_commande
    ORDER BY mois_num, statut_commande
";

$pdo = getDatabaseConnection();
$stmt = $pdo->prepare($sql);
$stmt->execute([$annee]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialisation des tableaux à zéro pour les 12 mois
$ventes = $appro = $achats = array_fill(0, 12, 0);

foreach ($results as $row) {
    $index = $row['mois_num'] - 1; // Janvier = 0
    $total = (float)$row['total'];

    if ($row['statut_commande'] === 'Vente') {
        $ventes[$index] = $total;
    } elseif ($row['statut_commande'] === 'Approvisionnement') {
        $appro[$index] = $total;
    } elseif ($row['statut_commande'] === 'Achat') {
        $achats[$index] = $total;
    }
}

// Mois en français court
$mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

header('Content-Type: application/json');
echo json_encode([
    'mois'               => $mois,
    'ventes'             => array_values($ventes),
    'approvisionnements' => array_values($appro),
    'achats'             => array_values($achats)
]);