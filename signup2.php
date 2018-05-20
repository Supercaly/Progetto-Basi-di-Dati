<?php
require_once "querys.php";

if (isset($_POST)) {
    //se è presente il valore di controllo sono al secondo passo della registrazione: devo inserire i dati carriera
    if ($_POST['controllo'] == 'ok') {

        $inizio_lavoro = $_POST['lavoro_inizio'];
        $fine_lavoro = $_POST['lavoro_fine'];

        foreach ($inizio_lavoro as $key => $value) {
            if (empty($value))
                $inizio_lavoro[$key] = null;
        }
        foreach ($fine_lavoro as $key => $value) {
            if (empty($value))
                $fine_lavoro[$key] = null;
        }

        $carriera = array('titoli_studio' => $_POST['titoli_studio'],
            'studio_voti' => $_POST['titoli_studio_voto'],
            'skill' => $_POST['skill'],
            'lavoro' => $_POST['lavoro'],
            'lavoro_inizio' => $inizio_lavoro,
            'lavoro_fine' => $fine_lavoro,
            'competenza' => $_POST['competenza']);

        addDatiCarrieraToDatabase($carriera);
        header('Location: home.php');
    }
}
else
    echo 'non sono passati dati';


?>

<!DOCTYPE html>
<html>
    <head>
        <title>EsameBasi - Registrati</title>
        <script type="application/javascript">
            var titoli_counter = 0;
            var skill_counter = 0;
            var lavoro_counter = 0;
            var competenza_counter = 0;
            var limit = 10;

            function addInputTitoli() {
                if (titoli_counter == limit)  {
                    alert("You have reached the limit of adding " + limit + " inputs");
                }
                else {
                    var newdiv = document.createElement('div');
                    newdiv.innerHTML = "<br><input placeholder='titolo di studio' required name=titoli_studio[]><input type='number' value='0' min='0' placeholder='valutazione' name='titoli_studio_voto[]'>";
                    document.getElementById('div_titoli_studio').appendChild(newdiv);
                    titoli_counter++;
                }
            }
            function addInpuSkill() {
                if (skill_counter == limit)  {
                    alert("You have reached the limit of adding " + limit + " inputs");
                }
                else {
                    var newdiv = document.createElement('div');
                    newdiv.innerHTML = "<br><input name=skill[] required placeholder='nome Skill'>";
                    document.getElementById('div_skill').appendChild(newdiv);
                    skill_counter++;
                }
            }
            function addInputLavoro() {
                if (lavoro_counter == limit)  {
                    alert("You have reached the limit of adding " + limit + " inputs");
                }
                else {
                    var newdiv = document.createElement('div');
                    newdiv.innerHTML = "<br><input name=lavoro[] placeholder='nome posizione' required><input type='date' name='lavoro_inizio[]' placeholder='data inizio lavoro'><input type='date' name='lavoro_fine[]' placeholder='data fine lavoro'>";
                    document.getElementById('div_lavoro').appendChild(newdiv);
                    lavoro_counter++;
                }
            }
            function addInputCompetenza() {
                if (competenza_counter == limit)  {
                    alert("You have reached the limit of adding " + limit + " inputs");
                }
                else {
                    var newdiv = document.createElement('div');
                    newdiv.innerHTML = "<br><input name=competenza[] placeholder='nome competenza' required>";
                    document.getElementById('div_competenza').appendChild(newdiv);
                    competenza_counter++;
                }
            }
        </script>
    </head>
    <body>

    <h3>Ancora un attimo...</h3>
    <p>Inserisci alcune informazioni sul tuo passato</p>
    <form action="" method="post">
        <div id="div_titoli_studio">
            <label>Titoli di studio conseguiti</label>
            <button type="button" onclick="addInputTitoli()">Agiungi Titolo di studio</button>
        </div>
        <div id="div_skill">
            <label>Skill</label>
            <button type="button" onclick="addInpuSkill()">Aggiungi Skill</button>
        </div>
        <div id="div_lavoro">
            <label>Posizioni lavorative ricoperte</label>
            <button type="button" onclick="addInputLavoro()">Aggiungi Posizione lavorativa passata</button>
        </div>
        <div id="div_competenza">
            <label>Competenze lavorative</label>
            <button type="button" onclick="addInputCompetenza()">Aggiungi Competenza lavorativa</button>
        </div>
        <input name="controllo" hidden value="ok">
        <br><button>Continua con la registrazione</button>
    </form>
    </body>
</html>
