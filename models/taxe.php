<?php

class taxemodel
{
    public $code_taxe, $titre_taxe, $taux_taxe, $action_taxe, $etat_taxe, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }


    public function AjoutTaxe()
    {
        $req = $this->con->prepare("INSERT INTO taxes VALUES (null,:titre_taxe,:taux_taxe,:action_taxe,:etat_taxe)");
        $req->bindParam(":titre_taxe", $this->titre_taxe);
        $req->bindParam(":taux_taxe", $this->taux_taxe);
        $req->bindParam(":action_taxe", $this->action_taxe);
        $req->bindParam(":etat_taxe", $this->etat_taxe);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de la taxe : " . $th->getMessage();
        }
    }

    public function ModificationTaxe()
    {
        $req = $this->con->prepare("UPDATE taxes SET titre_taxe=:titre_taxe,taux_taxe=:taux_taxe,action_taxe=:action_taxe,etat_taxe=:etat_taxe WHERE code_taxe=:code_taxe");
        $req->bindParam(":titre_taxe", $this->titre_taxe);
        $req->bindParam(":taux_taxe", $this->taux_taxe);
        $req->bindParam(":action_taxe", $this->action_taxe);
        $req->bindParam(":etat_taxe", $this->etat_taxe);
        $req->bindParam(":code_taxe", $this->code_taxe);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de la modification de la taxe : " . $th->getMessage();
        }
    }

    public function SuppressionTaxe()
    {
        $req = $this->con->prepare("DELETE FROM taxes WHERE code_taxe=:code_taxe ");
        $req->bindParam(":code_taxe", $this->code_taxe);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de la suppression de la taxe : " . $th->getMessage();
        }
    }

    public function ListeTaxe()
    {
        $req = $this->con->prepare("SELECT * FROM taxes");
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des taxes : " . $th->getMessage();
        }
    }
}