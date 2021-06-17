<?php 
    session_start();
    
    if (!$_SESSION['id']) {
        header('Location: connexion.php');
        exit();
    }
    
    $success_updated = false;

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
    // condition et requête pour la mot de passe 
    if (isset($_POST['submit_password'])) {
     
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

    // condition pour la question secrète et réponse secrète
    if (isset($_POST['submit_question'])) {

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

?>
    
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
     <!-- Importations des fichiers CSS -->
    <link rel="stylesheet" href="compte_utilisateur.css">
   
    <?php include 'components/css-imports.php'; ?>
</head>
<body>

    <?php include 'components/header.php'; ?>

    <div class="titre">Paramètre de <?php echo $user['username']; ?></div>
    
    <?php if ($success_updated): ?>
    <div class="info-message">Vos informations ont été mises à jour</div>
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