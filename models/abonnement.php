<?php
class AbonnementModel
{
    public $id_abonnement, $id_societe, $id_plan, $date_debut, $date_fin, $statut, $prix_paye, $mode_paiement, $notes, $created_by, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    /**
     * Attribuer un abonnement à une société
     */
    public function attribuerAbonnement()
    {
        try {
            $this->con->beginTransaction();

            // 1. Annuler l'ancien abonnement actif s'il existe
            $stmt = $this->con->prepare("
                UPDATE abonnements
                SET statut = 'annule'
                WHERE id_societe = ? AND statut = 'actif'
            ");
            $stmt->execute([$this->id_societe]);

            // 2. Créer le nouvel abonnement
            $stmt = $this->con->prepare("
                INSERT INTO abonnements (id_societe, id_plan, date_debut, date_fin, statut, prix_paye, mode_paiement, notes, created_by)
                VALUES (?, ?, ?, ?, 'actif', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $this->id_societe,
                $this->id_plan,
                $this->date_debut,
                $this->date_fin,
                $this->prix_paye,
                $this->mode_paiement,
                $this->notes,
                $this->created_by
            ]);

            // 3. Mettre à jour la société
            $stmt = $this->con->prepare("
                UPDATE societes
                SET plan_actuel_id = ?,
                    abonnement_expire_le = ?,
                    statut_abonnement = 'actif'
                WHERE id_societe = ?
            ");
            $stmt->execute([$this->id_plan, $this->date_fin, $this->id_societe]);

            $this->con->commit();

            Logger::info('Abonnement attribué', [
                'id_societe' => $this->id_societe,
                'id_plan' => $this->id_plan,
                'date_fin' => $this->date_fin
            ]);

            return true;
        } catch (Exception $e) {
            $this->con->rollBack();
            Logger::error('Erreur attribuerAbonnement', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Récupérer tous les abonnements d'une société
     */
    public function getAbonnementsSociete($id_societe)
    {
        try {
            $stmt = $this->con->prepare("
                SELECT
                    a.*,
                    p.nom_plan,
                    p.couleur as couleur_plan,
                    u.nom as created_by_nom
                FROM abonnements a
                LEFT JOIN plans p ON a.id_plan = p.id_plan
                LEFT JOIN utilisateurs u ON a.created_by = u.utilisateur_id
                WHERE a.id_societe = ?
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([$id_societe]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Erreur getAbonnementsSociete', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Récupérer l'abonnement actif d'une société
     */
    public function getAbonnementActif($id_societe)
    {
        try {
            $stmt = $this->con->prepare("
                SELECT
                    a.*,
                    p.nom_plan,
                    p.nb_boutiques_max,
                    p.prix_mensuel,
                    p.couleur as couleur_plan,
                    DATEDIFF(a.date_fin, CURDATE()) as jours_restants
                FROM abonnements a
                LEFT JOIN plans p ON a.id_plan = p.id_plan
                WHERE a.id_societe = ? AND a.statut = 'actif'
                ORDER BY a.date_fin DESC
                LIMIT 1
            ");
            $stmt->execute([$id_societe]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Erreur getAbonnementActif', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Vue complète des abonnements (Admin)
     */
    public function getTousAbonnementsAvecStats()
    {
        try {
            $stmt = $this->con->query("SELECT * FROM v_abonnements_actifs ORDER BY jours_restants ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Erreur getTousAbonnementsAvecStats', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Mettre à jour les statuts expirés (Tâche CRON)
     */
    public function marquerAbonnementsExpires()
    {
        try {
            $this->con->beginTransaction();

            // Marquer abonnements expirés
            $this->con->exec("
                UPDATE abonnements
                SET statut = 'expire'
                WHERE date_fin < CURDATE() AND statut = 'actif'
            ");

            // Mettre à jour statut sociétés
            $this->con->exec("
                UPDATE societes
                SET statut_abonnement = 'expire'
                WHERE abonnement_expire_le < CURDATE() AND statut_abonnement = 'actif'
            ");

            $this->con->commit();
            return true;
        } catch (Exception $e) {
            $this->con->rollBack();
            Logger::error('Erreur marquerAbonnementsExpires', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Statistiques Dashboard Admin
     */
    public function getStatistiquesAbonnements()
    {
        try {
            $stats = [];

            // Total actifs
            $stmt = $this->con->query("SELECT COUNT(*) as total FROM societes WHERE statut_abonnement = 'actif'");
            $stats['total_actifs'] = $stmt->fetchColumn();

            // Expire < 7 jours
            $stmt = $this->con->query("
                SELECT COUNT(*) as total
                FROM societes
                WHERE statut_abonnement = 'actif'
                  AND DATEDIFF(abonnement_expire_le, CURDATE()) BETWEEN 0 AND 7
            ");
            $stats['expire_bientot'] = $stmt->fetchColumn();

            // Expirés
            $stmt = $this->con->query("SELECT COUNT(*) as total FROM societes WHERE statut_abonnement = 'expire'");
            $stats['expires'] = $stmt->fetchColumn();

            return $stats;
        } catch (Exception $e) {
            Logger::error('Erreur getStatistiquesAbonnements', ['error' => $e->getMessage()]);
            return ['total_actifs' => 0, 'expire_bientot' => 0, 'expires' => 0];
        }
    }
}
