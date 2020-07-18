<?php

session_start();
require_once('pdo.php');


$stmt = $pdo->prepare('SELECT * FROM quizzes where not (quizType = 3) and quizName like :term');
$stmt->execute(array(':term' => $_GET['term'].'%'));
$i = 0;
$array = array();

while ($i < 10 && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $array[] = $row['quizName'];
   $i++;
}

echo (json_encode($array));
