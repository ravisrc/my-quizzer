<?php

session_start();
require_once('pdo.php');

$stmt = $pdo->prepare(
   'SELECT 
         (case 
            when responses.responseValue is null then 0
            when responses.responseValue = questions.correctAns then questions.correctScore
            else -1*questions.wrongScore
         end) as score
         FROM responses JOIN questions JOIN options A JOIN options B 
         ON 
         responses.questionId = questions.questionId and
         questions.questionId = A.questionId and
         A.questionId = B.questionId and
         A.optionValue = questions.correctAns and 
         (case 
            when responses.responseValue is not null then B.optionValue = responses.responseValue
            else 1 
         end) and 
         responses.userId = :uId and 
         responses.quizId = :qId;'
);
$stmt->execute(array('uId' => $_SESSION['userId'], 'qId' => $_SESSION['quizId']));
$responses= $stmt->fetchAll(PDO::FETCH_ASSOC);

$score = null;
foreach($responses as $i => $response){
   $score += $response['score'];
}

$stmt = $pdo->prepare('UPDATE userquizrelation SET quizScore = :qScore where userId = :uId and quizId = :qId');
$stmt->execute(array(':qScore' => $score ,'uId' => $_SESSION['userId'], 'qId' => $_SESSION['quizId']));

echo"<pre>";
echo $score;
echo"</pre>";


