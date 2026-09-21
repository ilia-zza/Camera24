<?php
require_once __DIR__ . "/function/function.php";

$id = (int)($_GET['id'] ?? 0);
$legacy_slugs = [1 => 'article-1788563665', 2 => 'article-1788564493', 3 => 'article-1788618463'];
if ($id > 0 && isset($legacy_slugs[$id])) { header('Location: blog-single.php?slug=' . $legacy_slugs[$id]); exit; }
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, title, description, image, link, blog_post_id FROM photography_news WHERE id = ? AND status = 1 LIMIT 1");
if (!$stmt) {
    header("Location: index.php");
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$news = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if ($news && !empty($news['blog_post_id'])) {
    header('Location: blog-single.php?id=' . (int)$news['blog_post_id']);
    exit;
}

if ($news && preg_match('/blog-single\.php\?id=(\d+)/i', (string)($news['link'] ?? ''), $m)) {
    header('Location: blog-single.php?id=' . (int)$m[1]);
    exit;
}

if (!$news) {
    http_response_code(404);
    exit('مقاله موردنظر پیدا نشد.');
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="css/bootstrap.rtl.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/lib/header.php'; ?>
<main class="container py-5">
    <article class="photography-news-single">
        <h1 class="mb-4"><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if (!empty($news['image'])): ?>
            <img src="img/<?php echo htmlspecialchars($news['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded mb-4">
        <?php endif; ?>
        <div class="photography-news-single-content">
            <?php echo nl2br(htmlspecialchars($news['description'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
        </div>
        <a href="index.php" class="btn btn-primary mt-4">بازگشت به صفحه اصلی</a>
    </article>
</main>
<?php include __DIR__ . '/lib/footer.php'; ?>
</body>
</html>
