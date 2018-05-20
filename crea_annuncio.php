<?php
require_once "querys.php";

    $idUtente = getUserID();

    $isDisabled = true;
    if (canUserCreateAnnuncio($idUtente))
        $isDisabled = false;

    if (isset($_POST) && !empty($_POST['titolo'])){
        creaAnnuncio($_POST['titolo'], $_POST['descrizione'], $idUtente);
        header('location: home.php');
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Crea Annuncio</title>
        <script type="application/javascript">
            function onLoad() {
                var sendButton = document.getElementById('send');
                sendButton.disabled = <?php echo $isDisabled;?>;
            }
        </script>
    </head>
    <body onload="onLoad()">
        <h4>Inserisci un nuovo annuncio di lavoro</h4>
        <p>Completa tutti i campi per aggiungere un nuovo annuncio visibile a tutte le tue connessioni, <br>
            le connessioni di secondo grado, le connessioni di terzo grado e i gruppi di cui fai parte</p>
        <div>
            <form action="" method="post">
                <input name="titolo" placeholder="Titolo annuncio" required>
                <textarea name="descrizione" placeholder="Descrizione annuncio" maxlength="250" required></textarea>
                <button id="send">Aggiungi annuncio</button>
            </form>
            <?php
                if ($isDisabled)
                    echo '<b><p>Non puoi aggiungere un\'annuncio di lavoro perché non sei <a href="modifica_acc.php?idUtente='.$idUtente.'">un\'utente Premium</a></p></b>';
            ?>
        </div>
    </body>
</html>