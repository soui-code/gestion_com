<?php

class BaseController
{
    protected $commonData = [];

    public function __construct()
    {
        // Charge les données communes à TOUTES les pages connectées
        $utilisateurModel = new UtilisateurModel();
        $stockAlerts = $utilisateurModel->getStockAlerts() ?? [];

        $this->commonData = [
            'stockAlerts' => $stockAlerts,
            'alertCount'  => count($stockAlerts),
        ];
    }


    protected function render($view,$data = [])
    {
        // Fusion des données communes + données de la page actuelle
        $allData = array_merge($this->commonData, $data);

        // Crée les variables dans la vue ($alertCount, $stockAlerts, etc.)
        extract($allData);

        // Chemin propre vers la vue
        $viewPath = "./views/" . str_replace(".", "/", $view) . ".php";

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Erreur : Vue introuvable → $viewPath");
        }
    }
}