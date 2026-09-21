<?php


require_once __DIR__ . "/function/function.php";


// اگر وارد نشده، بره صفحه ورود
if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit;

}


// مدیران وارد پنل مدیریت می‌شوند؛ role=0 در همین صفحه می‌ماند.
if ((int)$_SESSION['user_role'] === 1 || (int)$_SESSION['user_role'] === 2) {

    header("Location: admin/index.php");
    exit;

}


// اطلاعات کاربر
$user_id = $_SESSION['user_id'];

$user = getUser($user_id);

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>حساب کاربری</title>

    <link rel="stylesheet" href="css/bootstrap.rtl.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

    <h1 class="mt-5">
        حساب کاربری
    </h1>


    <div class="mt-4">

        <h3>
            سلام <?php echo $user['name']; ?> 👋
        </h3>

        <p>
            ایمیل:
            <?php echo $user['email']; ?>
        </p>

    </div>


    <div class="mt-4">

        <a
            href="cart.php"
            class="btn btn-primary">
            سبد خرید
        </a>


        <a
            href="logout.php"
            class="btn btn-danger">
            خروج از حساب
        </a>

    </div>

</div>

</body>

</html>