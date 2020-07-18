var i = 1,
   j = 0;
//We have to keep a track of the number of questions and also an array of numbers of questions !!!!!!!!!!!

let quesArray = new Array();
let numQues = 0;

$(document).ready(function () {
   window.location.replace("#");
   // document.getElementById('quizType').addEventListener(
   //    :oninput
   // );
   $("#quizType").on("input", function () {
      if ($(this).val() == "2") {
         $(this).after(
            '<p id="quizpassP"><label for="quizPassword">Quiz Password: </label><input minLength=6 type="password" id="quizPassword" name="quizPassword" value="" /></p>'
         );
      }
      if ($(this).val() != "2") {
         $("#quizpassP").remove();
      }
      if ($(this).val() == "3") {
         $(this).after(
            '<p id="countPara"><label for="numStudents">Number of students (to generate temporary profiles): </label><input type="number" id="numStudents" min=1 name="numStudents" value="" /></p>'
         );
      }
      if ($(this).val() != "3") {
         $("#countPara").remove();
      }
   });
   quesArray.push(i);
   $("#level1").prepend(
      $("#questionTemplate")
         .html()
         .replace(/@number@/g, i)
         .replace(/@level@/g, 1)
   );
   i++;
   numQues++;
   $(".addNewQues").click(function (e) {
      e.preventDefault();
      quesArray.push(i);
      $(e.target).before(
         $("#questionTemplate")
            .html()
            .replace(/@number@/g, i)
            .replace(/@level@/g, e.target.attributes["level"].value)
      );
      i++;
      numQues++;
   });
   var createForm = document.getElementById("createForm");
   $("#createForm").submit(function (e) {
      e.preventDefault();
      // window.location.hash = "#";
      // window.location.hash = '#quizName';
      // return;

      var formdata = new FormData(createForm);
      formdata.append("questionArrays", quesArray);
      $.ajax({
         url: "createQuiz.php",
         data: formdata,
         processData: false,
         type: "POST",
         contentType: false,
         success: function (data) {
            data = JSON.parse(data);

            if (data.error !== undefined) {
               $("#error").remove();
               console.log(data["error"]);
               window.location.replace("#");
               window.location.replace("#" + data.cause);
               $("#" + data.cause).after(
                  "<p id='error' style='color: red'>" + data.error + "</p>"
               );
               return;
            }
            console.log(data);
            if (data.successStatus) {
               location.replace("index.php");
            }
         },
      });
   });
});

// "+a+"
