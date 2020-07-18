<?php
session_start();
require_once('pdo.php');

$publicQuizzes = false;
$myQuizzes = false;
$regQuizzes = false;

$stmt = $pdo->prepare('SELECT quizzes.quizId,quizzes.description,quizzes.quizMode, quizzes.quizName,count(*) FROM quizzes join userquizrelation WHERE quizzes.quizType=1 and quizzes.quizId = userquizrelation.quizId group by quizzes.quizId order by count(*) DESC limit 10');

$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($quizzes as $quiz) {
   $publicQuizzes .= "
         <div class='publicQuiz' id='quiz" . $quiz['quizId'] . "'>
         <a href='info.php?quizId=" . $quiz['quizId'] . "'>
            <h3 class='quizName'>" . $quiz['quizName'] . "</h3>
            <p class='quizDescription'>Description: " . $quiz['description'] . "</p>
            <p class='quizMode'>QuizMode : ".((0+$quiz['quizMode'] === 1)? "Normal" : "Real Time")."
         </a>
         </div>
      ";
}


$stmt = $pdo->prepare('SELECT quizzes.quizId,quizzes.description,quizzes.quizMode, quizzes.quizName FROM quizzes join userquizrelation WHERE userquizrelation.relation = 0 and userquizrelation.userId = :uId and quizzes.quizId = userquizrelation.quizId group by quizzes.quizId ');

$stmt->execute(array(':uId' => 0+$_SESSION['userId']));
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($quizzes as $quiz) {
   $regQuizzes .= "
         <div class='regQuiz' id='quiz" . $quiz['quizId'] . "'>
         <a href='info.php?quizId=" . $quiz['quizId'] . "'>
            <h3 class='quizName'>" . $quiz['quizName'] . "</h3>
            <p class='quizDescription'>Description: " . $quiz['description'] . "</p>
            <p class='quizMode'>QuizMode : " . ((0+$quiz['quizMode'] === 1) ? "Normal" : "Real Time") . "
         </a>
         </div>
      ";
}

$stmt = $pdo->prepare('SELECT * FROM quizzes WHERE quizAdmin = :qAdmin');
$stmt->execute(array(':qAdmin' => $_SESSION['userId']));
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($quizzes as $quiz) {
   $myQuizzes .= "
         <div class='orgQuiz' id='quiz" . $quiz['quizId'] . "'>
         <a href='info.php?quizId=" . $quiz['quizId'] . "'>
            <h3 class='quizName'>" . $quiz['quizName'] . "</h3>
            <p class='quizDescription'>Description: " . $quiz['description'] . "</p>
            <p class='quizMode'>QuizMode : " . ((0+$quiz['quizMode'] === 1) ? "Normal" : "Real Time") . "
         </a>
         </div>
      ";
}

echo json_encode(array('publicQuizzes' => $publicQuizzes, 'regQuizzes'=> $regQuizzes, 'myQuizzes' => $myQuizzes));
