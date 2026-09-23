<?php
require 'models/transaction.php';

class transaction extends BaseController{
    public function liste(){
        $transaction = new transactionmodel();
        $transaction->id_societe = getSocieteActive() ?? null;
        $transaction->id_boutique = getBoutiqueActive() ?? null;
        $result = $transaction->ListeTransaction();

        $this->render('transaction/liste-transaction',[
            'result' => $result,
        ]);
    }
}