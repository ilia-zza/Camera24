<?php


require_once __DIR__ . "/function/function.php";


/* =========================
   اطلاعات سایت
========================= */

$settings = getSiteSettings();

$menus = getMenus();

$banner = getHomeContent('banner');

$banners_result = getBanners();

$banners = [];

if ($banners_result) {

    while ($row = mysqli_fetch_assoc($banners_result)) {

        $banners[] = $row;

    }

}

$flash = getHomeContent('flash');

$short = getHomeContent('short');

$short_boxes = getShortBoxes();

/* =========================
   گالری محصولات
========================= */
$gallery = getProductGallery();
$gallery_title = getHomeContent('gallery_title');
$latest_products_title = getHomeContent('latest_products_title');

$latest_product_boxes = getLatestProductBoxes();
$photography_news = getPhotographyNews();
$photography_news_settings = getHomeContent('photography_news');

$customer_comments = getCustomerComments();

$brands = getBrands();
$footer = getFooter();
$footer_related_links = getFooterItems('related_link');

$footer_latest_posts = getFooterItems('latest_post');

$footer_contacts = getFooterItems('contact');

$footer_socials = getFooterSocials();
/* =========================
   تعداد سبد خرید
========================= */

$cart_count = 0;

foreach ($_SESSION['cart'] ?? [] as $quantity) {

    $cart_count += (int)$quantity;

}
include"lib/header.php";

?>

<!-- =========================
     بخش بنر
========================= -->

<div class="container">

    <div class="boxax1">


        <!-- اسلایدر -->

        <div class="ax1">

            <div
                id="mainSlider"
                class="main-slider"
            >


              <?php

$slideIndex = 0;

?>

<?php if (!empty($banners)) { ?>

    <?php foreach ($banners as $slide) { ?>

        <a
            href="<?php echo htmlspecialchars($slide['link'] ?? '#'); ?>"
            class="slide <?php echo $slideIndex === 0 ? 'active' : ''; ?>"
            data-title="<?php echo htmlspecialchars($slide['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
        >

            <img
                src="img/<?php echo htmlspecialchars($slide['image'] ?? ''); ?>"
                class="ax"
                alt="<?php echo htmlspecialchars($slide['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            >

        </a>

        <?php $slideIndex++; ?>

    <?php } ?>

<?php } ?>
                <?php if ($slideIndex > 0) { ?>


                    <div class="slider-control">


                        <button
                            type="button"
                            id="sliderPrev"
                            class="slider-arrow"
                        >
                            ↑
                        </button>


                        <button
                            type="button"
                            id="sliderNext"
                            class="slider-arrow"
                        >
                            ↓
                        </button>


                        <div class="slider-number">


                            <span id="currentSlide">
                                01
                            </span>


                            <div class="slider-lines">

                                <span></span>

                                <span></span>

                                <span></span>

                            </div>


                            <span id="totalSlides">

                                <?php
                                echo str_pad(
                                    $slideIndex,
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                );
                                ?>

                            </span>


                        </div>


                    </div>


                <?php } ?>


            </div>

        </div>


        <div class="textboxnoghte1">

            <img
                src="img/10.png"
                width="100%"
                alt=""
            >

        </div>


        <!-- =========================
             عنوان بنر فعلی
        ========================= -->

        <div class="textbox1">

            <div class="text1">

            <?php if ($slideIndex > 0) { ?>

    <h1 id="bannerTitle">

        <?php
        echo htmlspecialchars(
            $banners[0]['title'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        );
        ?>

    </h1>


<?php }  elseif ($banner) { ?>

                    <h1>

                        <?php
                        echo htmlspecialchars(
                            $banner['title'] ?? ''
                        );
                        ?>

                    </h1>

                <?php } ?>


            </div>

        </div>


        <div class="downbox1">

            <a
                href="<?php echo htmlspecialchars($flash['link'] ?? '#'); ?>"
            ></a>

        </div>


       <?php
include "lib/bottons.php";
?>
<!-- =========================
     کوتاه از Digi24
========================= -->

<div class="container">

    <div class="kotah">


        <div class="kotahdaiere">

            <img
                src="img/11.png"
                class="daiere"
                alt=""
            >

        </div>


        <div class="boxkotah">

<div class="titlekotah">

    <?php
    $title = $short['title'] ?? '';

    preg_match('/^(.*?)(\d+)$/u', $title, $matches);

    $title_text = $matches[1] ?? $title;
    $title_number = $matches[2] ?? '';
    ?>

    <h1>
        <bdi>
            <span class="title-text"><?php echo htmlspecialchars($title_text); ?></span><?php if ($title_number !== '') { ?><span class="title-number"><?php echo htmlspecialchars($title_number); ?></span><?php } ?>
        </bdi>
    </h1>

</div>
<div class="textkotah">

    <div
        class="short-text-preview"
        id="shortTextPreview"
    >
        <?php
        $short_text = strip_tags($short['content'] ?? '');
        echo nl2br(htmlspecialchars($short_text));
        ?>
    </div>

    <button
        type="button"
        class="short-text-more"
        id="shortTextMore"
    >
        ...
    </button>

</div>
           <div class="all3box">

    <?php if ($short_boxes) { ?>

        <?php
        $box_number = 1;

        while ($box = mysqli_fetch_assoc($short_boxes)) {
        ?>

            <div class="short-box">

                <h1 class="numberofbox">

                    <?php
                    echo str_pad(
                        $box_number,
                        2,
                        '0',
                        STR_PAD_LEFT
                    );
                    ?>

                </h1>


                <div class="titleboxin1">

                    <h1>

                        <?php
                        echo htmlspecialchars(
                            $box['title'] ?? ''
                        );
                        ?>

                    </h1>

                </div>


                <?php if (!empty($box['image'])) { ?>

                    <img
                        src="img/<?php echo htmlspecialchars($box['image']); ?>"
                        alt="<?php echo htmlspecialchars($box['title'] ?? ''); ?>"
                        class="short-box-image"
                    >

                <?php } ?>


                <div class="textboxin1">

                    <h1>

                        <?php
                        echo htmlspecialchars(
                            $box['content'] ?? ''
                        );
                        ?>

                    </h1>

                </div>


            </div>


        <?php
            $box_number++;
        }
        ?>

    <?php } ?>

</div>
<!-- =========================
     گالری محصولات
========================= -->

<div class="container">

    <div class="productgallarybox">


        <div class="productgallaryboxdaiere">

            <img
                src="img/11.png"
                class="daiere"
                alt=""
            >

        </div>


        <div class="productgallaryboxleft">

            <img
                src="img/10.png"
                class="daiere"
                alt=""
            >

        </div>


        <div class="productgallaryboxtitlefirs">
    <h1 class="pg">
        <?php
        echo htmlspecialchars(
            $gallery_title['title'] ?? 'گالری محصولات'
        );
        ?>
    </h1>
</div>


        <div class="productgallaryboxtitlefirskhat"></div>

        <div class="productgallaryboxtitlefirskhat1"></div>


        <div class="product-gallery-items">


            <?php if ($gallery) { ?>


                <?php while (
                    $item = mysqli_fetch_assoc($gallery)
                ) { ?>


                    <div class="productgallaryboxtitle">


                        <a
                            href="<?php
                                $category_id = getGalleryCategoryId($item);
                                $gallery_link = $category_id > 0
                                    ? 'category.php?id=' . $category_id
                                    : '#';
                                echo htmlspecialchars($gallery_link, ENT_QUOTES, 'UTF-8');
                            ?>"
                        >


                            <img
                                src="img/<?php echo htmlspecialchars($item['image'] ?? ''); ?>"
                                class="axha"
                                alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"
                            >


                            <div class="productgallarycheshm">

                                <img
                                    src="img/1000.png"
                                    class="cheshm"
                                    alt=""
                                >

                            </div>


                            <div class="productgallarycategory">

                                <h1 class="ctxt">

                                    <?php
                                    echo htmlspecialchars(
                                        $item['title'] ?? ''
                                    );
                                    ?>

                                </h1>

                            </div>


                        </a>

                        
                    </div>


                <?php } ?>


            <?php } ?>


            
        </div>
<!-- =========================
     برخی از جدیدترین محصولات
========================= -->

<div class="container">

    <div class="latest-products-section">

        <!-- عنوان بخش -->

        <div class="latest-products-title">

            <h1>
                <?php
                echo htmlspecialchars(
                    $latest_products_title['title'] ?? ''
                );
                ?>
            </h1>

        </div>


        <?php if ($latest_product_boxes) { ?>

            <?php while ($box = mysqli_fetch_assoc($latest_product_boxes)) { ?>

                <?php
                $products = getLatestProductsByBox($box);
                ?>


                <div class="latest-products-box">


                    <!-- عنوان باکس -->

                    <div class="latest-products-box-header">

                        <h2>
                            <?php
                            echo htmlspecialchars(
                                $box['title'] ?? ''
                            );
                            ?>
                        </h2>


                        <div class="latest-products-arrows">

                            <button
                                type="button"
                                class="latest-prev"
                            >
                                ‹
                            </button>

                            <button
                                type="button"
                                class="latest-next"
                            >
                                ›
                            </button>

                        </div>

                    </div>


                    <!-- محصولات -->

                    <div class="latest-products-window">

                        <div class="latest-products-list">


                            <?php if ($products) { ?>

                                <?php while ($product = mysqli_fetch_assoc($products)) { ?>

                                    <a
                                        href="product.php?id=<?php echo (int)$product['id']; ?>"
                                        class="latest-product"
                                    >

                                        <img
                                            src="img/<?php echo htmlspecialchars($product['image'] ?? ''); ?>"
                                            alt="<?php echo htmlspecialchars($product['name'] ?? ''); ?>"
                                        >


                                        <h3>
                                            <?php
                                            echo htmlspecialchars(
                                                $product['name'] ?? ''
                                            );
                                            ?>
                                        </h3>


                                        <?php if (
                                            !empty($product['old_price']) &&
                                            $product['old_price'] > $product['price']
                                        ) { ?>

                                            <div class="old-price">

                                                <?php
                                                echo number_format(
                                                    $product['old_price']
                                                );
                                                ?>

                                                تومان

                                            </div>

                                        <?php } ?>


                                        <strong class="latest-product-price">

                                            <?php
                                            echo number_format(
                                                $product['price']
                                            );
                                            ?>

                                            تومان

                                        </strong>


                                        <p>
                                            <?php
                                            echo htmlspecialchars(
                                                $product['discription'] ?? ''
                                            );
                                            ?>
                                        </p>

                                    </a>

                                <?php } ?>

                            <?php } ?>


                        </div>

                    </div>


                    <!-- مشاهده دیگر محصولات -->

                    <a
                        href="<?php echo htmlspecialchars($box['link'] ?? '#'); ?>"
                        class="latest-products-more"
                    >
                        مشاهده دیگر محصولات
                    </a>


                    <div class="latest-products-corner">

                        ★

                    </div>


                </div>

            <?php } ?>

        <?php } ?>


    </div>

</div>

<!-- =========================
     تازه های دنیای عکاسی و فیلمبرداری
========================= -->

<section class="photography-news">

    <div class="container">

        <div class="photography-news-title">

            <h2>
    <?php echo htmlspecialchars(
        $photography_news_settings['title'] ?? ''
    ); ?>
</h2>

<p>
    <?php echo nl2br(
        htmlspecialchars(
            $photography_news_settings['content'] ?? ''
        )
    ); ?>
</p>

        </div>


        <div class="photography-news-grid">

            <?php if ($photography_news) { ?>

                <?php
                $news_items = [];

                while ($news = mysqli_fetch_assoc($photography_news)) {

                    $news_items[] = $news;

                }
                ?>


                <?php if (!empty($news_items[0])) { ?>

                    <a
                        href="<?php echo 'blog-single.php?slug=article-1788563665'; ?>"
                        class="photography-news-large"
                    >

                        <img
                            src="img/<?php echo htmlspecialchars($news_items[0]['image'] ?? ''); ?>"
                            alt="<?php echo htmlspecialchars($news_items[0]['title'] ?? ''); ?>"
                        >

                        <div class="photography-news-overlay">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $news_items[0]['title'] ?? ''
                                );
                                ?>
                            </h3>

                        </div>

                    </a>

                <?php } ?>


                <div class="photography-news-small-column">


                    <?php if (!empty($news_items[1])) { ?>

                        <a
                            href="<?php echo 'blog-single.php?slug=article-1788564493'; ?>"
                            class="photography-news-small"
                        >

                            <img
                                src="img/<?php echo htmlspecialchars($news_items[1]['image'] ?? ''); ?>"
                                alt="<?php echo htmlspecialchars($news_items[1]['title'] ?? ''); ?>"
                            >

                            <div class="photography-news-overlay">

                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $news_items[1]['title'] ?? ''
                                    );
                                    ?>
                                </h3>

                            </div>

                        </a>

                    <?php } ?>


                    <?php if (!empty($news_items[2])) { ?>

                        <a
                            href="<?php echo 'blog-single.php?slug=article-1788618463'; ?>"
                            class="photography-news-small"
                        >

                            <img
                                src="img/<?php echo htmlspecialchars($news_items[2]['image'] ?? ''); ?>"
                                alt="<?php echo htmlspecialchars($news_items[2]['title'] ?? ''); ?>"
                            >

                            <div class="photography-news-overlay">

                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $news_items[2]['title'] ?? ''
                                    );
                                    ?>
                                </h3>

                            </div>

                        </a>

                    <?php } ?>


                </div>

            <?php } ?>

        </div>

    </div>

</section>
<!-- =========================
     نظرات مشتریان
========================= -->
<section class="customer-comments">

    <div class="container">

        <div class="customer-comments-title">

            <a href="customer-comment.php">

                <h2>
                    نظرات شما عزیزان درباره تجربه خرید از
                    <span>
                        <?php
                        echo htmlspecialchars(
                            $settings['site_name'] ?? 'Digi24'
                        );
                        ?>
                    </span>
                </h2>

            </a>

        </div>


        <div class="customer-comments-content">

            <?php if ($customer_comments) { ?>

                <?php

                $comments = [];

                while ($comment = mysqli_fetch_assoc($customer_comments)) {

                    $comments[] = $comment;

                }

                ?>


                <?php if (!empty($comments)) { ?>


                    <!-- فلش قبلی -->

                    <button
                        type="button"
                        class="comments-arrow comments-prev"
                    >
                        ‹
                    </button>


                    <!-- محتوای اصلی -->

                    <div class="customer-comments-main">


                        <!-- عکس مشتری‌ها -->

                        <div class="customer-comments-users">

                            <?php foreach ($comments as $index => $comment) { ?>

                                <div
                                    class="comment-user <?php echo $index === 0 ? 'active' : ''; ?>"
                                    data-comment="<?php echo $index; ?>"
                                >

                                    <img
                                        src="img/<?php echo htmlspecialchars($comment['image'] ?? ''); ?>"
                                        alt="<?php echo htmlspecialchars($comment['name'] ?? ''); ?>"
                                    >

                                </div>

                            <?php } ?>

                        </div>


                        <div class="comment-line"></div>


                        <!-- متن نظر -->

                        <p class="customer-comment-text">
                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $comments[0]['comment'] ?? ''
                                )
                            );
                            ?>
                        </p>


                        <!-- ستاره‌ها -->

                        <div class="comment-stars">

                            <?php

                            $rating =
                                (int)($comments[0]['rating'] ?? 5);

                            for ($i = 1; $i <= 5; $i++) {

                                echo $i <= $rating ? '★' : '☆';

                            }

                            ?>

                        </div>


                        <!-- نام مشتری -->

                        <div class="comment-user-name">

                            <?php
                            echo htmlspecialchars(
                                $comments[0]['name'] ?? ''
                            );
                            ?>

                        </div>


                    </div>


                    <!-- فلش بعدی -->

                    <button
                        type="button"
                        class="comments-arrow comments-next"
                    >
                        ›
                    </button>


                <?php } ?>


            <?php } ?>


        </div>

    </div>

</section>
<!-- =========================
     برندهایی که با آن ها کار میکنیم
========================= -->

<section class="brands-section">

    <div class="container">

        <div class="brands-title">

            <h2>
                برندهایی که با آن ها کار میکنیم
            </h2>

        </div>


        <div class="brands-list">

            <?php if ($brands) { ?>

                <?php while ($brand = mysqli_fetch_assoc($brands)) { ?>

                    <a
                        href="<?php echo htmlspecialchars($brand['link'] ?? '#'); ?>"
                        class="brand-item"
                    >

                        <?php if (!empty($brand['logo'])) { ?>

                            <img
                                src="img/<?php echo htmlspecialchars($brand['logo']); ?>"
                                alt="<?php echo htmlspecialchars($brand['name'] ?? ''); ?>"
                            >

                        <?php } else { ?>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    $brand['name'] ?? ''
                                );
                                ?>
                            </span>

                        <?php } ?>

                    </a>

                <?php } ?>

            <?php } ?>

        </div>

    </div>

</section>
<?php 
include"lib/footer.php";
?>
<!-- =========================
     کپی رایت
========================= -->

<div class="copyright">

    <div class="container">

       <p>
    <?php
    echo htmlspecialchars(
        $footer['copyright_text'] ?? ''
    );
    ?>
</p>

    </div>

</div>
<script src="js/code.js"></script>

<script>
const shortText = document.getElementById("shortTextPreview");
const shortMore = document.getElementById("shortTextMore");

if (shortText && shortMore) {

    shortMore.addEventListener("click", function () {

        if (shortText.classList.contains("show-full")) {

            shortText.classList.remove("show-full");

            shortMore.textContent = "...";

        } else {

            shortText.classList.add("show-full");

            shortMore.textContent = "بستن";

        }

    });

}

</script>
<script>

document.querySelectorAll(".latest-products-box").forEach(function(box) {

    const list = box.querySelector(".latest-products-list");

    const buttons = box.querySelectorAll(".latest-products-arrows button");

    let position = 0;

    const moveAmount = 440;


    if (!list || buttons.length < 2) {
        return;
    }


    buttons[0].addEventListener("click", function() {

        position += moveAmount;

        list.style.transform = "translateX(" + position + "px)";

    });


    buttons[1].addEventListener("click", function() {

        position -= moveAmount;

        if (position < 0) {
            position = 0;
        }

        list.style.transform = "translateX(" + position + "px)";

    });

});

</script>
<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =========================
           اطلاعات نظرات
        ========================= */

        const comments = <?php echo json_encode(
            $comments ?? [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ); ?>;


        /* =========================
           عناصر
        ========================= */

        const users =
            document.querySelectorAll(
                '.comment-user'
            );

        const commentText =
            document.querySelector(
                '.customer-comment-text'
            );

        const commentStars =
            document.querySelector(
                '.comment-stars'
            );

        const commentName =
            document.querySelector(
                '.comment-user-name'
            );

        const prevButton =
            document.querySelector(
                '.comments-prev'
            );

        const nextButton =
            document.querySelector(
                '.comments-next'
            );


        /* =========================
           اگر نظری وجود نداشت
        ========================= */

        if (
            comments.length === 0 ||
            !commentText ||
            !commentStars ||
            !commentName
        ) {

            return;

        }


        /* =========================
           نظر فعلی
        ========================= */

        let currentIndex = 0;


        /* =========================
           نمایش نظر
        ========================= */

        function showComment(index) {


            currentIndex = index;


            const comment =
                comments[currentIndex];


            /* =========================
               فعال کردن عکس
            ========================= */

            users.forEach(
                function (user, userIndex) {

                    user.classList.toggle(
                        'active',
                        userIndex === currentIndex
                    );

                }
            );


            /* =========================
               متن
            ========================= */

            commentText.innerHTML =
                escapeHtml(
                    comment.comment || ''
                ).replace(
                    /\n/g,
                    '<br>'
                );


            /* =========================
               ستاره‌ها
            ========================= */

            const rating =
                parseInt(
                    comment.rating || 5
                );


            let stars = '';


            for (
                let i = 1;
                i <= 5;
                i++
            ) {

                stars +=
                    i <= rating
                        ? '★'
                        : '☆';

            }


            commentStars.innerHTML =
                stars;


            /* =========================
               نام
            ========================= */

            commentName.textContent =
                comment.name || '';

        }


        /* =========================
           جلوگیری از HTML
        ========================= */

        function escapeHtml(text) {

            const div =
                document.createElement('div');

            div.textContent = text;

            return div.innerHTML;

        }


        /* =========================
           فلش قبلی
        ========================= */

        if (prevButton) {

            prevButton.addEventListener(
                'click',
                function () {

                    currentIndex--;

                    if (currentIndex < 0) {

                        currentIndex =
                            comments.length - 1;

                    }

                    showComment(
                        currentIndex
                    );

                }
            );

        }


        /* =========================
           فلش بعدی
        ========================= */

        if (nextButton) {

            nextButton.addEventListener(
                'click',
                function () {

                    currentIndex++;

                    if (
                        currentIndex >=
                        comments.length
                    ) {

                        currentIndex = 0;

                    }

                    showComment(
                        currentIndex
                    );

                }
            );

        }


        /* =========================
           کلیک روی عکس مشتری
        ========================= */

        users.forEach(
            function (user) {

                user.addEventListener(
                    'click',
                    function () {

                        const index =
                            parseInt(
                                user.dataset.comment
                            );

                        showComment(index);

                    }
                );

            }
        );


    }
);

</script>
</body>

</html>
