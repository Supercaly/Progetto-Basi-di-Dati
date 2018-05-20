<?php
require_once "querys.php";


    if (!empty($_POST)){
        $email = $_POST['email_utente'];
        $password = $_POST['pswd_utente'];
        login($email, $password);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>EsameBasi - Accedi</title>
    </head>
    <body>
    <h1>Accedi al servizio</h1>
    <p>Inserisci email e password per poter accedere al sito.</p>
    <form action="" method="post">
        <input type="email" placeholder="email" name="email_utente" required>
        <input type="password" placeholder="password" name="pswd_utente" required>
        <button type="submit">Login</button>
    </form>
    </body>
</html>