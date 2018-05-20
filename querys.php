<?php
require_once "database_var.php";
/**
 * Si connette al database
 * @return mysqli
 */
function connectToDatabase(){
    $db = dbValues::getInstance();
    $link = new mysqli($db->getHost(), $db->getUser(), $db->getPass(), $db->getDb());
    if (mysqli_connect_errno()){
        die("Connessione con il database fallita... riprovare in un altro momento " . mysqli_connect_error());
    }
    return $link;
}

/**
 * Si disconnette dal database
 * @param $link
 */
function disconnectFromDatabase($link){
    mysqli_close($link);
}

/**
 * Eseguo il login
 * @param $email
 * @param $password
 */
function login($email, $password){
    $link = connectToDatabase();
    mysqli_query($link, 'SELECT * FROM datiAccount AS acc WHERE acc.email = "'. $email .'" AND acc.password = "'. $password .'"');
    $aff_row = mysqli_affected_rows($link);

    if ($aff_row == 0)
        die("Email o Password Errate!!!!");
    else{
        if (!isset($_SESSION)){
            session_start();
            $query = 'SELECT u.idUtente FROM utente AS u NATURAL JOIN datiAccount AS acc WHERE acc.email = "'.$email.'" AND acc.password = "'.$password.'"';
            $id = mysqli_fetch_assoc(mysqli_query($link, $query));

            $_SESSION['sessione_user'] = $id['idUtente'];
            header("Location: home.php");
        }
    }
}

/**
 * Eseguo il logout
 */
function logout(){
    if (!isset($_SESSION)) {
        session_start();
    }
    session_unset();
    session_destroy();
    header('Location: home.php');
}
/**
 * Restituisce l'id dell'utente correntemente loggato
 * @return mixed
 */
function getUserID(){
    if (!isset($_SESSION))
        session_start();
    if (!empty($_SESSION['sessione_user']))
        $idUtente = $_SESSION['sessione_user'];
    else
        header('location: login.php');
    return $idUtente;
}

/**
 * Ritorna il nome dell'utente
 * @param $idUtente
 * @return mixed
 */
function getUserName($idUtente){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT da.nomeUtente FROM datiAnagrafici AS da NATURAL JOIN utente WHERE utente.idUtente ='.$idUtente);
    $nome = mysqli_fetch_assoc($res);
    disconnectFromDatabase($link);
    return $nome['nomeUtente'];
}
/**
 * Inserisco un nuovo utente nel database.
 * Completa i dati delle tabelle utente, datiAnagrafici, datiAccount, datiCarriera
 * @param array $valori
 */
function addUserToDatabase(array $valori){
    $link = connectToDatabase();

    //Controllo che l'email non sia già presente
    mysqli_query($link, 'SELECT * FROM datiAccount WHERE datiAccount.email = "'.$valori['email'].'"');
    if (mysqli_affected_rows($link) == 0) {

        mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
         //INSERISCO I DATI ANAGRAFICI
         $datiAnagrafici = 'INSERT INTO datiAnagrafici (nomeUtente, sesso, dataNascita, luogoNascita, luogoResidenza) VALUES (?,?,?,?,?)';
         $stmt = mysqli_prepare($link, $datiAnagrafici);
         mysqli_stmt_bind_param($stmt, 'sssss', $valori['nome_utente'], $valori['sesso'], $valori['data_n'], $valori['luogo_n'], $valori['luogo_r']);
         mysqli_stmt_execute($stmt);
         $idDatiAnag = mysqli_stmt_insert_id($stmt);

         //INSERISCO DATI ACCOUNT
         $datiAccount = 'INSERT INTO datiAccount(email, password, tipoUtente, numCartaCredito) VALUES (?,?,?,?)';
         $stmt_acc = mysqli_prepare($link, $datiAccount);
         mysqli_stmt_bind_param($stmt_acc, 'sssi', $valori['email'], $valori['pswd'], $valori['tipo'], $valori['num_carta']);
         mysqli_stmt_execute($stmt_acc);
         $idDatiAcc = mysqli_stmt_insert_id($stmt_acc);

         //INSERISCO DATI Carriera
         mysqli_query($link, 'INSERT INTO datiCarriera () VALUES ()');
         $idDatiCarr = mysqli_insert_id($link);

         //INSERISCO L'UTENTE
         $utente = 'INSERT INTO utente (idDatiAnagrafici, idDatiAccount, idDatiCarriera) VALUES (?, ?, ?)';
         $stmt_user = mysqli_prepare($link, $utente);
         mysqli_stmt_bind_param($stmt_user, 'iii', $idDatiAnag, $idDatiAcc, $idDatiCarr);
         mysqli_stmt_execute($stmt_user);
         $last_id = mysqli_stmt_insert_id($stmt_user);

         mysqli_commit($link);

         //considero l'utente come correntemente loggato al sito
         session_start();
         $_SESSION['sessione_user'] = $last_id;
    }
    else {
        die('Impossibile completare la registrazione!! E-mail già presente nel database');
    }
    disconnectFromDatabase($link);
}

/**
 * Aggiunge i datiCarriera dell'utente appena creato
 * @param array $carriera
 */
function addDatiCarrieraToDatabase(array $carriera){
    $idUtente = getUserID();
    if ($idUtente != null){
        $link = connectToDatabase();
        $idCarriera = mysqli_fetch_assoc(mysqli_query($link, 'SELECT utente.idDatiCarriera FROM utente WHERE utente.idUtente = "' . $idUtente . '"'));
        $idCarriera = $idCarriera['idDatiCarriera'];
        //inserisco i titoli di studio
        if (!is_null($carriera['titoli_studio'])){
            for ($i = 0; $i < count($carriera['titoli_studio']); $i++){
                $titolo = $carriera['titoli_studio'][$i];
                $voto = $carriera['studio_voti'][$i];
                addTitolStudio($link, $idCarriera, $titolo, $voto);
            }
        }
        //inserisco le skill
        if (!is_null($carriera['skill'])){
            for ($i = 0; $i < count($carriera['skill']); $i++){
                $skill = $carriera['skill'][$i];
                addSkill($link, $idCarriera, $skill);
            }
        }
        //inserisco i lavori passati
        if (!is_null($carriera['lavoro'])){
            for ($i = 0; $i < count($carriera['lavoro']); $i++){
                $lavoro = $carriera['lavoro'][$i];
                $l_inizio = $carriera['lavoro_inizio'][$i];
                $l_fine = $carriera['lavoro_fine'][$i];
                addLavoroPassato($link, $idCarriera, $lavoro, $l_inizio, $l_fine);
            }
        }
        //inserisco le competenze
        if (!is_null($carriera['competenza'])){
            for ($i = 0; $i < count($carriera['competenza']); $i++){
                $competenza = $carriera['competenza'][$i];
                addCompetenza($link, $idCarriera, $competenza);
            }
        }
        disconnectFromDatabase($link);
    }
    else
        die('Impossibile inserire i dati');
}

/**
 * Aggiungo al database un nuovo titolo di studio
 * @param $link
 * @param $idCarriera
 * @param $nomeTitolo
 * @param $valutazione
 */
function addTitolStudio($link, $idCarriera, $nomeTitolo, $valutazione){
    $res = mysqli_query($link, 'SELECT * FROM titoloStudio WHERE nomeTitolo="'.$nomeTitolo.'" AND valutazioneConseguita='.$valutazione);
    if (mysqli_affected_rows($link) == 0){
        mysqli_query($link, 'INSERT INTO titoloStudio (nomeTitolo, valutazioneConseguita) VALUES ("'.$nomeTitolo.'", "'.$valutazione.'")');
        $idTitolo = mysqli_insert_id($link);
    }
    else if (mysqli_affected_rows($link) > 0){
        $idTitolo = mysqli_fetch_assoc($res);
        $idTitolo = $idTitolo['idTitoloStudio'];
    }
    mysqli_query($link, 'INSERT INTO carriera_has_titoloStudio (idDatiCarriera, idTitoloStudio) VALUES ('.$idCarriera.', '.$idTitolo.')');
}

/**
 * Aggiungo al dataase una nuova skill
 * @param $link
 * @param $idCarriera
 * @param $nomeSkill
 */
function addSkill($link, $idCarriera, $nomeSkill){
    $res = mysqli_query($link, 'SELECT * FROM skill WHERE nomeSkill="'.$nomeSkill.'"');
    if (mysqli_affected_rows($link) == 0){
        mysqli_query($link, 'INSERT INTO skill (nomeSkill) VALUES ("'.$nomeSkill.'")');
        $idSkill = mysqli_insert_id($link);
    }
    else if (mysqli_affected_rows($link) > 0){
        $idSkill = mysqli_fetch_assoc($res);
        $idSkill = $idSkill['idSkill'];
    }
    mysqli_query($link, 'INSERT INTO carriera_has_skill (idDatiCarriera, idSkill) VALUES ('.$idCarriera.', '.$idSkill.')');
}

/**
 * Aggiungo al database una nuova posizione lavorativa passata
 * @param $link
 * @param $idCarriera
 * @param $nomeLavoro
 * @param $dataInizio
 * @param $dataFine
 */
function addLavoroPassato($link, $idCarriera, $nomeLavoro, $dataInizio, $dataFine){
    if ($dataInizio == '')
        $dataInizio = null;
    if ($dataFine == '')
        $dataFine = null;
    $res = mysqli_query($link, 'SELECT * FROM lavoroPassato WHERE nomeLavoroPassato="'.$nomeLavoro.'" AND dataInizio="'.$dataInizio.'" AND dataFine="'.$dataFine.'"');
    if (mysqli_affected_rows($link) == 0){
        $stmt = mysqli_prepare($link, 'INSERT INTO lavoroPassato (nomeLavoroPassato, dataInizio, dataFine) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sss', $nomeLavoro, $dataInizio, $dataFine);
        mysqli_stmt_execute($stmt);
        $idLavoro = mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);
    }
    else if (mysqli_affected_rows($link) > 0){
        $idLavoro = mysqli_fetch_assoc($res);
        $idLavoro = $idLavoro['idLavoroPassato'];
    }
    mysqli_query($link, 'INSERT INTO carriera_has_lavoroPassato (idDatiCarriera, idLavoroPassato) VALUES ('.$idCarriera.', '.$idLavoro.')');
}

/**
 * Aggiungo al database una nuova competenza lavorativa
 * @param $link
 * @param $idCarriera
 * @param $nomeComp
 */
function addCompetenza($link, $idCarriera, $nomeComp){
    $res = mysqli_query($link, 'SELECT * FROM competenza WHERE nomeCompetenza="'.$nomeComp.'"');
    if (mysqli_affected_rows($link) == 0){
        mysqli_query($link, 'INSERT INTO competenza (nomeCompetenza) VALUES ("'.$nomeComp.'")');
        $idComp = mysqli_insert_id($link);
    }
    else if (mysqli_affected_rows($link) > 0){
        $idComp = mysqli_fetch_assoc($res);
        $idComp = $idComp['idCompetenza'];
    }
    mysqli_query($link, 'INSERT INTO carriera_has_competenza (idDatiCarriera, idCompetenza) VALUES ('.$idCarriera.', '.$idComp.')');
}

/**Rimuove il tito di studio
 * @param $idTitolo
 */
function removeTitoloStudio($idTitolo, $idCarriera){
    $link = connectToDatabase();
    mysqli_query($link, 'DELETE FROM carriera_has_titoloStudio WHERE idTitoloStudio='.$idTitolo.' AND idDatiCarriera='.$idCarriera);
    disconnectFromDatabase($link);
}

/**
 * Rimuove la skill
 * @param $idSkill
 */
function removeSkill($idSkill, $idCarriera){
    $link = connectToDatabase();
    mysqli_query($link, 'DELETE FROM carriera_has_skill WHERE idSkill='.$idSkill.' AND idDatiCarriera='.$idCarriera);
    disconnectFromDatabase($link);
}

/**
 * Rimuove il lavoro passato
 * @param $idLavoro
 */
function removeLavoro($idLavoro, $idCarriera){
    $link = connectToDatabase();
    mysqli_query($link, 'DELETE FROM carriera_has_lavoroPassato WHERE idLavoroPassato='.$idLavoro.' AND idDatiCarriera='.$idCarriera);
    disconnectFromDatabase($link);
}

/**
 * Rimuove la competenza
 * @param $idCompetenza
 */
function removeCompetenza($idCompetenza, $idCarriera){
    $link = connectToDatabase();
    mysqli_query($link, 'DELETE FROM carriera_has_competenza WHERE idCompetenza='.$idCompetenza.' AND idDatiCarriera='.$idCarriera);
    disconnectFromDatabase($link);
}

/**
 * Ritona i titoli di studio dell'utente
 * @return array
 */
function getTitoliStudio(){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT titoloStudio.* FROM utente NATURAL JOIN datiCarriera NATURAL JOIN carriera_has_titoloStudio NATURAL JOIN titoloStudio WHERE utente.idUtente='.getUserID());
    $titoliStudio = array();
    while ($row = mysqli_fetch_assoc($res))
        $titoliStudio[] = $row;
    disconnectFromDatabase($link);
    return $titoliStudio;
}

/**
 * Ritorna le skill dell'utente
 * @return array
 */
function getSkills(){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT skill.* FROM utente NATURAL JOIN datiCarriera NATURAL JOIN carriera_has_skill NATURAL JOIN skill WHERE utente.idUtente='.getUserID());
    $skills = array();
    while ($row = mysqli_fetch_assoc($res))
        $skills[] = $row;
    disconnectFromDatabase($link);
    return $skills;
}

/**
 * Ritorna i lavori passati dell'utente
 * @return array
 */
function getLavoriPassati(){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT lavoroPassato.* FROM utente NATURAL JOIN datiCarriera NATURAL JOIN carriera_has_lavoroPassato NATURAL JOIN lavoroPassato WHERE utente.idUtente='.getUserID());
    $lavoriPassati = array();
    while ($row = mysqli_fetch_assoc($res))
        $lavoriPassati[] = $row;
    disconnectFromDatabase($link);
    return $lavoriPassati;
}

/**
 * Ritorna le competenze dell'utente
 * @return array
 */
function getCompetenze(){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT competenza.* FROM utente NATURAL JOIN datiCarriera NATURAL JOIN carriera_has_competenza NATURAL JOIN competenza WHERE utente.idUtente='.getUserID());
    $competenze = array();
    while ($row = mysqli_fetch_assoc($res))
        $competenze[] = $row;
    disconnectFromDatabase($link);
    return $competenze;
}

/**
 * Aggiorna i dati anagrafici dell'utente
 * @param $valori
 */
function updateDatiAnagrafici($valori){
    $link = connectToDatabase();
    $stmt = mysqli_prepare($link, 'UPDATE datiAnagrafici AS anag SET anag.nomeUtente=?, anag.sesso=?,anag.dataNascita=?, anag.luogoNascita=?, anag.luogoResidenza=? WHERE anag.idDatiAnagrafici=(SELECT u.idDatiAnagrafici FROM utente AS u WHERE u.idUtente=?)');
    mysqli_stmt_bind_param($stmt, 'sssssi', $valori['nomeUtente'], $valori['sesso'], $valori['dataN'], $valori['luogoN'], $valori['luogoR'], getUserID());
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    disconnectFromDatabase($link);
}

/**
 * Aggiorna i dati dell'account
 * @param $valori
 */
function updateDatiAccount($valori){
    $link = connectToDatabase();
    mysqli_query($link, 'SELECT * FROM datiAccount WHERE datiAccount.email="'.$valori['email'].'" AND datiAccount.idDatiAccount != (SELECT u.idDatiAccount FROM utente AS u WHERE u.idUtente="'.getUserID().'")');
    if (mysqli_affected_rows($link) == 0){
        //l'email non è mai stata usata da nessuno
        $stmt = mysqli_prepare($link, 'UPDATE datiAccount AS acc SET acc.email=?, acc.password=?, acc.tipoUtente=?, acc.numCartaCredito=? WHERE acc.idDatiAccount = (SELECT u.idDatiAccount FROM utente AS u WHERE u.idUtente=?)');
        mysqli_stmt_bind_param($stmt, 'ssssi', $valori['email'], $valori['pswd'], $valori['tipo'], $valori['carta'], getUserID());
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        logout();
    }
    else{
        //l'email è utilizzata da qualcun'altro
        die('Email già utilizzata per un altro account');
    }
}
/**
 * Ottiene un'array con tutti gli annunci inseriti dalle proprie connessioni di 1°, 2° e 3° grado
 * @param $idUtente
 * @return array
 */
function getAnnunciConnessioni($idUtente){
    $link = connectToDatabase();
    $query_amici = 'SELECT annuncio.*, datiAnagrafici.nomeUtente FROM
                              (SELECT c1.idConnessione FROM utente_has_utente AS c1 WHERE c1.idUtente='.$idUtente.'
                            UNION
                              SELECT u2.idConnessione FROM utente_has_utente AS u1 INNER JOIN utente_has_utente AS u2 ON u1.idConnessione=u2.idUtente
                                WHERE u2.idConnessione<>u1.idUtente AND u1.idUtente='.$idUtente.'
                            UNION
                              SELECT u3.idConnessione FROM utente_has_utente AS u1 INNER JOIN utente_has_utente AS u2 ON u1.idConnessione=u2.idUtente
                                INNER JOIN utente_has_utente AS u3 ON u2.idConnessione=u3.idUtente
                                  WHERE u3.idConnessione<>u1.idUtente AND u1.idUtente='.$idUtente.')
                            AS conn INNER JOIN annuncio ON conn.idConnessione=annuncio.idUtente INNER JOIN utente ON annuncio.idUtente=utente.idUtente
                              INNER JOIN datiAnagrafici ON utente.idDatiAnagrafici=datiAnagrafici.idDatiAnagrafici';
    $res = mysqli_query($link, $query_amici);
    $annunci = array();
    while ($row = mysqli_fetch_assoc($res))
        $annunci[] = $row;
    disconnectFromDatabase($link);
    return $annunci;
}

/**
 * Ottiene un'array con tutti gli annunci inseriti da utenti presenti negli stessi gruppi dell'utente
 * @param $idUtente
 * @return array
 */
function getAnnunciGruppi($idUtente){
    $link = connectToDatabase();
    $query_gruppi = 'SELECT tab2.idGruppo, gruppo.nomeGruppo, annuncio.*, datiAnagrafici.nomeUtente
                    FROM utente_has_gruppo AS tab1 INNER JOIN utente_has_gruppo as tab2 ON tab1.idGruppo=tab2.idGruppo
                      INNER JOIN annuncio ON tab2.idUtente=annuncio.idUtente INNER JOIN gruppo ON tab2.idGruppo=gruppo.idGruppo
                      INNER JOIN utente ON tab2.idUtente=utente.idUtente INNER JOIN datiAnagrafici ON utente.idDatiAnagrafici=datiAnagrafici.idDatiAnagrafici
                        WHERE tab2.idUtente!=tab1.idUtente AND tab1.idUtente='.$idUtente;
    $res = mysqli_query($link, $query_gruppi);
    $annunci_gruppi = array();
    while ($row = mysqli_fetch_assoc($res))
        $annunci_gruppi[] = $row;
    disconnectFromDatabase($link);
    return $annunci_gruppi;
}

/**
 * Creo un nuovo gruppo
 * @param $idUtente
 * @param $nomeNuovoGruppo
 * @param $carComuni
 */
function creaGruppo($idUtente, $nomeNuovoGruppo, $carComuni){
    $link = connectToDatabase();
    $stmt = mysqli_prepare($link, 'INSERT INTO gruppo (nomeGruppo) VALUES (?)');
    mysqli_stmt_bind_param($stmt, 's', $nomeNuovoGruppo);
    mysqli_stmt_execute($stmt);
    $idNuovoGruppo = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);
    foreach ($carComuni as $carComune){
        $idCarComune = null;
        $res = mysqli_query($link, 'SELECT cc.idCarComune FROM caratteristicaComune AS cc WHERE cc.nomeCarComune="'.$carComune.'"');
        if (mysqli_affected_rows($link) == 0){
            $stmt = mysqli_prepare($link, 'INSERT INTO caratteristicaComune (nomeCarComune) VALUES (?)');
            mysqli_stmt_bind_param($stmt, 's', $carComune);
            mysqli_stmt_execute($stmt);
            $idCarComune = mysqli_stmt_insert_id($stmt);
            mysqli_stmt_close($stmt);
        }
        else{
            $idCarComune = mysqli_fetch_assoc($res);
            $idCarComune = $idCarComune['idCarComune'];
        }
        $stmt = mysqli_prepare($link, 'INSERT INTO gruppo_has_carComune (idGruppo, idCarComune) VALUES (?, ?)');
        mysqli_stmt_bind_param($stmt, 'ii', $idNuovoGruppo, $idCarComune);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    mysqli_query($link, 'INSERT INTO utente_has_gruppo (idUtente, idGruppo) VALUES ('.$idUtente.', '.$idNuovoGruppo.')');
    disconnectFromDatabase($link);
}

/**
 * Ritorna true se l'utente è nel gruppo altrimenti false
 * @param $idUtente
 * @param $idGruppo
 * @return bool
 */
function isUserInGroup($idUtente, $idGruppo){
    $link = connectToDatabase();
    mysqli_query($link, 'SELECT * FROM utente_has_gruppo AS ug WHERE ug.idUtente='.$idUtente.' AND ug.idGruppo='.$idGruppo);
    $return = true;
    if (mysqli_affected_rows($link) == 0){
        $return = false;
    }
    disconnectFromDatabase($link);
    return $return;
}

/**
 * Rimuove l'utente dal gruppo
 * @param $idUtente
 * @param $idGruppo
 */
function esciDaGruppo($idUtente, $idGruppo){
    $link = connectToDatabase();
    $query = 'DELETE FROM utente_has_gruppo WHERE utente_has_gruppo.idUtente='.$idUtente.' AND utente_has_gruppo.idGruppo='.$idGruppo;
    mysqli_query($link, $query);
    mysqli_query($link, 'SELECT * FROM utente_has_gruppo AS ug WHERE ug.idGruppo='.$idGruppo);
    echo mysqli_error($link);
    $aff_rows = mysqli_affected_rows($link);
    if ($aff_rows == 0) {
        mysqli_query($link, 'DELETE FROM gruppo_has_carComune WHERE gruppo_has_carComune.idGruppo=' . $idGruppo);
        mysqli_query($link, 'DELETE FROM gruppo WHERE gruppo.idGruppo=' . $idGruppo);
        header("Location: gruppi.php");
    }
    disconnectFromDatabase($link);
}

/**
 * Aggiunge i due utenti alla lista di connessioni
 * @param $idUtente
 * @param $idAmico
 * @return bool
 */
function areFriends($idUtente, $idAmico){
    $link = connectToDatabase();
    mysqli_query($link, 'SELECT * FROM utente_has_utente AS uu WHERE uu.idUtente='.$idUtente.' AND uu.idConnessione='.$idAmico);
    $aff_rows = mysqli_affected_rows($link);
    disconnectFromDatabase($link);
    if ($aff_rows > 0)
        return true;
    else
        return false;
}

/**
 * Invia una richiesta di connessione ad un untente
 * @param $utente
 * @param $messaggio
 */
function inviaRichiestaConnessione($utente, $messaggio){
    $link = connectToDatabase();
    $stmt = mysqli_prepare($link, 'INSERT INTO richiestaConnessione (idUtenteRichiedente, commentoRichiesta) VALUES (?, ?)');
    mysqli_stmt_bind_param($stmt, 'is', getUserID(), $messaggio);
    mysqli_stmt_execute($stmt);
    $idRichiesta = mysqli_stmt_insert_id($stmt);
    $stmt = mysqli_prepare($link, 'INSERT INTO utente_has_richiesta (idUtente, idRichiesta) VALUES (?, ?)');
    mysqli_stmt_bind_param($stmt, 'ii', $utente, $idRichiesta);
    mysqli_stmt_execute($stmt);
    disconnectFromDatabase($link);
}

/**
 * Rimuove la richiesta di connessione appena accettata
 * @param $link
 * @param $utenteDaAggiungere
 * @param $idUtente
 */
function rimuoviRichiesta($link, $utenteDaAggiungere, $idUtente){
    //rimuovo le richieste di connessione che li coinvolgono
    $res = mysqli_query($link, 'SELECT ur.idUtente, rc.idRichiesta
                                            FROM richiestaConnessione AS rc
                                              INNER JOIN utente_has_richiesta AS ur ON rc.idRichiesta = ur.idRichiesta
                                                WHERE rc.idUtenteRichiedente=' . $utenteDaAggiungere . ' AND ur.idUtente=' . $idUtente);
    $richiesteConnesioni = array();
    while ($row = mysqli_fetch_assoc($res))
        $richiesteConnesioni[] = $row;

    foreach ($richiesteConnesioni as $rc) {
        mysqli_query($link, 'DELETE FROM richiestaConnessione WHERE idRichiesta=' . $rc['idRichiesta']);
        mysqli_query($link, 'DELETE FROM utente_has_richiesta WHERE idUtente=' . $rc['idUtente'] . ' AND idRichiesta=' . $rc['idRichiesta']);
    }
}

/**
 * Ritorna un determinato annuncio
 * @param $idAnnuncio
 * @return array|null
 */
function getAnnuncio($idAnnuncio){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT * FROM annuncio WHERE idAnnuncio=' . $idAnnuncio);
    $annuncio = mysqli_fetch_assoc($res);
    disconnectFromDatabase($link);
    return $annuncio;
}

/**
 * Ritorna la possibilità di un utente di inserire un'annuncio (se è free o pro)
 * @param $idUtente
 * @return bool
 */
function canUserCreateAnnuncio($idUtente){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT acc.tipoUtente FROM utente NATURAL JOIN datiAccount AS acc WHERE utente.idUtente='.$idUtente);
    $tipoU = mysqli_fetch_assoc($res);
    disconnectFromDatabase($link);
    if ($tipoU['tipoUtente'] == 'pro')
        return true;
    else
        return false;
}

/**
 * Agiunge un nuovo annuncio
 * @param $titolo
 * @param $descrizione
 * @param $idCreatore
 */
function creaAnnuncio($titolo, $descrizione, $idCreatore){
    $link = connectToDatabase();
    $stmt = mysqli_prepare($link, 'INSERT INTO annuncio (titoloAnnuncio, descrizioneAnnuncio, idUtente) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'ssi', $titolo, $descrizione, $idCreatore);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    disconnectFromDatabase($link);
}

/**
 * Aggiunge una valutazione ad un utente
 * @param $idValutato
 * @param $val
 */
function valutaUtente($idValutato, $val){
    $link = connectToDatabase();
    $res = mysqli_query($link, 'SELECT val.idValutazione FROM valutazione AS val WHERE val.valutazione ="'.$val.'" AND val.idValutante='.getUserID());
    $idVal = mysqli_fetch_assoc($res);
    $idVal = $idVal['idValutazione'];
    if ($idVal == null){
        //non è presente la valutazione
        $stmt = mysqli_prepare($link, 'INSERT INTO valutazione (valutazione, idValutante) VALUES (?, ?)');
        mysqli_stmt_bind_param($stmt, 'si', $val, getUserID());
        mysqli_stmt_execute($stmt);
        $idVal = mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);
    }
    $stmt = mysqli_prepare($link, 'INSERT INTO utente_has_valutazione (idValutato, idValutazione) VALUES (?, ?)');
    mysqli_stmt_bind_param($stmt, 'ii', $idValutato, $idVal);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    disconnectFromDatabase($link);
}
?>
