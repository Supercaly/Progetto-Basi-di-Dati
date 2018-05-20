<?php
require_once "querys.php";

if (isset($_POST)) {

//se è presente il nome utente sono al primo passo della registrazione: devo aggiungere l'utente
    if (isset($_POST['nome_utente'])) {
        //controllo se i valori inseriti rispettano la forma
        if ($_POST['sesso'] == 'n')
            $_POST['sesso'] = null;
        if ($_POST['data_nascita'] == '')
            $_POST['data_nascita'] = null;
        if ($_POST['luogo_nascita'] == '')
            $_POST['luogo_nascita'] = null;
        if ($_POST['luogo_residenza'] == '')
            $_POST['luogo_residenza'] = null;
        if ($_POST['dati_carta'] == '')
            $_POST['dati_carta'] = null;

        $valori = array('nome_utente' => $_POST['nome_utente'] . " " . $_POST['cognome_utente'],
            'email' => $_POST['email_utente'],
            'pswd' => $_POST['password_utente'],
            'tipo' => $_POST['tipo_account'],
            'num_carta' => $_POST['dati_carta'],
            'sesso' => $_POST['sesso'],
            'data_n' => $_POST['data_nascita'],
            'luogo_n' => $_POST['luogo_nascita'],
            'luogo_r' => $_POST['luogo_residenza']);

        addUserToDatabase($valori);
        header('Location: signup2.php');

    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>EsameBasi - Registrati</title>
    <script type="application/javascript">
        //Permette l'inserimento del numero di carta di credito solo se l'account è 'pro'
        function checkAccountType(val) {
            var datiCarta = document.getElementById('dati_carta');
            if (val == 'pro'){
                datiCarta.style.display='block';
                datiCarta.value = null;
            }
            else {
                datiCarta.style.display = 'none';
                datiCarta.value = null;
            }
        }
    </script>
</head>
<body>
<h1>Registrazine al sito</h1>
<p>Completa tutti i campi richiesti per poterti registrare al servizio.</p>
<form action="" method="post">
    <div>
        <input placeholder="Nome" name="nome_utente" required>
        <input placeholder="Cognome" name="cognome_utente" required>
        <input type="email" placeholder="Email" name="email_utente" required>
        <input type="password" placeholder="password" name="password_utente" required>
        <select name="tipo_account" onchange="checkAccountType(this.value)">
            <option value="free">Account Gratuito</option>
            <option value="pro">Account Professionale (a pagamento)</option>
        </select>
        <input maxlength="16"  name="dati_carta" id="dati_carta" placeholder="numero carta di credito" style='display: none'>
    </div>
    <div>
        <select name="sesso">
            <option value="n">Sesso</option>
            <option value="m">Maschio</option>
            <option value="f">Femmina</option>
        </select>
        <input type="date" placeholder="data di nascita" name="data_nascita">
        <input placeholder="luogo di nascita" name="luogo_nascita">
        <input placeholder="luogo di residenza" name="luogo_residenza">
    </div>
    <button>Registrati</button>
</form>


</body>
</html>