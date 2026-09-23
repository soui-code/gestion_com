<?php
// Récupération de l'utilisateur connecté
$id_societe = $_SESSION['id_societe'] ?? null;
$id_boutique = $_SESSION['id_boutique'] ?? null;
$role = $_SESSION['role'] ?? 'caissier';

$annee = date('Y');

// Construction de la requête avec filtrage
$sql = "
    SELECT
        MONTH(date_commande) AS mois_num,
        statut_commande,
        COALESCE(SUM(montant_commande), 0) AS total
    FROM commandes
    WHERE YEAR(date_commande) = :annee
      AND etat_commande = 'validée'
      AND statut_commande IN ('Vente', 'Approvisionnement', 'Achat')
";

// Filtrage par société/boutique (sauf pour admin)
if ($role !== 'admin' && $id_societe) {
    $sql .= " AND id_societe = :id_societe";
}

if ($role === 'caissier' && $id_boutique) {
    $sql .= " AND id_boutique = :id_boutique";
}

$sql .= "
    GROUP BY MONTH(date_commande), statut_commande
    ORDER BY mois_num, statut_commande
";

$pdo = getDatabaseConnection();
$stmt = $pdo->prepare($sql);

// Bind des paramètres
$stmt->bindParam(':annee', $annee, PDO::PARAM_INT);

if ($role !== 'admin' && $id_societe) {
    $stmt->bindParam(':id_societe', $id_societe, PDO::PARAM_INT);
}

if ($role === 'caissier' && $id_boutique) {
    $stmt->bindParam(':id_boutique', $id_boutique, PDO::PARAM_INT);
}

$stmt->execute();
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