<?php require_once('inc/auth-header.php'); ?>

    <form action="" id="forgotForm" class="authform" role="form" method="post" accept-charset="utf-8">
    
        <img src="http://lorempixel.com/120/120/abstract/10" class="img-responsive img-circle logo" />
    
        <h4 class="heading"><strong>Forgot Your Password?</strong></h4>
        
        <div class="well well-sm">Type in your email address. Then we'll email a code to this address.</div>
        
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
            <input type="email" class="form-control" name="email" placeholder="Email address" required autofocus />
        </div>
        
        <button class="btn btn-primary btn-block"><i class="fa fa-unlock-alt"></i> RETRIEVE PASSWORD</button>
        
        <a href="login.php">back to login <i class="fa fa-angle-double-right"></i></a>
    </form>

<?php require_once('inc/auth-footer.php'); ?>