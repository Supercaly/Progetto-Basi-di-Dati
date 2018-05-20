<?php
require_once "querys.php";

    $idUtente = getUserID();


    if (isset($_GET)) {
        $idAnnuncio = $_GET['idAnnuncio'];

        $link = connectToDatabase();
        $annuncio = getAnnuncio($idAnnuncio);

        $titoloAnnuncio = $annuncio['titoloAnnuncio'];
        $descrizioneAnnuncio = $annuncio['descrizioneAnnuncio'];
        $idCreatoreAnnuncio = $annuncio['idUtente'];

        $isTheOwner = false;
        if ($idUtente == $idCreatoreAnnuncio)
            $isTheOwner = true;
    }

    if (isset($_POST) && !empty($_POST['utente'])){
        //aggiungo l'utente alle persone interessate all'annuncio
        $stmt = mysqli_prepare($link, 'INSERT INTO annuncio_has_utente (idAnnuncio, idUtente) VALUES (?, ?)');
        mysqli_stmt_bind_param($stmt, 'ii', $idAnnuncio, $idUtente);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Annuncio - <?php echo $titoloAnnuncio;?></title>
    </head>
    <body>
    <div>
        <ul>
            <li><a class="active" href='home.php'>Home</a></li>
            <li><a href="connessioni.php">Connessioni</a></li>
            <li><a href="gruppi.php">Gruppi</a> </li>
            <li><a href="notifiche.php">Notifiche</a></li>
            <li><a href='profilo.php'>Profilo</a></li>
            <li><a href="logout.php">Esci</a></li>
        </ul>
    </div>
        <div>
            <h1><?php echo $titoloAnnuncio;?></h1>
            <p><?php echo $descrizioneAnnuncio?></p>
        </div>
        <?php
            if (!$isTheOwner)
                echo '<form action="" method="post"><input name="utente" value="'.$idUtente.'" hidden><button>Sono Interessato</button></form>';
        ?>
        <div class="div_infoutente">
            <h4>Utenti interessati all'annuncio</h4>
            <?php
                $res = mysqli_query($link, 'SELECT DISTINCT utente.idUtente, datiAnagrafici.nomeUtente 
                                                    FROM annuncio_has_utente NATURAL JOIN utente NATURAL JOIN datiAnagrafici 
                                                      WHERE annuncio_has_utente.idAnnuncio='.$idAnnuncio);
                $utentiInteressati = array();
                while ($row = mysqli_fetch_assoc($res))
                    $utentiInteressati[] = $row;
                foreach ($utentiInteressati as $utente){
                    echo '<div class="div_element"><a href="profilo.php?idUtente='.$utente['idUtente'].'">'.$utente['nomeUtente'].'</a></div>';
                }
            ?>
        </div>
    </body>
</html>