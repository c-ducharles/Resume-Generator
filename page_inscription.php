<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="grid-container">
    <div class = "carre_acceuil">
        <div class = "titre_acceuil">
            Inscription
        </div>
        <form method="post" action="inscrire.php">  <!-- création du formulaire d'inscription -->
            <div class = "formulaire">
                <label>
                    <input type = "text" name = "prenom" placeholder="Prenom" required class="input_acceuil"><br><br>
                    <input type = "text" name = "nom" placeholder ="Nom" required class="input_acceuil"><br><br>
                    <input type = "email" name = "email" placeholder ="Adresse email" required class="input_acceuil"><br><br>
                    <input type = "password" name = "password" placeholder ="Mot de passe" required class="input_acceuil"><br><br>
                </label>
                <input type="submit" value="S'inscrire" class="bouton">
            </div>
        </form>
    </div>
</div>
</body>
</html>