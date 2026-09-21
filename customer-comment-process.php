<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


require_once __DIR__ . "/function/function.php";

require_valid_csrf();


/* =========================
   فقط POST
========================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: customer-comment.php");
    exit;

}


/* =========================
   دریافت اطلاعات
========================= */

$name = trim($_POST['name'] ?? '');

$comment = trim($_POST['comment'] ?? '');

$rating = (int)($_POST['rating'] ?? 5);


/* =========================
   بررسی اطلاعات
========================= */

if ($name === '' || $comment === '') {

    header("Location: customer-comment.php");
    exit;

}


/* =========================
   بررسی امتیاز
========================= */

if ($rating < 1 || $rating > 5) {

    $rating = 5;

}


/* =========================
   تصویر
========================= */

$image = '';


if (
    isset($_FILES['image']) &&
    $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
) {


    /* =========================
       بررسی خطای آپلود
    ========================= */

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

        die(
            'خطای آپلود عکس: ' .
            $_FILES['image']['error']
        );

    }


    /* =========================
       حداکثر حجم 15 مگابایت
    ========================= */

    if (
        $_FILES['image']['size'] >
        15 * 1024 * 1024
    ) {

        header("Location: customer-comment.php");
        exit;

    }


    /* =========================
       فرمت‌های مجاز
    ========================= */

    $allowed_extensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];


    $extension = strtolower(
        pathinfo(
            $_FILES['image']['name'],
            PATHINFO_EXTENSION
        )
    );


    if (
        !in_array(
            $extension,
            $allowed_extensions,
            true
        )
    ) {

        header("Location: customer-comment.php");
        exit;

    }


    /* =========================
       نام جدید تصویر
    ========================= */

    $image =
        uniqid('comment_', true)
        . '.'
        . $extension;


    /* =========================
       مسیر ذخیره تصویر
    ========================= */

    $upload_path =
        __DIR__ . "/img/" . $image;


    /* =========================
       انتقال تصویر
    ========================= */

    if (
        !move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $upload_path
        )
    ) {

        header("Location: customer-comment.php");
        exit;

    }

}


/* =========================
   ذخیره نظر در دیتابیس
========================= */

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO customer_comments
    (
        name,
        image,
        comment,
        rating,
        sort_order,
        status
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        0,
        0
    )"
);


mysqli_stmt_bind_param(
    $stmt,
    "sssi",
    $name,
    $image,
    $comment,
    $rating
);


$success = mysqli_stmt_execute($stmt);


mysqli_stmt_close($stmt);


/* =========================
   اگر ذخیره ناموفق بود
========================= */

if (!$success) {


    /* حذف تصویر آپلودشده */

    if (
        !empty($image) &&
        file_exists(__DIR__ . "/img/" . $image)
    ) {

        unlink(__DIR__ . "/img/" . $image);

    }


    header("Location: customer-comment.php");
    exit;

}


/* =========================
   موفقیت
========================= */

header("Location: customer-comment.php?success=1");
exit;