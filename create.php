<?php
//
?>

<!DOCTYPE html>

<html>

<head>
   <title>
      Create your own quiz
   </title>
   <link rel="stylesheet" type="text/CSS" href="style.css">
   <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

</head>

<body>
   <h1>My Quizzer</h1>
   <div>Here goes the profile of the user to the right.</div>

   <h2>Create a Quiz</h2>
   <p style="color: rgb(57, 124, 163); font-weight: 800;">Once saved, cannot be edited (for the convinience of the participants) !</p>


   <form id="createForm">
      <label for="quizName">Quiz Name: </label><input type="text" id="quizName" name="quizName" value="" /></br></br>
      <label for="quizDescription">Quiz Description: </label><input type="text" id="quizDescription" name="quizDescription" value="" size="100" /></br></br>
      <label for="quizType">Quiz Type: </label>
      <select id="quizType" name="quizType" value="">
         <option value="0">--- Select Type ---</option>
         <option value="1">Public</option>
         <option value="2">Private</option>
         <option value="3">Classroom</option>
      </select></br></br>
      <label for="quizMode">Quiz Mode: </label>
      <select id="quizMode" name="quizMode" value="">
         <option value="0">--- Select Type ---</option>
         <option value="1">General</option>
         <option value="2">Real Time</option>
      </select></br></br>
      </br>
      <ol id="questionsSet" class="questionsSet">
         <fieldset>
            <li>
               <br />Score for each question of this level: <br />Correct : <input type="number" id="score1" name="score1" min=1 placeholder="" value="1" />
               Wrong(-ve) : <input value="0" type="number" id="negScore1" name="negScore1" min=0 placeholder="" />
               <ol class="questionsOfLevel" id="level1">
                  <input type="submit" name="addQuestion" value="Add A New Question" class="addNewQues" level="1" /><br /><br />
               </ol>
            </li>
         </fieldset>
         <fieldset>
            <li>
               <br />Score for each question of this level: <br />Correct : <input type="number" id="score2" name="score2" min=1 placeholder="" value="1" />
               Wrong(-ve) : <input value="0" type="number" id="negScore2" name="negScore2" min=0 placeholder="" />
               <ol class="questionsOfLevel">
                  <input type="submit" name="addQuestion" value="Add A New Question" class="addNewQues" level="2" /><br /><br />
               </ol>
            </li>
         </fieldset>
         <fieldset>
            <li>
               <br />Score for each question of this level: <br />Correct : <input type="number" id="score3" name="score3" min=1 placeholder="" value="1" />
               Wrong(-ve) : <input value="0" type="number" id="negScore3" name="negScore3" min=0 placeholder="" />
               <ol class="questionsOfLevel">
                  <input type="submit" name="addQuestion" value="Add A New Question" class="addNewQues" level="3" /><br /><br />
               </ol>
            </li>
         </fieldset>
         <fieldset>
            <li>
               <br />Score for each question of this level: <br />Correct : <input type="number" id="score4" name="score4" min=1 placeholder="" value="1" />
               Wrong(-ve) : <input value="0" type="number" id="negScore4" name="negScore4" min=0 placeholder="" />
               <ol class="questionsOfLevel">
                  <input type="submit" name="addQuestion" value="Add A New Question" class="addNewQues" level="4" /><br /><br />
               </ol>
            </li>
         </fieldset>
      </ol>
      <p style="color: red;" id="error"></p>
      <input type="submit" name="submit" value="Create" id="create" />
   </form>

   <p style="color: rgb(57, 124, 163);">Note: <br />
      Minimum of Five questions and Maximum of Ninety questions are allowed per quiz.<br />
      <b>Public</b> quiz is a type of quiz which can be participated by any one and it will be shown in the search queries.<br />
      <b>Private</b> quiz is a type of quiz which needs a password to be participated and it will be shown in the search queries. Anyone with the quiz Id and password can participate.<br />
      <b>Classroom</b> quiz is a type of quiz which can be participated by the students (temporary ids will be generated as per the count you enter) of your class and it won't be shown in the search queries. Also the people with the quiz Id cannot enter the quiz unless they are your students.
      </p>
      <p>Here goes the footer of the quiz website.</p>

      <script type="text/html" id="questionTemplate">
         <li id="questionLI@number@">
            <p>
               <p id="@level@question@number@"><input class="quesInput" type="text" placeholder="Enter the question here" name="@level@question@number@" />
                  <button onClick="
               numQues--;
                  $('#questionLI@number@').remove();
                  let newArray = quesArray.filter(function(num){return num != @number@});
                  quesArray = newArray.filter(function () {return(1);});
                  "> - </button></p>
               <p><input type="radio" name="answerFor@number@" value="1" checked=true /><input class="optionInput" type="text" id="option@number@-1" name="option@number@-1" placeholder="Option 1" /></p>
               <p><input type="radio" name="answerFor@number@" value="2" /><input class="optionInput" type="text" id="option@number@-2" name="option@number@-2" placeholder="Option 2" /></p>
               <p><input type="radio" name="answerFor@number@" value="3" /><input class="optionInput" type="text" id="option@number@-3" name="option@number@-3" placeholder="Option 3" /></p>
               <p><input type="radio" name="answerFor@number@" value="4" /><input class="optionInput" type="text" id="option@number@-4" name="option@number@-4" placeholder="Option 4" /></p>
            </p>
         </li>
      </script>

      <script src="create.js">
      </script>
</body>

</html>