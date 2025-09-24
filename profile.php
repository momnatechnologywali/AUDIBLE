<?php
// profile.php - User profile management
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
 
if ($_POST && isset($_POST['update'])) {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    if ($stmt->execute([$new_username, $new_email, $user_id])) {
        $_SESSION['username'] = $new_username;
        $success = 'Profile updated!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIBLE - Profile</title>
    <style>
        /* Internal CSS - Simple profile form */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; }
        .profile-container { max-width: 500px; margin: 0 auto; background: rgba(255,255,255,0.95); padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #333; }
        input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { background: #ff6b6b; color: #fff; padding: 1rem; width: 100%; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; transition: background 0.3s; }
        .btn:hover { background: #ff5252; }
        .success { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; text-align: center; background: #e8f5e8; color: #2e7d32; }
        a { text-align: center; display: block; margin-top: 1rem; color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Profile</h2>
        <?php if (isset($success)): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($user['username']); ?>">
            <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($user['email']); ?>">
            <button type="submit" name="update" class="btn">Update Profile</button>
        </form>
        <a href="#" onclick="redirectTo('index.php')">Back to Home</a>
    </div>
 
    <script>
        function redirectTo(url) { window.location.href = url; }
    </script>
</body>
</html>
