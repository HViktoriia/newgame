<?php
session_start();

if (isset($_POST['email'])){
    //Udana walidacja
    $wszystko_OK=true;
    $nick = $_POST['nick'];

    if ((strlen($nick)<3) || (strlen($nick)>20))
		{
			$wszystko_OK=false;
			$_SESSION['e_nick']="Nick musi posiadać od 3 do 20 znaków!";
		}
		
		if (ctype_alnum($nick)==false)
		{
			$wszystko_OK=false;
			$_SESSION['e_nick']="Nick może składać się tylko z liter i cyfr (bez polskich znaków)";
		}
$email = $_POST['email'];
$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email)){
    $wszystko_OK=false;
    $_SESSION['e_email']="Podaj poprawny adres e-mail!";
}
//echo $emailB; exit();  

$haslo1 = $_POST['haslo1'];
$haslo2 = $_POST['haslo2'];

if((strlen($haslo1)<8) || (strlen($haslo1)>20)){
    $wszystko_OK=false;
    $_SESSION['e_haslo']="Hasło musi posiadać od 8 do 20 znaków!";
}
if($haslo1!=$haslo2){
    $wszystko_OK=false;
    $_SESSION['e_haslo']="Podane hasła nie są identyczne!";
}

$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
//echo $haslo_hash; exit();

if(!isset($_POST['regulamin'])){
    $wszystko_OK=false;
    $_SESSION['e_regulamin']="Potwierdź akceptację regulaminu!";
}

$sekret = "6LfizFslAAAAAFRLI5xZ-K-k50ijA74j2y_Yn_Ut";
@$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
$odpowiedz = json_decode($sprawdz);
if($odpowiedz->success == false){
    $wszystko_OK=false;
    $_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
}

require_once "connect.php";

mysqli_report(MYSQLI_REPORT_STRICT);
try{
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    
if($polaczenie->connect_errno!=0){
    throw new Exception(mysqli_connect_errno());
} else{
    $rezultat = $polaczenie->query("SELECT userID FROM users WHERE email = '$email'");
    if(!$rezultat) throw new Exception($polaczenie->error);
    $ile_takich_maili = $rezultat->num_rows;
    if($ile_takich_maili>0){
        $wszystko_OK=false;
        $_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
    }

    $rezultat = $polaczenie->query("SELECT userID FROM users WHERE user_name = '$nick'");
    if(!$rezultat) throw new Exception($polaczenie->error);
    $ile_takich_nickow = $rezultat->num_rows;
    if($ile_takich_nickow>0){
        $wszystko_OK=false;
        $_SESSION['e_nick']="Istnieje już gracz w takim nicku! Wybierz inny!";
    }

    if($wszystko_OK==true){
       if($polaczenie->query("INSERT INTO users VALUES('$nick', '$email', '$haslo_hash', NULL, 1, 'drzewo, broń, zborze')")){
        $_SESSION['udanarejestracja']=true;
        header('Location: witamy.php');
       }else{
        if(!$rezultat) throw new Exception($polaczenie->error);
       }
    }

    $polaczenie->close();
}
}
catch(Exception $e){
    echo '<span style = "color:red"> Bląd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie! </span>';
    //echo '<br/> Informacja developerska: '.$e;
}


}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .error{
            color: red;
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
    
    <script src='https://www.google.com/recaptcha/api.js'></script>    
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfizFslAAAAALtQra01DF0W-tPe2E51Dd5xgIyg"></script>
    <!--
    <script>
    grecaptcha.enterprise.ready(function() {
    grecaptcha.enterprise.execute('6LfizFslAAAAALtQra01DF0W-tPe2E51Dd5xgIyg', {action: 'login'}).then(function(token) {
       ...
    });
    });
    </script>
-->

    <title>Nowa gra- załóż nowe konto!</title>
</head>
<body>
    <form action="" method="post">

    Nickname: <br> <input type="text" name="nick"><br>

    <?php
    if(isset($_SESSION['e_nick'])){
        echo '<div class = "error">'.$_SESSION['e_nick'].'</div>';
        unset($_SESSION['e_nick']);
    }
    ?>

    E-mail: <br> <input type="text" name="email"><br>
    <?php
    if(isset($_SESSION['e_email'])){
        echo '<div class = "error">'.$_SESSION['e_email'].'</div>';
        unset($_SESSION['e_email']);
    }
    ?>

    Twoje hasło: <br> <input type="password" name="haslo1"><br>
    <?php
    if(isset($_SESSION['e_haslo'])){
        echo '<div class = "error">'.$_SESSION['e_haslo'].'</div>';
        unset($_SESSION['e_haslo']);
    }
    ?>
    Powtórz hasło: <br> <input type="password" name="haslo2"><br>
    <label><input type="checkbox" name="regulamin" id="">Akceptuję regulamin </label> 
    <?php
    if(isset($_SESSION['e_regulamin'])){
        echo '<div class = "error">'.$_SESSION['e_regulamin'].'</div>';
        unset($_SESSION['e_regulamin']);
    }
    ?>
<div class="g-recaptcha" data-sitekey="6LfizFslAAAAALtQra01DF0W-tPe2E51Dd5xgIyg"></div>
<?php
    if(isset($_SESSION['e_bot'])){
        echo '<div class = "error">'.$_SESSION['e_bot'].'</div>';
        unset($_SESSION['e_bot']);
    }
    ?>
<br>
    <input type="submit" value="Zarejestruj się">

    </form>
</body>
</html>