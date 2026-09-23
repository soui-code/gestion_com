<?php

class BaseController
{
    protected $commonData = [];

    public function __construct()
    {
        // Charge les données communes à TOUTES les pages connectées
        $id_societe = getSocieteActive() ?? null;
        $nom_societe = "Gestion Commerciale";
        $logo_societe = null;

        if ($id_societe) {
            try {
                $con = getDatabaseConnection();
                $stmt = $con->prepare("SELECT nom_societe, logo_societe FROM societes WHERE id_societe = ?");
                $stmt->execute([$id_societe]);
                $societe = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($societe) {
                    $nom_societe = $societe['nom_societe'];
                    $logo_societe = $societe['logo_societe'];
                }
            } catch (Exception $e) {
                // Silently fallback if database is not fully set up
            }
        }

        $utilisateurModel = new UtilisateurModel();
        $stockAlerts = [];
        $peremptionAlerts = [];

        if ($id_societe) {
            try {
                $stockAlerts = $utilisateurModel->getStockAlerts($id_societe, $_SESSION['id_boutique'] ?? null) ?? [];
            } catch (Exception $e) {
                Logger::warning('Erreur lors de la récupération des alertes stock', [
                    'error' => $e->getMessage()
                ]);
                $stockAlerts = [];
            }

            try {
                $peremptionAlerts = $utilisateurModel->getPeremptionAlerts($id_societe, $_SESSION['id_boutique'] ?? null) ?? [];
            } catch (Exception $e) {
                Logger::warning('Erreur lors de la récupération des alertes péremption', [
                    'error' => $e->getMessage()
                ]);
                $peremptionAlerts = [];
            }
        }

        $this->commonData = [
            'stockAlerts'       => $stockAlerts,
            'peremptionAlerts'  => $peremptionAlerts,
            'alertCount'        => count($stockAlerts) + count($peremptionAlerts),
            'id_societe'        => $id_societe,
            'id_boutique'       => $_SESSION['id_boutique'] ?? null,
            'nom_societe'       => $nom_societe,
            'logo_societe'      => $logo_societe,
        ];
    }


    protected function render($view, $data = [])
    {
        // Fusion des données communes + données de la page actuelle
        $allData = array_merge($this->commonData, $data);

        // Ajouter le token CSRF aux données
        $allData['csrf_token'] = CSRF::getToken();
        $allData['csrf_field'] = CSRF::field();

        // Crée les variables dans la vue ($alertCount, $stockAlerts, etc.)
        extract($allData);

        // Chemin propre vers la vue
        $viewPath = "./views/" . str_replace(".", "/", $view) . ".php";

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            Logger::error("Vue introuvable", ['view' => $view, 'path' => $viewPath]);
            if (isDebug()) {
                die("Erreur : Vue introuvable → $viewPath");
            } else {
                http_response_code(404);
                require './views/error/error404.php';
            }
        }
    }

    protected function validate($data, $rules)
    {
        require_once './config/validator.php';
        $validator = new Validator($data);

        if (!$validator->validate($rules)) {
            return $validator;
        }

        return null;
    }

    protected function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGetRequest()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}