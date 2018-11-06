<?php
//view.php Will show all of the positions in an un-numbered list.
//view.php Will show all of the educations in an un-numbered list.


session_start();
require_once "pdo.php";

//Allows the form details to be viewed  = Lexical Scoping, very important to define EVERYTHING that is not a Global Variable
$sqlt = $pdo->query("SELECT * FROM Profile where profile_id=".$_REQUEST['profile_id']);
$row=$sqlt->fetch(PDO::FETCH_ASSOC); {
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
}


?>

<!DOCTYPE>
<html>
<head>
        <title>Rebecca's Profile Database 66cf2135</title>
        <meta charset="UTF-8">
        <meta content="Coursera: Javascript Week 1 Course">
    </head>
<body>
<main>

<p class="name">First Name:</p> <?php echo htmlentities($fn)?></br>

<p class="name">Last Name:</p> <?php echo htmlentities($ln)?></br>

<p class="name">Email:</p> <?php echo htmlentities($em)?></br>

<p class="name">Headline:</p> <?php echo htmlentities($he)?></br>

<p class="name">Summary:</p> <?php echo htmlentities($su)?></br></br>

<p class="name">Positions</p> 
<?php

  $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  echo "<p><ul> ";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    echo ('<li>'.$row['year'].': '.$row['description'].'</li> ');

  }
  echo "</ul></p>";

?>

<p class="name">Education</p> 
<?php
$stmt = $pdo->prepare("SELECT * FROM Education 
LEFT JOIN Institution on education.institution_id = institution.institution_id where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
echo "<p><ul> ";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

  echo ('<li>'.$row['year'].': '.$row['name'].'</li> ');

}
echo "</ul></p>";


?>

<a href="index.php">Done</a>

</main>

<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

</body>
<style>

main {
    border-style: solid;
    border-color: white;
    padding: 20px;
    border-radius: 5px;
    background-color: #83868c;
    text-align: left;
  
   
    
}

body {
    background-color: #6a6c70;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    font-family: didot;
}

.form-row {
  display: flex;
  justify-content: flex-end;
  padding: .5em;

}

.form-row > label {
    padding: .5em 1em .5em 0;
    flex: 1;
  }
  .form-row > input {
    flex: 2;
  }
  .form-row > input{
    padding: .5em;
  }


.wrapper {
    background-color: #6a6c70;
    list-style-type: none;
    padding: 10;
    border-radius: 3px;
  }

a:hover {
    color: black;
}

.log {
    text-decoration: none;
    color: white;
    background-color: #2ca353;
    padding: 8px;
}

a {
    font-family: didot;
    border-radius: 2px;
    text-decoration: none;
    border: none;
    background-color: #c6c4c4;
    color: white;
    padding: 8px;
    margin: 5px;

}

.name {
    color: black;
}

.session {
    color: #58595b;
    font-size: 8pt;
}

</style>
</html>




<!--
