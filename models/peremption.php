<?php

class PeremptionModel
{
    public $id, $id_article, $date_fabrication, $date_peremption, $nombre, $id_societe, $id_boutique, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function Ajouter()
    {
        try {
            $req = $this->con->prepare("INSERT INTO peremption (id_article, date_fabrication, date_peremption, nombre, id_societe, id_boutique) VALUES (:id_article, :date_fabrication, :date_peremption, :nombre, :id_societe, :id_boutique)");
            $req->bindParam(":id_article", $this->id_article);
            $req->bindParam(":date_fabrication", $this->date_fabrication);
            $req->bindParam(":date_peremption", $this->date_peremption);
            $req->bindParam(":nombre", $this->nombre);
            $req->bindParam(":id_societe", $this->id_societe);
            $req->bindParam(":id_boutique", $this->id_boutique);
            return $req->execute();
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'ajout de péremption : " . $th->getMessage());
        }
    }

    public function Modifier()
    {
        try {
            $req = $this->con->prepare("UPDATE peremption SET id_article=:id_article, date_fabrication=:date_fabrication, date_peremption=:date_peremption, nombre=:nombre WHERE id=:id AND id_societe=:id_societe");
            $req->bindParam(":id", $this->id);
            $req->bindParam(":id_article", $this->id_article);
            $req->bindParam(":date_fabrication", $this->date_fabrication);
            $req->bindParam(":date_peremption", $this->date_peremption);
            $req->bindParam(":nombre", $this->nombre);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la modification de péremption : " . $th->getMessage());
        }
    }

    public function Supprimer()
    {
        try {
            $req = $this->con->prepare("DELETE FROM peremption WHERE id=:id AND id_societe=:id_societe");
            $req->bindParam(":id", $this->id);
            $req->bindParam(":id_societe", $this->id_societe);
            return $req->execute();
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la suppression de péremption : " . $th->getMessage());
        }
    }

    public function Lister()
    {
        try {
            $sql = "
                SELECT p.*, pr.designation AS produit_designation
                FROM peremption p
                LEFT JOIN produits pr ON p.id_article = pr.reference
                WHERE 1=1
            ";
            if (!empty($this->id_societe)) {
                $sql .= " AND p.id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $sql .= " AND p.id_boutique = :id_boutique";
            }
            $sql .= " ORDER BY p.date_peremption ASC";
            $req = $this->con->prepare($sql);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            if (!empty($this->id_boutique)) {
                $req->bindParam(":id_boutique", $this->id_boutique);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            // Si les colonnes manquent, essayer sans filtrage
            Logger::warning('Erreur lors de la récupération des péremptions (probablement colonnes manquantes)', [
                'error' => $th->getMessage()
            ]);

            try {
                $sql = "
                    SELECT p.*, pr.designation AS produit_designation
                    FROM peremption p
                    LEFT JOIN produits pr ON p.id_article = pr.reference
                    ORDER BY p.date_peremption ASC
                ";
                $req = $this->con->query($sql);
                return $req->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $th2) {
                Logger::error('Erreur critique lors de la récupération des péremptions', [
                    'error' => $th2->getMessage()
                ]);
                return [];
            }
        }
    }

    public function getPerimes()
    {
        try {
            $sql = "
                SELECT p.*, pr.designation AS produit_designation
                FROM peremption p
                LEFT JOIN produits pr ON p.id_article = pr.reference
                WHERE p.date_peremption <= CURDATE()
            ";
            if (!empty($this->id_societe)) {
                $sql .= " AND p.id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $sql .= " AND p.id_boutique = :id_boutique";
            }
            $sql .= " ORDER BY p.date_peremption ASC";
            $req = $this->con->prepare($sql);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            if (!empty($this->id_boutique)) {
                $req->bindParam(":id_boutique", $this->id_boutique);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la récupération des produits périmés : " . $th->getMessage());
        }
    }

    public function getAlertesProchePeremption($jours = 30)
    {
        try {
            $sql = "
                SELECT p.*, pr.designation AS produit_designation
                FROM peremption p
                LEFT JOIN produits pr ON p.id_article = pr.reference
                WHERE p.date_peremption BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :jours DAY)
            ";
            if (!empty($this->id_societe)) {
                $sql .= " AND p.id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $sql .= " AND p.id_boutique = :id_boutique";
            }
            $sql .= " ORDER BY p.date_peremption ASC";
            $req = $this->con->prepare($sql);
            $req->bindParam(":jours", $jours, PDO::PARAM_INT);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            if (!empty($this->id_boutique)) {
                $req->bindParam(":id_boutique", $this->id_boutique);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la récupération des alertes péremption : " . $th->getMessage());
        }
    }
}
