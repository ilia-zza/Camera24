<?php


require_once __DIR__ . "/function/function.php";


/* =========================================================
   SITE DATA
========================================================= */

$settings = getSiteSettings();
$menus = getMenus();

$footer = getFooter();
$footer_related_links = getFooterItems('related_link');
$footer_latest_posts = getFooterItems('latest_post');
$footer_contacts = getFooterItems('contact');
$footer_socials = getFooterSocials();


/* =========================================================
   CART COUNT
========================================================= */

$cart_count = 0;

foreach ($_SESSION['cart'] ?? [] as $quantity) {
    $cart_count += (int)$quantity;
}


/* =========================================================
   PRODUCT
========================================================= */

$product = false;

if (isset($_GET['id'])) {

    $product_id = (int)$_GET['id'];

    $result = getProduct($product_id);

    if ($result) {
        $product = mysqli_fetch_assoc($result);
    }
    $review = false;

if ($product) {

    $review_result = getProductReview($product['id']);

    if ($review_result && mysqli_num_rows($review_result) > 0) {
        $review = mysqli_fetch_assoc($review_result);
    }
}
}


$page_title = $product
    ? ($product['name'] ?? 'محصول')
    : 'محصول';


include "lib/header.php";

?>
<link rel="stylesheet" href="css/product-review.css">

<main class="product-page">

<?php if ($product): ?>


<!-- =====================================================
     BREADCRUMB
===================================================== -->

<div class="product-wrap">

    <div class="product-breadcrumb">

        <a href="index.php">خانه</a>

        <span>/</span>

        <a href="category.php">دوربین عکاسی</a>

        <span>/</span>

        <a href="#">کانن</a>

        <span>/</span>

        <strong>
            <?php echo htmlspecialchars($product['name']); ?>
        </strong>

    </div>

</div>



<!-- =====================================================
     PRODUCT TOP
===================================================== -->

<div class="product-wrap">

<section class="product-top">


    <!-- =================================================
         RIGHT : PRODUCT IMAGE
    ================================================== -->

    <div class="product-gallery">


        <div class="main-product-photo">

            <img
                id="mainProductImage"
                src="img/<?php echo htmlspecialchars($product['image']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>"
            >

        </div>


        <div class="product-thumbs">

            <?php for ($i = 0; $i < 5; $i++): ?>

                <button
                    type="button"
                    class="product-thumb <?php echo $i === 0 ? 'active' : ''; ?>"
                    data-image="img/<?php echo htmlspecialchars($product['image']); ?>"
                >

                    <img
                        src="img/<?php echo htmlspecialchars($product['image']); ?>"
                        alt=""
                    >

                </button>

            <?php endfor; ?>

        </div>


    </div>



    <!-- =================================================
         CENTER : PRODUCT INFORMATION
    ================================================== -->

    <div class="product-information">


        <div class="product-title-box">

            <h1>
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>

        </div>


        <div class="product-price-line">

            <span>
                قیمت :
            </span>

            <strong>
                <?php
                echo number_format(
                    (int)$product['price']
                );
                ?>
                تومان
            </strong>

        </div>


        <!-- WARRANTY -->

        <div class="warranty-line">

            <span class="option-label">
                گارانتی :
            </span>


            <select>

                <option>
                    شرکت دوربین ایران
                </option>

                <option>
                    گارانتی ۲۴ ماهه
                </option>

                <option>
                    گارانتی ۴۸ ماهه
                </option>
                </select>


            <label class="warranty-four">

                <input type="checkbox">

                <span></span>

                ۴ ساله

            </label>

        </div>


        <!-- BUY -->

        <?php if ((int)$product['stock'] > 0): ?>

        <form
            action="cart.php"
            method="post"
            class="buy-form"
        >
<?php echo csrf_field(); ?>

            <input
                type="hidden"
                name="product_id"
                value="<?php echo (int)$product['id']; ?>"
            >

            <input
                type="hidden"
                name="add_to_cart"
                value="1"
            >


            <button
                type="submit"
                class="buy-button"
            >

                <span class="buy-cart-icon">
                    🛒
                </span>

                افزودن به سبد خرید

            </button>


            <div class="quantity-control">

                <button
                    type="button"
                    class="quantity-minus"
                >
                    −
                </button>

                <input
                    type="number"
                    name="quantity"
                    value="1"
                    min="1"
                    max="<?php echo max(1, (int)$product['stock']); ?>"
                >

                <button
                    type="button"
                    class="quantity-plus"
                >
                    +
                </button>

            </div>


        </form>


        <div class="ready-send">

            <span class="truck">
                🚚
            </span>

            آماده ارسال

        </div>


        <?php else: ?>


        <div class="not-available">
            این محصول ناموجود است.
        </div>


        <?php endif; ?>


        <!-- ACTIONS -->

        <div class="product-actions">


            <button type="button">

                <span>♡</span>

                افزودن به لیست علاقه‌مندی‌ها

            </button>


            <button type="button">

                <span>⇄</span>

                مقایسه محصول

            </button>


        </div>


    </div>



    <!-- =================================================
         LEFT : SUGGESTED PACKAGE
    ================================================== -->

    <aside class="suggested-product">


        <div class="suggested-image">

            <!--
                اگر تصویر بسته پیشنهادی خودت را داری،
                فقط src همین تصویر را عوض کن.
            -->

            <img
                src="img/contact_hero_1788642794_5895.png"
                alt="بسته پیشنهادی"
            >

        </div>


        <div class="suggested-body">

            <h3>
                بسته پیشنهادی
            </h3>


            <p>
                دوربین عکاسی مدل به همراه
                تجهیزات پیشنهادی
            </p>


            <ul>

                <li>
                    دوربین عکاسی
                </li>

                <li>
                    کارت حافظه
                </li>

                <li>
                    کیف دوربین
                </li>

                <li>
                    باتری اضافه
                </li>

            </ul>


            <strong class="suggested-price">
                33,000,000 تومان
            </strong>

        </div>

    </aside>


</section>

</div>



<!-- =====================================================
     SERVICE BAR
===================================================== -->

<div class="product-wrap">

<section class="service-bar">


    <div class="service">

        <span class="service-icon">
            🚚
        </span>

        <span>
            ارسال اکسپرس
        </span>

    </div>


    <div class="service">

        <span class="service-icon">
            ✓
        </span>

        <span>
            ضمانت اصالت بودن کالا
        </span>

    </div>


    <div class="service">

        <span class="service-icon">
            ✈
        </span>
        <span>
            ارسال به سراسر ایران
        </span>

    </div>


    <div class="service">

        <span class="service-icon">
            ↩
        </span>

        <span>
            موجودی کالا در صورت نقص فنی
        </span>

    </div>


    <div class="service">

        <span class="service-icon">
            🎁
        </span>

        <span>
            بسته بندی زیبا
        </span>

    </div>


</section>

</div>



<!-- =====================================================
     TABS + REVIEW
===================================================== -->

<div class="product-wrap">

<section class="review-box">


    <div class="review-tabs">


        <button
            type="button"
            data-tab="specs"
        >
            مشخصات
        </button>


        <button
            type="button"
            class="active"
            data-tab="review"
        >
            نقد و بررسی
        </button>


        <button
            type="button"
            data-tab="questions"
        >
            پرسش و پاسخ
        </button>


    </div>



    <!-- =================================================
         REVIEW TAB
    ================================================== -->

    <div
        class="tab-content active"
        id="review"
    >

        <?php if ($review): ?>

    <article class="review-article">

        <?php if (!empty($review['title'])): ?>

            <h2>
                <?php echo htmlspecialchars($review['title']); ?>
            </h2>

        <?php endif; ?>


        <?php
        echo nl2br(
            htmlspecialchars(
                $review['content']
            )
        );
        ?>


        <?php if (!empty($review['image'])): ?>

            <div class="review-picture">

                <img
                    src="img/<?php echo htmlspecialchars($review['image']); ?>"
                    alt="<?php echo htmlspecialchars($review['title']); ?>"
                >

            </div>

        <?php endif; ?>
        <?php if (!empty($review['content_after_image'])): ?>
    <div class="review-content-after-image">
        <?php
        echo nl2br(
            htmlspecialchars(
                $review['content_after_image']
            )
        );
        ?>
    </div>
<?php endif; ?>

    </article>

<?php else: ?>

    <article class="review-article">

        <p>
            نقد و بررسی این محصول هنوز ثبت نشده است.
        </p>

    </article>

<?php endif; ?>
    </div>



    <!-- =================================================
         SPECIFICATIONS
    ================================================== -->

    <div
        class="tab-content"
        id="specs"
    >

        <div class="specifications">

            <div class="specification-row">

                <span>
                    نام محصول
                </span>

                <strong>
                    <?php echo htmlspecialchars($product['name']); ?>
                </strong>

            </div>


            <div class="specification-row">

                <span>
                    دسته‌بندی
                </span>

                <strong>
                    <?php echo htmlspecialchars($product['category'] ?? 'دوربین عکاسی'); ?>
                </strong>

            </div>


            <div class="specification-row">

                <span>
                    قیمت
                </span>
                <strong>
                    <?php
                    echo number_format(
                        (int)$product['price']
                    );
                    ?>
                    تومان
                </strong>

            </div>


            <div class="specification-row">

                <span>
                    موجودی
                </span>

                <strong>
                    <?php
                    echo (int)$product['stock'] > 0
                        ? 'موجود'
                        : 'ناموجود';
                    ?>
                </strong>

            </div>

        </div>

    </div>



    <!-- =================================================
         QUESTIONS
    ================================================== -->

    <div
        class="tab-content"
        id="questions"
    >

        <div class="questions-empty">

            هنوز پرسشی برای این محصول ثبت نشده است.

        </div>

    </div>


</section>

</div>


<?php else: ?>


<!-- =====================================================
     NOT FOUND
===================================================== -->

<div class="product-wrap">

    <div class="product-not-found">

        <h2>
            محصول پیدا نشد
        </h2>

        <a href="category.php">
            بازگشت به فروشگاه
        </a>

    </div>

</div>


<?php endif; ?>


</main>


<?php include "lib/footer.php"; ?>



<script>

/* =========================================================
   GALLERY
========================================================= */

document.querySelectorAll('.product-thumb')
.forEach(function (thumb) {

    thumb.addEventListener('click', function () {

        const main =
            document.getElementById('mainProductImage');

        if (main) {

            main.src =
                this.dataset.image;

        }


        document
        .querySelectorAll('.product-thumb')
        .forEach(function (item) {

            item.classList.remove('active');

        });


        this.classList.add('active');

    });

});



/* =========================================================
   QUANTITY
========================================================= */

const quantityInput =
    document.querySelector(
        '.quantity-control input'
    );


const plus =
    document.querySelector(
        '.quantity-plus'
    );


const minus =
    document.querySelector(
        '.quantity-minus'
    );


if (quantityInput) {


    plus.addEventListener(
        'click',
        function () {

            let value =
                parseInt(
                    quantityInput.value
                ) || 1;

            let max =
                parseInt(
                    quantityInput.max
                ) || 999;

            if (value < max) {

                quantityInput.value =
                    value + 1;

            }

        }
    );


    minus.addEventListener(
        'click',
        function () {

            let value =
                parseInt(
                    quantityInput.value
                ) || 1;

            if (value > 1) {

                quantityInput.value =
                    value - 1;

            }

        }
    );

}



/* =========================================================
   TABS
========================================================= */

document
.querySelectorAll('.review-tabs button')
.forEach(function (button) {


    button.addEventListener(
        'click',
        function () {


            const target =
                this.dataset.tab;


            document
            .querySelectorAll(
                '.review-tabs button'
            )
            .forEach(function (item) {

                item.classList.remove(
                    'active'
                );

            });


            document
            .querySelectorAll(
                '.tab-content'
            )
            .forEach(function (item) {

                item.classList.remove(
                    'active'
                );

            });
            this.classList.add('active');


            const content =
                document.getElementById(
                    target
                );


            if (content) {

                content.classList.add(
                    'active'
                );

            }

        }
    );

});

</script>