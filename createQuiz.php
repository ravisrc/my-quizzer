<?php

session_start();
require_once('pdo.php');

if (strlen($_POST['quizName']) == 0) {
   echo json_encode(array("error" => "Quiz Name is required.", "cause" => "quizName", "resp" => $_POST));
   return;
} else if (strlen($_POST['quizDescription']) == 0) {
   echo json_encode(array("error" => "Quiz Description is required.", "cause" => "quizDescription", "resp" => $_POST));
   return;
} else if (isset($_POST['numStudents']) && strlen($_POST['numStudents']) == 0) {
   echo json_encode(array("error" => "Number of students have to be entered for a Classroom Quiz.", "cause" => "numStudents",  "resp" => $_POST));
   return;
} else if (isset($_POST['quizPassword']) && strlen($_POST['quizPassword']) == 0) {
   echo json_encode(array("error" => "Quiz Password have to be entered for a Private Quiz.", "cause" => "quizPassword",  "resp" => $_POST));
   return;
} else if ($_POST['quizType'] == 0) {
   echo json_encode(array("error" => "Quiz Type needs to be selected.", "cause" => "quizType",  "resp" => $_POST));
   return;
} else if ($_POST['quizMode'] == 0) {
   echo json_encode(array("error" => "Quiz Mode needs to be selected.", "cause" => "quizMode",  "resp" => $_POST));
   return;
}

$allFilled = true;
$notFilled = false;
foreach ($_POST as $i => $postItem) {
   if (strlen($postItem) === 0) {
      $allFilled = false;
      $notFilled = $notFilled ? $notFilled : $i;
   }
}
if ($allFilled === false) {
   echo json_encode(array("error" => "All the score boxes, questions and options needs to be completed.", "cause" => $notFilled,  "resp" => $_POST));
   return;
}

$questionsArray = explode(",", $_POST['questionArrays']);

$searchIfQuiz = $pdo->prepare('SELECT * FROM quizzes WHERE quizName = :qName');
$searchIfQuiz->execute(array('qName' => $_POST['quizName']));
$existingQuiz = $searchIfQuiz->fetch(PDO::FETCH_ASSOC);

if ($existingQuiz !== false) {
   echo json_encode(array("error" => "Already a quiz exists with that name.", "cause" => "quizName", "resp" => $_POST));
   return;
}

// If the quiz is of public type

if ($_POST['quizType'] != 0) {

   if ($_POST['quizType'] != 2) {
      //Insertion of Quiz
      $createQuiz = $pdo->prepare('INSERT INTO quizzes (quizName,quizType,quizMode,description,quizAdmin) VALUES (:qName,:qType,:qMode,:desc,:qAdmin)');
      $createQuiz->execute(array(':qName' => $_POST['quizName'], ':qMode' => $_POST['quizMode'], ':qType' => $_POST['quizType'], ':desc' => $_POST['quizDescription'], ':qAdmin' => $_SESSION['userId']));
   }

   if ($_POST['quizType'] == 2) {
      //Insertion of Quiz
      $createQuiz = $pdo->prepare('INSERT INTO quizzes (quizName,quizType,quizMode,description,quizAdmin,quizPassword ) VALUES (:qName,:qType,:qMode,:desc,:qAdmin,:quizPass)');
      $createQuiz->execute(array(':qName' => $_POST['quizName'], ':qMode' => $_POST['quizMode'], ':qType' => $_POST['quizType'], ':desc' => $_POST['quizDescription'], ':qAdmin' => $_SESSION['userId'], ':quizPass' => hash('md5', $_POST['quizPassword'])));
   }

   $insertQuestionSQL = 'INSERT INTO questions (correctAns,questionNumber,level,question,quizId,correctScore,wrongScore) VALUES (:corrAns,:qNo,:level,:ques,:qId,:corrScore,:wrongScore)';
   $insertOptionSQL = 'INSERT INTO options (questionId,optionValue,content) VALUES (:qId,:optValue,:content)';

   $quizId = $pdo->lastInsertId();

   $stmt = $pdo->prepare('INSERT INTO userquizrelation (userId,quizId,relation) VALUES (:uId,:qId,1)');
   $stmt->execute(array('uId' => $_SESSION['userId'], 'qId' => $quizId));

   $j = 1;  //Number of Questions

   for ($i = 1; $i <= 4; $i++) {
      foreach ($questionsArray as $questionNumber) {
         if (isset($_POST[$i . "question" . $questionNumber])) {

            //Insertion of Questions
            $insertQuestion = $pdo->prepare($insertQuestionSQL);
            $insertQuestion->execute(array(
               ':corrAns' => $_POST["answerFor" . $questionNumber],
               ':qNo' => $j,
               ':level' => $i,
               ':ques' => $_POST[$i . "question" . $questionNumber],
               ':qId' => $quizId,
               ':corrScore' => $_POST["score" . $i],
               ':wrongScore' => $_POST["negScore" . $i]
            ));

            $questionId = $pdo->lastInsertId();

            //Insertion of Options
            for ($k = 1; $k <= 4; $k++) {
               if (isset($_POST["option" . $questionNumber . "-" . $k])) {
                  $insertOption = $pdo->prepare($insertOptionSQL);
                  $insertOption->execute(array(
                     ':qId' => $questionId,
                     ':optValue' => $k,
                     ':content' => $_POST["option" . $questionNumber . "-" . $k]
                  ));
               }
            }

            $j++;
         }
      }
   }

   if ($_POST['quizType'] == 3) {

      $numStudents = 0 + $_POST['numStudents'];
      $insertTempUsersSql = 'INSERT INTO tempUsers (quizID) VALUES (:qId)';
      for ($i = 1; $i <= $numStudents; $i++) {

         $insertTempUsers = $pdo->prepare($insertTempUsersSql);
         $insertTempUsers->execute(array(':qId' => $quizId));
         $lastTemp = $pdo->lastInsertId();

         $updateTempUsers = $pdo->prepare('UPDATE tempUsers SET tempName = :name, password = :pass WHERE tempId = :tempid');
         $updateTempUsers->execute(array(':name' => substr(hash('md5', $lastTemp), 0, -26), ':pass' => substr(hash('md5', "pass" . $lastTemp), 0, -24), ':tempid' => $lastTemp));
      }
   }
}

echo json_encode(array("successStatus" => "Success", "resp" => $_POST, "array" => $questionsArray));
