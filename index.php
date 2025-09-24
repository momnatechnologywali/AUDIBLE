<?php
// index.php - Homepage showcasing featured audiobooks and categories
include 'db.php';
 
// Fetch featured books
$stmt = $pdo->query("SELECT * FROM books WHERE is_featured = TRUE LIMIT 4");
$featured_books = $stmt->fetchAll();
 
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIBLE - Home</title>
    <style>
        /* Internal CSS - Professional, modern design inspired by Audible */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #333; line-height: 1.6; }
        header { background: rgba(0,0,0,0.8); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .logo { color: #fff; font-size: 1.8rem; font-weight: bold; }
        nav a { color: #fff; text-decoration: none; margin-left: 1rem; padding: 0.5rem 1rem; border-radius: 20px; transition: background 0.3s; }
        nav a:hover { background: rgba(255,255,255,0.2); }
        .hero { text-align: center; padding: 4rem 2rem; color: #fff; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero p { font-size: 1.2rem; margin-bottom: 2rem; }
        .btn { background: #ff6b6b; color: #fff; padding: 1rem 2rem; text-decoration: none; border-radius: 30px; font-size: 1.1rem; transition: transform 0.3s, box-shadow 0.3s; display: inline-block; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .categories { padding: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .category { background: rgba(255,255,255,0.9); padding: 1rem; border-radius: 10px; text-align: center; cursor: pointer; transition: transform 0.3s; }
        .category:hover { transform: scale(1.05); }
        .featured { padding: 2rem; }
        .featured h2 { color: #fff; margin-bottom: 1rem; text-align: center; }
        .books-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
        .book-card { background: rgba(255,255,255,0.95); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transition: transform 0.3s; }
        .book-card:hover { transform: translateY(-5px); }
        .book-cover { width: 100%; height: 300px; object-fit: cover; }
        .book-info { padding: 1rem; }
        .book-title { font-size: 1.3rem; font-weight: bold; margin-bottom: 0.5rem; }
        .book-desc { color: #666; margin-bottom: 1rem; }
        .book-category { background: #667eea; color: #fff; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.9rem; }
        footer { background: rgba(0,0,0,0.8); color: #fff; text-align: center; padding: 1rem; margin-top: 2rem; }
        @media (max-width: 768px) { .hero h1 { font-size: 2rem; } .books-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="logo">AUDIBLE</div>
        <nav>
            <?php if ($is_logged_in): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="library.php">Library</a>
                <a href="#" onclick="redirectTo('profile.php')">Profile</a>
                <a href="#" onclick="logout()">Logout</a>
            <?php else: ?>
                <a href="#" onclick="redirectTo('login.php')">Login</a>
                <a href="#" onclick="redirectTo('signup.php')">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>
 
    <section class="hero">
        <h1>Discover Your Next Story</h1>
        <p>Immerse yourself in worlds of audio magic.</p>
        <a href="library.php" class="btn" onclick="redirectTo('library.php')">Browse Books</a>
    </section>
 
    <section class="categories">
        <h2 style="grid-column: 1 / -1; text-align: center; color: #fff; font-size: 2rem;">Categories</h2>
        <div class="category" onclick="redirectTo('library.php?cat=Fiction')">Fiction</div>
        <div class="category" onclick="redirectTo('library.php?cat=Non-Fiction')">Non-Fiction</div>
        <div class="category" onclick="redirectTo('library.php?cat=Self-Development')">Self-Development</div>
        <div class="category" onclick="redirectTo('library.php?cat=Mystery')">Mystery</div>
    </section>
 
    <section class="featured">
        <h2>Featured Audiobooks</h2>
        <div class="books-grid">
            <?php foreach ($featured_books as $book): ?>
                <div class="book-card">
                    <img src="<?php echo htmlspecialchars($book['cover_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                    <div class="book-info">
                        <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                        <div class="book-desc"><?php echo substr(htmlspecialchars($book['description']), 0, 100); ?>...</div>
                        <div class="book-category"><?php echo htmlspecialchars($book['category']); ?></div>
                        <a href="#" onclick="redirectTo('player.php?book=<?php echo $book['id']; ?>')" class="btn" style="margin-top: 1rem; display: block; text-align: center;">Listen Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
 
    <footer>
        <p>&copy; 2025 AUDIBLE. All rights reserved.</p>
    </footer>
 
    <script>
        // Internal JS - For JS-based redirection (no PHP redirects)
        function redirectTo(url) {
            window.location.href = url;
        }
        function logout() {
            if (confirm('Are you sure?')) {
                fetch('logout.php', { method: 'POST' });
                redirectTo('index.php');
            }
        }
    </script>
</body>
</html>
