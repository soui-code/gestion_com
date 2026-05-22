<?php
class ProduitModel
{
    public $reference, $designation, $id_categorie, $id_fournisseur, $prix_achat, $prix_vente, $stock, $stock_alert, $photo, $photo_type, $description, $etat, $utilisateur_id, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjouterProduit()
    {
        try {
            $this->con->beginTransaction();

            $req = $this->con->prepare("INSERT INTO produits (reference, designation, id_categorie, id_fournisseur, prix_achat, prix_vente, stock, stock_alert, photo, photo_type, description, etat) VALUES (:reference, :designation, :id_categorie, :id_fournisseur, :prix_achat, :prix_vente, :stock, :stock_alert, :photo, :photo_type, :description, :etat)");
            $req->bindParam(":reference", $this->reference);
            $req->bindParam(":designation", $this->designation);
            $req->bindParam(":id_categorie", $this->id_categorie);
            $req->bindParam(":id_fournisseur", $this->id_fournisseur);
            $req->bindParam(":prix_achat", $this->prix_achat);
            $req->bindParam(":prix_vente", $this->prix_vente);
            $req->bindParam(":stock", $this->stock);
            $req->bindParam(":stock_alert", $this->stock_alert);
            $req->bindParam(":photo", $this->photo);
            $req->bindParam(":photo_type", $this->photo_type);
            $req->bindParam(":description", $this->description);
            $req->bindParam(":etat", $this->etat);
            $req->execute();

            $new_id = $this->con->lastInsertId();

            if ($this->stock > 0) {
                $req_mouv = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif) VALUES (?, 'entree', ?, ?, 'Stock initial')");
                $req_mouv->execute([$new_id, $this->stock, $this->utilisateur_id]);
            }

            $this->con->commit();
            return true;
        }
        catch (\Throwable $th) {
            if ($this->con->inTransaction()) {
                $this->con->rollBack();
            }
            throw new Exception("Erreur lors de l'ajout du produit : " . $th->getMessage());
        }
    }

    public function ModifierProduit()
    {
        try {
            $sql = "UPDATE produits SET 
                    designation = :designation,
                    id_categorie = :id_categorie,
                    id_fournisseur = :id_fournisseur,
                    prix_achat = :prix_achat,
                    prix_vente = :prix_vente,
                    stock_alert = :stock_alert,
                    description = :description,
                    etat = :etat";

            if (!empty($this->photo) && $this->photo !== null) {
                $sql .= ", photo = :photo, photo_type = :photo_type";
            }

            $sql .= " WHERE reference = :reference";

            $req = $this->con->prepare($sql);
            $req->bindParam(":reference", $this->reference);
            $req->bindParam(":designation", $this->designation);
            $req->bindParam(":id_categorie", $this->id_categorie);
            $req->bindParam(":id_fournisseur", $this->id_fournisseur);
            $req->bindParam(":prix_achat", $this->prix_achat);
            $req->bindParam(":prix_vente", $this->prix_vente);
            $req->bindParam(":stock_alert", $this->stock_alert);

            if (!empty($this->photo) && $this->photo !== null) {
                $req->bindParam(":photo", $this->photo, PDO::PARAM_LOB);
                $req->bindParam(":photo_type", $this->photo_type);
            }

            $req->bindParam(":description", $this->description);
            $req->bindParam(":etat", $this->etat);
            
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de la modification du produit : " . $th->getMessage());
        }
    }

    public function ApprovisionnerProduit()
    {
        try {
            $this->con->beginTransaction();

            $req = $this->con->prepare("UPDATE produits SET stock = stock+:stock WHERE reference=:reference");
            $req->bindParam(":stock", $this->stock);
            $req->bindParam(":reference", $this->reference);
            $req->execute();

            if ($this->stock > 0) {
                $req_mouv = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif) VALUES (?, 'entree', ?, ?, 'Approvisionnement')");
                $req_mouv->execute([$this->reference, $this->stock, $this->utilisateur_id]);
            }

            $this->con->commit();
            return true;
        }
        catch (\Throwable $th) {
            if ($this->con->inTransaction()) {
                $this->con->rollBack();
            }
            throw new Exception("Erreur lors de l'approvisionnement du produit : " . $th->getMessage());
        }
    }

    public function SupprimerProduit()
    {
        try {
            $req = $this->con->prepare("DELETE FROM produits WHERE reference=:reference");
            $req->bindParam(":reference", $this->reference);
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de la suppression du produit : " . $th->getMessage());
        }
    }

    public function ListeProduit()
    {
        try {
            $query = "
                SELECT p.*, c.nom_categorie, f.nom_fournisseur 
                FROM produits p
                LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                LEFT JOIN fournisseurs f ON p.id_fournisseur = f.id_fournisseur
                ORDER BY p.reference DESC
            ";
            $req = $this->con->prepare($query);
            $req->execute();
            $result = $req->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'affichage des produits : " . $th->getMessage());
        }
    }

}