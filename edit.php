<?php

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
 
     //Validate Positions
 
     for($i=1; $i<=9; $i++) {
         if ( ! isset($_POST['year'.$i]) ) continue;
         if ( ! isset($_POST['desc'.$i]) ) continue;
         $year = $_POST['year'.$i];
         $desc = $_POST['desc'.$i];
 
         if ( strlen($year) == 0 || strlen($desc) == 0) {
             $_SESSION['error'] = "All fields are required for the Position";
         header("Location: add.php");
         error_log("Field entry fail ");
         return; 
             
         }
 
         if ( ! is_numeric($year) ) {
             $_SESSION['error'] = "Position year must be numeric";
             header("Location: add.php");
             error_log("Field entry fail ");
             return; 
             
         }
     }
 
     //Validate Education
 
     for($i=1; $i<=9; $i++) {
         if ( ! isset($_POST['edu_year'.$i]) ) continue;
         if ( ! isset($_POST['edu_school'.$i]) ) continue;
         $year = $_POST['edu_year'.$i];
         $school = $_POST['edu_school'.$i];
         if ( strlen($year) == 0 || strlen($school) == 0) {
             $_SESSION['error'] = "All fields are required for the Education";
         header("Location: add.php");
         error_log("Field entry fail ");
         return; 
             
         }
  
         if ( ! is_numeric($year) ) {
             $_SESSION['error'] = "Education year must be numeric";
             header("Location: add.php");
             error_log("Field entry fail ");
             return; 
             
         }
 
 
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
      
      //Position Table: This will clear the previous entry
    $stmt = $pdo->prepare('DELETE from Position where profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    //This inserts the position entries
    insertPositions($pdo, $_REQUEST['profile_id']);

     //Education Table: This will clear the previous entry
     $stmt = $pdo->prepare('DELETE from Education where profile_id = :pid');
     $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

     //This inserts the education entries
     insertEducations($pdo, $_REQUEST['profile_id']);

      
        $_SESSION['success'] = "Profile Updated";
        header("Location: index.php");
        return;


}//Main DIV end


$sql = "SELECT * FROM position WHERE profile_id = :pid ORDER BY rank";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":pid" => $_GET['profile_id']));
$positions = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	$positions[]=$row;
}

$sql = "SELECT year, name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :pid ORDER BY rank";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":pid" => $_GET['profile_id']));
$schools = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	$schools[]=$row;
}


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
        <title>Rebecca's Profile Database c68bd905</title>
        <meta charset="UTF-8">
        <meta content="Coursera: Javascript Week 4 Course - c68bd905">
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
          Education: <input type="submit" id="addEdu" value="+">
<div id="education_fields">
<?php
$countEdu=0;
if (! empty ($schools)){
	foreach ( $schools as $school ) {
		$countEdu++;
		echo('<div id="education'.$countEdu.'"><p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.htmlentities($school['year']).'"/>');
		echo('<input type="button" value="-" onclick="$(\'#education'.$countEdu.'\').remove();return false;"></p>');
		echo('<p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" value="'.htmlentities($school['name']).'"/></div>');
	}
}
?>
</div>
</p>

<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
<?php
$countPos=0;
if (! empty ($positions)){
	foreach ( $positions as $position ) {
		$countPos++;
		echo('<div id="position'.$countPos.'"><p>Year: <input type="text" name="year'.$countPos.'" value="'.htmlentities($position['year']).'"/>');
		echo('<input type="button" value="-" onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>');
		echo('<textarea name="desc'.$countPos.'" rows="8" cols="80">'.htmlentities($position['description']).'</textarea></div>');
	}
}
?>
</div>
          <input type="submit" value="Save" name="save" id="submit" size="45">
          <input type="submit" value="Cancel" name="cancel" id="cancel" size="45">
        </ul>
</form>
</main>
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
  crossorigin="anonymous"></script>
  <script>
countPos = <?= $countPos ?>;
countEdu = <?= $countEdu ?>;

$(document).ready(function(){
	$('#addPos').click(function(event){
		event.preventDefault();
		if (countPos >=9){
			alert("Maximum of nine position entries exceeded");
			return;
		}
		countPos++;
		$('#position_fields').append('<div id="position'+countPos+'"><p>Year: <input type="text" name="year'+countPos+'" value=""/><input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p><textarea name="desc'+countPos+'" rows="8" cols="80"></textarea></div>');	
	});
});

$(document).ready(function(){
	$('#addEdu').click(function(event){
		event.preventDefault();
		if (countPos >=9){
			alert("Maximum of nine education entries exceeded");
			return;
		}
		countEdu++;
		$('#education_fields').append('<div id="education'+countEdu+'"><p>Year: <input type="text" name="edu_year'+countEdu+'" value=""/><input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"></p><p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" /></p></div>');
                $('.school').autocomplete({
			source: "school.php"
		});
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

.ui-helper-hidden-accessible {

display: none;


}

.ui-menu-item:hover {
    background-color: #d9d9db;
    

}

.ui-menu {
    background-color: white;
    color: black;
    list-style-type: none;
    padding: 5px;
    margin: 0px;
    outline: 0px;
    text-align: left; 
    font-family: arial;  
}

.ui-front {
    z-index: 100;
}

.ui-autocomplete {
    position: absolute;
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


