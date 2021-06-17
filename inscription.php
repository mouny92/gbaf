<?php 

$error_password_not_match = false;
$error_username_exists = false;

if (isset($_POST['submit'])) {

    if ($_POST['password'] != $_POST['password_bis']) { // comparaison du mot de passe et mot de passe confirmation 
        $error_password_not_match = true;
    }
    else {
        $bdd = new PDO('mysql:host=localhost;dbname=gbaf','root','root');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $request = $bdd->prepare('SELECT username FROM users WHERE username =  :username'); // comparer l'identifiant avec un autre existant dans la base de donnée 
    
        $request->execute(array( 
        'username' =>  $_POST['username']));
        $user = $request->fetch();

        if ($user) {
            $error_username_exists = true;
        }
        else {
            // créer un nouvel utilisateur
            $request = $bdd->prepare(
                'INSERT INTO users(lastname, firstname, username, password, secret_question, secret_answer) 
                VALUES (?,?,?,?,?,?)'
            );
        
            $userData = array(
                $_POST['lastname'],
                $_POST['firstname'],
                $_POST['username'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $_POST['secret_question'],
                $_POST['secret_answer']
            );
            $request->execute($userData);

            // récupérer l'utilisateur précédemment créé pour obtenir son id
            $request = $bdd->prepare('SELECT * FROM users WHERE username =  :username');
            $request->execute(
                array('username' =>  $_POST['username'])
            );
            $user = $request->fetch();
        
            // démarrer la session
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            header('Location: index.php');
            exit();
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
    <title>Inscription</title>
    <link rel="stylesheet" href="inscription.css">

    <!-- Importations des fichiers CSS -->
    <?php include 'components/css-imports.php'; ?>
</head>

<body>

    <?php if ($error_password_not_match): ?>
        <div class="error_message">Mot de passe différent</div>  
    <?php endif ?>

    <?php if ($error_username_exists): ?>
        <div class="error_message">Ce nom d'utilsateur existe déjà</div>  
    <?php endif ?>

<!-- Formulaire d'inscription -->

<form action="" method=POST>

    <img class="logo-img" src="/assets/images/logo.png">   

    <div class="titre">Inscription</div>

    <div class="form-group">
        <label  for="lastname">Nom</label>
        <input id="lastname" type="text" name="lastname" required />
    </div>

    <div class="form-group">
        <label for="firstname">Prénom</label>
        <input id="firstname" type="text" name="firstname" required />
    </div>

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

    <div class="form-group form-group-with-icon">
        <label for="password_bis">Confirmer votre mot de passe</label>
        <input id="password_bis" type="password" name="password_bis" required />
        <i class="fas fa-lock form-group-icon"></i>
    </div>
    
    <div class="form-group form-group-with-icon">
        <label for="secret_question">Question secrète</label> 
        <input id="secret_question" type="text" name="secret_question" required />    
        <i class="fas fa-question form-group-icon"></i>
        </div>      
    </div>

    <div class="form-group">
        <label for="secret_answer">Réponse à la question secrète</label>
        <input id="secret_answer" type="text" name="secret_answer" required />
    </div>


    <button name="submit" type="submit">S'inscrire</button>

    <a class="link-sign-up" href="connexion.php">Se connecter</a>
       
    <a class="link-new-password" href="nouveau_mot_de_passe.php">Mot de passe oublié ?</a>

</form>  
<footer>

</footer>
</body>
</html>
