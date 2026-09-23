<?php
class ProduitModel
{
    public $reference, $designation, $id_categorie, $id_fournisseur, $prix_achat, $prix_vente, $stock, $stock_alert, $photo, $photo_type, $description, $etat, $utilisateur_id, $id_societe, $id_boutique, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjouterProduit()
    {
        try {
            $this->con->beginTransaction();

            $req = $this->con->prepare("INSERT INTO produits (reference, designation, id_categorie, id_fournisseur, prix_achat, prix_vente, stock, stock_alert, photo, photo_type, description, etat, id_societe, id_boutique) VALUES (:reference, :designation, :id_categorie, :id_fournisseur, :prix_achat, :prix_vente, :stock, :stock_alert, :photo, :photo_type, :description, :etat, :id_societe, :id_boutique)");
            $req->bindParam(":reference", $this->reference);
            $req->bindParam(":designation", $this->designation);
            $req->bindParam(":id_categorie", $this->id_categorie);
            $req->bindParam(":id_fournisseur", $this->id_fournisseur);
            $req->bindParam(":prix_achat", $this->prix_achat);
            $req->bindParam(":prix_vente", $this->prix_vente);
            $req->bindParam(":stock", $this->stock);
            $req->bindParam(":stock_alert", $this->stock_alert);
            // PDO::PARAM_LOB : évite toute troncature du contenu binaire de l'image
            // (cohérent avec ModifierProduit ci-dessous)
            $req->bindParam(":photo", $this->photo, PDO::PARAM_LOB);
            $req->bindParam(":photo_type", $this->photo_type);
            $req->bindParam(":description", $this->description);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":id_societe", $this->id_societe);
            $req->bindParam(":id_boutique", $this->id_boutique);
            $req->execute();

            $new_id = $this->con->lastInsertId();

            if ($this->stock > 0) {
                $req_mouv = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif, id_societe, id_boutique) VALUES (?, 'entree', ?, ?, 'Stock initial', ?, ?)");
                $req_mouv->execute([$new_id, $this->stock, $this->utilisateur_id, $this->id_societe, $this->id_boutique]);
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
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }

            $req = $this->con->prepare($sql);
            $req->bindParam(":reference", $this->reference);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
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

            $sql = "UPDATE produits SET stock = stock+:stock WHERE reference=:reference";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $req = $this->con->prepare($sql);
            $req->bindParam(":stock", $this->stock);
            $req->bindParam(":reference", $this->reference);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();

            if ($this->stock > 0) {
                $req_mouv = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif, id_societe, id_boutique) VALUES (?, 'entree', ?, ?, 'Approvisionnement', ?, ?)");
                $req_mouv->execute([$this->reference, $this->stock, $this->utilisateur_id, $this->id_societe, $this->id_boutique]);
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
            $sql = "DELETE FROM produits WHERE reference=:reference";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $req = $this->con->prepare($sql);
            $req->bindParam(":reference", $this->reference);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
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
                WHERE 1=1
            ";
            if (!empty($this->id_societe)) {
                $query .= " AND p.id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $query .= " AND p.id_boutique = :id_boutique";
            }
            $query .= " ORDER BY p.reference DESC";
            $req = $this->con->prepare($query);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            if (!empty($this->id_boutique)) {
                $req->bindParam(":id_boutique", $this->id_boutique);
            }
            $req->execute();
            $result = $req->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'affichage des produits : " . $th->getMessage());
        }
    }

}