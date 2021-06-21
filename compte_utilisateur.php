<?php 

    include 'utils/validators.php';

    session_start();
    
    if (!$_SESSION['id']) {
        header('Location: connexion.php');
        exit();
    }
    
    $success_updated = false;

    $error_form = false;
    $error_firstname_invalid = false;
    $error_lastname_invalid = false;
    $error_password_invalid = false;
    $error_secret_question_invalid = false;
    $error_secret_answer_invalid = false;

    $bdd = new PDO('mysql:host=localhost;dbname=gbaf','root','root');

    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            
    /*Récupération de l'utilisateur*/
    $request = $bdd->prepare('SELECT * FROM users WHERE id =  :id ');
    
    $request->execute(array( 
        'id' =>  $_SESSION['id']));
    $user = $request->fetch();
    
    // on mofidie les données dans la base de donnée et on rafraichit ses  nouvelles données en exécutant une nouvelle requête
    // condition et requête pour le nom, prénom de l'utilisateur    
    if (isset($_POST['submit_name'])) {

        if (!is_firstname_valid($_POST['firstname'])) {
            $error_firstname_invalid = true;
            $error_form = true;
        }
        if (!is_lastname_valid($_POST['lastname'])) {
            $error_lastname_invalid = true;
            $error_form = true;
        }
    
        if (!$error_form) {
            $request = $bdd->prepare(
                'UPDATE users 
                SET lastname = :lastname,
                firstname = :firstname
                WHERE id = :id'     
            );
        
            $request->execute(
                array( 
                    'lastname' => $_POST['lastname'],
                    'firstname' => $_POST['firstname'],
                    'id' => $user['id']
                )
            );
    
            $request = $bdd->prepare('SELECT * FROM users WHERE id =  :id ');
        
            $request->execute(array( 
                'id' =>  $_SESSION['id']));
            $user = $request->fetch();
            $success_updated = true;
        }
    }
    // condition et requête pour la mot de passe 
    if (isset($_POST['submit_password'])) {
        if (!is_password_valid($_POST['password'])) {
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
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'id' => $user['id']
                )
            );
        
            $request = $bdd->prepare('SELECT * FROM users WHERE id =  :id ');
            
            $request->execute(array( 
                'id' =>  $_SESSION['id']));
            $user = $request->fetch();
            $success_updated = true;
        }
    } 

    // condition pour la question secrète et réponse secrète
    if (isset($_POST['submit_question'])) {
        if (!is_secret_question_valid($_POST['secret_question'])) {
            $error_secret_question_invalid = true;
            $error_form = true;
        }
        if (!is_secret_answer_valid($_POST['secret_answer'])) {
            $error_secret_answer_invalid = true;
            $error_form = true;
        }

        if (!$error_form) {
            $request = $bdd->prepare(
                'UPDATE users 
                SET secret_question = :secret_question,
                secret_answer = :secret_answer
               
                WHERE id = :id'     
            );
        
            $request->execute(
                array( 
                    'secret_question' => $_POST['secret_question'],
                    'secret_answer' => $_POST['secret_answer'],
                    'id' => $user['id']
                )
            );
    
            $request = $bdd->prepare('SELECT * FROM users WHERE id =  :id ');
        
            $request->execute(array( 
                'id' =>  $_SESSION['id']));
            $user = $request->fetch();
            $success_updated = true;
        }
    }         

?>
    
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres de <?php echo $user['username']; ?></title>
    <link rel="icon" href="assets/images/logo.png" type="image/x-icon">

     <!-- Importations des fichiers CSS -->
     <?php include 'components/css-imports.php'; ?>
     <link rel="stylesheet" href="compte_utilisateur.css">
</head>
<body>

    <?php include 'components/header.php'; ?>

    <div class="titre">Paramètres de <?php echo $user['username']; ?></div>
    
    <?php if ($success_updated): ?>
    <div class="info-message">Vos informations ont été mises à jour</div>
    <?php endif ?>

    <?php if ($error_form): ?>
        <div class="error_message">
            <?php if ($error_firstname_invalid): ?>
                <div><?php echo firstname_invalid_message(); ?></div>
            <?php endif ?>

            <?php if ($error_lastname_invalid): ?>
                <div><?php echo lastname_invalid_message(); ?></div>
            <?php endif ?>

            <?php if ($error_password_invalid): ?>
                <div><?php echo password_invalid_message(); ?></div>
            <?php endif ?>

            <?php if ($error_secret_question_invalid): ?>
                <div><?php echo secret_question_invalid_message(); ?></div>
            <?php endif ?>

            <?php if ($error_secret_answer_invalid): ?>
                <div><?php echo secret_answer_invalid_message(); ?></div>
            <?php endif ?>
        </div>
    <?php endif ?>

<!-- formualaire pour le prénom, nom et nom utilisateur -->

    <form class="form_user"action="" method="POST">
        <div class="form-group form-group-with-icon">
            <label class="form-title" for="username">Nom d'utilisateur</label>
            <input type="text" name="username" disabled="disabled" value="<?php echo $user['username']; ?>" />
            <i class="fas fa-user form-group-icon"></i>
        </div>

        <div  class="form-group">
            <label class="form-title" for="lastname">Nom</label>
            <input id="lastname" type="text" name="lastname" value="<?php echo $user['lastname']; ?>"required />
        </div>

        <div  class="form-group">
            <label  class="form-title" for="firstname">Prénom</label>
            <input id="firstname" type="text" name="firstname" value="<?php echo $user['firstname']; ?>"required />
        </div>

        <div>
            <button name="submit_name" type="submit">Enregistrer</button>
        </div>
    </form>     

<!-- formualaire pour le mot de passe -->

    <form class="form_user" action="" method="POST">
        <div class="form-group form-group-with-icon">
            <label class="form-title" for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required />
            <i class="fas fa-lock form-group-icon"></i>
        </div>

        <div>
            <button name="submit_password" type="submit">Enregistrer</button>
        </div>
             
    </form>
         
<!-- formualaire pour la question et réponse secrète -->

    <form class="form_user" action="" method="POST">
        <div class="form-group form-group-with-icon">
            <label class="form-title" for="secret_question">Question secrète</label>
            <input id="secret_question" type="secret_question" name="secret_question" value="<?php echo $user['secret_question']; ?>" required />
            <i class="fas fa-question form-group-icon"></i>
        </div> 

        <div class="form-group">
            <label class="form-title" for="secret_answer">Réponse à la question secrète</label>
            <input id="secret_answer" type="text" name="secret_answer" required />
        </div>

        <div>
            <button name="submit_question" type="submit">Enregistrer</button>
        </div>
                
    </form>
    <?php include 'components/footer.php'; ?>
</body>
</html>