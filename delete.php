<?php 

require_once "pdo.php";
session_start(); 

if (! isset ($_SESSION['name'])) {
    die('ACCES DENIED');

}

//Guardian: Make sure there is a Profile ID to delete is ok before submitting.
if (! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = 'Missing Profile id';
    header('Location:index.php');
    return;
    
}


//Guardian: Make sure the Profile_ID exists in the Database
$stmt = $pdo ->prepare("SELECT profile_id, first_name, last_name, email, headline, summary FROM Profile WHERE profile_id = :pd");  
        $stmt->execute(array(":pd" => $_GET['profile_id']));
        $row = $stmt ->fetch(PDO::FETCH_ASSOC);
        if ($row == false){
            $_SESSION['error'] = "Bad value for profile_id";
            header('Location:index.php');
            return;
}


//Guardian: Make sure the Profile ID can be found on the DB before submitting.
if (isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = "Record Deleted";
    header('Location:index.php');
    return;

}



        

?>
<!DOCTYPE>
<html>
    <head>
        <title>Rebecca's Profile Database 202ed379</title>
        <meta charset="UTF-8">
        <meta content="Coursera: Javascript Week 1 Course">
    </head>
    <body>
        <main>
            <h1>Rebecca Lain's Resume Registry</h1>


Confirm: Deleting <h3><p style="color:#f2f2f7"><strong>First Name: <?= htmlentities($row['first_name'])."<br>Second Name: ".($row['last_name'])?></strong></p></h3>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" name="delete" id="submit" value="Delete">
</form>

<a href="index.php">Cancel</a>

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