<?php
require_once "querys.php";

    $idUtente = getUserID();

    if (isset($_POST) ){
        if (!empty($_POST['utente_da_aggiungere'])) {
            $utenteDaAggiungere = $_POST['utente_da_aggiungere'];

            //aggiungo i due utenti alle loro connessioni
            $link = connectToDatabase();
            $stmt = mysqli_prepare($link, 'INSERT INTO utente_has_utente (idUtente, idConnessione) VALUES (?, ?)');
            mysqli_stmt_bind_param($stmt, 'ii', $idUtente, $utenteDaAggiungere);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_param($stmt, 'ii', $utenteDaAggiungere, $idUtente);
            mysqli_stmt_execute($stmt);

            rimuoviRichiesta($link, $utenteDaAggiungere, $idUtente);
        }
        if (!empty($_POST['utente_da_annullare'])) {
            $utenteDaAggiungere = $_POST['utente_da_annullare'];
            $link = connectToDatabase();
            rimuoviRichiesta($link, $utenteDaAggiungere, $idUtente);
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>EsameBasi - Notifiche</title>
    </head>
    <body>
    <div>
        <ul>
            <li><a href='home.php'>Home</a></li>
            <li><a href="connessioni.php">Connessioni</a></li>
            <li><a href="gruppi.php">Gruppi</a> </li>
            <li><a class="active" href="notifiche.php">Notifiche</a></li>
            <li><a href='profilo.php'>Profilo</a></li>
            <li><a href="logout.php">Esci</a></li>
        </ul>
    </div>
        <div>
            <h4>Richieste di connessioni</h4>
            <?php
                $link = connectToDatabase();
                $res = mysqli_query($link, 'SELECT rc.idUtenteRichiedente, datiAnagrafici.nomeUtente, rc.commentoRichiesta FROM utente_has_richiesta AS uu INNER JOIN richiestaConnessione AS rc ON uu.idRichiesta = rc.idRichiesta INNER JOIN utente ON rc.idUtenteRichiedente=utente.idUtente INNER JOIN datiAnagrafici ON utente.idDatiAnagrafici = datiAnagrafici.idDatiAnagrafici WHERE uu.idUtente='.$idUtente);
                $richieste = array();
                while ($row = mysqli_fetch_assoc($res))
                    $richieste[] = $row;
                foreach ($richieste as $richiesta){
                    echo '<div class="div_element">
                            Richiesta di connessione<br>
                            <form style="float: right" action="" method="post">
                                        <input name="utente_da_aggiungere" value="'.$richiesta['idUtenteRichiedente'].'" hidden>
                                        <button>Acceta richiesta</button>
                            </form>
                            <form style="float: right" action="" method="post">
                                <input name="utente_da_annullare" value="'.$richiesta['idUtenteRichiedente'].'" hidden>
                                <button>Annulla richiesta</button>
                            </form>
                            <b>Da parte di: <a href="profilo.php?idUtente='.$richiesta['idUtenteRichiedente'].'">'.$richiesta['nomeUtente'].'</a></b>
                                <div>
                                    '.$richiesta['commentoRichiesta'].'
                                </div>
                          </div>';
                }
            ?>
        </div>
    </body>
</html>