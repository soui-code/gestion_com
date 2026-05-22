<?php
class dashboardmodel
{
    public $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function getStats()
    {
        return [
            'produits' => $this->con->query("SELECT COUNT(*) FROM produits WHERE etat = 'actif'")->fetchColumn(),
            'clients' => $this->con->query("SELECT COUNT(*) FROM clients WHERE etat_client = 'actif'")->fetchColumn(),
            'boutiques' => $this->con->query("SELECT COUNT(*) FROM boutiques WHERE etat_boutique = 'active'")->fetchColumn(),
            'ventes' => $this->con->query("SELECT COUNT(*) FROM commandes WHERE statut_commande = 'Vente' AND etat_commande = 'validée'")->fetchColumn(),
            'ca_total' => number_format($this->con->query("SELECT COALESCE(SUM(montant_commande),0) FROM commandes WHERE statut_commande = 'Vente' AND etat_commande = 'validée'")->fetchColumn(), 0, ',', ' '),
            'ca_mois' => number_format($this->con->query("SELECT COALESCE(SUM(montant_commande),0) FROM commandes WHERE statut_commande = 'Vente' AND etat_commande = 'validée' AND MONTH(date_commande) = MONTH(CURDATE()) AND YEAR(date_commande) = YEAR(CURDATE())")->fetchColumn(), 0, ',', ' '),
            'ventes_recentes' => $this->con->query("SELECT c.numero_commande, c.date_commande, cl.nom_prenom_client, c.montant_commande FROM commandes c LEFT JOIN clients cl ON c.code_client = cl.code_client WHERE c.statut_commande = 'Vente' AND c.etat_commande = 'validée' ORDER BY c.date_commande DESC, c.heure_commande DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
            'produits_alerte' => $this->con->query("SELECT designation, stock, stock_alert FROM produits WHERE stock <= stock_alert AND etat = 'actif' ORDER BY stock ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
            'valeur_stock' => number_format($this->con->query("SELECT COALESCE(SUM(prix_achat * stock), 0) FROM produits WHERE etat = 'actif' AND stock > 0")->fetchColumn(), 0, ',', ' '),
            'mouvements_recents' => $this->con->query("SELECT m.type_mouvement, m.quantite, m.date_mouvement, p.designation FROM mouvements_stock m JOIN produits p ON m.reference_produit = p.reference ORDER BY m.date_mouvement DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
}