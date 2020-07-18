<?php
session_start();
require_once('pdo.php');
if (isset($_POST['cancel'])) {
   header('Location: index.php');
   return;
}

//Validating the form
if (isset($_POST['register'])) {
   if (strlen($_POST['uName']) === 0 || strlen($_POST['pass']) === 0 || strlen($_POST['repass']) === 0) {
      $_SESSION['loginError'] = 'All * Fields are required';
      header('Location: register.php');
      return;
   } else if (strlen($_POST['pass']) < 6) {
      $_SESSION['loginError'] = 'The password must be of min 6 chars.';
      header('Location: register.php');
      return;
   } else if ($_POST['pass'] !== $_POST['repass']) {
      $_SESSION['loginError'] = 'The  passwords must match.';
      header('Location: register.php');
      return;
   } else {
      $stmt = $pdo->prepare('SELECT * FROM users WHERE name = :name');
      $stmt->execute(array(':name' => $_POST['uName']));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row != false) {
         $_SESSION['loginError'] = 'The user name already exists.';
         header('Location: register.php');
         return;
      }

      $stmt = $pdo->prepare('INSERT INTO users (name,password) VALUES (:name,:pw)');
      $stmt->execute(array(':name' => $_POST['uName'], ':pw' => hash('md5', $_POST['pass'] . '123')));
      $_SESSION['success'] = 'Succesfully registered. Please login to continue.';
      header('Location: login.php');
      return;
   }
}
//Printing the error
$error = false;
if (isset($_SESSION['loginError'])) {
   $error = $_SESSION['loginError'];
   unset($_SESSION['loginError']);
}
$success = false;
if (isset($_SESSION['success'])) {
   $success = $_SESSION['success'];
   unset($_SESSION['success']);
}
?>

<!DOCTYPE html>

<html>

<head>
   <title>Login Page</title>
   <link rel="stylesheet" type="text/CSS" href="style.css">
   <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body>
   <div id="navbar">
      <h1 id="heading">My Quizzer</h1>
   </div>
   <div id="auth">
      <div id="switchAuth">
         <div id="login" class="switchBlocks "><a href="login.php">Log In</a></div>
         <div id="register" class="switchBlocks selected">Register</div>
      </div>
      <div id="authMain">
         <p style="color: red;"><?= $error ?></p>
         <p style="color: green;"><?= $success ?></p>
         <form method="POST">
            <p class="fields"><label class="label2" for="uName">User Name <span class="red">*</span> : </label><input type="text" name="uName" id="uName" size="35" /></p>
            <p class="fields"><label class="label2" for="password">Password <span class="red">*</span> : </label><input type="password" name="pass" id="password" size="35" /></p>
            <p class="fields"><label class="label2" for="repassword">Re-Enter Password <span class="red">*</span> : </label><input type="password" name="repass" id="repassword" size="35" /></p>
            <input type="submit" value="Register" name="register" class="btn good" />
            <input type="submit" value="Cancel" name="cancel" class="btn bad" />
            <p>Already have an account? <a class="href" href="login.php">Login</a>!</p>
         </form>
      </div>
   </div>
</body>

</html>