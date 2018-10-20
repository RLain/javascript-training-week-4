<?php

session_start();
require_once "pdo.php";

if (! isset ($_SESSION['name'])) {
    die('ACCES DENIED');

}

//If the Cancdel button is pressed go back to home page

if ( isset($_POST['cancel'] ) ) {
    $_SESSION['cancel'] = $_POST['cancel'];
    header("Location: index.php");
    return;
}

//Guardian: Checks that the Profile_ID is present
if (! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = 'Missing profile id';
    header('Location:index.php');
    return;
    
}

//Guardian: Make sure the Profile_ID exists in the Database
$stmt = $pdo ->prepare("SELECT profile_id, user_id, first_name, last_name, headline, summary FROM Profile WHERE profile_id = :pd");  
        $stmt->execute(array(":pd" => $_GET['profile_id']));
        $row = $stmt ->fetch(PDO::FETCH_ASSOC);
        if ($row == false){
            $_SESSION['error'] = "Bad value for autos_id";
            header('Location:index.php');
            return;
}



if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  )
 {//Main DIV start 
    if (( strlen($_POST['first_name']) < 1 ) or ( strlen($_POST['last_name']) < 1 ) or ( strlen($_POST['email']) < 1 ) or ( strlen($_POST['headline']) < 1 )  or ( strlen($_POST['summary']) < 1 ) ) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
        error_log("Field entry fail ");
        return;
    } 

    elseif (!strpos($_POST['email'],'@')) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
        error_log("Email entry fail ".$_POST['email']." $check");
        return; 
    }

    else {

        $sql ="UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
        WHERE profile_id = :profile_id";
       $stmt = $pdo->prepare($sql);
       $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']));
      
        $_SESSION['success'] = "Record Saved";
        header("Location: index.php");
        return;
       
        }


}//Main DIV end



//Allows the form details to be viewed  = Lexical Scoping, very important to define EVERYTHING that is not a Global Variable
$sqlt = $pdo->query("SELECT * FROM Profile where profile_id=".$_GET['profile_id']);
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
<?php
            if(isset($_SESSION['error'])) {
                echo ('<p style="color:orange">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
}
?>
<form method="post">
        <ul class="wrapper">
          <li class="form-row">
          <label for="first_namename">First Name:</label> <input type="text" name="first_name" id="first_name" size="50" value="<?= $fn ?>"></li>
          <li class="form-row">
          <label for="last_name">Last Name:</label> <input type="text" name="last_name" id="last_name" size="50" value="<?= $ln ?>"></li>
          <li class="form-row">
          <label for="email">Email:</label> <input type="text" name="email" id="email" size="50" value="<?= $em ?>"></li>
          <li class="form-row">
          <label for="headline">Headline:</label><br/>
          <input type="text" name="headline" id="headline" size="50" value="<?= $he ?>"></li>
          <li class="form-row">
          <label for="summary">Summary:</label><br/>
          <textarea name="summary" rows="8" cols="80"><?php echo htmlentities($su) ?></textarea></li> 
          <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
          <input type="submit" value="Save" name="save" id="submit" size="45">
          <input type="submit" value="Cancel" name="cancel" id="cancel" size="45">
        </ul>
</form>
<?php

if ( isset($_SESSION['error']) ) {
    echo('<p style="color: orange;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']); //Flash message code
}
?>
</main>
</body>
<style>

main {
    border-style: solid;
    border-color: white;
    padding: 20px;
    border-radius: 5px;
    background-color: #83868c;
  
   
    
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



.session {
    color: #58595b;
    font-size: 8pt;
}

</style>
</html>