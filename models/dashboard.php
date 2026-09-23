<?php
class dashboardmodel
{
    public $con;
    public $id_societe;
    public $id_boutique;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function getStats()
    {
        try {
            $sql_produits = "SELECT COUNT(*) FROM produits WHERE etat = 'actif'";
            $sql_clients = "SELECT COUNT(*) FROM clients WHERE etat_client = 'actif'";
            $sql_ventes = "SELECT COUNT(*) FROM commandes c WHERE c.statut_commande = 'Vente' AND c.etat_commande = 'validée'";
            $sql_ca_total = "SELECT COALESCE(SUM(c.montant_commande),0) FROM commandes c WHERE c.statut_commande = 'Vente' AND c.etat_commande = 'validée'";
            $sql_ca_mois = "SELECT COALESCE(SUM(c.montant_commande),0) FROM commandes c WHERE c.statut_commande = 'Vente' AND c.etat_commande = 'validée' AND MONTH(c.date_commande) = MONTH(CURDATE()) AND YEAR(c.date_commande) = YEAR(CURDATE())";
            $sql_ca_jour = "SELECT COALESCE(SUM(c.montant_commande),0) FROM commandes c WHERE c.statut_commande = 'Vente' AND c.etat_commande = 'validée' AND DATE(c.date_commande) = CURDATE()";
            $sql_ventes_recentes = "SELECT c.numero_commande, c.date_commande, cl.nom_prenom_client, c.montant_commande FROM commandes c LEFT JOIN clients cl ON c.code_client = cl.code_client WHERE c.statut_commande = 'Vente' AND c.etat_commande = 'validée'";
            $sql_produits_alerte = "SELECT designation, stock, stock_alert FROM produits WHERE stock <= stock_alert AND etat = 'actif'";
            $sql_valeur_stock = "SELECT COALESCE(SUM(prix_achat * stock), 0) FROM produits WHERE etat = 'actif' AND stock > 0";
            $sql_mouvements = "SELECT m.type_mouvement, m.quantite, m.date_mouvement, p.designation FROM mouvements_stock m JOIN produits p ON m.reference_produit = p.reference";
            $sql_boutiques = "SELECT COUNT(*) FROM boutiques b WHERE b.etat_boutique = 'active'";

        if ($this->id_societe) {
            $clause = " AND id_societe = " . (int)$this->id_societe;
            $clause_c = " AND c.id_societe = " . (int)$this->id_societe;
            $clause_m = " AND m.id_societe = " . (int)$this->id_societe;
            $clause_b = " AND b.id_societe = " . (int)$this->id_societe;
            $sql_produits .= $clause;
            $sql_clients .= $clause;
            $sql_ventes .= $clause_c;
            $sql_ca_total .= $clause_c;
            $sql_ca_mois .= $clause_c;
            $sql_ca_jour .= $clause_c;
            $sql_ventes_recentes .= $clause_c;
            $sql_produits_alerte .= $clause;
            $sql_valeur_stock .= $clause;
            $sql_mouvements .= $clause_m;
            $sql_boutiques .= $clause_b;
        }

        if ($this->id_boutique) {
            $clause = " AND id_boutique = " . (int)$this->id_boutique;
            $clause_c = " AND c.id_boutique = " . (int)$this->id_boutique;
            $clause_m = " AND m.id_boutique = " . (int)$this->id_boutique;
            $sql_produits .= $clause;
            $sql_clients .= $clause;
            $sql_ventes .= $clause_c;
            $sql_ca_total .= $clause_c;
            $sql_ca_mois .= $clause_c;
            $sql_ca_jour .= $clause_c;
            $sql_ventes_recentes .= $clause_c;
            $sql_produits_alerte .= $clause;
            $sql_valeur_stock .= $clause;
            $sql_mouvements .= $clause_m;
        }

            // Récupérer les mouvements avec gestion d'erreur
            $mouvements_recents = [];
            try {
                $mouvements_recents = $this->con->query($sql_mouvements . " ORDER BY m.date_mouvement DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Si la table mouvements_stock n'a pas les colonnes nécessaires, on retourne un tableau vide
                Logger::warning('Erreur lors de la récupération des mouvements récents', [
                    'error' => $e->getMessage()
                ]);
            }

            return [
                'produits' => $this->con->query($sql_produits)->fetchColumn(),
                'clients' => $this->con->query($sql_clients)->fetchColumn(),
                'boutiques' => $this->con->query($sql_boutiques)->fetchColumn(),
                'ventes' => $this->con->query($sql_ventes)->fetchColumn(),
                'ca_total' => number_format($this->con->query($sql_ca_total)->fetchColumn(), 0, ',', ' '),
                'ca_mois' => number_format($this->con->query($sql_ca_mois)->fetchColumn(), 0, ',', ' '),
                'ca_jour' => number_format($this->con->query($sql_ca_jour)->fetchColumn(), 0, ',', ' '),
                'ventes_recentes' => $this->con->query($sql_ventes_recentes . " ORDER BY c.date_commande DESC, c.heure_commande DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
                'produits_alerte' => $this->con->query($sql_produits_alerte . " ORDER BY stock ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
                'valeur_stock' => number_format($this->con->query($sql_valeur_stock)->fetchColumn(), 0, ',', ' '),
                'mouvements_recents' => $mouvements_recents
            ];
        } catch (PDOException $e) {
            // Si des colonnes manquent dans d'autres tables, on retourne des données par défaut
            Logger::error('Erreur critique lors de la récupération des stats du dashboard', [
                'error' => $e->getMessage()
            ]);

            return [
                'produits' => 0,
                'clients' => 0,
                'boutiques' => 0,
                'ventes' => 0,
                'ca_total' => '0',
                'ca_mois' => '0',
                'ca_jour' => '0',
                'ventes_recentes' => [],
                'produits_alerte' => [],
                'valeur_stock' => '0',
                'mouvements_recents' => []
            ];
        }
    }
}