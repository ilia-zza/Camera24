<?php
require_once __DIR__ . '/function/function.php';

// Legacy storefront URL: always land on the single canonical category/store page.
if (isset($_GET['category']) && (int)$_GET['category'] > 0) {
    header('Location: category.php?id=' . (int)$_GET['category'], true, 302);
    exit;
}

$categories = getCategories();
$first_category = $categories ? mysqli_fetch_assoc($categories) : null;

if ($first_category) {
    header('Location: category.php?id=' . (int)$first_category['id'], true, 302);
    exit;
}

header('Location: index.php', true, 302);
exit;
