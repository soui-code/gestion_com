<?php
// Récupération de l'utilisateur connecté
$id_societe = $_SESSION['id_societe'] ?? null;
$id_boutique = $_SESSION['id_boutique'] ?? null;
$role = $_SESSION['role'] ?? 'caissier';

// Construction de la requête avec filtrage
$sql = "
    SELECT
        COALESCE(t.mode_reglement, 'Non spécifié') AS mode,
        COUNT(*) AS nombre_transactions,
        COALESCE(SUM(t.montant_transaction), 0) AS total_montant
    FROM transactions t
    WHERE t.etat_transaction = 'validée'
";

// Filtrage par société/boutique (sauf pour admin)
if ($role !== 'admin' && $id_societe) {
    $sql .= " AND t.id_societe = :id_societe";
}

if ($role === 'caissier' && $id_boutique) {
    $sql .= " AND t.id_boutique = :id_boutique";
}

$sql .= "
    GROUP BY t.mode_reglement
    ORDER BY total_montant DESC
";

$pdo = getDatabaseConnection();
$stmt = $pdo->prepare($sql);

// Bind des paramètres
if ($role !== 'admin' && $id_societe) {
    $stmt->bindParam(':id_societe', $id_societe, PDO::PARAM_INT);
}

if ($role === 'caissier' && $id_boutique) {
    $stmt->bindParam(':id_boutique', $id_boutique, PDO::PARAM_INT);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si aucun résultat, retourner des données vides
if (empty($results)) {
    header('Content-Type: application/json');
    echo json_encode([
        'modes' => [],
        'montants' => [],
        'couleurs' => []
    ]);
    exit;
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