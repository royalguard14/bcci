<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($_SESSION['login_error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_SESSION['login_error']); ?></p>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>
        
        <!-- Corrected form action -->
 <form action="login/submit" method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

    </div>
</body>
</html>
