<?php 

    session_start();

    if (!$_SESSION['id']) {
        header('Location: connexion.php');
        exit();
    }

    $bdd = new PDO('mysql:host=localhost;dbname=gbaf','root','root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $request = $bdd->query('SELECT * FROM partners');
    $partners = [];
    
    while ($partner = $request->fetch()) {
        array_push($partners, $partner);
    }   
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accueil</title>
        <link rel="icon" href="assets/images/logo.png" type="image/x-icon">
    
        <!-- Importations des fichiers CSS -->
        <?php include 'components/css-imports.php'; ?>
    </head>

    <body>

        <?php include 'components/header.php'; ?>

        <h1>Qui sommes-nous ?</h1>
        
        
        <p>Le Groupement Banque Assurance Français (GBAF) est une fédération représentant les 6 grands groupes français :</p>
       
        <ul class="list-bank">
            <div class="bnp-paribas">
                <li>BNP Paribas</li>
            </div> 

            <div class="bpce">
                <li>BPCE</li>
            </div>  

            <div class="crédit-agricole">
                <li>Crédit Agricole</li>
            </div>     
            
            <div class="crédit-mutuel-CIC">
                <li>Crédit Mutuel-CIC</li>
            </div>

            <div class="société-générale">
              
                <li>Société Générale</li>
            </div>

            <div class="la-banque-postale">
                <li>La Banque Postale</li>
            </div>
              
        </ul>
              

        <div class="description">Même s’il existe une forte concurrence entre ces entités, elles vont toutes travailler
        de la même façon pour gérer près de 80 millions de comptes sur le territoire
        national.
        Le GBAF est le représentant de la profession bancaire et des assureurs sur tous
        les axes de la réglementation financière française. Sa mission est de promouvoir
        l'activité bancaire à l’échelle nationale. C’est aussi un interlocuteur privilégié des
        pouvoirs publics.</div> 

        <div class="img-index">
            <img src="assets/images/img-index.png"></img>
        </div>

        <div class="bloc-line">
            <div class="bloc-line-style"></div>
            <h2>Les partenaires</h2> 
        </div>

        <div class="description">Nous vous répertorions un grand nombre d’informations sur les partenaires et acteurs du groupe ainsi que sur les produits et services
        bancaires et financiers afin de proposer à chaque salarié de poster un commentaire et donner son avis.</div>

        <div class="partners">
            <?php
                $i = 0;
                $partners_count = count($partners);
                while ($i < $partners_count) {
            ?>
            <div class="partner">          
                <img class="partner-logo-img" src=<?php echo $partners[$i]['logo_url']; ?> />
                
                <div class="partner-title-description">
                    <div class="partner-title"> 
                        <?php echo $partners[$i]['title']; ?>
                    </div> 

                    <div class="partner-description">
                        <?php echo $partners[$i]['description']; ?>
                    </div>
                </div>

                <a href="page_partenaire.php?id=<?php echo $partners[$i]['id']; ?>">
                    <button class="button-partner" name="submit" type="button">Afficher la suite</button>
                </a>
            </div>      
            <?php        
                    $i++; 
                } 
            ?>
        </div>
        
        <?php include 'components/footer.php'; ?>
        
    
       
    </body>
        
</html>

