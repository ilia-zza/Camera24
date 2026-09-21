<?php
require_once __DIR__ . "/function/function.php";

$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $results = searchProducts($search);

    if ($results) {
        while ($product = mysqli_fetch_assoc($results)) {
            $id = (int)$product['id'];
            $image = htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8');
            $price = number_format((int)($product['price'] ?? 0));

            echo '<a class="search-item" href="product.php?id=' . $id . '">';
            echo '<img src="img/' . $image . '" alt="' . $name . '">';
            echo '<div class="search-info">';
            echo '<div class="search-name">' . $name . '</div>';
            echo '<div class="search-price">' . $price . ' تومان</div>';
            echo '</div>';
            echo '</a>';
        }
    }
}
