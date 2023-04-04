<?php
session_start();

if(!isset($_SESSION['zalogowany'])){
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zbieracze</title>
</head>
<body>
    <?php
    echo "<p> Hello ".$_SESSION['user']."!";
    echo "<p>Your items: ".$_SESSION['items']."<p>";
    echo "<p>Your day of the game: ".$_SESSION['dayOfGame']."<p>";
    echo '[<a href = "logout.php">Wylogować się</a>]'
    ?>
    
</body>
</html>