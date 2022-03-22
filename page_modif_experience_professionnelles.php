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
    <title>Modification expérience professionelle</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="grid-container-acceuil">
    <div class = "page">
        <div class = "info-compte">
            <h1>Ajouter une expérience professionnelle</h1><a href="compte.php" class="bouton_petit">Annuler</a>
        </div>
        <div class = "formulaire">
            <?php

            $bdd = new PDO("mysql:host=localhost;dbname=ifd1_gestion_cv;charset=utf8", "root", "");

            $type = $_GET['type'];
            //variables permettant de charger la page avec un filtre sur le secteur ou l'adresse par envoi de donne en $_GET
            $filtre_ajout_secteur = '';
            $filtre_ajout_adresse = '';

            if ($type=='suppr') //si on est arrivé sur la page par le bouton de suppression d'item
            {
                $nom = $_POST['entreprise_suppr']; //on récupère l'entreprise concernée pour suppr toute la ligne

                $req = $bdd->prepare("DELETE FROM experience_professionnelles WHERE experience_professionnelles.id_etudiant=(SELECT comptes.id FROM comptes WHERE
                    comptes.email=(?)) AND experience_professionnelles.id_entreprise=(SELECT entreprise.id FROM entreprise WHERE entreprise.nom=(?));");

                $req->execute([$_SESSION['mail'],$nom]);

                header("Location: compte.php"); //on reviens sur la page principale
            }
            else //si on vient par le bouton d'ajout
            {
                //on charge des variables pour creer les listes de tri
                $secteur = $bdd->prepare("SELECT DISTINCT secteur FROM entreprise ORDER BY secteur;");
                $adresse = $bdd->prepare("SELECT DISTINCT adressse FROM entreprise ORDER BY adressse;");
                $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise ORDER BY nom;");

                $secteur->execute();
                $adresse->execute();
                $nom-> execute();

                $data_secteur = $secteur->fetch();
                $data_adresse = $adresse->fetch(); //ATTENTION adresse correspond en réalité à la ville
                $date_nom = $nom->fetch();

                if ($type=='filtre') //si la page est lancée avec un filtre en paramètre (signifie que l'on a appuyer sur le bouton trier)
                {
                    //alors on charge la valeur de ces filtres
                    $filtre_ajout_secteur = $_POST['filtre_ajout_secteur'];
                    $filtre_ajout_adresse = $_POST['filtre_ajout_adresse'];

                }
            }
            //--on regarde si on a une école avec les paramétrage des filtre, on est obligé de dissocier pour faire les bonnes requetes
            if ($filtre_ajout_secteur=='' AND $filtre_ajout_adresse=='' ) //si pas de filtre on charge toutes les entreprises
            {
                $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise ORDER BY nom;");
                $nom ->execute();
            }
            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_adresse=='') //si filtre uniquement sur secteur, alors on charges les entreprise qui correspndent au critère
            {
                $nom  = $bdd->prepare("SELECT DISTINCT nom FROM entreprise WHERE secteur=(?) ORDER BY nom;");
                $nom ->execute([$filtre_ajout_secteur]);
            }
            else if ($filtre_ajout_secteur=='' AND $filtre_ajout_adresse!='' ) //si filtre uniquement sur adresse
            {
                $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise WHERE adressse=(?) ORDER BY nom;");
                $nom ->execute([$filtre_ajout_adresse]);
            }
            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_adresse!='' ) //si deux filtres
            {
                $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise WHERE secteurs=(?) AND adressse=(?)  ORDER BY nom;");
                $nom ->execute([$filtre_ajout_secteur,$filtre_ajout_adresse]);
            }
            $data_nom = $nom->fetch();

            //on vérifie si la requete a donné qqchose, au cas contraire on demande d'enregistrer une nouvelle entreprise directement
            if($data_nom==0)
            {
                $nb_entreprise = 0;
            } else
            {
                $nb_entreprise = 1;
            }
            ?>

            <h2>Vous pouvez affiner votre recherche d'entreprise pour compléter votre formation çi-dessous</h2>
            <!-- Affichage du formulaire permettant de filtrer les écoles -->
            <form method='post' action='page_modif_experience_professionnelles.php?type=filtre'>
                <label for='liste_filtres'>Filtrer les entreprises par :</label>
                <select name="filtre_ajout_adresse" id='liste_filtres' >
                    <option value="">--Choissisez une ville--</option>
                    <?php
                    while($data_adresse)
                    {
                        //on charge la liste des valeures du filtre des adresse des entreprises connues dans la base
                        echo "<option value='$data_adresse[adressse]'>$data_adresse[adressse]</option>";
                        $data_adresse = $adresse->fetch();
                    }
                    ?>
                </select>
                <select name="filtre_ajout_secteur" id='liste_filtres' >
                    <option value="">--Choissisez un secteur d'activités--</option>
                    <?php
                    while($data_secteur)
                    {
                        //on charge la liste des valeures du filtre des secteur des entreprises connues dans la base
                        echo "<option value='$data_secteur[secteur]'>$data_secteur[secteur]</option>";
                        $data_secteur = $secteur->fetch();
                    }
                    ?>

                <input type='submit' value='Appliquer le filtre' class='bouton_petit'> <!-- on lance la requete de filtrage -->
            </form>
            <?php
            //---Si une ou plusieures écoles sont trouvées
            if($nb_entreprise==1)
            {
                ?>
                <h2>Compléter votre experience</h2>
                <!-- Affichage du formulaire permettant d'ajouter une formation en fonction du filtre -->
                <form method='post' action='ajout_entreprise.php'>
                    <label>
                        <select name="nom_ajout" id='liste_filtres' >
                            <option value="">--Choissisez l'entreprise--</option> <!-- on remplit la liste d'entreprise correspondante selon la config des filtres -->
                            <?php
                            if ($filtre_ajout_secteur=='' AND $filtre_ajout_adresse=='' )
                                {
                                    $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise ORDER BY nom;");
                                    $nom->execute();
                                    $data_nom = $nom->fetch();
                                    //on remplit tant qu'on en trouve
                                    while($data_nom)
                                    {
                                        echo "
                                    <option value='$data_nom[nom]'>$data_nom[nom]</option>
                                    ";
                                        $data_nom = $nom->fetch();
                                    }
                                }
                            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_adresse=='')
                                {
                                    $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise WHERE secteur=(?) ORDER BY nom;");
                                    $nom->execute([$filtre_ajout_secteur]);
                                    $data_nom = $nom->fetch();

                                    while($data_nom)
                                    {
                                        echo "
                                    <option value='$data_nom[nom]'>$data_nom[nom]</option>
                                    ";
                                        $data_nom = $nom->fetch();
                                    }
                                }
                            else if ($filtre_ajout_secteur=='' AND $filtre_ajout_adresse!='' )
                                {
                                    $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise WHERE adressse=(?) ORDER BY nom;");
                                    $nom->execute([$filtre_ajout_adresse]);
                                    $data_nom = $nom->fetch();

                                    while($data_nom)
                                    {
                                        echo "
                                    <option value='$data_nom[nom]'>$data_nom[nom]</option>
                                    ";
                                        $data_nom = $nom->fetch();
                                    }
                                }
                            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_adresse!='' )
                                {
                                    $nom = $bdd->prepare("SELECT DISTINCT nom FROM entreprise WHERE adressse=(?) AND secteur=(?) ORDER BY nom;");
                                    $nom->execute([$filtre_ajout_adresse, $filtre_ajout_secteur]);
                                    $data_nom = $nom->fetch();

                                    while ($data_nom) {
                                        echo "
                                        <option value='$data_nom[nom]'>$data_nom[nom]</option>
                                        ";
                                        $data_nom = $nom->fetch();
                                    }
                                }
                            ?>
                        </select>
                        <!-- formulaire contenant les info à donner pour rajouter une exp pro -->
                        <label for='entree'><br><br>Année d'entrée : </label>
                        <input type="date" name="entree" placeholder="Année d'entrée" required>
                        <label for='entree'><br>Année de fin d'experience (ne pas remplir si en cours) : </label>
                        <input type="date" name="sortie" placeholder="Année fin" >
                        <input type='submit' value='Ajouter' class='bouton_petit'>
                        </label>
                </form>

                <h2>Vous ne trouvez pas ce que vous cherchez ? Rajoutez-le en dessous !</h2>
                <?php
            }
            //---Sinon si aucune école n'est touvée, on demande à l'utilisateur de tout saisir
            else
            {
                ?>
                <h2>Aucune entreprise n'a été trouvée... Rajoutez-la en dessous !</h2>
                <?php
            }
            ?>
            <!-- Affichage du formulaire permettant d'ajouter une formation-->
            <form method='post' action='ajout_entreprise.php'>
                <label>

                    <input type='text' list='liste_nom' placeholder="saisir nom de l'entreprise" value='' name='nom_ajout' required autocomplete='off'>
                    <input type='text' list='liste_adresse' placeholder="saisir la ville" value='<?php echo $filtre_ajout_adresse; ?>' name='adresse_ajout' required autocomplete='off'>
                    <input type='text' list='liste_secteur' placeholder="saisir le secteur" value='<?php echo $filtre_ajout_secteur; ?>' name='secteur_ajout' required autocomplete='off'>

                    <label for='entree'><br><br>Année d'entrée : </label>
                    <input type="date" name="entree" placeholder="Année d'entrée" required>
                    <label for='entree'>Année de fin d'experience (ne pas remplir si en cours) : </label>
                    <input type="date" name="sortie" placeholder="Année de fin" >
                    <input type='submit' value='Ajouter' class='bouton_petit'>
                </label>
            </form>


            <datalist id='liste_adresse'> <!-- permet de charger une liste de saisie prédictive par rapport aux adresse(villes) précédemment connues dans la bdd-->
                <?php
                $req = $bdd->prepare("SELECT DISTINCT adresse FROM entreprise ORDER BY adresse;");

                $req->execute();

                $data = $req->fetch();

                while($data)
                {
                    echo "
                                <option value='$data[adresse]'>
                            ";
                    $data = $req->fetch();
                }
                ?>
            </datalist>
            <datalist id='liste_secteur'> <!-- permet de charger une liste de saisie prédictive par rapport aux secteurs précédemment connues dans la bdd-->
                <?php
                $req = $bdd->prepare("SELECT DISTINCT secteur FROM entreprise ORDER BY secteur;");

                $req->execute();

                $data = $req->fetch();

                while($data)
                {
                    echo "
                                <option value='$data[secteur]'>
                            ";
                    $data = $req->fetch();
                }
                ?>
            </datalist>
        </div>
    </div>
</div>
</body>
</html>

