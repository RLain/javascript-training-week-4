<?php
//edit.php Will support the addition of new position entries, 
//the deletion of any or all of the existing entries, and the 
//modification of any of the existing entries. After the "Save" is done, 
//the data in the database should match whatever positions were on the screen and 
//in the same order as the positions on the screen.


session_start();
require_once "pdo.php";
require_once "util.php";

if (! isset ($_SESSION['name'])) {
    die('ACCESS DENIED');

}

//If the Cancel button is pressed go back to home page

if ( isset($_POST['cancel'] ) ) {
    $_SESSION['cancel'] = $_POST['cancel'];
    header("Location: index.php");
    return;
}

//Guardian: Checks that the Profile_ID is present
if (! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = 'Missing profile id';
    header('Location:index.php');
    return;
    
}

//Guardian: Make sure the Profile_ID exists in the Database
$stmt = $pdo ->prepare("SELECT * FROM Profile WHERE profile_id = :prof and user_id = :uid");  
        $stmt->execute(array(':prof' => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
        $profile = $stmt ->fetch(PDO::FETCH_ASSOC);
        if ($profile == false){
            $_SESSION['error'] = "Could not load profile";
            header('Location:index.php');
            return;
}


if ( isset($_POST['first_name']) && isset($_POST['last_name']) 
&& isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  )
 {//Main DIV start 
    $msg = validateProfile();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }

    $msg = validatePos();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }

       $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
       WHERE profile_id = :prof and user_id = :uid');
       $stmt->execute(array(
        ':prof' => $_REQUEST['profile_id'],
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    
    );
      
        //This will clear the previous entry
       $stmt = $pdo->prepare('DELETE from Position where profile_id = :pid');
       $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

       $rank = 1;

       for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description) 
        VALUES ( :pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );        
        $rank++;

}

      
        $_SESSION['success'] = "Profile Updated";
        header("Location: index.php");
        return;


}//Main DIV end

//Loading the position rows
$positions = loadPOS($pdo, $_REQUEST['profile_id']);



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

$stmt = $pdo->query("SELECT * from Position where profile_id=".$_REQUEST['profile_id']);
$row=$stmt->fetch(PDO::FETCH_ASSOC); {
    $yr = htmlentities($row['year']);
    $dc = htmlentities($row['description']);
}


?>
<!DOCTYPE>
<html>
<head>
        <title>Rebecca's Profile Database e2c4c0b3</title>
        <meta charset="UTF-8">
        <meta content="Coursera: Javascript Week 3 Course">
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
          <li>
          Position: <input type="submit" id="addPos" value="+">
            <div id="position_fields">
              <?php
              $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz ORDER BY rank");
              $stmt->execute(array(":xyz" => $_GET['profile_id']));
              $countPos = 1;
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //var_dump ($row);
                echo (' <div id="position'.$countPos.'">
                <p>Year: <input type="text" name="year'.$countPos.'" value="'.$row['year'].'" />
                <input type="button" value="-"
                    onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>
                <textarea name="desc'.$countPos.'" rows="8" cols="80">'.$row['description'].'</textarea>
                </div> ');
                $countPos += 1;
              }
               ?>
            </div>
    </li>
          <li class="form-row">
          <div id="position_fields"></div></li>
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
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>
<script>
countPos = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        //Description: If this method is called, the default action of the event will not be triggered.
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="<?= $yr ?>" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"><?php echo htmlentities($dc) ?></textarea>\
            </div>');
    });
});

$(document).ready(function(){  
    //countPos++; 
        $('#position_details').append(
            '<li><?= $yr.": ".$dc?><li>' 
            );

});




</script>

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

