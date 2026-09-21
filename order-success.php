<?php


require_once __DIR__ . "/function/function.php";


/* دریافت شماره سفارش */

$order_id = (int)($_GET['id'] ?? 0);


if ($order_id <= 0) {

    header("Location: index.php");

    exit;

}


/* دریافت سفارش */

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM orders
     WHERE id = ?"
);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$order = mysqli_fetch_assoc($result);


if (!$order) {

    header("Location: index.php");

    exit;

}

// An order confirmation is private: only its owner or an admin may view it.
if (
    !isset($_SESSION['user_id']) ||
    ((int)$_SESSION['user_id'] !== (int)$order['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin')
) {
    header("Location: index.php");
    exit;
}

?>


<!DOCTYPE html>

<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        سفارش با موفقیت ثبت شد
    </title>

    <link
        rel="stylesheet"
        href="css/bootstrap.rtl.css"
    >

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<div class="container">


    <div
        class="row justify-content-center mt-5"
    >


        <div class="col-md-7">


            <div
                class="card shadow-sm text-center"
            >


                <div class="card-body p-5">


                    <div
                        style="font-size:60px;"
                    >
                        ✓
                    </div>


                    <h1 class="mt-3">

                        سفارش شما با موفقیت ثبت شد

                    </h1>


                    <p class="mt-3">

                        از خرید شما متشکریم.

                    </p>


                    <p>

                        شماره سفارش شما:

                        <strong>

                            #<?php echo $order['id']; ?>

                        </strong>

                    </p>


                    <hr>


                    <h3>

                        مبلغ سفارش:

                        <?php

                        echo number_format(
                            $order['total_price']
                        );

                        ?>

                        تومان

                    </h3>


                    <p class="mt-3 text-muted">

                        وضعیت سفارش:

                        در انتظار بررسی

                    </p>


                    <a
                        href="index.php"
                        class="btn btn-primary mt-3"
                    >

                        بازگشت به فروشگاه

                    </a>


                </div>


            </div>


        </div>


    </div>


</div>


</body>

</html>