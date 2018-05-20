<?php
require_once "querys.php";

    $idUtente = getUserID();

    if (isset($_GET) && !empty($_GET)){
        $nomeGruppo = $_GET['nomeGruppo'];
        $idGruppo = $_GET['idGruppo'];

        if($_GET['esciGruppo'] == 'ok')
            esciDaGruppo($idUtente, $idGruppo);
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <title><?php echo $nomeGruppo?> - Gruppo</title>
    </head>
    <body>
    <div>
    <ul>
        <li><a href='home.php'>Home</a></li>
        <li><a href="connessioni.php">Connessioni</a></li>
        <li><a class="active" href="gruppi.php">Gruppi</a> </li>
        <li><a href='profilo.php'>Profilo</a></li>
        <li><a href="logout.php">Esci</a></li>
    </ul>
    </div>
    <h1>Gruppo <?php echo $nomeGruppo;?></h1>
    <div>
        <?php
        $link = connectToDatabase();
        mysqli_query($link, 'SELECT * FROM utente_has_gruppo AS ug WHERE ug.idUtente='.$idUtente.' AND ug.idGruppo='.$idGruppo);
        if(mysqli_affected_rows($link) > 0){
            echo '<form style="float: right"><input name="idGruppo" value="'.$idGruppo.'" hidden><input name="nomeGruppo" value="'.$nomeGruppo.'" hidden><input name="esciGruppo" value="ok" hidden><button>Esci dal Gruppo</button></form>';
        }

        ?>
    </div>
    <div class="div_infoutente">
        <h4>Caratteristiche del gruppo</h4>
        <div class="div_element">
            <?php
                $link = connectToDatabase();
                $res = mysqli_query($link, 'SELECT caratteristicaComune.nomeCarComune FROM gruppo_has_carComune NATURAL JOIN caratteristicaComune WHERE gruppo_has_carComune.idGruppo ='.$idGruppo);
                $caratteristiche = array();
                while ($row = mysqli_fetch_array($res))
                    $caratteristiche[] = $row;
                foreach ($caratteristiche as $c)
                    echo '<b>Caratteristica in comune: </b>'.$c['nomeCarComune'].'<br>';
            ?>
        </div>
    </div>
    <div class="div_infoutente">
        <h4>Utenti del gruppo</h4>
        <div class="div_element">
            <?php
            $res = mysqli_query($link, 'SELECT utente.idUtente, datiAnagrafici.nomeUtente FROM (utente_has_gruppo NATURAL JOIN utente) NATURAL JOIN datiAnagrafici WHERE utente_has_gruppo.idGruppo = '.$idGruppo);
            $utenti = array();
            while ($row = mysqli_fetch_array($res))
                $utenti[] = $row;
            foreach ($utenti as $u)
                echo '<a href="profilo.php?idUtente='.$u['idUtente'].'">'.$u['nomeUtente'].'</a><br>'
            ?>
        </div>
    </div>
    </body>
</html>
