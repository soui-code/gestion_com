<?php

class facturemodel
{
    public $numero_facture, $titre_facture, $date_facture, $type_facture, $montant_ht, $code_taxe, $montant_ttc, $code_client, $etat_facture, $logo_societe, $categorie, $format, $id_societe, $id_boutique, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutFacture()
    {
        $req = $this->con->prepare("INSERT INTO factures (titre_facture, date_facture, type_facture, montant_ht, code_taxe, montant_ttc, code_client, etat_facture, logo_societe, categorie, format, id_societe, id_boutique) VALUES (:titre_facture, :date_facture, :type_facture, :montant_ht, :code_taxe, :montant_ttc, :code_client, :etat_facture, :logo_societe, :categorie, :format, :id_societe, :id_boutique)");
        $req->bindParam(":titre_facture", $this->titre_facture);
        $req->bindParam(":date_facture", $this->date_facture);
        $req->bindParam(":type_facture", $this->type_facture);
        $req->bindParam(":montant_ht", $this->montant_ht);
        $req->bindParam(":code_taxe", $this->code_taxe);
        $req->bindParam(":montant_ttc", $this->montant_ttc);
        $req->bindParam(":code_client", $this->code_client);
        $req->bindParam(":etat_facture", $this->etat_facture);
        $req->bindParam(":logo_societe", $this->logo_societe);
        $req->bindParam(":categorie", $this->categorie);
        $req->bindParam(":format", $this->format);
        $req->bindParam(":id_societe", $this->id_societe);
        $req->bindParam(":id_boutique", $this->id_boutique);
        try {
            return $req->execute();
        }
        catch (\Throwable $th) {
            return "Erreur lors de l'ajout de la facture : " . $th->getMessage();
        }
    }

    public function ListeFacture()
    {
        try {
            $sql = "
                SELECT f.*,
                       c.nom_prenom_client AS client_nom,
                       t.titre_taxe AS taxe_titre,
                       t.taux_taxe AS taxe_taux
                FROM factures f
                LEFT JOIN clients c ON c.code_client = f.code_client
                LEFT JOIN taxes t ON t.code_taxe = f.code_taxe
                WHERE 1=1
            ";
            if (!empty($this->id_societe)) {
                $sql .= " AND f.id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $sql .= " AND f.id_boutique = :id_boutique";
            }
            $sql .= " ORDER BY f.date_facture DESC, f.numero_facture DESC";

            $req = $this->con->prepare($sql);

            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            if (!empty($this->id_boutique)) {
                $req->bindParam(":id_boutique", $this->id_boutique);
            }

            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Throwable $th) {
            // Si les colonnes id_societe/id_boutique n'existent pas, retourner sans filtrage
            Logger::warning('Erreur lors de la récupération des factures (probablement colonnes manquantes)', [
                'error' => $th->getMessage()
            ]);

            // Fallback : requête sans filtrage
            try {
                $sql = "
                    SELECT f.*,
                           c.nom_prenom_client AS client_nom,
                           t.titre_taxe AS taxe_titre,
                           t.taux_taxe AS taxe_taux
                    FROM factures f
                    LEFT JOIN clients c ON c.code_client = f.code_client
                    LEFT JOIN taxes t ON t.code_taxe = f.code_taxe
                    ORDER BY f.date_facture DESC, f.numero_facture DESC
                ";
                $req = $this->con->query($sql);
                return $req->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $th2) {
                Logger::error('Erreur critique lors de la récupération des factures', [
                    'error' => $th2->getMessage()
                ]);
                return [];
            }
        }
    }

    public function getFactureAvecClient($id)
    {
        // Chercher par numero_facture (string) ou par id_facture (int)
        $sql = "
            SELECT f.*,
                   c.nom_prenom_client AS nom_client,
                   c.adresse_client AS adresse_client,
                   c.telephone_client AS telephone_client,
                   c.email_client AS email_client
            FROM factures f
            LEFT JOIN clients c ON c.code_client = f.code_client
            WHERE (f.numero_facture = ? OR f.id_facture = ?)
        ";
        if (!empty($this->id_societe)) {
            $sql .= " AND f.id_societe = ?";
        }
        $sql .= " LIMIT 1";

        $req = $this->con->prepare($sql);
        $params = [$id, $id];
        if (!empty($this->id_societe)) {
            $params[] = $this->id_societe;
        }
        $req->execute($params);
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
