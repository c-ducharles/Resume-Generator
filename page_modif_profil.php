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
    <title>Modification de profil</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="grid-container-acceuil">
    <div class = "page">
        <div class = "info-compte">
            <h1>Mon profil</h1><a href="compte.php" class="bouton_petit">Annuler</a>
        </div>
        <form method="post" action="ajout_profil.php">
            <div class = "formulaire"> <!-- Formulaire pour modifier le mot de passe -->
                    <?php
                    $bdd = new PDO("mysql:host=localhost;dbname=ifd1_gestion_cv;charset=utf8", "root", "");
                    //on récup les infos du profiil
                    $req = $bdd->prepare("SELECT adresse, ville, code_postal, date_naissance, numero FROM comptes WHERE email = (?);");

                    $req->execute([$_SESSION['mail']]);

                    $data = $req->fetch();
                    ?>
                <label> <!-- liste des champs de modification des infos que l'on prérempli avec les existantes pour eviter de devoir tout retaper -->
                    <input type="text" name="adresse" value="<?php echo "$data[adresse]";?>" placeholder="Adresse Postale"><br><br>
                    <input type="text" name="ville" value="<?php echo "$data[ville]";?>" placeholder="Ville"><br><br>
                    <input type="number" name="code_postal" value="<?php if($data['code_postal']!=0){echo "$data[code_postal]";}?>" placeholder="Code Postal"><br><br>
                    <input type="date" name="date" value="<?php echo "$data[date_naissance]";?>"><br><br>
                    <input type="number" name="numero" value="<?php echo "$data[numero]";?>" placeholder="Numéro de télephone"><br><br>
                </label>
                <input type="submit" value="Valider" class="bouton_petit">
            </div>
        </form>
    </div>
</div>
</body>
</html>