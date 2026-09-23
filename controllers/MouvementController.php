<?php
require 'models/mouvement.php';

class mouvement extends BaseController {
    
    public function liste() {
        $mouv = new mouvementmodel();
        $mouv->id_societe = getSocieteActive() ?? null;
        $mouv->id_boutique = getBoutiqueActive() ?? null;
        $result = $mouv->ListeMouvements();
        
        $this->render('mouvement/liste', [
            'mouvements' => $result
        ]);
    }
}
?>
