<?php
class boutiquemodel
{
    public $code_boutique, $nom_boutique, $telephone_boutique, $latitude, $longitude, $produit_json, $etat_boutique, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutBoutique()
    {
        $req = $this->con->prepare("INSERT INTO boutiques VALUES (null,:nom_boutique,:telephone_boutique,:latitude,:longitude,:produit_json,:etat_boutique)");
        $req->bindParam(":nom_boutique", $this->nom_boutique);
        $req->bindParam(":telephone_boutique", $this->telephone_boutique);
        $req->bindParam(":latitude", $this->latitude);
        $req->bindParam(":longitude", $this->longitude);
        $req->bindParam(":produit_json", $this->produit_json);
        $req->bindParam(":etat_boutique", $this->etat_boutique);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de la boutique : " . $th->getMessage();
        }
    }

    public function ModificationBoutique()
    {
        $req = $this->con->prepare("UPDATE boutiques SET nom_boutique=:nom_boutique,telephone_boutique=:telephone_boutique,latitude=:latitude,longitude=:longitude,produit_json=:produit_json,etat_boutique=:etat_boutique WHERE code_boutique=:code_boutique");
        $req->bindParam(":code_boutique", $this->code_boutique);
        $req->bindParam(":nom_boutique", $this->nom_boutique);
        $req->bindParam(":telephone_boutique", $this->telephone_boutique);
        $req->bindParam(":latitude", $this->latitude);
        $req->bindParam(":longitude", $this->longitude);
        $req->bindParam(":produit_json", $this->produit_json);
        $req->bindParam(":etat_boutique", $this->etat_boutique);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de la boutique : " . $th->getMessage();
        }
    }


    public function SuppressionBoutique()
    {
        $req = $this->con->prepare("DELETE FROM boutiques WHERE code_boutique=:code_boutique ");
        $req->bindParam(":code_boutique", $this->code_boutique);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de la suppression de la boutique : " . $th->getMessage();
        }
    }

    public function ListeBoutique()
    {
        $req = $this->con->prepare("SELECT * FROM boutiques");
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des boutiques : " . $th->getMessage();
        }
    }


    public function getAllBoutiques()
    {
        $sql = "SELECT * FROM boutiques WHERE etat_boutique = 'active'";
        $req = $this->con->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }



}