<?php
require_once "querys.php";

    $idUtente = getUserID();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <title><?php echo getUserName($idUtente);?>- Connessioni</title>
    </head>
    <body>
    <div>
        <ul>
            <li><a href='home.php'>Home</a></li>
            <li><a class="active" href="connessioni.php">Connessioni</a></li>
            <li><a href="gruppi.php">Gruppi</a> </li>
            <li><a href="notifiche.php">Notifiche</a></li>
            <li><a href='profilo.php'>Profilo</a></li>
            <li><a href="logout.php">Esci</a></li>
        </ul>
    </div>
    <div>
        <form action="" method="post">
            <input name="utente_da_cercare" placeholder="Cerca utente...">
            <button>Cerca</button>
        </form>
    </div>
    <div style="margin-top: 10px">
        <?php

        if (isset($_POST) && !empty($_POST)){
            $cercaUtente = $_POST['utente_da_cercare'];

            $link = connectToDatabase();
            $res = mysqli_query($link, 'SELECT utente.idUtente, datiAnagrafici.nomeUtente FROM datiAnagrafici NATURAL JOIN utente WHERE datiAnagrafici.nomeUtente LIKE "%'. $cercaUtente.'%"');
            $utenti = array();
            while ($row = mysqli_fetch_array($res))
                $utenti[] = $row;

            foreach ($utenti as $utente){
                echo '<div class="div_element" "><a href="profilo.php?idUtente='.$utente['idUtente'].'">'.$utente['nomeUtente'].'</a>';

                mysqli_query($link, 'SELECT * FROM utente_has_utente AS uu WHERE uu.idUtente='.$utente['idUtente'].' AND uu.idConnessione='.$idUtente);
                if (mysqli_affected_rows($link) == 0)
                    echo '<form style="float: right" action="aggiungi_connessioni.php" method="post"><input name="utente" value="'.$utente['idUtente'].'" hidden><button>Aggiungi alle connessioni</button></form>';
                echo '</div>';
            }
        }

        ?>
    </div>
    </body>
</html>