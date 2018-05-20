<?php
require_once "querys.php";

   $idUtente = getUserID();

    if (isset($_POST) && !empty($_POST)){
        $link = connectToDatabase();
        $res = mysqli_query($link, 'SELECT carr.idDatiCarriera FROM datiCarriera AS carr NATURAL JOIN utente WHERE utente.idUtente='.$idUtente);
        $idCarriera = mysqli_fetch_assoc($res);
        $idCarriera = $idCarriera['idDatiCarriera'];
        if (isset($_POST['titolo_da_rimuovere'])){
            removeTitoloStudio($_POST['titolo_da_rimuovere'], $idCarriera);
        }
        if (isset($_POST['skill_da_rimuovere'])){
            removeSkill($_POST['skill_da_rimuovere'], $idCarriera);
        }
        if (isset($_POST['lavoro_da_rimuovere'])){
            removeLavoro($_POST['lavoro_da_rimuovere'], $idCarriera);
        }
        if (isset($_POST['competenza_da_rimuovere'])){
            removeCompetenza($_POST['competenza_da_rimuovere'], $idCarriera);
        }
    }

    if (isset($_GET) && !empty($_GET)){
        $titStud = json_decode($_GET['titolo_studio']);
        $skill = json_decode($_GET['skill']);
        $lavori = json_decode($_GET['lavoro']);
        $comp = json_decode($_GET['comp']);

        $link = connectToDatabase();
        $res = mysqli_query($link, 'SELECT u.idDatiCarriera from utente AS u WHERE u.idUtente='.$idUtente);
        $idCarriera = mysqli_fetch_assoc($res);
        $idCarriera = $idCarriera['idDatiCarriera'];
        foreach ($titStud as $titolo){
            echo 'ciao';
            $tit = $titolo[0];
            $voto = $titolo[1];
            addTitolStudio($link, $idCarriera, $tit, $voto);
        }
        foreach ($skill as $s){
            $nomeSkill = $s;
            addSkill($link, $idCarriera, $nomeSkill);
        }
        foreach ($lavori as $lavoro){
            $nomeLavoro = $lavoro[0];
            $dataInizio = $lavoro[1];
            $dataFine = $lavoro[2];
            addLavoroPassato($link, $idCarriera, $nomeLavoro, $dataInizio, $dataFine);
        }
        foreach ($comp as $competenza){
            $nomeComp = $competenza;
            addCompetenza($link, $idCarriera, $nomeComp);
        }
        header('location: profilo.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <title>Modifica Dati Carriera</title>
        <script type="application/javascript">
            function addTitolo() {
                var div_titoli = document.getElementById('div_titoli_studio');
                var newdiv = document.createElement('div');
                newdiv.innerHTML = "<form class='forms'><label>Titolo di studio</label><input required><label>Valutazione Conseguita</label><input type='number' min='0' value='0'></form>";
                newdiv.className = 'div_element';
                div_titoli.appendChild(newdiv);
            }
            function addSkill() {
                var div_titoli = document.getElementById('div_skill');
                var newdiv = document.createElement('div');
                newdiv.innerHTML = "<form class='forms'><label>Skill</label><input required></form>";
                newdiv.className = 'div_element';
                div_titoli.appendChild(newdiv);
            }
            function addLavoro() {
                var div_titoli = document.getElementById('div_lavoro');
                var newdiv = document.createElement('div');
                newdiv.innerHTML = "<form class='forms'><label class='bo'>Posizione lavorativa passata</label><input required><label>Data inizio lavoro</label><input type='date'><label>Data fine lavoro</label><input type='date'></form>";
                newdiv.className = 'div_element';
                div_titoli.appendChild(newdiv);
            }
            function addCompetenza() {
                var div_titoli = document.getElementById('div_competenza');
                var newdiv = document.createElement('div');
                newdiv.innerHTML = "<form class='forms'><label>Competenza lavorativa</label><input required></form>";
                newdiv.className = 'div_element';
                div_titoli.appendChild(newdiv);
            }

            function sendAllForms() {
                var forms = document.getElementsByClassName("forms");
                var titoli_studio = new Array();
                var skills = new Array();
                var lavori = new Array();
                var competenze = new Array();
                var count_titoli = 0;
                var count_skills = 0;
                var count_lavori = 0;
                var count_comp = 0;
                for (i = 0; i < forms.length; i++) {
                    if (forms[i].elements[0].value == ''){
                        alert('Il Campo "Nome titolo di studio" non può essere vuoto');
                        return;
                    }
                    if (forms[i].childNodes[0].innerHTML == 'Titolo di studio') {
                        titoli_studio[count_titoli] = new Array();
                        titoli_studio[count_titoli][0] = forms[i].elements[0].value;
                        titoli_studio[count_titoli][1] = forms[i].elements[1].value;
                        count_titoli++;
                    }
                    if (forms[i].childNodes[0].innerHTML == 'Skill') {
                        skills[count_skills] = forms[i].elements[0].value;
                        count_skills++;
                    }
                    if (forms[i].childNodes[0].innerHTML == 'Posizione lavorativa passata') {
                        lavori[count_lavori] = new Array();
                        lavori[count_lavori][0] = forms[i].elements[0].value;
                        lavori[count_lavori][1] = forms[i].elements[1].value;
                        lavori[count_lavori][2] = forms[i].elements[2].value;
                        count_lavori++;
                    }
                    if (forms[i].childNodes[0].innerHTML == 'Competenza lavorativa') {
                        competenze[count_comp] = forms[i].elements[0].value;
                        count_comp++;
                    }
                }
                url = "modifica_car.php?titolo_studio="+JSON.stringify(titoli_studio)+"&skill="+JSON.stringify(skills)+"&lavoro="+JSON.stringify(lavori)+"&comp="+JSON.stringify(competenze);
                window.location.href = url;

            }
        </script>
    </head>
    <body>
        <div id="div_titoli_studio">
            <h4>Titoli di studio</h4>
            <button onclick="addTitolo()">Aggiungi Titolo di studio</button>
            <?php
                $titoliStudio = getTitoliStudio();
                foreach ($titoliStudio as $titolo) {
                    echo '<div class="div_element">
                            <b>Titolo di studio: </b>'.$titolo['nomeTitolo'].'<b> Valutazione: </b>'.$titolo['valutazioneConseguita'].'
                        <form method="post" style="float: right"><input name="titolo_da_rimuovere" value="'.$titolo['idTitoloStudio'].'" hidden><button>-</button></form>
                      </div>';
            }
        ?>
        </div>
        <div id="div_skill">
            <h4>Skills</h4>
            <button onclick="addSkill()">Aggiungi Skill</button>
            <?php
                $skills = getSkills();
                foreach ($skills as $skill) {
                    echo '<div class="div_element">
                        <b>Skill: </b>'.$skill['nomeSkill'].'
                        <form method="post" style="float: right"><input name="skill_da_rimuovere" value="'.$skill['idSkill'].'" hidden><button>-</button></form>
                      </div>';
            }
            ?>
        </div>
        <div id="div_lavoro">
            <h4>Posizioni lavorative passate</h4>
            <button onclick="addLavoro()">Aggiungi Lavoro passato</button>
            <?php
                $lavoriPassati = getLavoriPassati();
                foreach ($lavoriPassati as $lavoro) {
                    echo '<div class="div_element">
                        <b>Lavoro Passato: </b>'.$lavoro['nomeLavoroPassato'].'<b> Data inizio: </b>'.$lavoro['dataInizio'].'<b> Data fine: </b>'.$lavoro['dataFine'].'
                        <form method="post" style="float: right"><input name="lavoro_da_rimuovere" value="'.$lavoro['idLavoroPassato'].'" hidden><button>-</button></form>
                      </div>';
            }
            ?>
        </div>
        <div id="div_competenza">
            <h4>Competenze lavorative</h4>
            <button onclick="addCompetenza()">Aggiungi Competenza lavorativa</button>
            <?php
                $competenze = getCompetenze();
                foreach ($competenze as $competenza)
                    echo '<div class="div_element"><b>Competenza lavorativa: </b>'.$competenza['nomeCompetenza'].'
                    <form method="post" style="float: right"><input name="competenza_da_rimuovere" value="'.$competenza['idCompetenza'].'" hidden><button>-</button></form></div>'
            ?>
        </div>
        <div class="div_infoutente">
            <button onclick="sendAllForms()">Salva</button>
        </div>
    </body>
</html>