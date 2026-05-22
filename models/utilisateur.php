<?php

class UtilisateurModel
{
    public $utilisateur_id, $login, $nom, $numero, $mdp, $role, $etat, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutUtilisateur()
    {
        try {
            $req = $this->con->prepare("INSERT INTO utilisateurs VALUES (null, :nom, :numero, :login, :mdp, :role, :etat)");
            $req->bindParam(":login", $this->login);
            $req->bindParam(":mdp", $this->mdp);
            $req->bindParam(":nom", $this->nom);
            $req->bindParam(":numero", $this->numero);
            $req->bindParam(":role", $this->role);
            $req->bindParam(":etat", $this->etat);
            $req->execute();
            return true;
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'ajout de l'utilisateur : " . $th->getMessage());
        }
    }

    public function ConnexionUtilisateur($login)
    {
        try {
            $req = $this->con->prepare("SELECT * FROM utilisateurs WHERE login=:login LIMIT 1");
            $req->bindParam(":login", $login);
            $req->execute();
            $result = $req->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : [];
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la connexion : " . $th->getMessage());
        }
    }

    public function SupprimerUtilisateur()
    {
        try {
            $req = $this->con->prepare("DELETE FROM utilisateurs WHERE utilisateur_id=:utilisateur_id");
            $req->bindParam(":utilisateur_id", $this->utilisateur_id);
            $req->execute();
            return true;
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la suppression de l'utilisateur : " . $th->getMessage());
        }
    }

    public function ListeUtilisateur()
    {
        try {
            $req = $this->con->prepare("SELECT * FROM utilisateurs ORDER BY utilisateur_id DESC");
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'affichage des utilisateurs : " . $th->getMessage());
        }
    }

    public function Recherche($nom)
    {
        try {
            $searchTerm = "%" . $nom . "%";
            $req = $this->con->prepare("SELECT * FROM utilisateurs WHERE nom LIKE :nom OR login LIKE :login ORDER BY utilisateur_id DESC");
            $req->bindParam(":nom", $searchTerm);
            $req->bindParam(":login", $searchTerm);
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la recherche : " . $th->getMessage());
        }
    }

    public function ModificationUtilisateur()
    {
        try {
            $req = $this->con->prepare("UPDATE utilisateurs SET login=:login, nom=:nom, numero=:numero, role=:role, etat=:etat WHERE utilisateur_id=:utilisateur_id");
            $req->bindParam(":login", $this->login);
            $req->bindParam(":nom", $this->nom);
            $req->bindParam(":numero", $this->numero);
            $req->bindParam(":role", $this->role);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":utilisateur_id", $this->utilisateur_id);
            $req->execute();
            return true;
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la modification de l'utilisateur : " . $th->getMessage());
        }
    }

    public function getStockAlerts()
    {
        try {
            $req = $this->con->prepare("SELECT reference, designation, stock, stock_alert 
                FROM produits 
                WHERE stock <= stock_alert 
                AND etat = 'actif' 
                ORDER BY designation");
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'affichage des alertes : " . $th->getMessage());
        }
    }
}