<?php
class categoriemodel {
    public $id_categorie, $nom_categorie, $description, $etat, $id_societe, $id_boutique, $con;

    public function __construct() {
        $this->con = getDatabaseConnection();
    }

    public function AjouterCategorie() {
        try {
            $req = $this->con->prepare("INSERT INTO categories (nom_categorie, description, etat, id_societe) VALUES (:nom, :desc, :etat, :id_societe)");
            $req->bindParam(":nom", $this->nom_categorie);
            $req->bindParam(":desc", $this->description);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (PDOException $e) {
            Logger::error('Erreur ajout catégorie', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function ModifierCategorie() {
        try {
            $req = $this->con->prepare("UPDATE categories SET nom_categorie = :nom, description = :desc, etat = :etat WHERE id_categorie = :id AND id_societe = :id_societe");
            $req->bindParam(":id", $this->id_categorie);
            $req->bindParam(":nom", $this->nom_categorie);
            $req->bindParam(":desc", $this->description);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function SupprimerCategorie() {
        try {
            $req = $this->con->prepare("DELETE FROM categories WHERE id_categorie = :id AND id_societe = :id_societe");
            $req->bindParam(":id", $this->id_categorie);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function ListeCategories() {
        try {
            $sql = "SELECT * FROM categories WHERE 1=1";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $sql .= " ORDER BY nom_categorie ASC";
            $req = $this->con->prepare($sql);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Erreur liste catégories', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function getActiveCategories() {
         try {
            $sql = "SELECT * FROM categories WHERE etat = 'actif'";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $sql .= " ORDER BY nom_categorie ASC";
            $req = $this->con->prepare($sql);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
