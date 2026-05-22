<?php
require 'models/transaction.php';

class transaction extends BaseController{
    public function liste(){
        $transaction = new transactionmodel();
        $result = $transaction->ListeTransaction();

        $this->render('transaction/liste-transaction',[
            'result' => $result,
        ]);
    }
}