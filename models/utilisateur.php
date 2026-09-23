<?php

class UtilisateurModel
{
    public $utilisateur_id, $login, $nom, $numero, $mdp, $role, $etat, $id_societe, $id_boutique, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutUtilisateur()
    {
        try {
            $req = $this->con->prepare("INSERT INTO utilisateurs (nom, numero, login, mdp, role, etat, id_societe, id_boutique) VALUES (:nom, :numero, :login, :mdp, :role, :etat, :id_societe, :id_boutique)");
            $req->bindParam(":login", $this->login);
            $req->bindParam(":mdp", $this->mdp);
            $req->bindParam(":nom", $this->nom);
            $req->bindParam(":numero", $this->numero);
            $req->bindParam(":role", $this->role);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":id_societe", $this->id_societe);
            $req->bindParam(":id_boutique", $this->id_boutique);
            $req->execute();
            return true;
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'ajout de l'utilisateur : " . $th->getMessage());
        }
    }

    public function ConnexionUtilisateur($login)
    {
        try {
            $req = $this->con->prepare("SELECT * FROM utilisateurs WHERE login=:login LIMIT 1");
            $req->bindParam(":login", $login);
            $req->execute();
            $result = $req->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : [];
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la connexion : " . $th->getMessage());
        }
    }

    public function SupprimerUtilisateur()
    {
        try {
            $req = $this->con->prepare("DELETE FROM utilisateurs WHERE utilisateur_id=:utilisateur_id");
            $req->bindParam(":utilisateur_id", $this->utilisateur_id);
            $req->execute();
            return true;
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la suppression de l'utilisateur : " . $th->getMessage());
        }
    }

    public function ListeUtilisateur()
    {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE 1=1";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $sql .= " ORDER BY utilisateur_id DESC";
            $req = $this->con->prepare($sql);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'affichage des utilisateurs : " . $th->getMessage());
        }
    }

    public function Recherche($nom)
    {
        try {
            $searchTerm = "%" . $nom . "%";
            $sql = "SELECT * FROM utilisateurs WHERE (nom LIKE :nom OR login LIKE :login)";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            $sql .= " ORDER BY utilisateur_id DESC";
            $req = $this->con->prepare($sql);
            $req->bindParam(":nom", $searchTerm);
            $req->bindParam(":login", $searchTerm);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la recherche : " . $th->getMessage());
        }
    }

    public function ModificationUtilisateur()
    {
        try {
            $req = $this->con->prepare("UPDATE utilisateurs SET login=:login, nom=:nom, numero=:numero, role=:role, etat=:etat, id_societe=:id_societe, id_boutique=:id_boutique WHERE utilisateur_id=:utilisateur_id");
            $req->bindParam(":login", $this->login);
            $req->bindParam(":nom", $this->nom);
            $req->bindParam(":numero", $this->numero);
            $req->bindParam(":role", $this->role);
            $req->bindParam(":etat", $this->etat);
            $req->bindParam(":id_societe", $this->id_societe);
            $req->bindParam(":id_boutique", $this->id_boutique);
            $req->bindParam(":utilisateur_id", $this->utilisateur_id);
            $req->execute();
            return true;
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la modification de l'utilisateur : " . $th->getMessage());
        }
    }

    public function MiseAJourMotDePasse($id, $nouveauMdpHash)
    {
        try {
            $req = $this->con->prepare("UPDATE utilisateurs SET mdp=:mdp WHERE utilisateur_id=:id");
            $req->bindParam(":mdp", $nouveauMdpHash);
            $req->bindParam(":id", $id);
            $req->execute();
            return true;
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de la mise à jour du mot de passe : " . $th->getMessage());
        }
    }


    public function getStockAlerts($id_societe = null, $id_boutique = null)
    {
        try {
            $sql = "SELECT reference, designation, stock, stock_alert 
                    FROM produits 
                    WHERE stock <= stock_alert 
                    AND etat = 'actif'";
            $params = [];
            if ($id_societe) {
                $sql .= " AND id_societe = :id_societe";
                $params[':id_societe'] = $id_societe;
            }
            if ($id_boutique) {
                $sql .= " AND id_boutique = :id_boutique";
                $params[':id_boutique'] = $id_boutique;
            }
            $sql .= " ORDER BY designation";
            $req = $this->con->prepare($sql);
            $req->execute($params);
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'affichage des alertes : " . $th->getMessage());
        }
    }

    public function getPeremptionAlerts($id_societe = null, $id_boutique = null)
    {
        try {
            $sql = "SELECT p.id, p.id_article, p.date_peremption, prod.designation
                    FROM peremption p
                    LEFT JOIN produits prod ON p.id_article = prod.reference
                    WHERE DATEDIFF(p.date_peremption, CURDATE()) <= 10
                    AND DATEDIFF(p.date_peremption, CURDATE()) >= 0";
            $params = [];
            if ($id_societe) {
                $sql .= " AND p.id_societe = :id_societe";
                $params[':id_societe'] = $id_societe;
            }
            if ($id_boutique) {
                $sql .= " AND p.id_boutique = :id_boutique";
                $params[':id_boutique'] = $id_boutique;
            }
            $sql .= " ORDER BY p.date_peremption ASC";
            $req = $this->con->prepare($sql);
            $req->execute($params);
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            Logger::warning('Erreur récupération alertes péremption', ['error' => $th->getMessage()]);
            return [];
        }
    }
}