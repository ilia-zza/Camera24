
<?php


require_once __DIR__ . "/function/function.php";


/* =========================
   اطلاعات سایت
========================= */

$settings = getSiteSettings();

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
        ثبت نظر مشتری
    </title>


    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <link
        rel="stylesheet"
        href="css/bootstrap.rtl.css"
    >

</head>


<body>


<!-- =========================
     Header
========================= -->

<div class="header">

    <div class="container">

        <div class="row">

            <!-- لوگو -->

            <div class="col-3 col-md-2">

                <div class="logo">

                    <a href="index.php">

                        <img
                            src="img/<?php echo htmlspecialchars($settings['logo'] ?? ''); ?>"
                            class="myimg"
                            alt="<?php echo htmlspecialchars($settings['site_name'] ?? 'Camera Shop'); ?>"
                        >

                    </a>

                </div>

            </div>


        </div>

    </div>

</div>


<!-- =========================
     فرم ثبت نظر
========================= -->

<div class="container">

    <div class="customer-comment-form">

        <div class="customer-comment-form-title">

            <h1>
                نظر خود را با ما به اشتراک بگذارید
            </h1>

            <p>
                تجربه خرید خود را برای ما و دیگر مشتریان بنویسید.
            </p>

        </div>


        <form
            action="customer-comment-process.php"
            method="POST"
            enctype="multipart/form-data"
        >
<?php echo csrf_field(); ?>


            <!-- نام -->

            <div class="mb-3">

                <label class="form-label">
                    نام شما
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="نام خود را وارد کنید"
                    required
                >

            </div>


            <!-- تصویر -->

            <div class="mb-3">

                <label class="form-label">
                    تصویر شما
                </label>

                <input
                    type="file"
                    name="image"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small class="text-muted">
                    این بخش اختیاری است.
                    حداکثر حجم تصویر ۵ مگابایت است.
                </small>

            </div>


            <!-- نظر -->

            <div class="mb-3">

                <label class="form-label">
                    نظر شما
                </label>

                <textarea
                    name="comment"
                    class="form-control"
                    rows="6"
                    placeholder="تجربه خرید خود را بنویسید..."
                    required
                ></textarea>

            </div>


            <!-- امتیاز -->

            <div class="mb-4">

                <label class="form-label">
                    امتیاز شما
                </label>

                <select
                    name="rating"
                    class="form-select"
                    required
                >

                    <option value="5" selected>
                        ★★★★★ - عالی
                    </option>

                    <option value="4">
                        ★★★★☆ - خوب
                    </option>

                    <option value="3">
                        ★★★☆☆ - متوسط
                    </option>

                    <option value="2">
                        ★★☆☆☆ - ضعیف
                    </option>

                    <option value="1">
                        ★☆☆☆☆ - خیلی ضعیف
                    </option>

                </select>

            </div>


            <!-- ارسال -->

            <button
                type="submit"
                class="admin-button"
            >
                ارسال نظر
            </button>


        </form>


    </div>

</div>


</body>

</html>
