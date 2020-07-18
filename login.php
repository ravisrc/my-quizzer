<?php
session_start();
require_once('pdo.php');
if (isset($_POST['cancel'])) {
   header('Location: index.php');
   return;
}

if (isset($_POST['login'])) {
   if (strlen($_POST['uName']) === 0 || strlen($_POST['pass'] === 0)) {
      $_SESSION['loginError'] = 'All * Fields are required';
      header('Location: login.php');
      return;
   } else {
      $stmt = $pdo->prepare('SELECT * FROM users WHERE name = :name AND password = :pw');
      $stmt->execute(array(':name' => $_POST['uName'], ':pw' => hash('md5',$_POST['pass'].'123')));
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user === false) {
         $_SESSION['loginError'] = 'Invalid User Name or Password';
         header('Location: login.php');
         return;
      } else {
         $_SESSION['userId'] = $user['userId'];
         if ($user['lastAttempt'] !== NULL)
            $_SESSION['lastRes'] = $user['lastAttempt'];
         else
            $_SESSION['lastRes'] = 1;
         $_SESSION['userName'] = $user['name'];
         $_SESSION['loggedIn'] = "Logged In as " . htmlentities($_SESSION['userName']) . ".";
         header('Location: index.php');
         return;
      }
   }
}

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
$loggedIn = false;
if (isset($_SESSION['loggedIn'])) {
   $loggedIn = $_SESSION['loggedIn'];
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
         <div id="login" class="switchBlocks selected">Log In</div>
         <div id="register" class="switchBlocks"><a href="register.php">Register</a></div>
      </div>
      <div id="authMain">
         <p style="color: red;"><?= $error ?></p>
         <p style="color: green;"><?= $success ?></p>
         <p style="color: green;"><?= $loggedIn ?></p>
         <form method="POST">
            <p class="fields"><label class="label" for="uName">User Name <span class="red">*</span> : </label><input type="text" name="uName" id="uName" size="35" value="Ravi Sri Ram Chowdary" /></p>
            <p class="fields"><label class="label" for="password">Password <span class="red">*</span> : </label><input type="password" name="pass" id="password" size="35" value="Vkanipaka@9" /></p>
            <input type="submit" value="Login" name="login" class="btn good" />
            <input type="submit" value="Cancel" name="cancel" class="btn bad" />
            <p>Do not have an account? <a class="href" href="register.php">Create One</a>!</p>
         </form>
      </div>
   </div>

</body>

</html>