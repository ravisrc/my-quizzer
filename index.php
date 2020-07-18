<?php
session_start();
require_once('pdo.php');

if (!isset($_SESSION['userId'])) {
   header('location: login.php');
   return;
}

if(isset($_POST['searchQuizBar']) && strlen($_POST['searchQuizBar']) > 0){
   $stmt = $pdo->prepare('SELECT * FROM quizzes where quizName = :qName');
   $stmt->execute(array(':qName' => $_POST['searchQuizBar']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if($row !== false){
      header('location: info.php?quizId=' . $row['quizId']);
      return;
   } else{
      $_SESSION['indexError'] = 'No quiz Found';
      header('location: index.php');
      return;
   }
}
if(isset($_POST['searchIDBar'])){
   $stmt = $pdo->prepare('SELECT * FROM quizzes where quizId = :qId');
   $stmt->execute(array(':qId' => $_POST['searchIDBar']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if($row !== false){
      header('location: info.php?quizId=' . $row['quizId']);
      return;
   } else{
      $_SESSION['indexError'] = 'No quiz Found';
      header('location: index.php');
      return;
   }
}

$error = false;
if(isset($_SESSION['indexError'])){
   $error = $_SESSION['indexError'];
   unset($_SESSION['indexError']);
}
?>

<!DOCTYPE html>

<html>

<head>
   <title>
      My Quizzer - Create your Own Quiz
   </title>
   <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

   <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
   <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
   <div id="search">

      <p class="text">Search for a quiz to register (search bar) or enter the code of the quiz to enter (search bar).</p>
      <p><?= $error ?></p>
      <form method="POST">
         <input type="text" name="searchQuizBar" id="searchQuizBar" placeholder="Enter the name of the quiz." size="60" />
         <input type="number" name="searchIDBar" id="searchIDBar" placeholder="Quiz Id" size="32" />
         <input type="submit" name="search" value="Search" class='btn neutral' />
      </form>

   </div>

   <div id="topPQ">
      <h2>You may like these (Top Public Quizzes)</h2>
      <div class="listofq" id="topPublicQuizzes">Top Public quizzes based on the number of the registered users.</div>
   </div>

   <div id="regQ">
      <h2>Your Registered Quizzes</h2>
      <div class="listofq" id="regQuizzes">Looks like you are not interested in taking part in quizzes.</div>
   </div>

   <div id="orgQ">
      <h2>Organize your quizzes here</h2>
      <div class="listofq" id="myQuizzes">You have not created a quiz till now. </div>
      <a href="create.php" class="btn" id="createQuiz">Create your own quiz.</a>
   </div>


   <div id="footer">
      <p class="leftFloat">&copy; 2020 Copy Rights Ravi Sri Ram Chowdary</p>
      <p class="rightFloat">All Rights Reserved</p>
   </div>

   <script src="index.js">

   </script>
</body>

</html>