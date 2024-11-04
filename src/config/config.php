<?php
// config.php

// Configuration de la base de données
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'nom_de_la_base',
    'user' => 'utilisateur',
    'pass' => 'mot_de_passe',
];

/**
 * Fonction pour obtenir une connexion PDO à la base de données.
 * @return PDO
 */
function getDBConnection() {
    global $dbConfig;
    
    $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['dbname'];
    
    try {
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
