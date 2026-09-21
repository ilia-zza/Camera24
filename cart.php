<?php

require_once __DIR__ . "/function/function.php";

require_valid_csrf();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/*
 * سبد خرید فقط در Session نگهداری می‌شود.
 * موجودی کالا هنگام افزودن به سبد کم نمی‌شود؛
 * موجودی در زمان ثبت سفارش به‌صورت اتمیک کم می‌شود.
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if ($product_id <= 0) {
        header("Location: category.php");
        exit;
    }

    // مهم: کاربر مهمان باید اول وارد شود.
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['pending_cart'] = [
            'product_id' => $product_id,
            'quantity'   => $quantity,
        ];

        header("Location: login.php?redirect=cart");
        exit;
    }

    $result = getProduct($product_id);
    $product = $result ? mysqli_fetch_assoc($result) : null;

    if (!$product || (int)$product['stock'] <= 0) {
        header("Location: product.php?id=" . $product_id);
        exit;
    }

    $stock = (int)$product['stock'];
    $current = (int)($_SESSION['cart'][$product_id] ?? 0);
    $new_quantity = min($stock, $current + $quantity);

    $_SESSION['cart'][$product_id] = $new_quantity;

    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['increase'])) {
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
        $result = getProduct($product_id);
        $product = $result ? mysqli_fetch_assoc($result) : null;

        if ($product) {
            $stock = (int)$product['stock'];
            $current = (int)$_SESSION['cart'][$product_id];

            if ($current < $stock) {
                $_SESSION['cart'][$product_id] = $current + 1;
            }
        }
    }

    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decrease'])) {
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]--;

        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id > 0) {
        unset($_SESSION['cart'][$product_id]);
    }

    header("Location: cart.php");
    exit;
}

// پاکسازی مقادیر خراب Session
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product_id = (int)$product_id;
    $quantity = (int)$quantity;

    if ($product_id <= 0 || $quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سبد خرید</title>
    <link rel="stylesheet" href="css/bootstrap.rtl.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h1 class="mt-5 mb-4">سبد خرید</h1>

    <?php $total = 0; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center mt-5">
            <h2>سبد خرید شما خالی است.</h2>
            <a href="category.php" class="btn btn-primary mt-3">مشاهده محصولات</a>
        </div>
    <?php else: ?>

        <?php foreach ($_SESSION['cart'] as $product_id => $quantity): ?>
            <?php
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;

            $result = getProduct($product_id);
            $product = $result ? mysqli_fetch_assoc($result) : null;

            if (!$product) {
                unset($_SESSION['cart'][$product_id]);
                continue;
            }

            $stock = (int)$product['stock'];

            // اگر موجودی از تعداد سبد کمتر شده، تعداد سبد را با موجودی هماهنگ کن.
            if ($stock <= 0) {
                unset($_SESSION['cart'][$product_id]);
                continue;
            }

            if ($quantity > $stock) {
                $quantity = $stock;
                $_SESSION['cart'][$product_id] = $quantity;
            }

            $item_total = (int)$product['price'] * $quantity;
            $total += $item_total;
            ?>

            <div class="row mb-4 border-bottom pb-4">
                <div class="col-md-3">
                    <img
                        src="img/<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>"
                        alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="img-fluid"
                    >
                </div>

                <div class="col-md-6">
                    <h2><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>

                    <p>
                        قیمت:
                        <?php echo number_format((int)$product['price']); ?>
                        تومان
                    </p>

                    <div class="d-flex align-items-center gap-2">
                        <form action="cart.php" method="post">
<?php echo csrf_field(); ?>
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <button type="submit" name="increase" class="btn btn-success"
                                <?php echo $quantity >= $stock ? 'disabled' : ''; ?>>+</button>
                        </form>

                        <span><?php echo $quantity; ?></span>

                        <form action="cart.php" method="post">
<?php echo csrf_field(); ?>
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <button type="submit" name="decrease" class="btn btn-warning">-</button>
                        </form>
                    </div>

                    <form action="cart.php" method="post" class="mt-3">
<?php echo csrf_field(); ?>
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" name="remove" class="btn btn-danger">
                            حذف از سبد خرید
                        </button>
                    </form>
                </div>

                <div class="col-md-3">
                    <h4>
                        مبلغ:
                        <?php echo number_format($item_total); ?>
                        تومان
                    </h4>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="text-center mt-5">
                <h2>محصولات سبد خرید دیگر موجود نیستند.</h2>
                <a href="category.php" class="btn btn-primary mt-3">مشاهده محصولات</a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <h2>مبلغ کل: <?php echo number_format($total); ?> تومان</h2>
                <a href="checkout.php" class="btn btn-primary mt-3">ادامه و ثبت سفارش</a>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

</body>
</html>
