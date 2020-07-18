<?php

session_start();
require_once('pdo.php');

$regButton = "<input type='submit' name='reg' id='reg' class='btn good' value='Register' />";
$deregButton = false;
$contButton = "<input type='submit' name='cont' id='cont' class='btn neutral' value='Continue' />";
$startButton = "<input type='submit' name='start' id='start' class='btn neutral' value='Start' />";
$viewButton = "<input type='submit' name='view' id='view' class='btn neutral' value='View' />";
$reportButton = "<input type='submit' name='view' id='view' class='btn good' value='View Report' />";
$endQuiz = "<input type='submit' name='endQuiz' id='endQuiz' class='btn bad' value='End the Quiz' />";
$endQuiz = false;

if (isset($_GET['quizId']) && is_numeric($_GET['quizId']) && strlen($_GET['quizId']) > 0) {
   $stmt = $pdo->prepare('SELECT * FROM quizzes WHERE quizId =  :quizId');
   $stmt->execute(array(':quizId' => $_GET['quizId']));
   $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($quiz == false) {
      $_SESSION['indexError'] = 'No such quiz Found.';
      header('location: index.php');
      return;
   }
   $quizType = $quiz['quizType'] + 0;
   $_SESSION['quizId'] = $_GET['quizId'];
   $stmt = $pdo->prepare('SELECT count(questionId), correctScore,wrongScore, level from questions where quizId = :qId group by level');
   $stmt->execute(array('qId' => $_SESSION['quizId']));
   $levels = $stmt->fetchAll((PDO::FETCH_ASSOC));
   $rules = false;
   $maxScore = 0;
   $numQues = 0;
   foreach ($levels as $level) {
      $maxScore += ($level['count(questionId)'] + 0) * ($level['correctScore'] + 0);
      $numQues += ($level['count(questionId)'] + 0);
      $rules .= "<p class='level'>" . $level['count(questionId)'] . " Questions of Level " . $level['level'] . "</p>";
      $rules .= "<p class='levelScores'> Correct Score: " . $level['correctScore'] . " Marks & If Incorrect " . $level['wrongScore'] . " Marks will be deducted and 0 Marks if un Attempted</p>";
   }

   $rules = "<p class='rulesHead'>" . "Total " . $numQues . " Question(s) and the quiz is of Max Marks: " . $maxScore . "</p>" . $rules;

   $stmt = $pdo->prepare('SELECT * FROM users WHERE userId = :adminId');
   $stmt->execute(array('adminId' => $quiz['quizAdmin']));
   $quizAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
   $quizAdmin = $quizAdmin['name'];

   $stmt = $pdo->prepare('SELECT * FROM userquizrelation WHERE quizId =  :quizId and userId = :userId');
   $stmt->execute(array(':quizId' => $_GET['quizId'], ':userId' => $_SESSION['userId']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($row === false) {
      if (isset($_POST['reg'])) {
         if (0 + $quizType === 1) {
            $stmt = $pdo->prepare('SELECT * FROM quizzes where quizId = :qId and quizPassword = :qPass ');
            $stmt->execute(array(':qId' => $_GET['quizId'], ':qPass' => hash('md5', $_POST['quizPass'])));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
               $_SESSION['infoError'] = 'Incorrect Password';
               header('location: info.php?quizId=' . $_GET['quizId']);
               return;
            }
         }

         $stmt = $pdo->prepare('INSERT INTO userquizrelation (quizId ,userId,relation,finished) values(:quizId, :userId,0,0)');
         $stmt->execute(array(':quizId' => $_GET['quizId'], ':userId' => $_SESSION['userId']));

         $stmt = $pdo->prepare('SELECT * FROM questions where quizId= :qId');
         $stmt->execute(array(':qId' => $_GET['quizId']));

         while ($question = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt2 = $pdo->prepare('INSERT INTO responses (questionId, userId,quizId) values (:qId, :uId,:quizId) ');
            $stmt2->execute(array(':qId' => $question['questionId'], ':uId' => $_SESSION['userId'], ':quizId' => $_SESSION['quizId']));
         }

         header('Location: info.php?quizId=' . $_GET['quizId']);
         $_SESSION['infoSuccess'] = "Succesfully registered into the quiz.";
         return;
      }
   } else {

      if (isset($_POST['view']) || isset($_POST['start']) || isset($_POST['cont'])) {
         header('Location: quiz.php?quizId=' . $_GET['quizId']);
         return;
      }

      if (isset($_POST['dereg'])) {
         $stmt = $pdo->prepare('DELETE from userquizrelation where quizId = :quizId and userId= :userId');
         $stmt->execute(array(':quizId' => $_GET['quizId'], ':userId' => $_SESSION['userId']));
         header('Location: info.php?quizId=' . $_GET['quizId']);
         $_SESSION['infoSuccess'] = "Succesfully de-registered from the quiz.";
         return;
      }
   }

   $reg = false;
   $regDereg = false;
   $finished = false;
   $status = false;

   if ($row === false) {
      $reg = true;
      $regDereg = $regButton;
   } else {
      $finished = 0 + $row['finished'];
      $regDereg = $deregButton;

      if (0 + $row['relation'] === 0) {
         $relation = 0;
         if ($row['quizTime'] === null) {
            $status = $startButton;
         } else {
            $status = $contButton;
         }
         if ($finished === 1) {
            $status = $reportButton;
         }
      } else if (0 + $row['relation'] === 1) {
         $relation = 1;
         $regDereg = $endQuiz;
         $status = $viewButton;
      }
   }

   $stmt = $pdo->prepare('SELECT B.name, B.userId ,A.finished,A.relation, A.quizScore FROM userquizrelation A JOIN users B where A.userId = B.userId and A.quizId = :qId and  A.relation = 0 and A.quizTime is not null order by A.quizScore DESC, quizTime ASC');
   $stmt->execute(array('qId' => $_SESSION['quizId']));
   $ranklist = $stmt->fetchAll(PDO::FETCH_ASSOC);
   $rank = false;
   $table = "<table id='rankList'><tr><th>Rank</th><th>Name</th><th>Score (" . $maxScore . ")</th><th>Status</th></tr>";
   foreach ($ranklist as $i => $row) {
      $table .= "<tr";
      if ($row['userId'] == $_SESSION['userId']) {
         $rank = $i + 1;
         $table .= " class='focus' ";
      }
      $table .= "><td>" . ($i + 1) . "</td><td>" . $row['name'] . "</td><td>" . (0 + $row['quizScore']) . "</td><td>" . (0 + $row['finished'] === 0 ? "In Progress" : "Finished") . "</td></tr>";
   }
   $table .= "</table>";
   if ($rank !== false)
      $rank = " At present you are standing at position -" . $rank;
   else if ($relation + 0 === 0) {
      $rank = false;
   } else {
      $rank = " You are the organiser of this quiz.";
   }
   if (count($ranklist) === 0) {
      $table = "Still no body attempted this quiz.";
   }

   $success = false;
   if (isset($_SESSION['infoSuccess'])) {
      $success = "<p class='success' style='color: green'>" . $_SESSION['infoSuccess'] . "</p>";
      unset($_SESSION['infoSuccess']);
   }
   $error = false;
   if (isset($_SESSION['infoError'])) {
      $error = "<p class='success' style='color: red'>" . $_SESSION['infoError'] . "</p>";
      unset($_SESSION['infoError']);
   }


   if ($quiz !== false) {
      $quizName = $quiz['quizName'];
      $quizInfo = "<h2 id='quizInfoName'>" . $quiz['quizName'] . "</h2>
      <p id='quizInfoDescription'>" . $quiz['description'] . "</p>
      <p  id='organiser'>Organiser: " . $quizAdmin . "</p>
      " . $success . $error . (($quizType === 2 && $reg === true) ?
         "<input type='password' id='quizPass' name='quizPass' placeholder='Quiz Password' />"
         : false) . $regDereg  . $status;
   }


   //Creating a table of respnses for those people who submitted their quiz
   $result = false;

   // if ($finished == 1) {
   //    $result .= "<table border='1'>";
   //    $result .= "<tr>
   //          <th>Question Number</th>
   //          <th>Question</th>
   //          <th>Response</th>
   //          <th>Correct Answer</th>
   //          <th>Score</th>
   //       </tr>";

   //    $stmt = $pdo->prepare(
   //       'SELECT DISTINCT questions.questionNumber,
   //       (case 
   //          when responses.responseValue is not null then B.content 
   //          else responses.responseValue  
   //       end)
   //       as Response,
   //       (case 
   //          when responses.responseValue is null then 0
   //          when responses.responseValue = questions.correctAns then questions.correctScore
   //          else questions.wrongScore
   //       end) as Score,
   //       A.content as CorrectAnswer,questions.question,questions.level  
   //       FROM responses JOIN questions JOIN options A JOIN options B 
   //       ON 
   //       responses.questionId = questions.questionId and
   //       questions.questionId = A.questionId and
   //       A.questionId = B.questionId and
   //       A.optionValue = questions.correctAns and 
   //       (case 
   //          when responses.responseValue is not null then B.optionValue = responses.responseValue
   //          else 1 
   //       end) and 
   //       responses.userId = :uId order by questions.questionNumber;'
   //    );
   //    $stmt->execute(array('uId' => $_SESSION['userId']));

   //    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   //       $result .= "<tr " . ">
   //          <td>" . $row['questionNumber'] . "</td>
   //          <td>" . $row['question'] . "</td>
   //          <td>" . $row['Response'] . "</td>
   //          <td>" . $row['CorrectAnswer'] . "</td>
   //          <td>" . $row['Score'] . "</td>
   //       </tr>";
   //    }

   //    $result .= "</table>";
   // }
} else {
   $_SESSION['indexError'] = 'No such quiz Found.';
   header('Location: index.php');
   return;
}
?>

<!DOCTYPE html>

<html>

<head>
   <title>
      Info | <?= $quizName ?>
   </title>
   <link rel="stylesheet" type="text/CSS" href="style.css">
</head>

<body>
   <div id="navbar">
      <h1 id="heading">My Quizzer</h1>
      <div id="profile">
         <div id="dPic">
            <img src="img/user.png" />
         </div>
         <p id="profileName"> <?= $_SESSION['userName'] ?></p>
         <p id="logout"><a href="logout.php">Log Out</a></p>
      </div>
   </div>
   <div id="info">
      <form method="post">
         <?php
         ?>
         <?= $quizInfo ?>
      </form>
   </div>
   <div id="rules">
      <p class="rulesHead">Quiz Marking Scheme : </p>
      <?= $rules ?>
   </div>
   <div id="standings">
      <p class="rulesHead">Quiz Standings : </p>
      <p id="rank"> <?= $rank ?></p>
      <?= $table ?>
   </div>
   <div id="result">
      <?= $result ?>
   </div>
</body>

</html>