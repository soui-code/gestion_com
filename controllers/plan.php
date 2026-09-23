<?php
require_once 'models/plan.php';
require_once 'models/abonnement.php';

class plan extends BaseController
{
    private $planModel;
    private $abonnementModel;

    public function __construct()
    {
        parent::__construct();
        $this->planModel = new PlanModel();
        $this->abonnementModel = new AbonnementModel();
    }

    /**
     * Liste des plans (ADMIN uniquement)
     */
    public function liste()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        $plans = $this->planModel->getTousPlans();
        $stats = $this->planModel->getStatistiquesPlans();

        $this->render('plan/liste', [
            'plans' => $plans,
            'stats' => $stats
        ]);
    }

    /**
     * Formulaire création nouveau plan (ADMIN uniquement)
     */
    public function nouveau()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        $this->render('plan/nouveau');
    }

    /**
     * Créer un nouveau plan (ADMIN uniquement)
     */
    public function creer()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/plan/liste');
            return;
        }

        $this->planModel->nom_plan = trim($_POST['nom_plan'] ?? '');
        $this->planModel->nb_boutiques_max = intval($_POST['nb_boutiques_max'] ?? 1);
        $this->planModel->prix_mensuel = floatval($_POST['prix_mensuel'] ?? 0);
        $this->planModel->description = trim($_POST['description'] ?? '');
        $this->planModel->couleur = trim($_POST['couleur'] ?? '#ff9b44');
        $this->planModel->actif = isset($_POST['actif']) ? 1 : 0;

        if (empty($this->planModel->nom_plan) || $this->planModel->nb_boutiques_max < 1 || $this->planModel->prix_mensuel < 0) {
            setFlash('error', 'Données invalides. Vérifiez les champs obligatoires.');
            redirect('/plan/nouveau');
            return;
        }

        if ($this->planModel->creerPlan()) {
            setFlash('success', 'Plan créé avec succès !');
        } else {
            setFlash('error', 'Erreur lors de la création du plan.');
        }

        redirect('/plan/liste');
        return;
    }

    /**
     * Formulaire modification plan (ADMIN uniquement)
     */
    public function modifier()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        $id_plan = $_GET['id'] ?? null;

        if (!$id_plan) {
            setFlash('error', 'Plan non spécifié.');
            redirect('/plan/liste');
        }

        $plan = $this->planModel->getPlanById($id_plan);

        if (!$plan) {
            setFlash('error', 'Plan introuvable.');
            redirect('/plan/liste');
        }

        $this->render('plan/modifier', ['plan' => $plan]);
    }

    /**
     * Enregistrer modification plan (ADMIN uniquement)
     */
    public function update()
    {
        // Vérification ADMIN uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('error', 'Accès refusé. Réservé aux administrateurs.');
            redirect('/utilisateur/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/plan/liste');
            return;
        }

        $this->planModel->id_plan = $_POST['id_plan'] ?? null;
        $this->planModel->nom_plan = trim($_POST['nom_plan'] ?? '');
        $this->planModel->nb_boutiques_max = intval($_POST['nb_boutiques_max'] ?? 1);
        $this->planModel->prix_mensuel = floatval($_POST['prix_mensuel'] ?? 0);
        $this->planModel->description = trim($_POST['description'] ?? '');
        $this->planModel->couleur = trim($_POST['couleur'] ?? '#ff9b44');
        $this->planModel->actif = isset($_POST['actif']) ? 1 : 0;

        if (empty($this->planModel->nom_plan) || $this->planModel->nb_boutiques_max < 1 || $this->planModel->prix_mensuel < 0) {
            setFlash('error', 'Données invalides. Vérifiez les champs obligatoires.');
            redirect('/plan/modifier?id=' . $this->planModel->id_plan);
            return;
        }

        if ($this->planModel->modifierPlan()) {
            setFlash('success', 'Plan modifié avec succès !');
        } else {
            setFlash('error', 'Erreur lors de la modification du plan.');
        }

        redirect('/plan/liste');
        return;
    }

    /**
     * Voir les plans disponibles (ACCESSIBLE À TOUS les utilisateurs connectés)
     */
    public function voir()
    {
        $plans = $this->planModel->getPlansActifs();

        // Récupérer infos société pour afficher plan actuel
        $id_societe = getSocieteActive();
        $abonnement_actif = null;
        $numero_contact = '+225 07 00 00 00 00'; // Défaut

        if ($id_societe) {
            $abonnement_actif = $this->abonnementModel->getAbonnementActif($id_societe);

            // Récupérer numéro contact depuis société
            $con = getDatabaseConnection();
            $stmt = $con->prepare("SELECT numero_contact_abonnement FROM societes WHERE id_societe = ?");
            $stmt->execute([$id_societe]);
            $societe = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($societe) {
                $numero_contact = $societe['numero_contact_abonnement'] ?? $numero_contact;
            }
        }

        $this->render('plan/voir', [
            'plans' => $plans,
            'abonnement_actif' => $abonnement_actif,
            'numero_contact' => $numero_contact
        ]);
    }
}
