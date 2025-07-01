<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LCN Management</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/public/css/login.css">
</head>
<body>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="container">
        <h2>Admin Login</h2>
        <form action="<?php echo BASE_PATH; ?>/login" method="POST">
            <div class="input-box">
                <input type="text" name="username" required>
                <span>Username</span>
            </div>
            <div class="input-box">
                <input type="password" name="password" required>
                <span>Password</span>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html> 