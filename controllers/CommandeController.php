<?php
require 'models/commande.php';

class commande extends BaseController{
    public function liste(){
        $commande = new commandemodel();
        $result = $commande->ListeCommande();

        $this->render('commande/liste-commande',[
            'result' => $result,
        ]);
    }
}