<!DOCTYPE html>
<html>
    <head>
        <title>Modifica Dati Anagrafici</title>
    </head>
    <body>

<?php
require_once "querys.php";

    $idUtente = getUserID();

    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT * FROM datiAnagrafici NATURAL JOIN utente WHERE utente.idUtente ='.$idUtente);
    echo mysqli_error($link);
    $row = mysqli_fetch_assoc($res);

    $nomeUtente = $row['nomeUtente'];
    $sesso = $row['sesso'];
    $data_n = $row['dataNascita'];
    $luogo_n = $row['luogoNascita'];
    $luogo_r = $row['luogoResidenza'];

    if ($sesso == 'm'){
        $sesso_form = '<select name="sesso"><option value="m" selected>Maschio</option><option value="f">Femmina</option></select>';
    }
    else
        $sesso_form = '<select name="sesso"><option value="m">Maschio</option><option value="f" selected>Femmina</option></select>';

    $form = '<div>
               <form action="" method="post">
                   <input name="nomeUtente" value="'.$nomeUtente.'" required>
                   '.$sesso_form.'
                   <input type="date" name="dataNascita" value="'.$data_n.'">
                   <input name="luogoNascita" value="'.$luogo_n.'">
                   <input name="luogoResidenza"value="'.$luogo_r.'">
                   <button>Salva</button>
               </form>
            </div>';

    echo $form;

    if (isset($_POST) && $_POST['nomeUtente'] != null){
        $valori = array('nomeUtente' => $_POST['nomeUtente'],
                        'sesso' => $_POST['sesso'],
                        'dataN' => (($_POST['dataNascita'] == '') ? null : $_POST['dataNascita']),
                        'luogoN' => (($_POST['luogoNascita'] == '') ? null : $_POST['luogoNascita']),
                        'luogoR' => (($_POST['luogoResidenza'] == '') ? null : $_POST['luogoResidenza']));

        updateDatiAnagrafici($valori);
        header('Location: profilo.php');
    }
?>
    </body>
</html>
