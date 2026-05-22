<?php
class fournisseurmodel {
    public $id_fournisseur, $nom_fournisseur, $telephone, $email, $adresse, $etat, $con;

    public function __construct() {
        $this->con = getDatabaseConnection();
    }

    public function AjouterFournisseur() {
        try {
            $req = $this->con->prepare("INSERT INTO fournisseurs (nom_fournisseur, telephone, email, adresse, etat) VALUES (:nom, :tel, :email, :adresse, :etat)");
            $req->bindParam(":nom", $this->nom_fournisseur);
            $req->bindParam(":tel", $this->telephone);
            $req->bindParam(":email", $this->email);
            $req->bindParam(":adresse", $this->adresse);
            $req->bindParam(":etat", $this->etat);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ModifierFournisseur() {
        try {
            $req = $this->con->prepare("UPDATE fournisseurs SET nom_fournisseur = :nom, telephone = :tel, email = :email, adresse = :adresse, etat = :etat WHERE id_fournisseur = :id");
            $req->bindParam(":id", $this->id_fournisseur);
            $req->bindParam(":nom", $this->nom_fournisseur);
            $req->bindParam(":tel", $this->telephone);
            $req->bindParam(":email", $this->email);
            $req->bindParam(":adresse", $this->adresse);
            $req->bindParam(":etat", $this->etat);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function SupprimerFournisseur() {
        try {
            $req = $this->con->prepare("DELETE FROM fournisseurs WHERE id_fournisseur = :id");
            $req->bindParam(":id", $this->id_fournisseur);
            return $req->execute();
        } catch (PDOException $e) {
            return false; 
        }
    }

    public function ListeFournisseurs() {
        try {
            $req = $this->con->prepare("SELECT * FROM fournisseurs ORDER BY nom_fournisseur ASC");
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getActiveFournisseurs() {
         try {
            $req = $this->con->prepare("SELECT * FROM fournisseurs WHERE etat = 'actif' ORDER BY nom_fournisseur ASC");
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
