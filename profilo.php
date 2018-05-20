<?php
require_once "querys.php";

$idUtenteVisitatore = getUserID();
$isTheOwner = true;

if (isset($_GET['idUtente']) && !empty($_GET['idUtente'])) {
    $idUtente = $_GET['idUtente'];
    $isTheOwner = false;
}
else
    $idUtente = getUserID();

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title><?php echo getUserName($idUtente);?> - Profilo</title>
</head>
<body>
<div>
    <ul>
        <li><a href='home.php'>Home</a></li>
        <li><a href="connessioni.php">Connessioni</a></li>
        <li><a href="gruppi.php">Gruppi</a> </li>
        <li><a href="notifiche.php">Notifiche</a></li>
        <li><a class="active" href='profilo.php'>Profilo</a></li>
        <li><a href="logout.php">Esci</a></li>
    </ul>
</div>
<h1>Profilo di: <?php echo getUserName($idUtente);?></h1>
<div><?php
        $link = connectToDatabase();
        if (!$isTheOwner && !areFriends($idUtente, $idUtenteVisitatore))
            echo '<div><form style="float: right" action="aggiungi_connessioni.php" method="post"><input name="utente" value="'.$idUtente.'" hidden><button>Aggiungi alle connessioni</button></form></div>';
    ?>
    <div id="div_curriculum" class="div_infoutente">
        <h4>Curriculum</h4>
        <?php
            $query = 'SELECT * FROM datiAnagrafici AS anag WHERE anag.idDatiAnagrafici = (SELECT u.idDatiAnagrafici FROM utente AS u WHERE u.idUtente = '. $idUtente.')';
            $aaid = mysqli_fetch_assoc(mysqli_query($link, $query));

            echo '<b>Anagrafica</b>';
            if ($isTheOwner)
                echo '<a style="float: right" href="modifica_anag.php">Modifica Dati Anagrafici</a>';
            echo '<div id="div_anagrafica" class="div_element">'.
                   '<br><b>Nome: </b>'.$aaid['nomeUtente']. PHP_EOL.'<b>Sesso: </b>'.$aaid['sesso']. PHP_EOL.
                '<b>Nato il </b>'.$aaid['dataNascita'].' <b>a </b>'.$aaid['luogoNascita'].'<b>e residente a </b>'.$aaid['luogoResidenza'].'</div>';
            echo '<b>Carriera</b>';

            if ($isTheOwner)
                echo '<a style="float: right" href="modifica_car.php">Modifica Dati Carriera</a>';

            echo '<div id="div_carriera" class="div_element">';
            $query = 'SELECT ts.nomeTitolo, ts.valutazioneConseguita FROM (titoloStudio AS ts NATURAL JOIN carriera_has_titoloStudio AS cts) NATURAL JOIN utente AS u WHERE u.idUtente ='. $idUtente;
            $res = mysqli_query($link, $query);
            $titoliStudio = array();
            while ($row = mysqli_fetch_assoc($res))
                array_push($titoliStudio, $row);

            foreach ($titoliStudio as $titoloStudio){
                echo '<br><b>Titolo Studio: </b>'. $titoloStudio['nomeTitolo'].'<b> Valutazione: </b>'.$titoloStudio['valutazioneConseguita'];
            }

            $query = 'SELECT s.nomeSkill FROM (skill AS s NATURAL JOIN carriera_has_skill) NATURAL JOIN utente AS u WHERE u.idUtente ='. $idUtente;
            $res = mysqli_query($link, $query);
            $skills = array();
            while ($row = mysqli_fetch_assoc($res))
                array_push($skills, $row);

            foreach ($skills as $skill){
                echo '<br><b>Skill: </b>'. $skill['nomeSkill'];
            }

            $query = 'SELECT lp.nomeLavoroPassato, lp.dataInizio, lp.dataFine FROM (lavoroPassato AS lp NATURAL JOIN carriera_has_lavoroPassato) NATURAL JOIN utente AS u WHERE u.idUtente ='. $idUtente;
            $res = mysqli_query($link, $query);
            $lavoriPassati = array();
            while ($row = mysqli_fetch_assoc($res))
                array_push($lavoriPassati, $row);

            foreach ($lavoriPassati as $lavoroPassato){
                echo '<br><b>Lavoro: </b>'. $lavoroPassato['nomeLavoroPassato'].'<b> Data inizio: </b>'.$lavoroPassato['dataInizio'].'<b> Data fine: </b>'.$lavoroPassato['dataFine'];
            }

            $query = 'SELECT c.nomeCompetenza FROM (competenza AS c NATURAL JOIN carriera_has_competenza) NATURAL JOIN utente AS u WHERE u.idUtente ='. $idUtente;
            $res = mysqli_query($link, $query);
            $competenze = array();
            while ($row = mysqli_fetch_assoc($res))
                array_push($competenze, $row);

            foreach ($competenze as $competenza){
                echo '<br><b>Competenza: </b>'. $competenza['nomeCompetenza'];
            }
            echo '</div>';
        ?>
    </div>
    <div id="div_account" class="div_infoutente">
        <?php
        if ($isTheOwner)
            echo '<a style="float: right" href="modifica_acc.php">Modifica Dati Account</a>';
        ?>
        <h4>Account</h4>
        <div class="div_element">
            <?php
                $query = 'SELECT acc.email, 
                                 acc.tipoUtente, 
                                 (CASE WHEN acc.numCartaCredito IS NOT NULL THEN "Si" ELSE "No" END) AS haCartaCredito 
                            FROM datiAccount AS acc NATURAL JOIN utente WHERE utente.idUtente="'.$idUtente.'"';
                $res = mysqli_fetch_assoc(mysqli_query($link, $query));
                echo '<b>Email: </b>'. $res['email'].'<br>';
                echo '<b>Tipo account: </b>'. $res['tipoUtente'].'<br>';
                echo '<b>Carta di credito: </b>'. $res['haCartaCredito'].'<br>';
            ?>
        </div>
    </div>
    <div id="div_valutazione" class="div_infoutente">
        <h4>Valutazioni</h4>
        <?php
        if (!$isTheOwner)
            echo '<button style="float: right"><a href="valuta_utente.php?idUtente='.$idUtente.'">Valuta l\'utente</a></button>'; ?>
        <div class="div_element">
            <?php
                $query = 'SELECT valutazione.valutazione, valutazione.idValutante, datiAnagrafici.nomeUtente 
                            FROM valutazione INNER JOIN utente_has_valutazione on valutazione.idValutazione=utente_has_valutazione.idValutazione 
                              INNER JOIN utente ON valutazione.idValutante=utente.idUtente 
                                NATURAL JOIN datiAnagrafici WHERE utente_has_valutazione.idValutato ='.$idUtente;
                $res = mysqli_query($link, $query);
                $valutazioni = array();
                while ($row = mysqli_fetch_assoc($res))
                    array_push($valutazioni, $row);
                foreach ($valutazioni as $valutazione){
                    echo '<b>Valutazione: </b>'.$valutazione['valutazione'].'<b> Inserita da: </b><a href="profilo.php?idUtente='.$valutazione['idValutante'].'">'. $valutazione['nomeUtente'].'</a><br>';
                }
            ?>
        </div>
    </div>
    <div id="div_connessioni" class="div_infoutente">
        <h4>Connessioni</h4>
        <div class="div_element">
            <?php
                $res = mysqli_query($link, 'SELECT uu.idConnessione, datiAnagrafici.nomeUtente 
                                                    FROM utente_has_utente AS uu INNER JOIN utente on uu.idConnessione=utente.idUtente 
                                                      NATURAL JOIN datiAnagrafici WHERE uu.idUtente='.$idUtente);
                $connessioni = array();
                while ($row = mysqli_fetch_assoc($res))
                    $connessioni[] = $row;
                foreach ($connessioni as $connessione){
                    echo '<div><a href="profilo.php?idUtente='.$connessione['idConnessione'].'">'.$connessione['nomeUtente'].'</a> </div>';
                }
            ?>
        </div>
    </div>
    <div id="div_gruppi" class="div_infoutente">
        <h4>Gruppi</h4>
        <div class="div_element">
            <?php
                $query = 'SELECT ug.idGruppo, gruppo.nomeGruppo FROM utente_has_gruppo AS ug NATURAL JOIN gruppo WHERE ug.idUtente='.$idUtente;
                $res = mysqli_query($link, $query);
                $gruppi = array();
                while ($row = mysqli_fetch_assoc($res))
                    array_push($gruppi, $row);
                foreach ($gruppi as $gruppo){
                    echo '<a href="gruppo.php?idGruppo='.$gruppo['idGruppo'].'&nomeGruppo='.$gruppo['nomeGruppo'].'">'.$gruppo['nomeGruppo'].'</a><br>';
                }
            ?>
        </div>
    </div>
</div>
</body>
</html>