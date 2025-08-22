<?php

$title = "Register";

include __DIR__ . '/./layouts/header.php';

?>

<main id="register">
    <div class="form-container">
        <h1>Sign Up</h1>
        <?php flash('register-success'); ?>
        <?php flash('register-error'); ?>
        <form action="/roster/controllers/Users.php" method="POST">
            <input type="hidden" name="action" value="register">

            <div class="form-group">
                <input type="text" class="form-input" id="username" name="username" placeholder="Username">
                <label for="username" class="form-label">Username</label>
                <i class="ri-user-line"></i>
            </div>
            <div class="form-group">
                <input type="text" class="form-input" id="email" name="email" placeholder="Email">
                <label for="email" class="form-label">Email</label>
                <i class="ri-mail-line"></i>
            </div>
            <div class="form-group">
                <input type="password" class="form-input" id="password" name="password" placeholder="Password">
                <label for="password" class="form-label">Password</label>
                <i class="ri-lock-line"></i>
            </div>
            <div class="form-group">
                <input type="password" class="form-input" id="repeatPwd" name="repeatPwd" placeholder="Repeat Password">
                <label for="repeatPwd" class="form-label">Repeat Password</label>
                <i class="ri-lock-line"></i>
            </div>
            <button type="submit" class="form-btn">Sign Up</button>
        </form>
        <div class="options">
            Already have account? <a href="./login">Login Here</a>
        </div>
    </div>
</main>

<?php

include './views/layouts/footer.php';

?>