<?php

session_start();
require_once "pdo.php";


if (! isset ($_SESSION['name'])) {
    die('ACCESS DENIED');
}


if ( isset($_POST['cancel'] ) ) {
    $_SESSION['cancel'] = $_POST['cancel'];
    header("Location: index.php");
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) 
&& isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  )
 {//Main DIV start 

    //ValidateProfile
    
    if ( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 ||
    strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 || 
    strlen($_POST['summary']) == 0 ) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        error_log("Field entry fail ");
        return;

    }

    if (strpos($_POST['email'],'@') == false ) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: add.php");
        error_log("Field entry fail ");
       return; 
    }

    $stmt = $pdo->prepare('INSERT INTO Profile
    (user_id, first_name, last_name, email, headline, summary)
    VALUES ( :uid, :fn, :ln, :em, :he, :su)');
   $stmt->execute(array(
    ':uid' => $_SESSION['user_id'],
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary'])
  );

  $profile_id = $pdo->lastInsertId();


    //ValidatePOS

    $rank = 1;
    
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required for the References";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
    }

     $stmt = $pdo-> prepare('INSERT INTO Position
         (profile_id, rank, year, description) 
     VALUES ( :pid, :rank, :year, :desc)');
     $stmt->execute(array(
         ':pid' => $profile_id,  //In the add.php file you cannot $_REQUEST a not yet set data point!
         ':rank' => $rank,
         ':year' => $year,
         ':desc' => $desc)
     );        
     $rank++;
    }


        $_SESSION['success'] = "Record added";
        header("Location: index.php");
        return;

}//Main DIV end

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

if(isset($_SESSION['success'])) {
    echo ('<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n");
    unset($_SESSION['success']);
}
           
?>
<form method="post">
        <ul class="wrapper">
          <li class="form-row">
          <label for="first_namename">First Name:</label> <input type="text" name="first_name" id="first_name" size="50"></li>
          <li class="form-row">
          <label for="last_name">Last Name:</label> <input type="text" name="last_name" id="last_name" size="50"></li>
          <li class="form-row">
          <label for="email">Email:</label> <input type="text" name="email" id="email" size="50"></li>
          <li class="form-row">
          <label for="headline">Headline:</label><br/>
          <input type="text" name="headline" id="headline" size="50"></li>
          <li class="form-row">
          <label for="summary">Summary:</label><br/>
          <textarea name="summary" rows="8" cols="80"></textarea></li>
          <input type="hidden" name="profile_id">
          <li>
          Education: <input type="submit" id="addEdu" value="+"><br/></li>
          <li class="form-row">
          <div id="education_fields"></div></li>
          <li>
          Position: <input type="submit" id="addPos" value="+"><br/></li>
          <li class="form-row">
          <div id="position_fields"></div></li>
          <input type="submit" value="Add" name="add" id="submit" size="45">
          <input type="submit" value="Cancel" name="cancel" id="cancel" size="45">
        </ul>
</form>
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
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });


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