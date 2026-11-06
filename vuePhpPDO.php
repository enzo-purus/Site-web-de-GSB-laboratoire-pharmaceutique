<?php
try
{
// On se connecte à MySQL
$bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
}

catch(Exception $e)
{
// En cas d'erreur, on affiche un message et on arrête tout
die('Erreur : '.$e->getMessage());
}
?>