<?php

class taxemodel
{
    public $code_taxe, $titre_taxe, $taux_taxe, $action_taxe, $etat_taxe, $id_societe, $id_boutique, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }


    public function AjoutTaxe()
    {
        $req = $this->con->prepare("INSERT INTO taxes (titre_taxe, taux_taxe, action_taxe, etat_taxe, id_societe) VALUES (:titre_taxe,:taux_taxe,:action_taxe,:etat_taxe,:id_societe)");
        $req->bindParam(":titre_taxe", $this->titre_taxe);
        $req->bindParam(":taux_taxe", $this->taux_taxe);
        $req->bindParam(":action_taxe", $this->action_taxe);
        $req->bindParam(":etat_taxe", $this->etat_taxe);
        $req->bindParam(":id_societe", $this->id_societe);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            Logger::error('Erreur ajout taxe', ['error' => $th->getMessage()]);
            return false;
        }
    }

    public function ModificationTaxe()
    {
        $req = $this->con->prepare("UPDATE taxes SET titre_taxe=:titre_taxe,taux_taxe=:taux_taxe,action_taxe=:action_taxe,etat_taxe=:etat_taxe WHERE code_taxe=:code_taxe AND id_societe=:id_societe");
        $req->bindParam(":titre_taxe", $this->titre_taxe);
        $req->bindParam(":taux_taxe", $this->taux_taxe);
        $req->bindParam(":action_taxe", $this->action_taxe);
        $req->bindParam(":etat_taxe", $this->etat_taxe);
        $req->bindParam(":code_taxe", $this->code_taxe);
        $req->bindParam(":id_societe", $this->id_societe);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de la modification de la taxe : " . $th->getMessage();
        }
    }

    public function SuppressionTaxe()
    {
        $req = $this->con->prepare("DELETE FROM taxes WHERE code_taxe=:code_taxe AND id_societe=:id_societe");
        $req->bindParam(":code_taxe", $this->code_taxe);
        $req->bindParam(":id_societe", $this->id_societe);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de la suppression de la taxe : " . $th->getMessage();
        }
    }

    public function ListeTaxe()
    {
        try {
            $sql = "SELECT * FROM taxes WHERE 1=1";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $sql .= " ORDER BY code_taxe DESC";
            $req = $this->con->prepare($sql);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            Logger::error('Erreur récupération taxes', ['error' => $th->getMessage()]);
            return [];
        }
    }
}