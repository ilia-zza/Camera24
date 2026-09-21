<?php


require_once __DIR__ . "/function/function.php";

require_valid_csrf();

$message = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || $password === '') {

        $message = "لطفاً همه فیلدها را پر کنید.";

    } else {

        $result = registerUser($name, $email, $password);

        if ($result === true) {

            header("Location: login.php");
            exit;

        } else {

            $message = $result;

        }

    }

}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ثبت نام</title>

    <link rel="stylesheet" href="css/bootstrap.rtl.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

    <h1 class="mt-5 mb-4">
        ثبت نام
    </h1>


    <?php if ($message != "") { ?>

        <p>
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </p>

    <?php } ?>


    <form action="register.php" method="post">
<?php echo csrf_field(); ?>

        <div class="mb-3">

            <label>
                نام
            </label>

            <input
                type="text"
                name="name"
                class="form-control"
                required
            >

        </div>


        <div class="mb-3">

            <label>
                ایمیل
            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                required
            >

        </div>


        <div class="mb-3">

            <label>
                رمز عبور
            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                required
            >

        </div>


        <button
            type="submit"
            name="register"
            class="btn btn-primary"
        >
            ثبت نام
        </button>

        <p class="mt-3">قبلاً حساب ساخته‌اید؟ <a href="login.php">وارد شوید</a></p>

    </form>

</div>

</body>

</html>
