<?php
session_start();

if(!isset($_POST['login']) ||(!isset($_POST['haslo']))){
    header('Location: index.php');
    exit();
}

require_once "connect.php";

$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

if($polaczenie->connect_errno!=0){
    echo "Error:".$polaczenie->connect_errno; //" Opis: ".$polaczenie->connect_error;
}
else{

    $login = $_POST['login'];
    $haslo = $_POST['haslo'];

    $login = htmlentities($login, ENT_QUOTES, "UTF-8");
    
    //echo $login."<br>";
    //echo $haslo;
    //echo "It's works";

    //$sql = ;

    if ($rezultat = @$polaczenie->query(sprintf("SELECT * FROM   users WHERE user_name='%s'", mysqli_real_escape_string($polaczenie, $login)))){
        $ilu_userow = $rezultat->num_rows;
        if($ilu_userow>0){
            $wiersz = $rezultat->fetch_assoc();

            if(password_verify($haslo, $wiersz['passwordU'])){

            $_SESSION['zalogowany'] = true;
            $_SESSION['id'] = $wiersz['userID'];
            $_SESSION['user'] = $wiersz['user_name'];
            $_SESSION['items'] = $wiersz['items'];
            $_SESSION['dayOfGame'] = $wiersz['dayOfGame'];
            unset($_SESSION['blad']);

            $rezultat->free_result();
            header('Location: gra.php');
            }else{
                $_SESSION['blad'] ='<span style = "color:red">Wrong login or password!</span>';
                header('Location: index.php');
            }

        }else{
            $_SESSION['blad'] ='<span style = "color:red">Wrong login or password!</span>';
            header('Location: index.php');
        }
    }

    $polaczenie->close();
}



?>