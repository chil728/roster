<?php

$title = "Email Verification";
include __DIR__ . '/./layouts/header.php';

?>

<main id="verify">
    <div class="form-container">
        <h1>Enter the Code</h1>

        <?php flash('verify'); ?>
        <?php flash('verify-error'); ?>

        <form action="/Roster/controllers/Users.php" method="POST">
            <input type="hidden" name="action" value="verify">
            <div class="form-group">
                <input type="text" class="form-input" id="code" name="code" placeholder="Code">
                <label for="code" class="form-label">Code</label>
            </div>
            <button type="submit">Confirm</button>
        </form>
    </div>
</main>