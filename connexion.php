<?php

$error_bad_password = false;
$error_bad_username = false; 

if (isset($_POST['submit'])) {
    
    $bdd = new PDO('mysql:host=localhost;dbname=gbaf','root','root');

    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $username=$_POST['username'];
    $password= password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    //requête préprarée d'insertion
    
    /*Récupération de l'utilisateur et du mot de passe hashé*/
    $request = $bdd->prepare('SELECT * FROM users WHERE username =  :username');
    
    $request->execute(array( 
        'username' =>  $username));
    $user = $request->fetch(); 


    if (!$user) {
        $error_bad_username = true;
    }
    else {
        /*Comparaison du pass envoyé via le formulaire avec la base*/
        $isPasswordCorrect = password_verify($_POST['password'], $user['password']);

        if ($isPasswordCorrect)  {
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            header('Location: index.php');
            exit();
        } 
        else {
            $error_bad_password = true;
        }
    }

}    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="connexion.css">

    <!-- Importations des fichiers CSS -->
    <?php include 'components/css-imports.php'; ?>
</head>
<body>

   
    <?php if ($error_bad_username == true): ?>
            <div class="error_message">Mauvais identifiant ou mot de passe</div>
    <?php endif ?>  
        
    <?php if ($error_bad_password == true): ?>
        <div class="error_message">Mauvais identifiant ou mot de passe</div>
    <?php endif ?>

<!-- Formulaire de connexion -->     
    <form action="" method=POST>

        <img class="logo-img" src="/assets/images/logo.png">

        <div class="titre">Connexion</div>
     
        <div class="form-group form-group-with-icon"> 
            <label for="username">Nom d'utilisateur</label>
            <input id="username" type="text" name="username" required />
            <i class="fas fa-user form-group-icon"></i>
        </div>

        <div class="form-group form-group-with-icon">
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required />
            <i class="fas fa-lock form-group-icon"></i>
        </div>

        <button class= "button"name="submit" type="submit">Connexion</button>   
 
        <a class="link-sign-up" href="inscription.php">S'inscrire</a>
       
        <a class="link-new-password" href="nouveau_mot_de_passe.php">Mot de passe oublié ?</a>

        
    </form>

    <footer>
        
    </footer>

</body>
</html>