<?php
session_start();
require_once('pdo.php');
if (!isset($_GET['qNo'])) {
   echo json_encode("Question Not Found");
   return;
}

$disabled = false;
$corrAns = false;
$finished = false;
usleep(100 * 1000);
if ($_SESSION['relation'] === 0 && $_SESSION['finished'] !== 1) {

   $stmt = $pdo->prepare('SELECT * FROM UserQuizRelation WHERE quizId = :qID and userId = :uID');
   $stmt->execute(array(':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
   error_log('Fetching the database time.');
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   $time = $row['quizTime'];
   $preset = $time === null ? 0 : $time;

   if (!isset($_SESSION['startTime'])) {
      error_log('Setting the session start time.');
      $_SESSION['startTime'] = time();
   } else {

      $timePassedForQues = time() - $_SESSION['startTime'];

      if ($timePassedForQues - time() < 1000 && time() - $timePassedForQues < 1000) {
         return;
      }

      $stmt = $pdo->prepare('UPDATE userQuizRelation SET quizTime = :qTime WHERE quizId = :qID and userId = :uID');
      $stmt->execute(array(':qTime' => $preset + $timePassedForQues, ':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
      error_log('Updating the database time.');
      if ($preset + $timePassedForQues + 0 > 10000) {
         error_log(
            'Error occured here : questionFetch.php, preset' . $preset . 'timepassed' . $timePassedForQues . 'Session start time' . $_SESSION['startTime']
         );
      }

      $stmt = $pdo->prepare('SELECT * FROM UserQuizRelation WHERE quizId = :qID and userId = :uID');
      $stmt->execute(array(':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
      error_log(
         'Fetching the database time.'
      );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      $finished = 0 + $row['finished'];
      $time = 0 + $row['quizTime'];

      $_SESSION['startTime'] = time();
   }
} else {
   $time = false;
   if ($_SESSION['relation'] === 0) {
      $stmt = $pdo->prepare('SELECT * FROM UserQuizRelation WHERE quizId = :qID and userId = :uID');
      $stmt->execute(array(':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $time = 0 + $row['quizTime'];
   }
   $disabled = " disabled ";
}

$qNo = $_GET['qNo'];

$questionDisplay = false;

$stmt = $pdo->prepare('SELECT count(*) FROM Questions WHERE quizId = :quizId');
$stmt->execute(array(':quizId' => $_SESSION['quizId']));
$numQues = $stmt->fetch(PDO::FETCH_ASSOC);
$numQues = $numQues['count(*)'];

$stmt = $pdo->prepare('SELECT * FROM Questions WHERE questionNumber = :qNo and quizId = :quizId');
$stmt->execute(array(':qNo' => $qNo, ':quizId' => $_SESSION['quizId']));
$question = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM responses WHERE userId = :uId AND questionId = :qId');
$stmt->execute(array(':qId' => $question['questionId'], ':uId' => $_SESSION['userId']));
$response = $stmt->fetch(PDO::FETCH_ASSOC);

$questionDisplay .= "<li class='questions'>Level " . $question['level'] . " : Q.No. " . $question['questionNumber'] . ".<br/>" .  $question['question'];
$stmt = $pdo->prepare('SELECT * FROM options WHERE questionId = :qId ORDER BY optionValue');
$stmt->execute(array('qId' => $question['questionId']));
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);
$done = false;
foreach ($options as $option) {
   if ($_SESSION['relation'] === 0) {
      if ($option['optionValue'] == $response['responseValue']) {
         $checked = " checked ";
         $done = true;
      } else {
         $corrAns = false;
         $checked = false;
      }
      if ($_SESSION['finished'] === 1) {

         if ($option['optionValue'] == $response['responseValue']) {
            $checked = " checked ";
            $corrAns = " incorrAns ";
            $done = true;
         } else {
            $corrAns = false;
            $checked = false;
         }
         if ($option['optionValue'] == $question['correctAns']) {
            $corrAns = " corrAns ";
         } else {
            // $corrAns = false;
         }
      }
   }
   if ($_SESSION['relation'] === 1) {
      if ($option['optionValue'] == $question['correctAns']) {
         $corrAns = " corrAns ";
         $checked = " checked ";
         $done = true;
      } else {
         $checked = false;
         $corrAns = false;
      }
   }
   $questionDisplay .=   "<label for='option" . $option['optionId'] . "'
                              ><p class='option " . $corrAns . " '><input type='radio' 
                                 name='question" . $question['questionNumber'] . "' 
                                 id='option" . $option['optionId'] . "'
                                 value='" . $option['optionValue'] . "'" . $checked . $disabled . "
                           />" . $option['content'] . "
                           </p></label>";
}
$questionDisplay .= "</li>";
$_SESSION['lastRes'] = $question['questionNumber'];
$owner = $_SESSION['relation'];
if ($_SESSION['finished'] === 1 || $_SESSION['relation'] + 0 === 1) {
   $stmt = $pdo->prepare('SELECT count(*) from responses where  questionId = :qId and responseValue is not null and not responseValue = :answer ');
   $stmt->execute(array('answer' => $question['correctAns'], ':qId' => $question['questionId']));
   $wrongAtt = ($stmt->fetch((PDO::FETCH_ASSOC))['count(*)']) + 0;

   $stmt = $pdo->prepare('SELECT count(*) from responses where responseValue = :answer and questionId = :qId');
   $stmt->execute(array('answer' => $question['correctAns'], ':qId' => $question['questionId']));
   $corrAtt = ($stmt->fetch((PDO::FETCH_ASSOC))['count(*)']) + 0;
   $owner = 1;
   $questionDisplay .= "<p class='report'>" . $wrongAtt . " user (s) has given Wrong answers for this question.</p><p class='report'>" . $corrAtt . " user (s) has given Correct answers for this question.</p>";
}
echo json_encode(array('time' => $time, 'owner' => $owner, 'question' => $questionDisplay, 'quizMode' => 0 + $_SESSION['quizMode'], 'numQues' => $numQues, 'qId' => $question['questionId'], 'done' => $done, 'finished' => $_SESSION['finished']));
