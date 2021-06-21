<?php

include 'utils/validators.php';

$user = null;
$answer_correct = false;

$error_form = false;
$error_password_invalid = false;

// on cherche username dans la BDD
if (isset($_POST['submit_username']) || isset($_POST['submit_answer']) || isset($_POST['submit_new_password'])) {
    $bdd = new PDO('mysql:host=localhost;dbname=gbaf','root','root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $request = $bdd->prepare('SELECT * FROM users WHERE username =  :username');
    $request->execute(array('username' => $_POST['username']));
    $user = $request->fetch();
}

// on vérifie la reponse secrète
if (isset($_POST['submit_answer'])) {
    $answer_form = strtolower($_POST['secret_answer']);
    $answer_bdd = strtolower($user['secret_answer']);

    if ($answer_form == $answer_bdd) {
        $answer_correct = true; 
    }
    else {
        $answer_correct = false;
    }
}

// on change le mot de passe
if (isset($_POST['submit_new_password'])) {
    if (!is_password_valid($_POST['new_password'])) {
        $error_password_invalid = true;
        $error_form = true;
    }

    if (!$error_form) {
        $request = $bdd->prepare(
            'UPDATE users 
            SET password = :password
            WHERE id = :id'     
        );
    
        $request->execute(
            array( 
                'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT),
                'id' => $user['id']
            )
        );
    
        session_start();
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        header('Location: index.php');
        exit();
    }
}

    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe</title>
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">

    <!-- Importations des fichiers CSS -->
    <?php include 'components/css-imports.php'; ?>
    <link rel="stylesheet" href="nouveau_mot_de_passe.css">
</head>
<body>

    <?php if (isset($_POST['submit_username']) && !$user): ?>
        <div class="error_message">Votre réponse est incorrecte</div>
    <?php endif ?> 
   

    <?php if (isset($_POST['submit_answer']) && !$answer_correct): ?>
        <div class="error_message">Votre réponse est incorrecte</div>
    <?php endif ?> 

    <?php if ($error_form): ?>
        <div class="error_message">
            <?php if ($error_password_invalid): ?>
                <div><?php echo password_invalid_message(); ?></div>
            <?php endif ?>
        </div>
    <?php endif ?> 

    <form action="" method=POST>

        <img class="logo-img" src="/assets/images/logo.png"> 
        
        <div class="titre">Réinitialiser mot de passe</div>

        <?php if ($user): ?>
            <div class="form-group form-group-with-icon form-group-with-text">
                <label for="username">Nom d'utilisateur</label>
                <div class="form-group-text answer"><?php echo $user['username']; ?></div>
                <input type="hidden" name="username" value="<?php echo $user['username']; ?>" required />
                <i class="fas fa-user form-group-icon"></i>
            </div>
        <?php else: ?>
            <div class="form-group form-group-with-icon">
                <label for="username">Nom d'utilisateur</label>
                <input id="username" type="text" name="username" required />
                <i class="fas fa-user form-group-icon"></i>
            </div>
        <?php endif ?> 


        <?php if (!$user): ?>

            <button type="submit" name="submit_username">Valider</button>
            
        <?php endif ?> 


        <?php if ($user && !$answer_correct): ?>
            <div class="form-group form-group-with-icon form-group-with-text">
                <label for="secret_question">Question secrete</label>
                <div class="form-group-text answer"><?php echo $user['secret_question'];?></div>
                <i class="fas fa-question form-group-icon"></i>
            </div> 
        
            <div class="form-group">
                <label for="secret_answer">Réponse secretes</label>
                <input id="secret_answer" type="text" name="secret_answer" required />
            </div>

        
            <button type="submit" name="submit_answer">Valider</button>
        
        <?php endif ?> 


        <?php if ($answer_correct): ?>
            <div class="form-group form-group-with-icon">
                <label for="new_password">Nouveau mot de passe</label>
                <input id="new_password" type="password" name="new_password" required />
                <i class="fas fa-lock form-group-icon"></i>
            </div>

        
            <button type="submit" name="submit_new_password">Valider</button>
        
        <?php endif ?>  

    <a class="link-sign-up" href="connexion.php">Se connecter</a>
       
    <a class="link-new-password" href="inscription.php">S'inscire</a>
        
    </form> 
   <footer>

   </footer>
</body>
</html>

