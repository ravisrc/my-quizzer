<?php
session_start();
require_once('pdo.php');

if (isset($_SESSION['quizMode'])) {
   unset($_SESSION['quizMdoe']);
}

if (!isset($_SESSION['userId'])) {
   header('location: login.php');
   return;
}

if (isset($_GET['quizId']) && strlen($_GET['quizId']) !== 0) {

   $stmt = $pdo->prepare('SELECT * FROM quizzes WHERE quizId = :qId');
   $stmt->execute(array(':qId' => $_REQUEST['quizId']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($row !== false) {
      $_SESSION['quizId'] = $_GET['quizId'];
      $_SESSION['quizMode'] = $row['quizMode'];
      $quizName = $row['quizName'];
   } else {
      $_SESSION['indexError'] = 'No such quiz Found.';
      header('location: index.php');
      return;
   }
}

$stmt = $pdo->prepare('SELECT * FROM UserQuizRelation WHERE quizId = :qID and userId = :uID');
$stmt->execute(array(':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row === false) {
   header('location: info.php?quizId=' . $_GET['quizId']);
   return;
}


if ($row['lastAttempt'] !== null)
   $_SESSION['lastRes'] = 0 + $row['lastAttempt'];

$_SESSION['relation'] = 0 + $row['relation'];
$_SESSION['finished'] = 0 + $row['finished'];

if (isset($_SESSION['startTime'])) {
   error_log('Already a start time exists.');
   $stmt = $pdo->prepare('SELECT * FROM UserQuizRelation WHERE quizId = :qID and userId = :uID');
   $stmt->execute(array(':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   $time = $row['quizTime'];
   $preset = $time === null ? 0 : $time;

   $timePassedForQues = time() - $_SESSION['startTime'];

   if ($timePassedForQues - time() < 1000 && time() - $timePassedForQues < 1000) {
      return;
   }

   $stmt = $pdo->prepare('UPDATE userQuizRelation SET quizTime = :qTime WHERE quizId = :qID and userId = :uID');
   $stmt->execute(array(':qTime' => $preset + $timePassedForQues, ':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
   if ($preset + $timePassedForQues + 0 > 10000) {
      error_log(
         'Error occured here : quiz.php, preset' . $preset . 'timepassed' . $timePassedForQues . 'Session start time' . $_SESSION['startTime']
      );
   }
   error_log('Updating the database time.');

   $stmt = $pdo->prepare('SELECT * FROM UserQuizRelation WHERE quizId = :qID and userId = :uID');
   $stmt->execute(array(':uID' => $_SESSION['userId'], ':qID' => $_SESSION['quizId']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   error_log('Fetching the updated database time.');


   $time = $row['quizTime'];

   error_log('Unsetting the start time.');

   unset($_SESSION['startTime']);
}

if (isset($_SESSION['error'])) {
   $error = $_SESSION['error'];
   unset($_SESSION['error']);
} else {
   $error = false;
}
?>

<!DOCTYPE html>

<html>

<head>
   <title>Quiz Website</title>
   <link rel="stylesheet" type="text/CSS" href="style.css">
   <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
   <meta name="viewport" content="width=device-width, initial-scale=1">
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
   <div id="timer" style="display : none;"></div>
   <div id="quizDetails">
      <div id="quizHeading"><?= $quizName ?></div>
      <div id="clock"> -- : -- : -- </div>
      <div id="clockHeading">Timer : </div>
   </div>
   <?= $error ?>
   <div id="content">

      <div id="qPaper">

         <div id="spinner">
            <div class="loader"></div>
         </div>

         <div id="question"></div>
         <div class="clearBoth"></div>


      </div>

      <div id="fixedRight">
         <div id="rankPanel"> Rank List
            <div id="rankListQuiz"></div>
         </div>
         <div id="palette"></div>
         <div id="finishDiv"><button id="finish" class="good btn">Finish the Quiz</button></div>
      </div>
   </div>
   <input type="hidden" name="lastRes" value="<?php if (isset($_SESSION['lastRes'])) echo $_SESSION['lastRes'];
                                                else echo 1; ?>" />
   <script>
   </script>
   <script src="question.js"> </script>
</body>

</html>