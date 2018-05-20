<?php
require_once "querys.php";

   $idUtente = getUserID();

    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT * FROM datiAccount NATURAL JOIN utente WHERE utente.idUtente ='.$idUtente);
    echo mysqli_error($link);
    $row = mysqli_fetch_assoc($res);

    $email = $row['email'];
    $password = $row['password'];
    $tipoAccount = $row['tipoUtente'];
    $numCarta = $row['numCartaCredito'];


if (isset($_POST) && !empty($_POST)){
    $valori = array('email' => $_POST['email'],
                    'pswd' => $_POST['password'],
                    'tipo' => $_POST['tipoAccount'],
                    'carta' => (($_POST['numCarta'] == '') ? null : $_POST['numCarta']));

    updateDatiAccount($valori);
}


?>

<!DOCTYPE html>
<html>
    <head>
        <title>Modifica Dati Account</title>
    </head>
    <script type="application/javascript">
        function onLoad() {
            var select = document.getElementById("tipoAccount");
            select.value = 'free';
            select.value = '<?php echo $tipoAccount;?>';
        }
        
        function onSelected() {
            var select = document.getElementById("tipoAccount");
            var carta = document.getElementById("dati_carta");
            if (select.value == 'pro'){
                carta.hidden = false;
                carta.value = '<?php echo $numCarta;?>';
            }
            else {
                carta.hidden = true;
                carta.value = null;
            }
        }
    </script>
    <body onload="onLoad()">
        <div>
            <form action="" method="post">
                <input type="email" name="email" value="<?php echo $email;?>" required>
                <input type="password" name="password" value="<?php echo $password;?>" required>
                <select id="tipoAccount" name="tipoAccount" onchange="onSelected()">
                    <option value="free">Account Standard</option>
                    <option value="pro">Account Premium (a pagamento)</option>
                </select>
                <input maxlength="16" name="numCarta" id="dati_carta" value="<?php echo $numCarta;?>">
                <button>Conferma Modifiche</button>
            </form>
        </div>
    </body>
</html>
