<?php
// login.php - User login
include 'db.php';
 
$error = '';
 
if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
 
    if (empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
 
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIBLE - Login</title>
    <style>
        /* Internal CSS - Matching signup */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { background: rgba(255,255,255,0.95); padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #333; }
        input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { background: #ff6b6b; color: #fff; padding: 1rem; width: 100%; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; transition: background 0.3s; }
        .btn:hover { background: #ff5252; }
        .error { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; text-align: center; background: #ffebee; color: #c62828; }
        a { text-align: center; display: block; margin-top: 1rem; color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login to AUDIBLE</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username or Email" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        <a href="#" onclick="redirectTo('signup.php')">Don\'t have an account? Sign Up</a>
    </div>
 
    <script>
        function redirectTo(url) { window.location.href = url; }
    </script>
</body>
</html>
