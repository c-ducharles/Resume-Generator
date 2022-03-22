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
    <title>Modification langues</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="grid-container-acceuil">
    <div class = "page">
        <div class = "info-compte">
            <h1>Ajouter une expérience linguistique</h1><a href="compte.php" class="bouton_petit">Annuler</a>
        </div>
        <div class = "formulaire"> <!-- formulaire permettant d'ajouter des expériences linguistiques, le div class a été recyclé -->
            <?php

            $bdd = new PDO("mysql:host=localhost;dbname=ifd1_gestion_cv;charset=utf8", "root", "");

            $type = $_GET['type']; //on récup le type d'opération (suppresion ou ajout de donnee)

            if ($type=='suppr') //si on vient pour suppr une ligne avec le bouton suppr
            {
                $langue = $_POST['langue_suppr'];

                //requete de suppression par rapport à la langue en question qui a été selectionnée pour etre supprimée
                $req = $bdd->prepare("DELETE FROM experience_linguistiques WHERE experience_linguistiques.id_etudiant=(SELECT comptes.id FROM comptes WHERE
                    comptes.email=(?)) AND experience_linguistiques.id_langue=(SELECT langues.id_langue FROM langues WHERE langues.nom=(?));");

                $req->execute([$_SESSION['mail'],$langue]);

                header("Location: compte.php");
            }
            else //on vient pour rajouter
            {
                $langue = $bdd->prepare("SELECT nom FROM langues ORDER BY nom;");
                $niveau = $bdd->prepare("SELECT nom FROM niveau ORDER BY nom;");

                $langue->execute();
                $niveau->execute();
                //on charges de variables pour les listes
                $data_langue = $langue->fetch();
                $data_niveau = $niveau->fetch();
            }
            ?>
            <form method='post' action='ajout_langue.php' id="form_ajout_predef">
                <label for='liste_langue'>Choississez une langue et un niveau :</label>
                <select name="langue_ajout" id='liste_langue' required>
                    <?php //On charge ici la liste des langues
                    while($data_langue)
                    {
                        echo "<option value='$data_langue[nom]'>$data_langue[nom]</option>";
                        $data_langue = $langue->fetch();
                    }
                    ?>
                </select>
                <select name="niveau_ajout" id='liste_langue' required>
                    <?php
                    while($data_niveau)
                    {       //On charge ici la liste des niveaux
                        echo "<option value='$data_niveau[nom]'>$data_niveau[nom]</option>";
                        $data_niveau = $niveau->fetch();
                    }
                    ?>
                </select>
                <input type='submit' value='Ajouter' class='bouton_petit'> <!-- On envoi pour l'ajout dans la bdd -->
            </form>

            <h2>Votre langue n'est pas dans la liste ? Rajoutez-là en dessous !</h2>

            <form method='post' action='ajout_langue.php' id="form_ajout_qualif"> <!-- On ajoute une nouvelle langue -->
                <label><input type="text" value="" required name="langue_ajout" autocomplete='off'>
                </label>
                <select name="niveau_ajout" id='liste_langue' required> <!-- Par contre on doit aussi sélectionner un niveau-->
                    <?php
                    $niveau->execute();
                    $data_niveau = $niveau->fetch();
                    while($data_niveau)
                    {
                        echo "<option value='$data_niveau[nom]'>$data_niveau[nom]</option>";
                        $data_niveau = $niveau->fetch();
                    }
                    ?>
                </select>
                <input type='submit' value='Ajouter' class='bouton_petit'>
            </form>



        </div>
    </div>
</div>
</body>
</html>

