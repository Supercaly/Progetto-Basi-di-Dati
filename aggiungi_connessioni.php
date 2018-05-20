<?php
require_once "querys.php";

    $idUtente = getUserID();


    if (isset($_POST)){
        if (!empty($_POST['utente']))
            $utente = $_POST['utente'];

        if (!empty($_POST['messaggio'])) {
            $messaggio = $_POST['messaggio'];
            inviaRichiestaConnessione($utente, $messaggio);
            header('Location: home.php');
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Aggiungi alle connessioni</title>
    </head>
    <body>
        <div>
            <form action="" method="post">
                <label>Invia un messaggio alla persona che vuoi aggiungere alle connessioni</label><br>
                <input name="utente" value="<?php echo $utente;?>" hidden>
                <textarea name="messaggio" maxlength="250" required></textarea>
                <button>Invia richiesta</button>
            </form>
        </div>
    </body>
</html>