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
    <title>Modification de loisir</title>
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

                $type = $_GET['type'];

                if ($type=='suppr') //si on vient pour supprimer une ligne
                {
                    $loisir = $_POST['loisir_suppr'];
                    //requete de suppression par rapport au loisir selectionné pour être supprimé
                    $req = $bdd->prepare("DELETE FROM loisir WHERE loisir.id_etudiant=(SELECT comptes.id FROM comptes WHERE
                    comptes.email=(?)) AND loisir.id_loisir=(SELECT type_loisirs.id FROM type_loisirs WHERE type_loisirs.nom=(?));");

                    $req->execute([$_SESSION['mail'],$loisir]);

                    header("Location: compte.php");
                }
                ?>

                <form method='post' action='ajout_loisir.php'> <!-- Forum d'ajout de loisir -->
                    <label>
                        <input type='text' list='liste_loisir' value='' name='loisir_ajout' required autocomplete='off'> <!-- Ici pas de liste, uniquement de la saisie prédictive -->
                        <input type='submit' value='Ajouter' class='bouton_petit'>
                    </label>
                </form>

                <datalist id='liste_loisir'> <!-- On rempli la liste pour la saisie prédictive -->
                    <?php
                    $req = $bdd->prepare("SELECT nom FROM type_loisirs ORDER BY nom;");
                    $req->execute();
                    $data = $req->fetch();

                    while($data)
                    {
                        echo "<option value='$data[nom]'>";
                        $data = $req->fetch();
                    }
                    ?>
                </datalist>

            </div>
    </div>
</div>
</body>
</html>

