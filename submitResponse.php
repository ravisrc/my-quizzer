<?php

session_start();
require_once('pdo.php');

if (!isset($_SESSION['userId'])) {
   echo json_encode("You are not logged IN user !");
   return;
}
if (!isset($_POST['questionId'])) {
   echo json_encode("Response doesn't contain the required fields !");
   return;
}

$stmt = $pdo->prepare('SELECT * FROM responses WHERE userId =  :uId AND questionId = :qId');
$stmt->execute(array(
   ':uId' => $_SESSION['userId'],
   ':qId' => $_POST['questionId']
));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!isset($_POST['responseValue'])) {
   $resValue = NULL;
} else {
   $resValue = $_POST['responseValue'];
}

$stmt = $pdo->prepare('SELECT * FROM questions WHERE questionId = :qId');
$stmt->execute(array(
   ':qId' => $_POST['questionId']
));
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if($question == false) {
   echo json_encode("No such question found !");
   return;
}

$stmt = $pdo->prepare('SELECT * FROM questions WHERE questionId = :qId');
$stmt->execute(array(
   ':qId' => $_POST['questionId']
));

if ($row === false) {
   $stmt = $pdo->prepare('INSERT INTO responses (userId,questionId,responseValue,quizId) VALUES (:uId,:qId, :resVal,:quizId)');
   $stmt->execute(array(':uId' => $_SESSION['userId'], ':quizId' => $_SESSION['quizId'], ':qId' => $_POST['questionId'], ':resVal' => $resValue));
} else {
   $stmt = $pdo->prepare('UPDATE responses SET responseValue = :resVal WHERE userId = :uId AND questionId = :qId');
   $stmt->execute(array(':uId' => $_SESSION['userId'], ':qId' => $_POST['questionId'], ':resVal' => $resValue));
}

if(isset($_POST['finish']) && $_POST['finish'] == true){
   $stmt = $pdo->prepare('UPDATE userquizrelation set finished = 1 where userId  =:uId and quizID = :qId');
   $stmt->execute(array(':uId' => $_SESSION['userId'], ':qId' => $_SESSION['quizId']));
}
