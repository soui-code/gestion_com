<?php
require_once 'models/abonnement.php';
require_once 'models/plan.php';

class abonnement extends BaseController
{
    private $abonnementModel;
    private $planModel;

    public function __construct()
    {
        parent::__construct();
        $this->abonnementModel = new AbonnementModel();
        $this->planModel = new PlanModel();
    }

    /**
     * Dashboard Abonnements (ADMIN uniquement)
     */
    public function dashboard()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        $abonnements = $this->abonnementModel->getTousAbonnementsAvecStats();
        $stats = $this->abonnementModel->getStatistiquesAbonnements();

        $this->render('abonnement/dashboard', [
            'abonnements' => $abonnements,
            'stats' => $stats
        ]);
    }

    /**
     * Formulaire attribution abonnement (ADMIN uniquement)
     */
    public function attribuer()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        $id_societe = $_GET['id'] ?? null;

        if (!$id_societe) {
            setFlash('error', 'Société non spécifiée.');
            redirect('/abonnement/dashboard');
        }

        // Récupérer infos société
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT * FROM societes WHERE id_societe = ?");
        $stmt->execute([$id_societe]);
        $societe = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$societe) {
            setFlash('error', 'Société introuvable.');
            redirect('/abonnement/dashboard');
        }

        // Nombre de boutiques existantes
        $stmt = $con->prepare("SELECT COUNT(*) as nb FROM boutiques WHERE id_societe = ?");
        $stmt->execute([$id_societe]);
        $nb_boutiques = $stmt->fetchColumn();

        // Plans disponibles
        $plans = $this->planModel->getPlansActifs();

        // Abonnement actuel
        $abonnement_actuel = $this->abonnementModel->getAbonnementActif($id_societe);

        // Historique abonnements
        $historique = $this->abonnementModel->getAbonnementsSociete($id_societe);

        $this->render('abonnement/attribuer', [
            'societe' => $societe,
            'nb_boutiques' => $nb_boutiques,
            'plans' => $plans,
            'abonnement_actuel' => $abonnement_actuel,
            'historique' => $historique
        ]);
    }

    /**
     * Enregistrer attribution abonnement (ADMIN uniquement)
     */
    public function enregistrer()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/abonnement/dashboard');
            return;
        }

        $id_societe = intval($_POST['id_societe'] ?? 0);
        $id_plan = intval($_POST['id_plan'] ?? 0);
        $date_debut = $_POST['date_debut'] ?? date('Y-m-d');
        $duree_mois = intval($_POST['duree_mois'] ?? 1);
        $prix_paye = floatval($_POST['prix_paye'] ?? 0);
        $mode_paiement = trim($_POST['mode_paiement'] ?? 'Non spécifié');
        $notes = trim($_POST['notes'] ?? '');

        // Calculer date fin
        $date_fin = date('Y-m-d', strtotime($date_debut . ' + ' . $duree_mois . ' months'));

        // Vérifier que le plan existe
        $plan = $this->planModel->getPlanById($id_plan);
        if (!$plan) {
            setFlash('error', 'Plan invalide.');
            redirect('/abonnement/attribuer?id=' . $id_societe);
            return;
        }

        // Vérifier si société dépasse limite boutiques
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT COUNT(*) as nb FROM boutiques WHERE id_societe = ?");
        $stmt->execute([$id_societe]);
        $nb_boutiques = $stmt->fetchColumn();

        if ($nb_boutiques > $plan['nb_boutiques_max']) {
            setFlash('warning', "Cette société a déjà $nb_boutiques boutiques. Le plan {$plan['nom_plan']} est limité à {$plan['nb_boutiques_max']}. Nouvelles créations bloquées.");
        }

        // Enregistrer
        $this->abonnementModel->id_societe = $id_societe;
        $this->abonnementModel->id_plan = $id_plan;
        $this->abonnementModel->date_debut = $date_debut;
        $this->abonnementModel->date_fin = $date_fin;
        $this->abonnementModel->prix_paye = $prix_paye;
        $this->abonnementModel->mode_paiement = $mode_paiement;
        $this->abonnementModel->notes = $notes;
        $this->abonnementModel->created_by = $_SESSION['utilisateur_id'];

        if ($this->abonnementModel->attribuerAbonnement()) {
            setFlash('success', "Abonnement {$plan['nom_plan']} attribué avec succès ! Expire le : $date_fin");
        } else {
            setFlash('error', "Erreur lors de l'attribution de l'abonnement.");
        }

        redirect('/abonnement/attribuer?id=' . $id_societe);
        return;
    }

    /**
     * Tâche CRON : Marquer abonnements expirés
     */
    public function cronExpiration()
    {
        // Cette méthode peut être appelée par un CRON quotidien
        // Exemple : curl http://localhost/abonnement/cronExpiration

        if ($this->abonnementModel->marquerAbonnementsExpires()) {
            echo "✅ Abonnements expirés mis à jour avec succès.\n";
            Logger::info('CRON: Abonnements expirés mis à jour');
        } else {
            echo "❌ Erreur lors de la mise à jour.\n";
            Logger::error('CRON: Erreur mise à jour abonnements expirés');
        }
    }
}
