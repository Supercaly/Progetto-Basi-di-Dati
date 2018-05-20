<?php
require_once "querys.php";

    $idUtente = getUserID();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title><?php echo getUserName($idUtente);?> - Gruppi</title>

</head>
<body>
<div>
    <ul>
        <li><a href='home.php'>Home</a></li>
        <li><a href="connessioni.php">Connessioni</a></li>
        <li><a class="active" href="gruppi.php">Gruppi</a> </li>
        <li><a href="notifiche.php">Notifiche</a></li>
        <li><a href='profilo.php'>Profilo</a></li>
        <li><a href="logout.php">Esci</a></li>
    </ul>
</div>
<div class="div_infoutente">
    <form action="" method="post">
        <input name="gruppo_da_cercare" placeholder="Cerca gruppo...">
        <button>Cerca</button>
    </form>
</div>
<div class="div_infoutente">
    <?php

    if (isset($_POST) && !empty($_POST) && $_POST['crea_grp'] != 'ok'){
        //mostro tutti i gruppi che soddisfano la ricerca
        $cercaGruppo = $_POST['gruppo_da_cercare'];
        $link = connectToDatabase();
        $res = mysqli_query($link, 'SELECT * FROM gruppo WHERE gruppo.nomeGruppo LIKE "%' . $cercaGruppo . '%"');
        $gruppi[] = array();
        while ($row = mysqli_fetch_array($res))
            $gruppi[] = $row;

        echo '<h4>Gruppi</h4><div class="div_element">';
        foreach ($gruppi as $gruppo) {
            if (!empty($gruppo)) {
                echo '<div class="div_element"><a href="gruppo.php?idGruppo='.$gruppo['idGruppo'].'&nomeGruppo='.$gruppo['nomeGruppo'].'">'.$gruppo['nomeGruppo'].'</a>';
                if (!isUserInGroup($idUtente, $gruppo['idGruppo']))
                    echo '<form method="post" style="float: right"><input name="gruppo" value="' . $gruppo['idGruppo'] . '" hidden><input name="gruppo_da_cercare" value="' . $cercaGruppo . '" hidden><button>Entra nel gruppo</button></form>';
                echo '</div>';
            }
        }
        echo '</div>';

        if (isset($_POST['gruppo'])) {
            //l'utente ha chiesto di essere aggiunto ad un gruppo
            $stmt = mysqli_prepare($link, 'INSERT INTO utente_has_gruppo (idUtente, idGruppo) VALUES (?, ?)');
            mysqli_stmt_bind_param($stmt, 'ii', $idUtente, $_POST['gruppo']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        disconnectFromDatabase($link);
    }
    else{
        if (isset($_POST['crea_grp'])){
            //l'utente vuole creare un nuovo gruppo
            $nomeNuovoGruppo = $_POST['nome_gruppo'];
            $carComuni = $_POST['carat_comuni'];
            creaGruppo($idUtente, $nomeNuovoGruppo, $carComuni);
        }
    }

    ?>
</div>
<div class="div_infoutente" id="div_btnCreaGruppo">
    <button onclick="displayCreaGrp()">Crea nuovo Gruppo</button>
</div>
<div id="div_creaGruppo" class="div_infoutente" hidden>
    <form action="" method="post">
        <input name="crea_grp" value="ok" hidden>
        <input name="nome_gruppo" placeholder="Nome del gruppo" required>
        <div id="div_carcom">
        <input name="carat_comuni[]" placeholder="Caratteristica in comune" required>
        <button type="button" onclick="addCarrComune(this)">Aggiungi caratteristiche in comune</button>
        </div>
        <button>Crea Gruppo</button>
    </form>
</div>
<script type="application/javascript">
    var limit = 10;
    var counter = 1;
    function addCarrComune(btn) {
        if (counter == limit){
            alert('Limite di '+ limit + ' caratteristiche in comune raggiunto!!');
            btn.disabled = true;
        }
        else {
            var input = document.createElement('input');
            input.name = 'carat_comuni[]';
            input.setAttribute('name', 'carat_comuni[]');
            input.setAttribute('placeholder', 'Caratteristica in comune');
            input.setAttribute('required', '');
            var div = document.getElementById('div_carcom');
            var newdiv = document.createElement('div');
            newdiv.appendChild(input);
            div.appendChild(newdiv);
            counter++;
        }
    }

    function displayCreaGrp() {
        var div_btn = document.getElementById('div_btnCreaGruppo');
        var div_form = document.getElementById('div_creaGruppo');
        div_btn.hidden = true;
        div_form.hidden = false;
    }
</script>
</body>
</html>