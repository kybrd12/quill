<?php
require 'vendor/autoload.php';

$parsedown = new Parsedown();

$slug = $_GET['slug'] ?? '';

$postsData = json_decode(file_get_contents('posts/posts.json'), true);
if (!$postsData) {
    header('HTTP/1.0 404 Not Found');
    echo 'Posts data not found';
    exit;
}

$post = null;
foreach ($postsData['posts'] as $p) {
    if ($p['slug'] === $slug) {
        $post = $p;
        break;
    }
}

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    echo 'Post not found';
    exit;
}

$postFile = "posts/" . $post['file'];
if (!file_exists($postFile)) {
    header('HTTP/1.0 404 Not Found');
    echo 'Markdown file not found';
    exit;
}

$content = file_get_contents($postFile);

function generateTOC($content) {
    preg_match_all('/^##+ (.+)$/m', $content, $matches, PREG_SET_ORDER);
    if (empty($matches)) return ['', $content];

    $toc = "<div class='toc'>\n<h2>Table of Contents</h2>\n<ul>\n";
    
    foreach ($matches as $match) {
        $heading = trim($match[1]);
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $heading));
        $slug = trim($slug, '-');
        $level = max(0, substr_count($match[0], '#') - 2);
        $indent = str_repeat('  ', $level);
        $toc .= "{$indent}<li><a href='#{$slug}'>{$heading}</a></li>\n";
    }
    $toc .= "</ul>\n</div>";

    $content = preg_replace_callback('/^(##+ )(.+)$/m', function($matches) {
        $heading = trim($matches[2]);
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $heading));
        $slug = trim($slug, '-');
        return $matches[1] . "<a id='{$slug}'></a>" . $heading;
    }, $content);

    return [$toc, $content];
}

list($toc, $processedContent) = generateTOC($content);
$htmlContent = $parsedown->text($processedContent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - [Your Site]</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap">
</head>
<body>
    <nav>
        <a href="/" class="nav-brand">[Your Site]</a>
        <div class="nav-links">
            <a href="#">Blog</a>
            <span>/</span>
            <a href="#">Projects</a>
            <span>/</span>
            <a href="#" onclick="toggleSearch()">Search</a>
        </div>
    </nav>

    <main>
        <article class="post-content">
            <header class="post-header">
                <h1><?= htmlspecialchars($post['title']) ?></h1>
                <div class="post-meta">
                    <div class="post-date"><?= date('F j, Y', strtotime($post['date'])) ?></div>
                </div>
            </header>
            <?= $toc ?>
            <?= $htmlContent ?>
        </article>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html> 