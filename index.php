<?php

//This is week 3, testing github. 
require_once "pdo.php";

session_start();
$_SESSION['time'] = time();
$login = '<p><a href="login.php" class="log">Please log in</a></p>';
$add = '<p><a href="add.php" class="button">Add New Entry</a></p>';
$logout = '<p><a href="logout.php" class="button">Logout</a></p>';
$norows = "No data";

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
            <h1>Rebecca Lain's Resume Registry</h1>

<?php
            if(isset($_SESSION['error'])) {
                echo ('<p style="color:orange">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }

            if(isset($_SESSION['success'])) {
                echo ('<p style="color:lightgreen">'.htmlentities($_SESSION['success'])."</p>\n");
                unset($_SESSION['success']);
            }

?>
            
<?php
if (!isset($_SESSION['name'])) { 
echo $login;
echo('<table border="1">'."\n");
include 'pdo.php';
$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
echo "<tr><td>";
echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
echo "</td><td>";
echo (htmlentities ($row['headline']));
echo "</td></tr>\n";
    }

if (empty($row) == "0") {
    echo ('<p style="color: orange;">'.$norows."</p>\n"); 
}


}
//ToDO!! I need to add a validation check into the following to make sure only the logged in user can Edit & Delete!!


if (isset($_SESSION['name'])) {
    //$check = $_POST['user_id']);  //Validation to check User can view Edit & Delete buttons
    echo $logout;   

    echo('<table border="1">'."\n");
    include 'pdo.php';
    $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
    echo "</td><td>";
    echo (htmlentities ($row['headline']));
    echo "</td><td>";
    echo ('<a class="table_button" href="edit.php?profile_id='.($row['profile_id']).'">Edit</a> |');
    echo ('<a class="table_button" href="delete.php?profile_id='.($row['profile_id']).'">Delete</a>');
    echo "</td></tr>\n";
    
 }
 echo $add;


}


?>
        </main>
    </body>
<style>

.button {
    font-family: didot;
    border-radius: 2px;
    text-decoration: none;
    border: none;
    background-color: #c6c4c4;
    color: white;
    padding: 8px;
    margin: 5px;
    display: inline-block;




}
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

a:hover {
    color: black;
}

.log {
    text-decoration: none;
    color: white;
    background-color: #2ca353;
    padding: 8px;
}



.table_button {
    font-family: didot;
    border-radius: 2px;
    text-decoration: none;
    border: none;
    color: #c6c4c4;
    padding: 2px;
    margin: 5px;

}

table {
    display: flex;
    justify-content: center;
}



.session {
    color: #58595b;
    font-size: 8pt;
}

a {
    text-decoration: none;
    color: white;
}

</style>
</html>

