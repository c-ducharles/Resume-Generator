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
    <title>Modification scolarité</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="grid-container-acceuil">
    <div class = "page">
        <div class = "info-compte">
            <h1>Ajouter une expérience scolaire</h1><a href="compte.php" class="bouton_petit">Annuler</a>
        </div>
        <div class = "formulaire">
            <?php
            $bdd = new PDO("mysql:host=localhost;dbname=ifd1_gestion_cv;charset=utf8", "root", "");

            $type = $_GET['type'];
            //initialisation de variable pour effectuer le tri
            $filtre_ajout_ville = '';
            $filtre_ajout_secteur = '';
            $filtre_ajout_niveau = '';

            if ($type=='suppr') //si on vient pour supprimer une expériencce scolaire
            {
                $diplome = $_POST['diplome_suppr']; //on récupère les variables pour effectuer la suppresion de la ligne
                $nom_ecole = $_GET['ecole'];
                $ville_ecole = $_GET['ville'];
                $secteur_ecole = $_GET['secteur'];

                $req = $bdd->prepare("DELETE FROM experience_scolaire WHERE experience_scolaire.id_etudiant=(SELECT comptes.id FROM comptes WHERE
                    comptes.email=(?)) AND experience_scolaire.id_diplome=(SELECT diplome.id FROM diplome WHERE diplome.nom_diplome=(?)) AND 
                    experience_scolaire.id_ecole=(SELECT id FROM ecole WHERE nom_ecole=(?) AND ville=(?) AND secteurs=(?));");

                $req->execute([$_SESSION['mail'],$diplome,$nom_ecole,$ville_ecole,$secteur_ecole]);

                header("Location: compte.php");
            }
            else //sinn on vient pour rajouter
            {
                //on charge les variables pour remplir la liste des filtres
                $secteur = $bdd->prepare("SELECT DISTINCT secteurs FROM ecole ORDER BY secteurs;");
                $ville = $bdd->prepare("SELECT DISTINCT ville FROM ecole ORDER BY ville;");
                $niveau = $bdd->prepare("SELECT DISTINCT niveau_etudes FROM ecole ORDER BY niveau_etudes;");
                $diplome = $bdd->prepare("SELECT DISTINCT nom_diplome FROM diplome ORDER BY nom_diplome;");

                $secteur->execute();
                $ville->execute();
                $niveau->execute();
                $diplome->execute();

                $data_secteur = $secteur->fetch();
                $data_ville = $ville->fetch();
                $data_niveau = $niveau->fetch();
                $data_diplome = $diplome->fetch();

                if ($type=='filtre') //si on recharge la page pour appliquer les filtre, on les récupère
                {
                    $filtre_ajout_ville = $_POST['filtre_ajout_ville'];
                    $filtre_ajout_secteur = $_POST['filtre_ajout_secteur'];
                    $filtre_ajout_niveau = $_POST['filtre_ajout_niveau'];
                }
            }
            //--on regarde si on a une école avec les paramétrage des filtre, on est obligé de dissocier pour faire les bonnes requetes
            if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville=='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole ORDER BY nom_ecole;");
                $ecole->execute();
            }
            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville=='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) ORDER BY nom_ecole;");
                $ecole->execute([$filtre_ajout_secteur]);
            }
            else if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville=='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE niveau_etudes=(?) ORDER BY nom_ecole;");
                $ecole->execute([$filtre_ajout_niveau]);
            }
            else if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville!='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE ville=(?) ORDER BY nom_ecole;");
                $ecole->execute([$filtre_ajout_ville]);
            }
            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville=='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) AND niveau_etudes=(?) ORDER BY nom_ecole;");
                $ecole->execute([$filtre_ajout_secteur,$filtre_ajout_niveau]);
            }
            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville!='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) AND ville=(?) ORDER BY nom_ecole;");
                $ecole->execute([$filtre_ajout_secteur,$filtre_ajout_ville]);
            }
            else if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville!='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE ville=(?) AND niveau_etudes=(?) ORDER BY nom_ecole;");
                $ecole->execute([$filtre_ajout_ville,$filtre_ajout_niveau]);
            }
            else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville!='')
            {
                $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) AND niveau_etudes=(?) AND ville=(?) ORDER BY nom_ecole;");
                $ecole->execute([$filtre_ajout_secteur,$filtre_ajout_niveau,$filtre_ajout_ville]);
            }
            $data_ecole = $ecole->fetch();

            if($data_ecole==0) //on regarde s'il y a au moins eu 1 résultat
            {
                $nb_ecole = 0;
            }
            else
            {
                $nb_ecole = 1;
            }
            ?>
            <h2>Vous pouvez affiner votre recherche d'école pour compléter votre formation çi-dessous</h2>
            <!-- Affichage du formulaire permettant de filtrer les écoles -->
            <form method='post' action='page_modif_scolarite.php?type=filtre'>
                <label for='liste_filtres'>Filtrer les écoles par :</label>
                <select name="filtre_ajout_ville" id='liste_filtres' >
                    <option value="">--Choissisez une ville--</option>
                    <?php
                    while($data_ville) //on rempli la liste des villes pour les filtres
                    {
                        echo "<option value='$data_ville[ville]'>$data_ville[ville]</option>";
                        $data_ville = $ville->fetch();
                    }
                    ?>
                </select>
                <select name="filtre_ajout_secteur" id='liste_filtres' >
                    <option value="">--Choissisez un secteur d'activités--</option>
                    <?php
                    while($data_secteur) //on rempli la liste des secteur pour les filtres
                    {
                        echo "<option value='$data_secteur[secteurs]'>$data_secteur[secteurs]</option>";
                        $data_secteur = $secteur->fetch();
                    }
                    ?>
                </select>
                <select name="filtre_ajout_niveau" id='liste_filtres' >
                    <option value="">--Choissisez un niveau d'études--</option>
                    <?php
                    while($data_niveau) //on rempli la liste des niveau d'études pour les filtres
                    {
                        echo "<option value='$data_niveau[niveau_etudes]'>$data_niveau[niveau_etudes]</option>";
                        $data_niveau = $niveau->fetch();
                    }
                    ?>
                </select>
                <input type='submit' value='Appliquer le filtre' class='bouton_petit'>
            </form>
            <?php
            //---Si une ou plusieures écoles sont trouvées
            if($nb_ecole==1)
            {
                ?>
                <h2>Compléter votre formation</h2>
                <!-- Affichage du formulaire permettant d'ajouter une formation en fonction du filtre -->
                <form method='post' action='ajout_diplome.php'>
                    <label>
                    <select name="diplome_ajout" id='liste_diplomes' required>
                        <option value="">--Choissisez un diplome--</option>
                        <?php
                        while($data_diplome) //on rempli la liste des diplomes avec ceux de la bdd
                        {
                            echo "<option value='$data_diplome[nom_diplome]'>$data_diplome[nom_diplome]</option>";
                            $data_diplome = $diplome->fetch();
                        }
                        ?>
                    </select>
                    <select name="ecole_ajout" id='liste_diplomes' required> <!-- On rempli la liste des exoles en fonction des filtres-->
                        <option value="">--Choissisez une ecole--</option>
                        <?php
                        if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville=='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole ORDER BY nom_ecole;");
                            $ecole->execute();
                        }
                        else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville=='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) ORDER BY nom_ecole;");
                            $ecole->execute([$filtre_ajout_secteur]);
                        }
                        else if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville=='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE niveau_etudes=(?) ORDER BY nom_ecole;");
                            $ecole->execute([$filtre_ajout_niveau]);
                        }
                        else if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville!='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE ville=(?) ORDER BY nom_ecole;");
                            $ecole->execute([$filtre_ajout_ville]);
                        }
                        else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville=='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) AND niveau_etudes=(?) ORDER BY nom_ecole;");
                            $ecole->execute([$filtre_ajout_secteur,$filtre_ajout_niveau]);
                        }
                        else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau=='' AND $filtre_ajout_ville!='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) AND ville=(?) ORDER BY nom_ecole;");
                            $ecole->execute([$filtre_ajout_secteur,$filtre_ajout_ville]);
                        }
                        else if ($filtre_ajout_secteur=='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville!='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE ville=(?) AND niveau_etudes=(?) ORDER BY nom_ecole;");
                            $ecole->execute([$filtre_ajout_ville,$filtre_ajout_niveau]);
                        }
                        else if ($filtre_ajout_secteur!='' AND $filtre_ajout_niveau!='' AND $filtre_ajout_ville!='')
                        {
                            $ecole = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole WHERE secteurs=(?) AND niveau_etudes=(?) AND ville=(?) ORDER BY nom_ecole;");
                            $ecole->execute([$filtre_ajout_secteur,$filtre_ajout_niveau,$filtre_ajout_ville]);
                        }
                        $data_ecole = $ecole->fetch();

                        while($data_ecole) //on rempli la liste des ecoles
                        {
                            echo "<option value='$data_ecole[nom_ecole]'>$data_ecole[nom_ecole]</option>";
                            $data_ecole = $ecole->fetch();
                        }
                        ?>
                    </select>
                    <select name="ville_ajout" id='liste_filtres' required>
                        <?php
                        $ville = $bdd->prepare("SELECT DISTINCT ville FROM ecole ORDER BY ville;");
                        $ville->execute();
                        $data_ville = $ville->fetch();
                        if ($filtre_ajout_ville!="") //s'il y a un filtre sur la ville on l'affiche directement
                        {
                            echo "<option value='$filtre_ajout_ville'>$filtre_ajout_ville</option>";
                        }
                        else{                           //sinon on rempli toute la liste
                            echo "<option value=''>--Choisissez une ville--</option>";
                            while($data_ville)
                            {
                                echo "
                                <option value='$data_ville[ville]'>$data_ville[ville]</option>
                                ";
                                $data_ville = $ville->fetch();
                            }
                        }
                        ?>
                    </select>
                    <select name="secteur_ajout" id='liste_filtres' required>
                        <?php
                        $secteur = $bdd->prepare("SELECT DISTINCT secteurs FROM ecole ORDER BY secteurs;");
                        $secteur->execute();
                        $data_secteur = $secteur->fetch();
                        if ($filtre_ajout_secteur!="") //si il y a un filtre sur le secteur on le met directement
                        {
                            echo "<option value='$filtre_ajout_secteur'>$filtre_ajout_secteur</option>";
                        }
                        else{
                            echo "<option value=''>--Choisissez un secteur--</option>"; //sinon on rempli la liste des secteurs
                            while($data_secteur)
                            {
                                echo "
                                <option value='$data_secteur[secteurs]'>$data_secteur[secteurs]</option>
                                ";
                                $data_secteur = $secteur->fetch();
                            }
                        }
                        ?>
                    </select>
                        <!-- Information compplémentaire pour l'ajout dans la bdd -->
                    <label for='entree'><br><br>Année d'entrée : </label>
                    <input type="number" name="entree" placeholder="Année d'entrée" required>
                    <label for='entree'><br>Année de fin de formation (ne pas remplir si en cours) : </label>
                    <input type="number" name="sortie" placeholder="Année fin" >
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
                <h2>Aucune école n'a été trouvée... Rajoutez-la en dessous !</h2>
                <?php
                }
                ?>
                <!-- Affichage du formulaire permettant d'ajouter une formation -->
                <form method='post' action='ajout_diplome.php'>
                    <label>
                        <select name="diplome_ajout" id='liste_diplomes' required>
                            <option value="">--Choissisez un diplome--</option>
                            <?php //On affiche la liste des diplomes dispo
                            $diplome = $bdd->prepare("SELECT DISTINCT nom_diplome FROM diplome ORDER BY nom_diplome;");
                            $diplome->execute();
                            $data_diplome=$diplome->fetch();
                            while($data_diplome)
                            {
                                echo "=<option value='$data_diplome[nom_diplome]'>$data_diplome[nom_diplome]</option>= ";
                                $data_diplome = $diplome->fetch();
                            }
                            ?>
                        </select>
                        <!-- Formulaire pour rajouter une formation à la main -->
                        <input type='text' list='liste_ecole' placeholder="saisir ecole" value='' name='ecole_ajout' required autocomplete='off'>
                        <input type='text' list='liste_ville' placeholder="saisir ville" value='<?php echo $filtre_ajout_ville; ?>' name='ville_ajout' required autocomplete='off'>
                        <input type='text' list='liste_secteur' placeholder="saisir secteur" value='<?php echo $filtre_ajout_secteur; ?>' name='secteur_ajout' required autocomplete='off'>
                        <input type='text' list='liste_niveau' placeholder="saisir niveau" value='<?php echo $filtre_ajout_niveau; ?>' name='niveau_ajout' required autocomplete='off'>
                        <label for='entree'><br><br>Année d'entrée : </label>
                        <input type="number" name="entree" placeholder="Année d'entrée" required>
                        <label for='entree'>Année de fin de formation (ne pas remplir si en cours) : </label>
                        <input type="number" name="sortie" placeholder="Année fin" >
                        <input type='submit' value='Ajouter' class='bouton_petit'>
                    </label>
                </form>

                <datalist id='liste_diplome'> <!-- Permet de faire une liste de saisie prédictive-->
                    <?php
                    $req = $bdd->prepare("SELECT DISTINCT nom_diplome FROM diplome ORDER BY nom_diplome;");

                    $req->execute();

                    $data = $req->fetch();

                    while($data)
                    {
                        echo "
                                <option value='$data[nom_diplome]'>
                            ";
                        $data = $req->fetch();
                    }
                    ?>
                </datalist>
                <datalist id='liste_ville'>  <!-- Permet de faire une liste de saisie prédictive-->
                    <?php
                    $req = $bdd->prepare("SELECT DISTINCT ville FROM ecole ORDER BY ville;");

                    $req->execute();

                    $data = $req->fetch();

                    while($data)
                    {
                        echo "
                                <option value='$data[ville]'>
                            ";
                        $data = $req->fetch();
                    }
                    ?>
                </datalist>
                <datalist id='liste_secteur'>  <!-- Permet de faire une liste de saisie prédictive-->
                    <?php
                    $req = $bdd->prepare("SELECT DISTINCT secteurs FROM ecole ORDER BY secteurs;");

                    $req->execute();

                    $data = $req->fetch();

                    while($data)
                    {
                        echo "
                                <option value='$data[secteurs]'>
                            ";
                        $data = $req->fetch();
                    }
                    ?>
                </datalist>
                <datalist id='liste_niveau'>  <!-- Permet de faire une liste de saisie prédictive-->
                    <?php
                    $req = $bdd->prepare("SELECT DISTINCT niveau_etudes FROM ecole ORDER BY niveau_etudes;");

                    $req->execute();

                    $data = $req->fetch();

                    while($data)
                    {
                        echo "
                                    <option value='$data[niveau_etudes]'>
                                ";
                        $data = $req->fetch();
                    }
                    ?>
                </datalist>
                <datalist id='liste_ecole'>  <!-- Permet de faire une liste de saisie prédictive-->
                    <?php
                    $req = $bdd->prepare("SELECT DISTINCT nom_ecole FROM ecole ORDER BY nom_ecole;");

                    $req->execute();

                    $data = $req->fetch();

                    while($data)
                    {
                        echo "
                                    <option value='$data[nom_ecole]'>
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

