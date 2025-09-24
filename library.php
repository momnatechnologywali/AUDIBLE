
<?php
// library.php - Audiobook library with categories
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
 
$category = $_GET['cat'] ?? '';
$where = $category ? "WHERE category = ?" : '';
$stmt = $pdo->prepare("SELECT * FROM books $where ORDER BY title");
$params = $category ? [$category] : [];
$stmt->execute($params);
$books = $stmt->fetchAll();
 
// Handle save book
if (isset($_POST['save_book'])) {
    $book_id = $_POST['book_id'];
    $stmt = $pdo->prepare("INSERT INTO user_progress (user_id, book_id, is_saved) VALUES (?, ?, TRUE) ON DUPLICATE KEY UPDATE is_saved = TRUE");
    $stmt->execute([$_SESSION['user_id'], $book_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIBLE - Library</title>
    <style>
        /* Internal CSS - Grid layout for books */
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
        .book-desc { color: #666; margin-bottom: 1rem; }
        .book-category { background: #667eea; color: #fff; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.9rem; }
        .btn { background: #ff6b6b; color: #fff; padding: 0.5rem 1rem; text-decoration: none; border-radius: 5px; display: inline-block; margin: 0.5rem 0; }
        .btn:hover { background: #ff5252; }
        .save-btn { background: #4caf50; }
        .save-btn:hover { background: #45a049; }
        a { text-align: center; display: block; margin-top: 1rem; color: #667eea; text-decoration: none; }
        @media (max-width: 768px) { .books-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="logo">AUDIBLE</div>
        <nav>
            <a href="#" onclick="redirectTo('index.php')">Home</a>
            <a href="#" onclick="redirectTo('dashboard.php')">Dashboard</a>
            <a href="#" onclick="redirectTo('profile.php')">Profile</a>
        </nav>
    </header>
 
    <div class="container">
        <h1><?php echo $category ? ucfirst($category) . ' Books' : 'All Books'; ?></h1>
        <div class="books-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <img src="<?php echo htmlspecialchars($book['cover_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                    <div class="book-info">
                        <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                        <div class="book-desc"><?php echo substr(htmlspecialchars($book['description']), 0, 100); ?>...</div>
                        <div class="book-category"><?php echo htmlspecialchars($book['category']); ?></div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                            <button type="submit" name="save_book" class="btn save-btn">Save to Library</button>
                        </form>
                        <a href="#" onclick="redirectTo('player.php?book=<?php echo $book['id']; ?>')" class="btn">Listen</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="#" onclick="redirectTo('index.php')">Back to Home</a>
    </div>
 
    <script>
        function redirectTo(url) { window.location.href = url; }
    </script>
</body>
</html>
