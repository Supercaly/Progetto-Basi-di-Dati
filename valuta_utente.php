<?php
require_once "querys.php";

    $idUtente = getUserID();

    if (isset($_GET) && !empty($_GET['idUtente'])){
        $idValutato = $_GET['idUtente'];
    }

    if (isset($_POST) && !empty($_POST['valutazione'])){
        $val = $_POST['valutazione'];
        valutaUtente($idValutato, $val);
        header('Location: profilo.php?idUtente='.$idValutato);
    }
?>
<!DOCTYPE html>
<html>
    <head>

    </head>
    <body>
        <h4>Valuta l'utente <?php echo getUserName($idValutato);?></h4>
        <div>
            <form action="" method="post">
                <textarea maxlength="250" name="valutazione" required></textarea>
                <button>Invia valutazione</button>
            </form>
        </div>
    </body>
</html>
