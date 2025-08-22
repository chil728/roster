<?php

$title = "Login";

include __DIR__ . '/./layouts/header.php';

?>

<main id="login">
    <div class="form-container">
        <h1>Login</h1>
        <?php flash('login-success'); ?>
        <?php flash('login-error'); ?>
        <form action="/roster/controllers/Users.php" method="POST">
            <input type="hidden" name="action" value="login">

            <div class="form-group">
                <input type="text" class="form-input" id="username-email" name="username-email" placeholder="Username or Email">
                <label for="username-email" class="form-label">Username or Email</label>
                <i class="ri-user-line"></i>
            </div>
            <div class="form-group">
                <input type="password" class="form-input" id="password" name="password" placeholder="Password">
                <label for="password" class="form-label">Password</label>
                <i class="ri-lock-line"></i>
            </div>
            <button type="submit" class="form-btn">Login</button>
        </form>
        <div class="options">
            Don't have an account? <a href="./register">Sign up here</a>
        </div>
    </div>
</main>

<?php

include './views/layouts/footer.php';

?>