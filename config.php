<?php
$host = 'localhost'; // ou 127.0.0.1
$dbname = 'ecommerce'; // Remplace par le nom réel de ta base de données
$username = 'root'; // Par défaut sur XAMPP
$password = ''; // Par défaut sur XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
