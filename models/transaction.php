<?php
class transactionmodel
{
    public $numero_transaction, $date_transaction, $heure_transaction, $montant_transaction, $mode_reglement, $numero_facture, $utilisateur_id, $etat_transaction, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutTransaction()
    {
        $req = $this->con->prepare("INSERT INTO transactions VALUES (:numero_transaction,:date_transaction,:heure_transaction,:montant_transaction,:mode_reglement,:numero_facture,:utilisateur_id,:etat_transaction)");
        $req->bindParam(":numero_transaction", $this->numero_transaction);
        $req->bindParam(":date_transaction", $this->date_transaction);
        $req->bindParam(":heure_transaction", $this->heure_transaction);
        $req->bindParam(":montant_transaction", $this->montant_transaction);
        $req->bindParam(":mode_reglement", $this->mode_reglement);
        $req->bindParam(":numero_facture", $this->numero_facture);
        $req->bindParam(":utilisateur_id", $this->utilisateur_id);
        $req->bindParam(":etat_transaction", $this->etat_transaction);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de la transaction : " . $th->getMessage();
        }
    }


    public function ListeTransaction()
    {
        $sql = "SELECT t.*, u.nom as nom_utilisateur 
                FROM transactions t
                LEFT JOIN utilisateurs u ON t.utilisateur_id = u.utilisateur_id
                ORDER BY t.date_transaction DESC, t.heure_transaction DESC";
        $req = $this->con->prepare($sql);
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des transactions : " . $th->getMessage();
        }
    }







}