<?php
class fournisseurmodel {
    public $id_fournisseur, $nom_fournisseur, $telephone, $email, $adresse, $etat, $id_societe, $id_boutique, $con;

    public function __construct() {
        $this->con = getDatabaseConnection();
    }

    public function AjouterFournisseur() {
        try {
            $req = $this->con->prepare("INSERT INTO fournisseurs (nom_fournisseur, telephone, email, adresse, etat, id_societe) VALUES (:nom, :tel, :email, :adresse, :etat, :id_societe)");
            $req->bindParam(":nom", $this->nom_fournisseur);
            $req->bindParam(":tel", $this->telephone);
            $req->bindParam(":email", $this->email);
            $req->bindParam(":adresse", $this->adresse);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (PDOException $e) {
            Logger::error('Erreur ajout fournisseur', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function ModifierFournisseur() {
        try {
            $req = $this->con->prepare("UPDATE fournisseurs SET nom_fournisseur = :nom, telephone = :tel, email = :email, adresse = :adresse, etat = :etat WHERE id_fournisseur = :id AND id_societe = :id_societe");
            $req->bindParam(":id", $this->id_fournisseur);
            $req->bindParam(":nom", $this->nom_fournisseur);
            $req->bindParam(":tel", $this->telephone);
            $req->bindParam(":email", $this->email);
            $req->bindParam(":adresse", $this->adresse);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function SupprimerFournisseur() {
        try {
            $req = $this->con->prepare("DELETE FROM fournisseurs WHERE id_fournisseur = :id AND id_societe = :id_societe");
            $req->bindParam(":id", $this->id_fournisseur);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ListeFournisseurs() {
        try {
            $sql = "SELECT * FROM fournisseurs WHERE 1=1";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $sql .= " ORDER BY nom_fournisseur ASC";
            $req = $this->con->prepare($sql);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Erreur liste fournisseurs', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function getActiveFournisseurs() {
         try {
            $sql = "SELECT * FROM fournisseurs WHERE etat = 'actif'";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $sql .= " AND id_boutique = :id_boutique";
            }
            $sql .= " ORDER BY nom_fournisseur ASC";
            $req = $this->con->prepare($sql);
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
