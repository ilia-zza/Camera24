<?php

require_once __DIR__ . "/function/function.php";

require_valid_csrf();

// ثبت سفارش فقط برای کاربر واردشده
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=cart");
    exit;
}

if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');

    if ($customer_name === '' || $customer_phone === '' || $customer_address === '') {
        $error = "لطفاً تمام اطلاعات ضروری را وارد کنید.";
    } elseif ($customer_email !== '' && !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $error = "ایمیل واردشده معتبر نیست.";
    } else {
        mysqli_begin_transaction($conn);

        try {
            $cart_products = [];
            $total = 0;

            /*
             * موجودی در لحظه ثبت سفارش قفل می‌شود تا دو سفارش همزمان
             * نتوانند بیشتر از موجودی واقعی ثبت شوند.
             */
            $product_stmt = mysqli_prepare(
                $conn,
                "SELECT id, name, price, stock
                 FROM products
                 WHERE id = ?
                 FOR UPDATE"
            );

            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product_id = (int)$product_id;
                $quantity = (int)$quantity;

                if ($product_id <= 0 || $quantity <= 0) {
                    continue;
                }

                mysqli_stmt_bind_param($product_stmt, "i", $product_id);
                mysqli_stmt_execute($product_stmt);
                $result = mysqli_stmt_get_result($product_stmt);
                $product = mysqli_fetch_assoc($result);

                if (!$product) {
                    throw new RuntimeException("یکی از محصولات سبد خرید دیگر وجود ندارد.");
                }

                $stock = (int)$product['stock'];

                if ($stock < $quantity) {
                    throw new RuntimeException(
                        "موجودی «" . $product['name'] . "» برای تعداد انتخاب‌شده کافی نیست."
                    );
                }

                $price = (int)$product['price'];
                $subtotal = $price * $quantity;
                $total += $subtotal;

                $cart_products[] = [
                    'id' => (int)$product['id'],
                    'name' => $product['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            }

            mysqli_stmt_close($product_stmt);

            if (empty($cart_products)) {
                throw new RuntimeException("سبد خرید شما خالی است.");
            }

            $user_id = (int)$_SESSION['user_id'];

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO orders
                (user_id, customer_name, customer_phone, customer_email, customer_address, total_price, status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "issssi",
                $user_id,
                $customer_name,
                $customer_phone,
                $customer_email,
                $customer_address,
                $total
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new RuntimeException("خطا در ثبت سفارش.");
            }

            $order_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            $item_stmt = mysqli_prepare(
                $conn,
                "INSERT INTO order_items
                (order_id, product_id, product_name, price, quantity, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stock_stmt = mysqli_prepare(
                $conn,
                "UPDATE products
                 SET stock = stock - ?
                 WHERE id = ? AND stock >= ?"
            );

            foreach ($cart_products as $item) {
                mysqli_stmt_bind_param(
                    $item_stmt,
                    "iisiii",
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['subtotal']
                );

                if (!mysqli_stmt_execute($item_stmt)) {
                    throw new RuntimeException("خطا در ثبت اقلام سفارش.");
                }

                mysqli_stmt_bind_param(
                    $stock_stmt,
                    "iii",
                    $item['quantity'],
                    $item['id'],
                    $item['quantity']
                );

                if (!mysqli_stmt_execute($stock_stmt) || mysqli_stmt_affected_rows($stock_stmt) !== 1) {
                    throw new RuntimeException("موجودی یکی از محصولات تغییر کرده است. دوباره تلاش کنید.");
                }
            }

            mysqli_stmt_close($item_stmt);
            mysqli_stmt_close($stock_stmt);

            mysqli_commit($conn);

            $_SESSION['cart'] = [];

            header("Location: order-success.php?id=" . $order_id);
            exit;

        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

// محاسبه مبلغ نمایشی سبد
$total = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product_id = (int)$product_id;
    $quantity = (int)$quantity;

    $result = getProduct($product_id);
    $product = $result ? mysqli_fetch_assoc($result) : null;

    if ($product && $quantity > 0) {
        $total += (int)$product['price'] * $quantity;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت سفارش</title>
    <link rel="stylesheet" href="css/bootstrap.rtl.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="mb-4">ثبت سفارش</h1>

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted">لطفاً اطلاعات خود را برای ثبت سفارش وارد کنید.</p>

                    <form action="checkout.php" method="post">
<?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">نام و نام خانوادگی</label>
                            <input type="text" name="customer_name" class="form-control" required
                                   value="<?php echo htmlspecialchars($_POST['customer_name'] ?? $_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">شماره تماس</label>
                            <input type="tel" name="customer_phone" class="form-control" required
                                   value="<?php echo htmlspecialchars($_POST['customer_phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="customer_email" class="form-control"
                                   value="<?php echo htmlspecialchars($_POST['customer_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">آدرس</label>
                            <textarea name="customer_address" class="form-control" rows="4" required><?php
                                echo htmlspecialchars($_POST['customer_address'] ?? '', ENT_QUOTES, 'UTF-8');
                            ?></textarea>
                        </div>

                        <div class="border-top pt-3 mt-4">
                            <h3>مبلغ نهایی: <?php echo number_format($total); ?> تومان</h3>
                        </div>

                        <button type="submit" name="submit_order" class="btn btn-primary mt-3 w-100">
                            ثبت سفارش
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
