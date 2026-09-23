<?php
class mouvementmodel {
    public $id_mouvement, $reference_produit, $type_mouvement, $quantite, $date_mouvement, $utilisateur_id, $motif, $id_societe, $id_boutique, $con;

    public function __construct() {
        $this->con = getDatabaseConnection();
    }

    public function EnregistrerMouvement() {
        try {
            $req = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif, id_societe, id_boutique) VALUES (:ref, :type, :qte, :user_id, :motif, :id_societe, :id_boutique)");
            $req->bindParam(":ref", $this->reference_produit);
            $req->bindParam(":type", $this->type_mouvement);
            $req->bindParam(":qte", $this->quantite);
            $req->bindParam(":user_id", $this->utilisateur_id);
            $req->bindParam(":motif", $this->motif);
            $req->bindParam(":id_societe", $this->id_societe);
            $req->bindParam(":id_boutique", $this->id_boutique);
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
                      WHERE 1=1";
            if (!empty($this->id_societe)) {
                $query .= " AND m.id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $query .= " AND m.id_boutique = :id_boutique";
            }
            $query .= " ORDER BY m.date_mouvement DESC";
            $req = $this->con->prepare($query);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            if (!empty($this->id_boutique)) {
                $req->bindParam(":id_boutique", $this->id_boutique);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
             return [];
        }
    }
}
?>
