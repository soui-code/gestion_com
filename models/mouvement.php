<?php
class mouvementmodel {
    public $id_mouvement, $reference_produit, $type_mouvement, $quantite, $date_mouvement, $utilisateur_id, $motif, $con;

    public function __construct() {
        $this->con = getDatabaseConnection();
    }

    public function EnregistrerMouvement() {
        try {
            $req = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif) VALUES (:ref, :type, :qte, :user_id, :motif)");
            $req->bindParam(":ref", $this->reference_produit);
            $req->bindParam(":type", $this->type_mouvement);
            $req->bindParam(":qte", $this->quantite);
            $req->bindParam(":user_id", $this->utilisateur_id);
            $req->bindParam(":motif", $this->motif);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ListeMouvements() {
        try {
            $query = "SELECT m.*, p.designation, u.nom as nom_utilisateur 
                      FROM mouvements_stock m
                      LEFT JOIN produits p ON m.reference_produit = p.reference
                      LEFT JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id
                      ORDER BY m.date_mouvement DESC";
            $req = $this->con->prepare($query);
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
             return [];
        }
    }
}
?>
