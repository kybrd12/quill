<?php
require 'vendor/autoload.php';

$postsData = json_decode(file_get_contents('posts/posts.json'), true);
if (!$postsData) {
    $postsData = ['posts' => []];
}

usort($postsData['posts'], function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Your Name] Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap">
</head>
<body>
    <nav>
        <a href="/" class="nav-brand">[Site Name]</a>
        <div class="nav-links">
            <a href="#">Blog</a>
            <span>/</span>
            <a href="#">Projects</a>
            <span>/</span>
            <a href="#" onclick="toggleSearch()">Search</a>
        </div>
    </nav>

    <main>
        <div class="profile-section">
            <div class="profile-card">
                <div class="ghost-icon">
                    <svg viewBox="0 0 24 24" class="ghost">
                        <path fill="currentColor" d="M12,2A9,9 0 0,0 3,11V22L6,19L9,22L12,19L15,22L18,19L21,22V11A9,9 0 0,0 12,2M9,8A2,2 0 0,1 11,10A2,2 0 0,1 9,12A2,2 0 0,1 7,10A2,2 0 0,1 9,8M15,8A2,2 0 0,1 17,10A2,2 0 0,1 15,12A2,2 0 0,1 13,10A2,2 0 0,1 15,8Z" />
                    </svg>
                </div>
                <h1>[Your Name]</h1>
                <div class="profile-meta">
                    <span>🎂 This website is <span id="age"><script>
                        function updateAge() {
                            const startDate = new Date('2025-01-01T15:50:00+01:00'); // Example date, set it to your own. Hour is in GMT+1 timezone
                            const now = new Date();
                            const diff = now - startDate;
                            
                            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            
                            document.getElementById('age').innerHTML = `<strong>${days} days, ${hours} hours, and ${minutes} minutes</strong>`;
                        }
                        updateAge();
                        setInterval(updateAge, 60000);
                    </script></span> old!</span>
                    <span>🌍 Made in [Country]</span>
                </div>
            </div>

            <div class="about-section">
                <h2>About me</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.</p>
            </div>
        </div>

        <div class="socials">
            <h2>Socials</h2>
            <div class="social-links">
                <a href="https://github.com/example" class="social-link">
                    <svg viewBox="0 0 24 24" class="icon">
                        <path fill="currentColor" d="M12,2A10,10 0 0,0 2,12C2,16.42 4.87,20.17 8.84,21.5C9.34,21.58 9.5,21.27 9.5,21C9.5,20.77 9.5,20.14 9.5,19.31C6.73,19.91 6.14,17.97 6.14,17.97C5.68,16.81 5.03,16.5 5.03,16.5C4.12,15.88 5.1,15.9 5.1,15.9C6.1,15.97 6.63,16.93 6.63,16.93C7.5,18.45 8.97,18 9.54,17.76C9.63,17.11 9.89,16.67 10.17,16.42C7.95,16.17 5.62,15.31 5.62,11.5C5.62,10.39 6,9.5 6.65,8.79C6.55,8.54 6.2,7.5 6.75,6.15C6.75,6.15 7.59,5.88 9.5,7.17C10.29,6.95 11.15,6.84 12,6.84C12.85,6.84 13.71,6.95 14.5,7.17C16.41,5.88 17.25,6.15 17.25,6.15C17.8,7.5 17.45,8.54 17.35,8.79C18,9.5 18.38,10.39 18.38,11.5C18.38,15.32 16.04,16.16 13.81,16.41C14.17,16.72 14.5,17.33 14.5,18.26C14.5,19.6 14.5,20.68 14.5,21C14.5,21.27 14.66,21.59 15.17,21.5C19.14,20.16 22,16.42 22,12A10,10 0 0,0 12,2Z" />
                    </svg>
                    GitHub
                </a>
            </div>
        </div>

        <div class="latest-posts">
            <h2>Latest Posts</h2>
            <div class="posts-grid">
                <?php foreach ($postsData['posts'] as $post): ?>
                    <a href="post.php?slug=<?= htmlspecialchars($post['slug']) ?>" class="post-card">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <div class="post-date"><?= date('F j, Y', strtotime($post['date'])) ?></div>
                        <p><?= htmlspecialchars($post['excerpt']) ?></p>
                        <div class="post-tags">
                            <?php foreach ($post['tags'] as $tag): ?>
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        window.postsData = <?= json_encode($postsData) ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="js/main.js"></script>

    <div id="searchOverlay" class="search-overlay">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search articles..." autocomplete="off">
            <div id="searchResults" class="search-results"></div>
        </div>
    </div>
</body>
</html> 