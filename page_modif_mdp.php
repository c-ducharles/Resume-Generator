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
    <title>Modification mot de passe</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="grid-container-acceuil">
    <div class = "page">
        <div class = "info-compte">
            <h1>Changement de mot de passe</h1><a href="compte.php" class="bouton_petit">Annuler</a>
        </div>
        <form method="post" action="changement_mdp.php"> <!-- Formulaire pour modifier le mot de passe -->
            <div class = "formulaire">
                <label>
                    <input type = "password" name = "new" placeholder ="Noveau mot de passe" required class="input_acceuil"><br><br>
                    <input type = "password" name = "new_confirm" placeholder ="Confirmer votre nouveau mot de passe" required class="input_acceuil"><br><br>
                </label>
                <input type="submit" value="Valider" class="bouton">
            </div>
        </form>
    </div>
</div>
</body>
</html>