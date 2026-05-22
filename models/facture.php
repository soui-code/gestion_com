<?php

class facturemodel
{
    public $numero_facture, $titre_facture, $date_facture, $type_facture, $montant_ht, $code_taxe, $montant_ttc, $code_client, $etat_facture, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutFacture()
    {
        $req = $this->con->prepare("INSERT INTO factures VALUES (null,:titre_facture,:date_facture,:type_facture,:montant_ht,:code_taxe,:montant_ttc,:code_client,:etat_facture)");
        $req->bindParam(":titre_facture", $this->titre_facture);
        $req->bindParam(":date_facture", $this->date_facture);
        $req->bindParam(":type_facture", $this->type_facture);
        $req->bindParam(":montant_ht", $this->montant_ht);
        $req->bindParam(":code_taxe", $this->code_taxe);
        $req->bindParam(":montant_ttc", $this->montant_ttc);
        $req->bindParam(":code_client", $this->code_client);
        $req->bindParam(":etat_facture", $this->etat_facture);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de la facture : " . $th->getMessage();
        }
    }

    public function ListeFacture()
    {
        $req = $this->con->prepare("
            SELECT f.*, 
                   c.nom_prenom_client AS client_nom, 
                   t.titre_taxe AS taxe_titre, 
                   t.taux_taxe AS taxe_taux
            FROM factures f
            LEFT JOIN clients c ON c.code_client = f.code_client
            LEFT JOIN taxes t ON t.code_taxe = f.code_taxe
            ORDER BY f.date_facture DESC, f.numero_facture DESC
        ");
        try {
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'affichage des factures : " . $th->getMessage();
        }
    }

    public function getFactureAvecClient($id)
    {
        $sql = "
            SELECT f.*, 
                   c.nom_prenom_client AS nom_client,
                   c.adresse_client AS adresse_client,
                   c.telephone_client AS telephone_client,
                   c.email_client AS email_client
            FROM factures f
            LEFT JOIN clients c ON c.code_client = f.code_client
            WHERE f.numero_facture = ?
            LIMIT 1
        ";

        $req = $this->con->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function getFactureItems(string $numero_facture)
    {
        $sql = "
            SELECT c.*, p.designation 
            FROM commandes c
            LEFT JOIN produits p ON c.reference = p.reference
            WHERE c.numero_facture = ?
        ";
        $req = $this->con->prepare($sql);
        $req->execute([$numero_facture]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
