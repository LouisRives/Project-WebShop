<?php
try {
    $conn = new PDO("pgsql:host=pedago01c.univ-avignon.fr;dbname=etd", "uapv2203968", "0uWnrk"); //$conn = new PDO("pgsql:host=pedago01c.univ-avignon.fr;dbname=etd", "uapv2203968", "0uWnrk");
	echo "Connexion à la base de données établie";
	//$conn->exec("SET CHARACTHER SET utf8");
    // Effectuer des opérations sur la base de données...
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}
?>