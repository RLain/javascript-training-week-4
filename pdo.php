<?php
$pdo = new PDO('mysql:host=localhost;port=8888;dbname=profiles', 'rebecca', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//NBNBNB: Remember to GRANT ALL ON database.* TO 'fed'@'localhost' IDENTIFIED BY 'zap'; otherwise
//this doesn't work!!!!

?>
