var presentQues = 1;
var presentQuesId = null;
var numQues = 0;
var quizMode = false;

var timer = 0;

function submitResponse() {
   let answer = $('input[name="question' + presentQues + '"]:checked').val();
   console.log(answer, presentQues);

   console.log({ responseValue: answer, questionId: presentQuesId });
   $.post(
      "submitResponse.php",
      presentQues == numQues
         ? { responseValue: answer, questionId: presentQuesId, finish: true }
         : { responseValue: answer, questionId: presentQuesId },
      function (data) {
         console.log(data);
         evaluateTillNow();

         if (quizMode == 2 || data.owner === 1) {
            $.get("rankList.php", {}, function (data) {
               $("#rankListQuiz").html(data);
            });
         }
      }
   );
}
function clearResponse() {
   console.log({ questionId: presentQuesId });
   $.post("submitResponse.php", { questionId: presentQuesId }, function (data) {
      console.log(data);
      evaluateTillNow();
   });
   evaluateTillNow();
   $("#saveNext").hide();
   $("#skip").show();
}

function evaluateTillNow() {
   $.post("evaluate.php", function () {
      console.log("Succesfully saved the score till now. ");
   });
}

function displayQuestion(qNo) {
   $("#spinner").show();
   $("#question").hide();
   $("#finish").hide();
   $.getJSON("questionFetch.php", { qNo: qNo }, function (data) {
      presentQuesId = data.qId;
      numQues = data.numQues;

      if (data.finished === 1) {
         $("#finish").hide();
         $("#timer").text(data.time);
         var time = parseInt($("#timer").text());
         $("#timer").text(time + 1);
         var hrs = Math.floor(time / 3600);
         var min = Math.floor((time % 3600) / 60);
         var sec = Math.floor(time % 60);
         $("#clock").text(
            sterlize(hrs) + " : " + sterlize(min) + " : " + sterlize(sec)
         );
         $("#clockHeading").text("Time Taken : ");
      }
      if (data.owner === 1) {
         $("#finish").hide();
      }
      if (data.owner === 0) {
         $("#finish").show();
         $("#timer").text(data.time);
         clearInterval(timer);
         timer = setInterval(() => {
            var time = parseInt($("#timer").text());
            $("#timer").text(time + 1);
            var hrs = Math.floor(time / 3600);
            var min = Math.floor((time % 3600) / 60);
            var sec = Math.floor(time % 60);
            $("#clock").text(
               sterlize(hrs) + " : " + sterlize(min) + " : " + sterlize(sec)
            );
         }, 1000);
      }
      $("#question").html(data.question);
      if (qNo > 1 && (quizMode == 1 || data.owner === 1))
         $("#question").append(
            '<button id="prev" class="neutral btn">Previous</button>'
         );

      if (quizMode == 1 && data.owner === 0)
         $("#question").append('<div class="clearBoth"></div>');
      $("#question").append(
         '<button id="clrRes" class="bad btn">Clear Repsonse</button>'
      );
      if (qNo < numQues)
         $("#question").append(
            '<button id="saveNext" class="good btn">Save & Next</button>'
         );
      if (qNo < numQues)
         $("#question").append(
            '<button id="skip" class="neutral btn">Skip</button>'
         );
      if (qNo == numQues && data.owner === 0 && data.finished !== 1) {
         $("#question").append(
            '<button id="saveSubmit" class="good btn">Submit</button>'
         );
      }
      registerEvents(qNo);
      for (let i = 1; i <= numQues; i++) {
         $("#paletteQNo" + i).removeClass("selectedQuestion");
      }
      $("#paletteQNo" + qNo).addClass("selectedQuestion");

      $("#saveNext").hide();
      $("#skip").show();

      $('input[name="question' + presentQues + '"]').change(function () {
         $("#saveNext").show();
         $("#skip").hide();
      });

      if (data.done === true) {
         $("#saveNext").show();
         $("#skip").hide();
      }
      if (data.owner === 1) {
         $("#saveNext").hide();
         $("#clrRes").hide();
         $("#skip").show();
         $("#skip").text("Next");
      }
   }).always(function () {
      $("#spinner").hide();
      $("#question").show();
   });
}

function registerEvents(qNo) {
   $("#skip").click(function () {
      presentQues++;
      displayQuestion(qNo + 1);
      if (quizMode === 2) {
         evaluateTillNow();

         if (quizMode == 2 || data.owner === 1) {
            $.get("rankList.php", {}, function (data) {
               $("#rankListQuiz").html(data);
            });
         }
      }
   });
   $("#prev").click(function () {
      presentQues--;
      displayQuestion(qNo - 1);
   });
   $("#clrRes").click(function () {
      console.log("clearing repsonse");
      $('.option input[type="radio"]').each(function () {
         $(this).prop("checked", false);
      });
      clearResponse();
   });
   $("#saveNext").click(function () {
      if (submitResponse() !== false) {
         displayQuestion(qNo + 1);
         presentQues++;
      }
   });
   $("#saveSubmit").click(function () {
      submitResponse();
      console.log(window.location.href);
      console.log(window.history);
      window.history.back();
      // window.location.replace(
      //    window.location.href.replace("quiz.php", "info.php")
      // );
   });
}

// $(document).on('beforeunload', function () {
//    $.post('timeSave.php', function () {
//       console.log('Saving time.');
//    })
// })

$(window).on("beforeunload", function (e) {
   clearInterval(timer);
   $.post("quizOut.php", function () {
      console.log("Saving time.");
   });
});

$(document).ready(function () {
   $("#spinner").hide();
   $("#finish").click(function () {
      submitResponse();
      presentQues = numQues;
      console.log(window.location.href);
      console.log(window.history);
      window.history.back();
      // window.location.replace(
      //    window.location.href.replace("quiz.php", "info.php")
      // );
   });
   $("#rankListQuiz").hide();

   $("#rankPanel").click(function () {
      $("#rankListQuiz").slideToggle();
      return;
   });
   // console.log(lastResponse);
   // quizId = parseInt($('input[name="quizId"]').val());
   //First of all fetch if any lastAttempt was there so that we can continue from there or else 1
   presentQues = parseInt($('input[name="lastRes"]').val());
   console.log();
   //Then we have to fetch the question and display it in the right place
   //and traversing over the various questions and displaying the buttons as per situation
   $.getJSON("questionFetch.php", { qNo: presentQues }, function (data) {
      numQues = data.numQues;
      if (data.owner === 1) {
         timer = -5;
      }
      quizMode = data.quizMode;
      if (quizMode == 2) {
         for (var i = 1; i <= parseInt(numQues); i++) {
            $("#palette").empty();
            $("#palette").append(
               `<div class="paletteQNo" id="paletteQNo${i}">${i}</div>`
            );
         }
      } else if (data.owner === 0) {
         $("#rankPanel").hide();
      }
      if ((quizMode && quizMode == 1) || data.owner === 1) {
         $("#palette").empty();
         for (var i = 1; i <= parseInt(numQues); i++) {
            $("#palette").append(
               `<div class="paletteQNo" id="paletteQNo${i}" onClick="presentQues=${i};displayQuestion(${i});" >${i}</div>`
            );
         }
      }
      if (quizMode == 2 || data.owner === 1) {
         $.get("rankList.php", {}, function (data) {
            $("#rankListQuiz").html(data);
         });
      }
   });
   displayQuestion(presentQues);
   if (timer === 0) {
   }
});

function sterlize(i) {
   if (i < 10) {
      return "0" + i;
   } else {
      return i;
   }
}
