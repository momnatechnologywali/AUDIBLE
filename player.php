<?php
// player.php - Media player for audiobooks
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
 
$book_id = $_GET['book'] ?? 0;
$resume = $_GET['resume'] ?? 0;
 
if (!$book_id) {
    header('Location: library.php');
    exit;
}
 
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();
 
if (!$book) {
    header('Location: library.php');
    exit;
}
 
// Update progress on load if resuming
if ($resume > 0) {
    $stmt = $pdo->prepare("UPDATE user_progress SET progress_seconds = ? WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$resume, $_SESSION['user_id'], $book_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIBLE - Player: <?php echo htmlspecialchars($book['title']); ?></title>
    <style>
        /* Internal CSS - Sleek audio player design */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; display: flex; flex-direction: column; height: 100vh; }
        header { background: rgba(0,0,0,0.8); padding: 1rem; display: flex; justify-content: space-between; }
        .logo { font-size: 1.5rem; font-weight: bold; }
        .back-btn { color: #fff; text-decoration: none; }
        .player-container { flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem; }
        .book-cover { width: 300px; height: 300px; object-fit: cover; border-radius: 15px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .book-title { font-size: 1.8rem; margin-bottom: 0.5rem; text-align: center; }
        .book-author { text-align: center; opacity: 0.8; margin-bottom: 2rem; }
        audio { width: 100%; max-width: 600px; margin-bottom: 1rem; }
        .controls { display: flex; justify-content: center; gap: 1rem; margin-bottom: 1rem; }
        .control-btn { background: rgba(255,255,255,0.2); border: none; color: #fff; padding: 1rem; border-radius: 50%; font-size: 1.2rem; cursor: pointer; transition: background 0.3s; }
        .control-btn:hover { background: rgba(255,255,255,0.3); }
        .speed-select { background: rgba(255,255,255,0.2); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 0.5rem; border-radius: 5px; }
        .progress-container { background: rgba(255,255,255,0.2); height: 5px; border-radius: 5px; margin: 1rem 0; cursor: pointer; }
        .progress-bar { background: #ff6b6b; height: 100%; border-radius: 5px; width: 0%; transition: width 0.1s; }
        .time { display: flex; justify-content: space-between; font-size: 0.9rem; opacity: 0.8; }
        @media (max-width: 768px) { .book-cover { width: 200px; height: 200px; } .controls { gap: 0.5rem; } .control-btn { padding: 0.8rem; font-size: 1rem; } }
    </style>
</head>
<body>
    <header>
        <a href="#" onclick="redirectTo('dashboard.php')" class="back-btn">← Back</a>
        <div class="logo">AUDIBLE</div>
        <div></div>
    </header>
 
    <div class="player-container">
        <img src="<?php echo htmlspecialchars($book['cover_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
        <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
        <div class="book-author">Category: <?php echo htmlspecialchars($book['category']); ?></div>
 
        <audio id="audioPlayer" src="<?php echo htmlspecialchars($book['audio_file']); ?>" preload="metadata"></audio>
 
        <div class="controls">
            <button class="control-btn" id="rewind" onclick="rewind()">⏪</button>
            <button class="control-btn" id="playPause" onclick="togglePlay()">▶</button>
            <button class="control-btn" id="forward" onclick="forward()">⏩</button>
        </div>
 
        <select id="speedControl" class="speed-select" onchange="changeSpeed()">
            <option value="1">1x</option>
            <option value="1.5">1.5x</option>
            <option value="2">2x</option>
        </select>
 
        <div class="progress-container" id="progressContainer" onclick="seek(event)">
            <div class="progress-bar" id="progressBar"></div>
        </div>
        <div class="time">
            <span id="currentTime">00:00</span>
            <span id="duration">00:00</span>
        </div>
    </div>
 
    <script>
        // Internal JS - Custom audio player with controls, speed, progress tracking
        const audio = document.getElementById('audioPlayer');
        const playPauseBtn = document.getElementById('playPause');
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const currentTimeEl = document.getElementById('currentTime');
        const durationEl = document.getElementById('duration');
 
        let isPlaying = false;
        let currentSpeed = 1;
 
        // Resume from progress
        audio.currentTime = <?php echo $resume; ?>;
 
        audio.addEventListener('loadedmetadata', () => {
            durationEl.textContent = formatTime(audio.duration);
        });
 
        audio.addEventListener('timeupdate', () => {
            const percent = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = percent + '%';
            currentTimeEl.textContent = formatTime(audio.currentTime);
            // Save progress every 10 seconds
            if (Math.floor(audio.currentTime) % 10 === 0) {
                saveProgress(audio.currentTime);
            }
        });
 
        function togglePlay() {
            if (isPlaying) {
                audio.pause();
                playPauseBtn.textContent = '▶';
            } else {
                audio.play();
                playPauseBtn.textContent = '⏸';
            }
            isPlaying = !isPlaying;
        }
 
        function rewind() { audio.currentTime -= 10; }
        function forward() { audio.currentTime += 10; }
 
        function changeSpeed() {
            currentSpeed = parseFloat(document.getElementById('speedControl').value);
            audio.playbackRate = currentSpeed;
        }
 
        function seek(event) {
            const rect = progressContainer.getBoundingClientRect();
            const pos = (event.clientX - rect.left) / rect.width;
            audio.currentTime = pos * audio.duration;
        }
 
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
 
        function saveProgress(time) {
            fetch('save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `book_id=<?php echo $book_id; ?>&progress=${Math.floor(time)}`
            });
        }
 
        function redirectTo(url) { window.location.href = url; }
    </script>
</body>
</html>
