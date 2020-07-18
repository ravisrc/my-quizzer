<?php

session_start();
require_once('pdo.php');
error_log('Came into time save php.');

$stmt = $pdo->prepare('UPDATE userquizrelation SET lastAttempt=:lastRes WHERE quizId = :qId AND userId= :uId');
$stmt->execute(array(':uId' => $_SESSION['userId'], ':qId' => $_SESSION['quizId'], ':lastRes' => $_SESSION['lastRes']));

if (isset($_SESSION['startTime'])) {

   $stmt = $pdo->prepare('SELECT * FROM UserQuizRelation WHERE quizId = :qID and userId = :uID');
   $stmt->execute(array(':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   error_log('Fetching the database time.');

   $time = $row['quizTime'];
   $preset = $time === null ? 0 : $time;

   $timePassedForQues = time() - $_SESSION['startTime'];

   if ($timePassedForQues - time() < 1000 && time() - $timePassedForQues < 1000) {
      return;
   }

   $stmt = $pdo->prepare('UPDATE userQuizRelation SET quizTime = :qTime WHERE quizId = :qID and userId = :uID');
   $stmt->execute(array(':qTime' => $preset + $timePassedForQues, ':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
   if ($preset + $timePassedForQues + 0 > 10000) {
      error_log('Error occured here : quizOut.php, preset' . $preset . 'timepassed' . $timePassedForQues . 'Session start time' . $_SESSION['startTime']);
   }
   error_log('Updating the database time.');


   unset($_SESSION['startTime']);
   unset($_SESSION['lastRes']);
   error_log('Unsetting the start time.');
}
