<?php

class ClientModel
{
    public $code_client, $nom_prenom_client, $telephone_client, $email_client, $adresse_client, $etat_client, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutClient()
    {
        try {
            $req = $this->con->prepare("INSERT INTO clients VALUES (null,:nom_prenom_client,:telephone_client,:email_client,:adresse_client,:etat_client)");
            $req->bindParam(":nom_prenom_client", $this->nom_prenom_client);
            $req->bindParam(":telephone_client", $this->telephone_client);
            $req->bindParam(":email_client", $this->email_client);
            $req->bindParam(":adresse_client", $this->adresse_client);
            $req->bindParam(":etat_client", $this->etat_client);
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'ajout du client : " . $th->getMessage());
        }
    }

    public function SupprimerClient()
    {
        try {
            $req = $this->con->prepare("DELETE FROM clients WHERE code_client=:code_client");
            $req->bindParam(":code_client", $this->code_client);
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de la suppression du client : " . $th->getMessage());
        }
    }

    public function ModifierClient()
    {
        try {
            $req = $this->con->prepare("UPDATE clients SET nom_prenom_client=:nom_prenom_client,telephone_client=:telephone_client,email_client=:email_client,adresse_client=:adresse_client,etat_client=:etat_client WHERE code_client=:code_client");
            $req->bindParam(":code_client", $this->code_client);
            $req->bindParam(":nom_prenom_client", $this->nom_prenom_client);
            $req->bindParam(":telephone_client", $this->telephone_client);
            $req->bindParam(":email_client", $this->email_client);
            $req->bindParam(":adresse_client", $this->adresse_client);
            $req->bindParam(":etat_client", $this->etat_client);
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de la modification du client : " . $th->getMessage());
        }
    }

    public function ListeClient()
    {
        try {
            $req = $this->con->prepare("SELECT * FROM clients ORDER BY code_client DESC");
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'affichage des clients : " . $th->getMessage());
        }
    }

}