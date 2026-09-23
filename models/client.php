<?php

class ClientModel
{
    public $code_client, $nom_prenom_client, $telephone_client, $email_client, $adresse_client, $etat_client, $registre_commerce, $compte_contribuable, $regime_imposition, $id_societe, $id_boutique, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    public function AjoutClient()
    {
        try {
            $req = $this->con->prepare("INSERT INTO clients (nom_prenom_client, telephone_client, email_client, adresse_client, etat_client, registre_commerce, compte_contribuable, regime_imposition, id_societe, id_boutique) VALUES (:nom_prenom_client, :telephone_client, :email_client, :adresse_client, :etat_client, :registre_commerce, :compte_contribuable, :regime_imposition, :id_societe, :id_boutique)");
            $req->bindParam(":nom_prenom_client", $this->nom_prenom_client);
            $req->bindParam(":telephone_client", $this->telephone_client);
            $req->bindParam(":email_client", $this->email_client);
            $req->bindParam(":adresse_client", $this->adresse_client);
            $req->bindParam(":etat_client", $this->etat_client);
            $req->bindParam(":registre_commerce", $this->registre_commerce);
            $req->bindParam(":compte_contribuable", $this->compte_contribuable);
            $req->bindParam(":regime_imposition", $this->regime_imposition);
            $req->bindParam(":id_societe", $this->id_societe);
            $req->bindParam(":id_boutique", $this->id_boutique);
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de l'ajout du client : " . $th->getMessage());
        }
    }

    public function SupprimerClient()
    {
        try {
            $sql = "DELETE FROM clients WHERE code_client=:code_client";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe=:id_societe";
            }
            $req = $this->con->prepare($sql);
            $req->bindParam(":code_client", $this->code_client);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de la suppression du client : " . $th->getMessage());
        }
    }

    public function ModifierClient()
    {
        try {
            $sql = "UPDATE clients SET nom_prenom_client=:nom_prenom_client, telephone_client=:telephone_client, email_client=:email_client, adresse_client=:adresse_client, etat_client=:etat_client, registre_commerce=:registre_commerce, compte_contribuable=:compte_contribuable, regime_imposition=:regime_imposition WHERE code_client=:code_client";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe=:id_societe";
            }
            $req = $this->con->prepare($sql);
            $req->bindParam(":code_client", $this->code_client);
            $req->bindParam(":nom_prenom_client", $this->nom_prenom_client);
            $req->bindParam(":telephone_client", $this->telephone_client);
            $req->bindParam(":email_client", $this->email_client);
            $req->bindParam(":adresse_client", $this->adresse_client);
            $req->bindParam(":etat_client", $this->etat_client);
            $req->bindParam(":registre_commerce", $this->registre_commerce);
            $req->bindParam(":compte_contribuable", $this->compte_contribuable);
            $req->bindParam(":regime_imposition", $this->regime_imposition);
            if (!empty($this->id_societe)) {
                $req->bindParam(":id_societe", $this->id_societe);
            }
            $req->execute();
            return true;
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de la modification du client : " . $th->getMessage());
        }
    }

    public function ListeClient()
    {
        try {
            $sql = "SELECT * FROM clients WHERE 1=1";
            if (!empty($this->id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            }
            if (!empty($this->id_boutique)) {
                $sql .= " AND id_boutique = :id_boutique";
            }
            $sql .= " ORDER BY code_client DESC";
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
            throw new Exception("Erreur lors de l'affichage des clients : " . $th->getMessage());
        }
    }

    /**
     * Récupère le client générique "Client de passage" pour une société donnée,
     * ou le crée s'il n'existe pas encore (un seul par société, réutilisé ensuite).
     *
     * Utile car la colonne ventes.code_client est NOT NULL : on ne peut donc pas
     * laisser ce champ vide/null pour une vente sans client identifié.
     */
    public function getOuCreerClientPassage($id_societe = null, $id_boutique = null)
    {
        try {
            $sql = "SELECT code_client FROM clients WHERE nom_prenom_client = 'Client de passage'";
            if (!empty($id_societe)) {
                $sql .= " AND id_societe = :id_societe";
            } else {
                $sql .= " AND id_societe IS NULL";
            }
            $sql .= " LIMIT 1";

            $req = $this->con->prepare($sql);
            if (!empty($id_societe)) {
                $req->bindParam(":id_societe", $id_societe);
            }
            $req->execute();
            $existing = $req->fetchColumn();

            if ($existing) {
                return (int)$existing;
            }

            // Le client générique n'existe pas encore pour cette société : on le crée
            $insert = $this->con->prepare("
                INSERT INTO clients (nom_prenom_client, telephone_client, email_client, adresse_client, etat_client, registre_commerce, compte_contribuable, regime_imposition, id_societe, id_boutique)
                VALUES ('Client de passage', NULL, NULL, NULL, 'actif', NULL, NULL, NULL, :id_societe, :id_boutique)
            ");
            $insert->bindParam(":id_societe", $id_societe);
            $insert->bindParam(":id_boutique", $id_boutique);
            $insert->execute();

            return (int)$this->con->lastInsertId();
        }
        catch (\Throwable $th) {
            throw new Exception("Erreur lors de la récupération/création du client de passage : " . $th->getMessage());
        }
    }

}