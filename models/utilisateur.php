<?php

class utilisateurmodel
{
    public $utilisateur_id, $login, $nom, $numero, $mdp, $role, $etat, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutUtilisateur()
    {
        $req = $this->con->prepare("INSERT INTO utilisateurs VALUES (null, :nom, :numero, :login, :mdp, :role, :etat)");
        $req->bindParam(":login", $this->login);
        $req->bindParam(":mdp", $this->mdp);
        $req->bindParam(":nom", $this->nom);
        $req->bindParam(":numero", $this->numero);
        $req->bindParam(":role", $this->role);
        $req->bindParam(":etat", $this->etat);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de l'utilisateur : " . $th->getMessage();
        }
    }

    public function ConnexionUtilisateur()
    {
        $req = $this->con->prepare("SELECT * FROM utilisateurs WHERE login=:login AND mdp=:mdp");
        $req->bindParam(":login", $this->login);
        $req->bindParam(":mdp", $this->mdp);
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de la connexion de l'utilisateur : " . $th->getMessage();
        }
    }

    public function SupprimerUtilisateur()
    {
        $req = $this->con->prepare("DELETE FROM utilisateurs WHERE utilisateur_id=:utilisateur_id ");
        $req->bindParam(":utilisateur_id", $this->utilisateur_id);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de la suppression de l'utilisateur : " . $th->getMessage();
        }
    }

    public function ListeUtilisateur()
    {
        $req = $this->con->prepare("SELECT * FROM utilisateurs");
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des utilisateurs : " . $th->getMessage();
        }
    }

    public function Recherche()
    {
        $req = $this->con->prepare("SELECT * FROM utilisateurs WHERE nom=:nom");
        $req->bindParam(":nom", $this->nom);
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des utilisateurs : " . $th->getMessage();
        }
    }

    public function ModificationUtilisateur()
    {
        $req = $this->con->prepare("UPDATE utilisateurs SET login=:login, nom=:nom, numero=:numero, role=:role, etat=:etat WHERE utilisateur_id=:utilisateur_id ");
        $req->bindParam(":login", $this->login);
        $req->bindParam(":nom", $this->nom);
        $req->bindParam(":numero", $this->numero);
        $req->bindParam(":role", $this->role);
        $req->bindParam(":etat", $this->etat);
        $req->bindParam(":utilisateur_id", $this->utilisateur_id);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de l'utilisateur : " . $th->getMessage();
        }
    }

    public function getStockAlerts()
    {
        $req = $this->con->prepare("SELECT reference, designation, stock, stock_alert 
            FROM produits 
            WHERE stock <= stock_alert 
            AND etat = 'actif' 
            ORDER BY designation");
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des alertes : " . $th->getMessage();
        }
    }


}