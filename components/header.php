<div class="header">
  
    <!-- / = index.php -->
    <a class="header-logo" href="/">
        <img class="header-logo-img" src="/assets/images/logo.png">
    </a> 

    <div class="header-user">
        <a class="header-user-name" href="/compte_utilisateur.php">
            <i class="fas fa-user icon-logo"></i>
            <span><?php echo $_SESSION['firstname']; ?></span>
            <span><?php echo $_SESSION['lastname']; ?></span>
            
        </a>
            
        <form class="header-user-logout">
            <button class="header-form-button" type="submit"  formaction= deconnexion.php >
                <i class="fas fa-power-off"></i>
            </button>
        </form> 
    </div>
</div>        
