<?php
// dashboard.php - User dashboard for saved books and progress
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT b.*, up.progress_seconds, up.last_listened FROM books b JOIN user_progress up ON b.id = up.book_id WHERE up.user_id = ? AND up.is_saved = TRUE");
$stmt->execute([$user_id]);
$saved_books = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIBLE - Dashboard</title>
    <style>
        /* Internal CSS - Dashboard cards */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #333; }
        header { background: rgba(0,0,0,0.8); padding: 1rem 2rem; display: flex; justify-content: space-between; }
        .logo { color: #fff; font-size: 1.8rem; font-weight: bold; }
        nav a { color: #fff; text-decoration: none; margin-left: 1rem; }
        .container { padding: 2rem; }
        h1 { color: #fff; margin-bottom: 1rem; text-align: center; }
        .books-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
        .book-card { background: rgba(255,255,255,0.95); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .book-cover { width: 100%; height: 300px; object-fit: cover; }
        .book-info { padding: 1rem; }
        .book-title { font-size: 1.3rem; font-weight: bold; margin-bottom: 0.5rem; }
        .progress { background: #ddd; height: 5px; border-radius: 5px; margin: 1rem 0; }
        .progress-bar { background: #4caf50; height: 100%; border-radius: 5px; transition: width 0.3s; }
        .btn { background: #ff6b6b; color: #fff; padding: 0.5rem 1rem; text-decoration: none; border-radius: 5px; display: inline-block; margin: 0.5rem 0; }
        .btn:hover { background: #ff5252; }
        a { text-align: center; display: block; margin-top: 1rem; color: #667eea; text-decoration: none; }
        @media (max-width: 768px) { .books-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="logo">AUDIBLE</div>
        <nav>
            <a href="#" onclick="redirectTo('index.php')">Home</a>
            <a href="#" onclick="redirectTo('library.php')">Library</a>
            <a href="#" onclick="redirectTo('profile.php')">Profile</a>
        </nav>
    </header>
 
    <div class="container">
        <h1>Your Library</h1>
        <?php if (empty($saved_books)): ?>
            <p style="color: #fff; text-align: center;">No saved books yet. <a href="#" onclick="redirectTo('library.php')">Browse now</a></p>
        <?php else: ?>
            <div class="books-grid">
                <?php foreach ($saved_books as $book): 
                    $progress_percent = $book['duration'] > 0 ? ($book['progress_seconds'] / $book['duration']) * 100 : 0;
                ?>
                    <div class="book-card">
                        <img src="<?php echo htmlspecialchars($book['cover_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                        <div class="book-info">
                            <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%;"></div>
                            </div>
                            <small>Progress: <?php echo round($progress_percent); ?>% (<?php echo gmdate('H:i:s', $book['progress_seconds']); ?>)</small>
                            <a href="#" onclick="redirectTo('player.php?book=<?php echo $book['id']; ?>&resume=<?php echo $book['progress_seconds']; ?>')" class="btn">Continue Listening</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <a href="#" onclick="redirectTo('library.php')">Browse More</a>
    </div>
 
    <script>
        function redirectTo(url) { window.location.href = url; }
    </script>
</body>
</html>
