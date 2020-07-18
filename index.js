$.getJSON("fetchQuizzes.php", {}, function (data) {
   if (data.publicQuizzes.length > 0) {
      $("#topPublicQuizzes").empty();
      $("#topPublicQuizzes").html(data.publicQuizzes);
   }
   if (data.regQuizzes.length > 0) {
      $("#regQuizzes").empty();
      $("#regQuizzes").html(data.regQuizzes);
   }
   if (data.myQuizzes.length > 0) {
      $("#myQuizzes").empty();
      $("#myQuizzes").html(data.myQuizzes);
   }
});

$("#searchQuizBar").autocomplete({
   source: "quizzesList.php",
});
