<?php
session_start();
require_once "pdo.php";

$_SESSION['time'] = time();

if (isset($_POST['cancel']) ) {
    $_SESSION['cancel'] = $_POST['cancel'];
    header("Location:index.php");
    return;
}

//Password is php123, the stored_hash is no longer saved on the PHP file and is now being 
//checked against the details stored on the database
$salt ="XyZzy12*_";

if(isset($_POST['email']) && isset($_POST['pass'])) {
    unset($_SESSION['name']); //This logs out the current user
    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em  AND password = :pw');
    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ((strlen($_POST['email']) <1) && (strlen($_POST['pass']) <1 )) {   
        $_SESSION['error'] = "User name and password are required";
        header("Location: login.php");
        error_log("Login fail "." $check");
        return;
    } 
    
    if (!strpos($_POST['email'],'@')) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        error_log("Login fail ".$_POST['email']." $check");
        return; 
    
  }
    
    elseif ( $row !== false ) {
    $_SESSION['success'] = "Login Success";
    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];
    error_log("Login success ".$_POST['email']);
    header("Location: index.php");
    return;
    } 
     
    elseif ( $row == false ) {
        $_SESSION['error'] = "Incorrect password";
        error_log("Login fail ".$_POST['email']." $check");
        header("Location: login.php");
        return;
    
    }  

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
            <h1>Rebecca Lain's Resume Registry</h1>
            <?php
            if(isset($_SESSION['error'])) {
                echo ('<p style="color:orange">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }

            ?>
          <form class="fields" method="post">
          Email: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="email" id="email"><br>
          Password: <input type="password" name="pass" id="password"><br>
          <input type="submit" onclick="return doValidate();" value="Log In" name="login" id="submit" size="45">
          <input type="submit" value="Cancel" name="cancel" id="cancel" size="45">
          </form>
            <div class="session">
            <?php
            //This shows the Session Start time, so I can check if running accross the various pages
            echo "<p>Session Start: ";
            echo date('Y m d H:i:s', $_SESSION['time']);
            echo "</p>\n";
            ?>
            </div>
            <script >
                function doValidate() {
                    console.log('Validating...');
                    try {
                        //The following confirms if the email & password have been entered
                        em = document.getElementById('email').value;
                        pw = document.getElementById('password').value;
                        console.log("Validating em=" + em + " pw=" + pw);
                        if (em == null || em == "" || pw == null || pw == "") {
                            alert("Both fields must be filled out");
                            return false;
                        }
                        //The following validates the email address
                        if (em.indexOf('@') == -1) {
                            alert("Invalid email address");
                            return false;
                        }
                        return true;
                    } catch (e) {
                        return false;
                    }
                    return false;
                }
                </script>
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

a:hover {
    color: #066b27;
}

a {
    text-decoration: none;
    color: white;
    background-color: #2ca353;
    padding: 8px;
}

.session {
    color: #58595b;
    font-size: 8pt;
}

#submit {

background-color: #6a6c70;
color: white;
border-radius: 2px;
font-family: didot;
}


#cancel {
    background-color: #6a6c70;
    color: white;
    border-radius: 2px;
    font-family: didot;

}
</style>
</html>