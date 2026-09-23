<?php
require 'models/commande.php';

class commande extends BaseController{
    public function liste(){
        $commande = new commandemodel();
        $commande->id_societe = getSocieteActive() ?? null;
        $commande->id_boutique = getBoutiqueActive() ?? null;
        $result = $commande->ListeCommande();

        $this->render('commande/liste-commande',[
            'result' => $result,
        ]);
    }
}