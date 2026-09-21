<?php

require_once __DIR__ . "/function/function.php";

require_valid_csrf();

$message = "";

// فقط مسیرهای داخلی مجاز هستند.
$redirect = ($_GET['redirect'] ?? '') === 'cart' ? 'cart.php' : 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = loginUser($email, $password);

    if ($result) {
        // جلوگیری از Session Fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$result['id'];
        $_SESSION['user_name'] = $result['name'];
        $_SESSION['user_role'] = $result['role'];

        /*
         * اگر کاربر قبل از ورود روی «افزودن به سبد» زده بود،
         * همان محصول بعد از ورود با همان تعداد وارد سبد می‌شود.
         */
        if (isset($_SESSION['pending_cart']) && is_array($_SESSION['pending_cart'])) {
            $pending = $_SESSION['pending_cart'];
            unset($_SESSION['pending_cart']);

            $product_id = (int)($pending['product_id'] ?? 0);
            $quantity = max(1, (int)($pending['quantity'] ?? 1));

            if ($product_id > 0) {
                $product_result = getProduct($product_id);
                $product = $product_result ? mysqli_fetch_assoc($product_result) : null;

                if ($product && (int)$product['stock'] > 0) {
                    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }

                    $stock = (int)$product['stock'];
                    $current = (int)($_SESSION['cart'][$product_id] ?? 0);

                    $_SESSION['cart'][$product_id] = min(
                        $stock,
                        $current + $quantity
                    );
                }
            }

            header("Location: cart.php");
            exit;
        }

        if ((int)$result['role'] === 1 || (int)$result['role'] === 2) {
            header("Location: admin/index.php");
        } else {
            // کاربر عادی (role=0) مستقیماً وارد پروفایل می‌شود.
            header("Location: account.php");
        }
        exit;
    }

    $message = "ایمیل یا رمز عبور اشتباه است.";
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود</title>
    <link rel="stylesheet" href="css/bootstrap.rtl.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">ورود</h1>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="login.php<?php echo $redirect === 'cart.php' ? '?redirect=cart' : ''; ?>" method="post">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label>ایمیل</label>
            <input type="email" name="email" class="form-control" required autocomplete="email">
        </div>

        <div class="mb-3">
            <label>رمز عبور</label>
            <input type="password" name="password" class="form-control" required autocomplete="current-password">
        </div>

        <button type="submit" name="login" class="btn btn-primary">ورود</button>
        <p class="mt-3">حساب کاربری ندارید؟ <a href="register.php">ثبت‌نام کنید</a></p>
    </form>
</div>
</body>
</html>
