<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster | Logout</title>
</head>

<body>
    <form id="logout" action="/roster/controllers/Users.php" method="POST">
        <input type="hidden" name="action" value="logout">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            logoutForm = document.getElementById('logout');
            logoutForm.submit();
        });
    </script>
</body>

</html>