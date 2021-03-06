<?php   
$bdd = new PDO('mysql:host=localhost;dbname=hairdresser;charset=utf8', 'root', ''); 
//mysql://root:@127.0.0.1:3306/hairdresser
$title = $_GET['title'];
$start = $_GET['start'];
$endd = $_GET['endd'];

$requete = $bdd->prepare("INSERT INTO Event (title, start, endd) VALUES (:title, :start, :endd)");

// On lie la variable $email définie au-dessus au paramètre :email de la requête préparée
$requete->bindValue(':title', $title, PDO::PARAM_STR);
$requete->bindValue(':start', $start, PDO::PARAM_STR);
$requete->bindValue(':endd', $endd, PDO::PARAM_STR);

//On exécute la requête
$requete->execute();