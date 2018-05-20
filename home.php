<?php
require_once "querys.php";

    $idUtente = getUserID();
    $nome = getUserName($idUtente);

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <title><?php echo $nome;?> - Home</title>
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
        <h1>Home page di: <?php echo $nome;?></h1>
        <div>
            <button onclick="window.location.href='crea_annuncio.php'">Inserisci un annuncio di lavoro</button>
        </div>
        <div>
            <h2>Tuoi annunci di lavoro</h2>
            <div class="div_annunci">
                <?php
                    $link = connectToDatabase();
                    $res = mysqli_query($link, 'SELECT * FROM annuncio WHERE annuncio.idUtente='.$idUtente);
                    $miei_annunci = array();
                    while ($row = mysqli_fetch_assoc($res))
                        $miei_annunci[] = $row;
                    disconnectFromDatabase($link);
                    foreach ($miei_annunci as $annuncio){
                        echo '<div class="div_annuncio"><h3><a href="annuncio.php?idAnnuncio='.$annuncio['idAnnuncio'].'">'.$annuncio['titoloAnnuncio'].'</a></h3><p>'.$annuncio['descrizioneAnnuncio'].'</p></div>';
                    }
                ?>
            </div>
        </div>
        <div>
            <h2>Annunci di lavoro delle connessioni di 1°, 2°, 3° grado</h2>
        </div>
        <div class="div_annunci">
            <?php
                $annunci = getAnnunciConnessioni($idUtente);
                foreach ($annunci as $item)
                    echo '<div class="div_annuncio"><h3><a href="annuncio.php?idAnnuncio='.$item['idAnnuncio'].'">'.$item['titoloAnnuncio'].'</a></h3><p>'.$item['descrizioneAnnuncio'].'</p>
                <p><b>Inserito da: </b> <a href="profilo.php?idUtente='.$item['idUtente'].'">'.$item['nomeUtente'].'</a></p></div>';
            ?>
            </div>
        </div>
        <div>
            <h2>Annunci di lavoro dei gruppi</h2>
        </div>
        <div class="div_annunci">
            <?php
            $annunci_gruppi = getAnnunciGruppi($idUtente);
            foreach ($annunci_gruppi as $item){
                echo '<div class="div_annuncio"><h3><a href="annuncio.php?idAnnuncio='.$item['idAnnuncio'].'">'.$item['titoloAnnuncio'].'</a></h3><p>'.$item['descrizioneAnnuncio'].'</p>
                <p><b>Inserito da: </b> <a href="profilo.php?idUtente='.$item['idUtente'].'">'.$item['nomeUtente'].'</a> Nel gruppo: <a href="gruppo.php?idGruppo='.$item['idGruppo'].'&nomeGruppo='.$item['nomeGruppo'].'">'.$item['nomeGruppo'].'</a></p></div>';
            }
            ?>
        </div>
        <style type="text/css">
            .div_annunci {

            }
            .div_annuncio {
                margin-left: 20px;
                border-style: groove;
                padding-left: 20px;
            }</style>

    </body>
</html>