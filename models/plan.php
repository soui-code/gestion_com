<?php
class PlanModel
{
    public $id_plan, $nom_plan, $nb_boutiques_max, $prix_mensuel, $description, $couleur, $actif, $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    /**
     * Récupérer tous les plans
     */
    public function getTousPlans()
    {
        try {
            $stmt = $this->con->query("SELECT * FROM plans ORDER BY prix_mensuel ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Erreur getTousPlans', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Récupérer les plans actifs uniquement
     */
    public function getPlansActifs()
    {
        try {
            $stmt = $this->con->query("SELECT * FROM plans WHERE actif = 1 ORDER BY prix_mensuel ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Erreur getPlansActifs', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Récupérer un plan par ID
     */
    public function getPlanById($id_plan)
    {
        try {
            $stmt = $this->con->prepare("SELECT * FROM plans WHERE id_plan = ?");
            $stmt->execute([$id_plan]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Erreur getPlanById', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Modifier un plan
     */
    public function modifierPlan()
    {
        try {
            $stmt = $this->con->prepare("
                UPDATE plans SET
                    nom_plan = ?,
                    nb_boutiques_max = ?,
                    prix_mensuel = ?,
                    description = ?,
                    couleur = ?,
                    actif = ?
                WHERE id_plan = ?
            ");

            return $stmt->execute([
                $this->nom_plan,
                $this->nb_boutiques_max,
                $this->prix_mensuel,
                $this->description,
                $this->couleur,
                $this->actif,
                $this->id_plan
            ]);
        } catch (Exception $e) {
            Logger::error('Erreur modifierPlan', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Créer un nouveau plan
     */
    public function creerPlan()
    {
        try {
            $stmt = $this->con->prepare("
                INSERT INTO plans (nom_plan, nb_boutiques_max, prix_mensuel, description, couleur, actif)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $this->nom_plan,
                $this->nb_boutiques_max,
                $this->prix_mensuel,
                $this->description,
                $this->couleur ?? '#ff9b44',
                $this->actif ?? 1
            ]);
        } catch (Exception $e) {
            Logger::error('Erreur creerPlan', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Statistiques des plans
     */
    public function getStatistiquesPlans()
    {
        try {
            $stmt = $this->con->query("
                SELECT
                    p.id_plan,
                    p.nom_plan,
                    p.prix_mensuel,
                    COUNT(DISTINCT s.id_societe) as nb_societes
                FROM plans p
                LEFT JOIN societes s ON p.id_plan = s.plan_actuel_id AND s.statut_abonnement = 'actif'
                WHERE p.actif = 1
                GROUP BY p.id_plan
                ORDER BY p.prix_mensuel ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Erreur getStatistiquesPlans', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
