<?php
require 'models/mouvement.php';

class mouvement extends BaseController {
    
    public function liste() {
        $mouv = new mouvementmodel();
        $result = $mouv->ListeMouvements();
        
        $this->render('mouvement/liste', [
            'mouvements' => $result
        ]);
    }
}
?>
