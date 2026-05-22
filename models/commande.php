<?php
class commandemodel
{
    public $numero_commande, $date_commande, $heure_commande, $prix_achat, $prix_vente, $prix_commande, $quantite_commande, $montant_commande, $reference, $code_client, $numero_facture, $utilisateur_id, $statut_commande, $etat_commande, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutCommande()
    {
        $req = $this->con->prepare("INSERT INTO commandes VALUES (:numero_commande,:date_commande,:heure_commande,:prix_achat,:prix_vente,:prix_commande,:quantite_commande,:montant_commande,:reference,:code_client,:numero_facture,:utilisateur_id,:statut_commande,:etat_commande)");
        $req->bindParam(":numero_commande", $this->numero_commande);
        $req->bindParam(":date_commande", $this->date_commande);
        $req->bindParam(":heure_commande", $this->heure_commande);
        $req->bindParam(":prix_achat", $this->prix_achat);
        $req->bindParam(":prix_vente", $this->prix_vente);
        $req->bindParam(":prix_commande", $this->prix_commande);
        $req->bindParam(":quantite_commande", $this->quantite_commande);
        $req->bindParam(":montant_commande", $this->montant_commande);
        $req->bindParam(":reference", $this->reference);
        $req->bindParam(":code_client", $this->code_client);
        $req->bindParam(":numero_facture", $this->numero_facture);
        $req->bindParam(":utilisateur_id", $this->utilisateur_id);
        $req->bindParam(":statut_commande", $this->statut_commande);
        $req->bindParam(":etat_commande", $this->etat_commande);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de la commande : " . $th->getMessage();
        }
    }

    public function ListeCommande()
    {
        $sql = "SELECT c.*, cl.nom_prenom_client, u.nom as nom_vendeur 
                FROM commandes c
                LEFT JOIN clients cl ON c.code_client = cl.code_client
                LEFT JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id
                ORDER BY c.date_commande DESC, c.heure_commande DESC";
        $req = $this->con->prepare($sql);
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des commandes : " . $th->getMessage();
        }
    }
}