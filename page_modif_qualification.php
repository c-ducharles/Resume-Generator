<?php
session_start(); //permet de démarrer la session
// verification de connection (si l'email est enregistré c'est que l'authentification a été réussie)
if($_SESSION['mail']=="") //$SESSION['mail'] variable de session qui permet l'authentification le long des pages web
{
    header("Location: page_connexion.php");
}
?>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <title>Modification qualification</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="grid-container-acceuil">
    <div class = "page">
        <div class = "info-compte">
            <h1>Ajouter un loisir</h1><a href="compte.php" class="bouton_petit">Annuler</a>
        </div>
        <div class = "formulaire">
            <?php
            $bdd = new PDO("mysql:host=localhost;dbname=ifd1_gestion_cv;charset=utf8", "root", "");

            $type = $_GET['type']; //variable qui l'action à faire sur la page (suppr ou ajout de donne)

            if ($type=='suppr') //savoir si on vient pour suppr une ligne
            {
                $qualification = $_POST['qualification_suppr'];
                //on supprime la ligne correspondante à la qualification à supprimer
                $req = $bdd->prepare("DELETE FROM qualification WHERE qualification.id_etudiant=(SELECT comptes.id FROM comptes WHERE
                    comptes.email=(?)) AND qualification.id_qualification=(SELECT type_qualification.id FROM type_qualification WHERE type_qualification.nom=(?));");

                $req->execute([$_SESSION['mail'],$qualification]);

                header("Location: compte.php");
            }
            else
            {
                $req = $bdd->prepare("SELECT nom FROM type_qualification ORDER BY nom;");

                $req->execute();

                $data = $req->fetch();
            }
            ?>
            <form method='post' action='ajout_qualification.php' id="form_ajout_predef"><!-- Formulaire d'ajout de qualification -->
                <label for='liste_qualification'>Choississez une formation :</label>
                <select name="qualification_ajout" id='liste_qualification' required> <!-- Création d'une liste de formation à selectionner -->
                    <?php
                    while($data)
                    {
                        echo "<option value='$data[nom]'>$data[nom]</option>";
                        $data = $req->fetch();
                    }
                    ?>
                </select>
                <input type='submit' value='Ajouter' class='bouton_petit'>
            </form>

            <h2>Votre qualification n'est pas dans la liste ? Rajoutez-là en dessous !</h2>

            <form method='post' action='ajout_qualification.php' id="form_ajout_qualif  "> <!-- Formulaire d'ajout de qualification dans la base -->
                <label><input type="text" value="" required name="qualification_ajout" autocomplete='off'>
                <input type='submit' value='Ajouter' class='bouton_petit'>
                </label>
            </form>



        </div>
    </div>
</div>
</body>
</html>

