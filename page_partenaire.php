<?php 
    session_start();

    if (!$_SESSION['id']) {
        header('Location: connexion.php');
        exit();
    }

    $sucess_new_comment = false;

    
    $bdd = new PDO('mysql:host=localhost;dbname=gbaf','root','root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $request = $bdd->prepare(
        'SELECT *
        FROM partners
        WHERE id = :id'
    ); // recupére l'ensemble des infos du partner
    
    $request->execute(
        array('id' =>  $_GET['id'])
    );
    $partner = $request->fetch();   
    
    if (!$partner) {
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['submit_comment'])) { // requête pour insérer le commentaire dans la BDD 
        $request = $bdd->prepare(
            'INSERT INTO comments (user_id, partner_id, created_at, comment) 
            VALUES (?,?,NOW(),?)'
        );
            
        $commentData = array(
            $_SESSION['id'], // on lui demande l'id qui se trouve dans la session de l'utilsateur
            $_GET['id'],
            $_POST['comment']
        );

        $request->execute($commentData);

        $sucess_new_comment = true; 
    }
       
    $request = $bdd->prepare(
        'SELECT
            comments.comment AS comments_comment,
            comments.created_at AS comments_created_at,
            users.username AS users_username
        FROM comments
        LEFT JOIN users ON users.id = comments.user_id
        WHERE partner_id = :partner_id
        ORDER BY created_at DESC'
    ); // récupère les commentaires

    $comments = []; // variable comments qui contient tous les commentaires 
    $request->execute(
        array('partner_id' =>  $_GET['id'])
    );

    while ($comment = $request->fetch()) {
        array_push($comments, $comment); // ajoute un commentaire au tableau comments 
    }

    // LIKE / DISLIKE
    if (isset($_POST["submit_like"]) || isset($_POST["submit_dislike"])) {

        $liked = 0;
        $disliked = 0;
       
        if (isset($_POST["submit_like"])) {
            $liked = 1;
        } 
        
        if (isset($_POST["submit_dislike"])) {
            $disliked = 1;
        }

        $request = $bdd->prepare('SELECT * FROM users_partners_feedback WHERE partner_id =  :partner_id AND user_id = :user_id'); // comparer l'identifiant avec un autre existant dans la base de donnée 
    
        $request->execute(
            array( 
                'partner_id' =>  $_GET['id'],
                'user_id' => $_SESSION['id']
            )
        );
        $user_feedback = $request->fetch();


        // J'insére un like ou un dislike s'il n'a jamais eu d'interaction
        if (!$user_feedback) {
            $request = $bdd->prepare(
            'INSERT INTO users_partners_feedback (user_id, partner_id, liked, disliked) 
            VALUES (?,?,?,?)'
            );
                
            $likeData = array(
                $_SESSION['id'], // on lui demande l'id qui se trouve dans la session de l'utilsateur
                $_GET['id'], // c'est l'id du partenaire
                $liked, // on mets le like à 1
                $disliked // on mets le dislike à 0 
            );
    
            $request->execute($likeData);
        }
        elseif (($user_feedback['liked'] && $liked) || ($user_feedback['disliked'] && $disliked))  {
        // Je modifie l'user qui à déja like ou dislike (il a déjà eu une interaction) 
            $request = $bdd->prepare(
                'DELETE FROM users_partners_feedback 
                WHERE user_id = :user_id AND partner_id = :partner_id'     
            );

            $request->execute(
                array( 
                    'partner_id' =>  $_GET['id'],
                    'user_id' => $_SESSION['id'] 
                )
            );
        }
        else {
            $request = $bdd->prepare(
                'UPDATE users_partners_feedback 
                SET user_id = :user_id,
                partner_id = :partner_id, 
                liked = :liked, 
                disliked = :disliked 
                WHERE user_id = :user_id AND partner_id = :partner_id'     
            );

            $request->execute(
                array( 
                    'partner_id' =>  $_GET['id'],
                    'user_id' => $_SESSION['id'],
                    'liked' => $liked,
                    'disliked' => $disliked 
                )
            );
        }
        


    }

    
    // on utilise la fonction d'agrégation SUM qui calcule une somme sur le champs qu'on lui fournit (liked et dislked)
    $request = $bdd->prepare(
        'SELECT SUM(liked), SUM(disliked)
        FROM users_partners_feedback
        WHERE partner_id = :partner_id'
    ); 
    
    $request->execute(
        array('partner_id' =>  $_GET['id']) // :partner_id sera remplacé par le paramètre id de l'url (qui corresponds au partenaire id)
    );
    $partner_feedback = $request->fetch(); 
    
    $number_like = 0;
    $number_dislike = 0;

    if ($partner_feedback[0]) {
        $number_like = $partner_feedback[0];
    }

    if ($partner_feedback[1]) {
        $number_dislike = $partner_feedback[1];
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $partner['title']; ?></title>
        <link rel="icon" href="/assets/images/logo_gbaf.png" type="image/x-icon">
        <link rel="stylesheet" href="page_partenaire.css">

        <!-- Importations des fichiers CSS -->
        <?php include 'components/css-imports.php'; ?>
    </head>

    <body>
        
        <?php include 'components/header.php'; ?>

        <div class="logo">
            <img class="logo-img" src="<?php echo $partner['logo_url']; ?>"/>
        </div>

        <div class="partner-info">
            <h1 class="partner-info-title"> 
                <?php echo $partner['title']; ?>
            </h1>

            <div class="partner-info-description"> 
                <?php echo $partner['description']; ?>
            </div>

        </div>

       

        <div class="interaction">
            <form class="interaction-like" method="POST" action="">
                <div class="interaction-like-partner">
                    <button class="interaction-like-button"  name="submit_like" type="submit">
                        <i class="fas fa-thumbs-up button-like"></i>
                    </button>

                    <div class="interaction-like-number">
                        <?php echo $number_like; ?>
                    </div>
                </div>
            </form>

            <form class="interaction-dislike" method="POST" action="">
                <div class="interaction-dislike-partner">
                    <button class="interaction-dislike-button"  name="submit_dislike" type="submit">
                        <i class="fas fa-thumbs-down  button-dislike"></i>
                    </button>

                    <div class="interaction-dislike-number">
                        <?php echo $number_dislike;  ?>
                    </div>
                </div>   
            </form>
        </div>

        <div class="interaction-comments">  
            <div class="interaction-comments-bg"></div>  
            <div class="bloc-line">
                <div class="bloc-line-style"></div>
                <h2>Commentaires</h2> 
            </div>

            <form class="form-comments" action="" method="POST">
                <div class="comments">
                    <label class="comments-title" for="comment">Poster un commentaire</label>
                    <textarea class="comments-textarea" id="comment" type="text" name="comment" required ></textarea>
                </div>
                    

                <button class="comments-button" name="submit_comment" type="submit">Envoyer</button>
        
                <?php if ($comments): ?>
                    <div class="comments-block">
                        <div class="comments-title">Commentaires</div>

                        <?php
                            $i = 0;
                            $comments_count = count($comments);
                            while ($i < $comments_count) {
                        ?>
                            <div class="comment">
                                <div class="comment-username"><?php echo $comments[$i]['users_username']; ?></div>
                                <div class="comment-created-at"><?php echo $comments[$i]['comments_created_at']; ?></div>
                                <div class="comment-comment"><?php echo $comments[$i]['comments_comment']; ?></div>
                            </div>
                        
                        <?php 
                                $i++; 
                            }    
                        ?>
                    </div>
                <?php endif ?>
            </form>
        </div>

        <?php include 'components/footer.php'; ?>
    </body>
</html>
   
