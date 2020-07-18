<?php

session_start();

require_once('pdo.php');

$stmt = $pdo->prepare('SELECT B.name, B.userId ,A.finished, A.quizScore FROM userquizrelation A JOIN users B where A.userId = B.userId and A.quizId = :qId and  A.relation = 0 and A.quizTime is not null order by A.quizScore DESC, quizTime ASC');
$stmt->execute(array('qId' => $_SESSION['quizId']));
$ranklist = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rank = false;
$table = "<table id='rankListTableQuiz'><tr><th>Rank</th><th>Name</th><th>Score</th><th>Status</th></tr>";
foreach ($ranklist as $i => $row) {
   $table .= "<tr";
   if ($row['userId'] == $_SESSION['userId']) {
      $rank = $i + 1;
      $table .= " class='focus' ";
   }
   $table .= "><td>" . ($i + 1) . "</td><td class='rankName'>" . $row['name'] . "</td><td>" . (0 + $row['quizScore']) . "</td><td>" . (0 + $row['finished'] === 0 ? "In Progress" : "Finished") . "</td></tr>";
}

$table .= "</table>";

echo($table);
