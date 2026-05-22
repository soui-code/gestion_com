<?php
class categoriemodel {
    public $id_categorie, $nom_categorie, $description, $etat, $con;

    public function __construct() {
        $this->con = getDatabaseConnection();
    }

    public function AjouterCategorie() {
        try {
            $req = $this->con->prepare("INSERT INTO categories (nom_categorie, description, etat) VALUES (:nom, :desc, :etat)");
            $req->bindParam(":nom", $this->nom_categorie);
            $req->bindParam(":desc", $this->description);
            $req->bindParam(":etat", $this->etat);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ModifierCategorie() {
        try {
            $req = $this->con->prepare("UPDATE categories SET nom_categorie = :nom, description = :desc, etat = :etat WHERE id_categorie = :id");
            $req->bindParam(":id", $this->id_categorie);
            $req->bindParam(":nom", $this->nom_categorie);
            $req->bindParam(":desc", $this->description);
            $req->bindParam(":etat", $this->etat);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function SupprimerCategorie() {
        try {
            $req = $this->con->prepare("DELETE FROM categories WHERE id_categorie = :id");
            $req->bindParam(":id", $this->id_categorie);
            return $req->execute();
        } catch (PDOException $e) {
            return false; // Typically fails if linked to a product due to constraints (if strict), though we use SET NULL
        }
    }

    public function ListeCategories() {
        try {
            $req = $this->con->prepare("SELECT * FROM categories ORDER BY nom_categorie ASC");
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getActiveCategories() {
         try {
            $req = $this->con->prepare("SELECT * FROM categories WHERE etat = 'actif' ORDER BY nom_categorie ASC");
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
