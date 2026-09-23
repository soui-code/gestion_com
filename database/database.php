<?php

function getDatabaseConnection(){
    static $pdo = null;
    if ($pdo === null) {
        try {
            $host = Env::required('DB_HOST');
            $dbname = Env::required('DB_NAME');
            $user = Env::required('DB_USER');
            $pass = Env::get('DB_PASS', '');

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            Logger::debug('Connexion à la base de données établie');
        } catch(PDOException $e) {
            Logger::critical('Erreur de connexion à la base de données', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            if (isDebug()) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            } else {
                die('Erreur de connexion à la base de données. Veuillez contacter l\'administrateur.');
            }
        }
    }
    return $pdo;
}