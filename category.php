<?php


include("function/config.php");
include("function/function.php");


/* =========================
   اطلاعات سایت
========================= */

$settings = getSiteSettings();

$menus = getMenus();

$brands = getBrands();


/* =========================
   اطلاعات فوتر
========================= */

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


/* =========================
   شناسه دسته بندی
========================= */

$category_id = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;


$brand_id = isset($_GET['brand'])
    ? intval($_GET['brand'])
    : 0;


$min_price = isset($_GET['min_price'])
    ? intval($_GET['min_price'])
    : 0;


$max_price = isset($_GET['max_price'])
    ? intval($_GET['max_price'])
    : 100000000;


/* =========================
   مرتب سازی و جستجوی محصولات
========================= */

$sort = $_GET['sort'] ?? 'newest';

$search = trim($_GET['search'] ?? '');

$order_by = 'created_at DESC';


switch ($sort) {

    case 'best-selling':

        break;

    case 'expensive':

        $order_by = 'price DESC';

        break;

    case 'cheap':

        $order_by = 'price ASC';

        break;

    case 'newest':

    default:

        $sort = 'newest';

        $order_by = 'created_at DESC';

        break;
}


/* =========================
   صفحه بندی
========================= */

$page = isset($_GET['page'])
    ? max(1, intval($_GET['page']))
    : 1;


$per_page = 12;

$offset = ($page - 1) * $per_page;


/* =========================
   تعداد کل محصولات
========================= */

$total_products = ($sort === 'best-selling')
    ? getBestSellingProductsCountByCategory($category_id)
    : getFilteredProductsCount(
        $category_id,
        $brand_id,
        $min_price,
        $max_price,
        $search
    );

$total_pages = max(1, (int)ceil($total_products / $per_page));

if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $per_page;
}


/* =========================
   اطلاعات دسته بندی
========================= */

$category_result = getCategory($category_id);

$category = $category_result
    ? mysqli_fetch_assoc($category_result)
    : null;

if (!$category) {
    $categories_for_fallback = getCategories();
    $first_category = $categories_for_fallback ? mysqli_fetch_assoc($categories_for_fallback) : null;
    if ($first_category) {
        header('Location: category.php?id=' . (int)$first_category['id']);
    } else {
        header('Location: index.php');
    }
    exit;
}


$category_description = $category
    ? $category['description']
    : '';


/* =========================
   FAQ دسته بندی
========================= */

$category_faqs = getCategoryFaqs($category_id);


/* =========================
   محصولات دسته بندی
========================= */

if ($sort === 'best-selling') {
    $products_result = getBestSellingProductsByCategoryPaginated($category_id, $per_page, $offset);
} else {
    $products_result = getFilteredProductsPaginated(
        $category_id,
        $brand_id,
        $min_price,
        $max_price,
        $search,
        $order_by,
        $per_page,
        $offset
    );
}


/* =========================
   عنوان صفحه
========================= */

$page_title = $category
    ? 'خرید ' . $category['name']
    : 'دسته بندی محصولات';


/* =========================
   هدر
========================= */

include("lib/header.php");

?>


<!-- =========================================
     CATEGORY CSS
========================================= -->

<link rel="stylesheet" href="css/category.css">


<!-- =========================================
     PINK LINE
========================================= -->

<div class="category-top-line"></div>


<!-- =========================================
     CATEGORY PAGE
========================================= -->

<div class="category-page">


    <!-- =====================================
         TOP CATEGORY MENU
    ====================================== -->

    <div class="category-top-menu">


        <div class="category-menu-right">
            

            <!-- جدیدترین ها -->

            <a
                href="category.php?id=<?php echo $category_id; ?>&sort=newest"
                class="<?php echo $sort === 'newest' ? 'category-menu-active' : ''; ?>"
            >

                جدیدترین ها

            </a>


            <!-- گران ترین -->

            <a
                href="category.php?id=<?php echo $category_id; ?>&sort=expensive"
                class="<?php echo $sort === 'expensive' ? 'category-menu-active' : ''; ?>"
            >

                گران ترین

            </a>


            <!-- ارزان ترین -->

            <a
                href="category.php?id=<?php echo $category_id; ?>&sort=cheap"
                class="<?php echo $sort === 'cheap' ? 'category-menu-active' : ''; ?>"
            >

                ارزان ترین

            </a>


            <!-- پرفروش ها -->

            <a
                href="category.php?id=<?php echo $category_id; ?>&sort=best-selling"
                class="<?php echo $sort === 'best-selling' ? 'category-menu-active' : ''; ?>"
            >

                پرفروش ها

            </a>


            <!-- محبوب ترین -->

            <a href="#">

                محبوب ترین

            </a>


        </div>


        <!-- فیلتر -->

        <div class="category-menu-filter">

            ☰

        </div>


    </div>


    <!-- =====================================
         MAIN CONTENT
    ====================================== -->

    <div class="category-main">


        <!-- =================================
             PRODUCTS
        ================================== -->

        <div class="category-products-area">


            <div class="products-grid">


                <?php if ($products_result && mysqli_num_rows($products_result) > 0) { ?>


                    <?php while ($product = mysqli_fetch_assoc($products_result)) { ?>


                        <div class="category-product">


                            <!-- تصویر محصول -->

                            <a
                                href="product.php?id=<?php echo (int)$product['id']; ?>"
                                class="category-product-image"
                            >

                                <img
                                    src="img/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                >

                            </a>


                            <!-- نام محصول -->

                            <a
                                href="product.php?id=<?php echo (int)$product['id']; ?>"
                                class="category-product-name"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $product['name']
                                );

                                ?>

                            </a>


                            <!-- قیمت -->

                            <div class="category-product-price">


                                <span>

                                    <?php

                                    echo number_format(
                                        (int)$product['price']
                                    );

                                    ?>

                                </span>


                                <small>

                                    تومان

                                </small>


                            </div>


                            <!-- توضیحات -->

                            <div class="category-product-info">


                                <?php

                                echo htmlspecialchars(
                                    mb_substr(
                                        $product['discription'],
                                        0,
                                        60
                                    )
                                );

                                ?>


                            </div>


                        </div>


                    <?php } ?>


                <?php } else { ?>


                    <div class="category-no-products">


                        <?php if ($search !== '') { ?>


                            محصولی با عبارت
                            «<?php echo htmlspecialchars($search); ?>»
                            در این دسته بندی پیدا نشد.


                        <?php } else { ?>


                            محصولی در این دسته بندی وجود ندارد.


                        <?php } ?>


                    </div>


                <?php } ?>


            </div>


            <!-- =================================
                 PAGINATION
            ================================== -->

            <div class="category-pagination">


                <?php if ($page > 1) { ?>

                    <a
                        href="?id=<?php echo $category_id; ?>&page=<?php echo $page - 1; ?>&sort=<?php echo urlencode($sort); ?>&brand=<?php echo $brand_id; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>&search=<?php echo urlencode($search); ?>"
                        class="pagination-arrow"
                    >

                        ←

                    </a>

                <?php } ?>


                <?php


                /*
                 * صفحه اول
                 */

                if ($page == 1) {

                    echo '<a href="?id=' . $category_id .
                         '&page=1' .
                         '&sort=' . urlencode($sort) .
                         '&brand=' . $brand_id .
                         '&min_price=' . $min_price .
                         '&max_price=' . $max_price .
                         '&search=' . urlencode($search) .
                         '" class="pagination-active">1</a>';

                } else {

                    echo '<a href="?id=' . $category_id .
                         '&page=1' .
                         '&sort=' . urlencode($sort) .
                         '&brand=' . $brand_id .
                         '&min_price=' . $min_price .
                         '&max_price=' . $max_price .
                         '&search=' . urlencode($search) .
                         '">1</a>';

                }


                /*
                 * اگر صفحات کم باشند
                 */

                if ($total_pages <= 5) {

                    for ($i = 2; $i <= $total_pages; $i++) {

                        $active = ($i == $page)
                            ? 'pagination-active'
                            : '';

                        echo '<a href="?id=' . $category_id .
                             '&page=' . $i .
                             '&sort=' . urlencode($sort) .
                             '&brand=' . $brand_id .
                             '&min_price=' . $min_price .
                             '&max_price=' . $max_price .
                             '&search=' . urlencode($search) .
                             '" class="' . $active . '">' .
                             $i .
                             '</a>';

                    }


                } else {


                    /*
                     * صفحه های زیاد
                     */

                    if ($page <= 3) {

                        for ($i = 2; $i <= 3; $i++) {

                            $active = ($i == $page)
                                ? 'pagination-active'
                                : '';

                            echo '<a href="?id=' . $category_id .
                                 '&page=' . $i .
                                 '&sort=' . urlencode($sort) .
                                 '&brand=' . $brand_id .
                                 '&min_price=' . $min_price .
                                 '&max_price=' . $max_price .
                                 '&search=' . urlencode($search) .
                                 '" class="' . $active . '">' .
                                 $i .
                                 '</a>';

                        }


                        echo '<span>...</span>';


                        echo '<a href="?id=' . $category_id .
                             '&page=' . $total_pages .
                             '&sort=' . urlencode($sort) .
                             '&brand=' . $brand_id .
                             '&min_price=' . $min_price .
                             '&max_price=' . $max_price .
                             '&search=' . urlencode($search) .
                             '">' .
                             $total_pages .
                             '</a>';


                    } elseif ($page >= $total_pages - 2) {


                        echo '<span>...</span>';


                        for ($i = $total_pages - 2; $i <= $total_pages; $i++) {

                            $active = ($i == $page)
                                ? 'pagination-active'
                                : '';

                            echo '<a href="?id=' . $category_id .
                                 '&page=' . $i .
                                 '&sort=' . urlencode($sort) .
                                 '&brand=' . $brand_id .
                                 '&min_price=' . $min_price .
                                 '&max_price=' . $max_price .
                                 '&search=' . urlencode($search) .
                                 '" class="' . $active . '">' .
                                 $i .
                                 '</a>';

                        }


                    } else {


                        echo '<span>...</span>';


                        for ($i = $page - 1; $i <= $page + 1; $i++) {

                            $active = ($i == $page)
                                ? 'pagination-active'
                                : '';

                            echo '<a href="?id=' . $category_id .
                                 '&page=' . $i .
                                 '&sort=' . urlencode($sort) .
                                 '&brand=' . $brand_id .
                                 '&min_price=' . $min_price .
                                 '&max_price=' . $max_price .
                                 '&search=' . urlencode($search) .
                                 '" class="' . $active . '">' .
                                 $i .
                                 '</a>';

                        }


                        echo '<span>...</span>';


                        echo '<a href="?id=' . $category_id .
                             '&page=' . $total_pages .
                             '&sort=' . urlencode($sort) .
                             '&brand=' . $brand_id .
                             '&min_price=' . $min_price .
                             '&max_price=' . $max_price .
                             '&search=' . urlencode($search) .
                             '">' .
                             $total_pages .
                             '</a>';

                    }

                }


                /*
                 * دکمه صفحه بعد
                 */

                if ($page < $total_pages) {

                    echo '<a href="?id=' . $category_id .
                         '&page=' . ($page + 1) .
                         '&sort=' . urlencode($sort) .
                         '&brand=' . $brand_id .
                         '&min_price=' . $min_price .
                         '&max_price=' . $max_price .
                         '&search=' . urlencode($search) .
                         '" class="pagination-arrow">→</a>';

                }

                ?>


            </div>


        </div>


        <!-- =================================
             SIDEBAR
        ================================== -->

        <aside class="category-sidebar">


            <!-- SEARCH -->

            <div class="sidebar-box">


                <h3>

                    جستجو

                </h3>


                <form
                    method="GET"
                    action="category.php"
                    class="sidebar-search"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo (int)$category_id; ?>"
                    >


                    <input
                        type="hidden"
                        name="sort"
                        value="<?php echo htmlspecialchars($sort); ?>"
                    >


                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="محصول مورد نظر را جستجو کنید"
                    >


                    <button type="submit">

                       <img src="img/searchicon.png"class="searchicon"alt=""/>

                    </button>


                </form>


            </div>


            <!-- BRAND -->

            <div class="sidebar-box">


                <h3>

                    برند

                </h3>


                <div class="sidebar-options">


                    <?php if ($brands && mysqli_num_rows($brands) > 0) { ?>


                        <?php while ($brand = mysqli_fetch_assoc($brands)) { ?>


                            <label>


                                <span>

                                    <?php echo htmlspecialchars($brand['name']); ?>

                                </span>


                                <input
                                    type="radio"
                                    name="brand"
                                    value="<?php echo (int)$brand['id']; ?>"
                                    <?php echo $brand_id == $brand['id'] ? 'checked' : ''; ?>
                                    onclick="window.location.href='category.php?id=<?php echo (int)$category_id; ?>&sort=<?php echo urlencode($sort); ?>&brand=<?php echo (int)$brand['id']; ?>'"
                                >


                            </label>


                        <?php } ?>


                    <?php } else { ?>


                        <span style="font-size:10px; color:#999;">

                            برندی ثبت نشده است.

                        </span>


                    <?php } ?>


                </div>


            </div>


            <!-- PRICE -->

            <div class="sidebar-box">


                <h3>

                    محدوده قیمت

                </h3>


                <div class="price-slider">


                    <div class="price-slider-line"></div>


                    <input
                        type="range"
                        class="price-range price-range-min"
                        min="0"
                        max="100000000"
                        step="100000"
                        value="<?php echo $min_price; ?>"
                    >


                    <input
                        type="range"
                        class="price-range price-range-max"
                        min="0"
                        max="100000000"
                        step="100000"
                        value="<?php echo $max_price; ?>"
                    >


                </div>


                <div class="price-range-text">


                    <span>

                        از:

                        <b>
                            <?php echo number_format($min_price); ?>
                        </b>

                    </span>


                    <span>

                        تا:

                        <b>
                            <?php echo number_format($max_price); ?>
                        </b>

                    </span>


                </div>


            </div>


            <!-- OTHER BRANDS -->

            <div class="sidebar-box">


                <h3>

                    دیگر برندها

                </h3>


                <div class="sidebar-options">


                    <label>

                        <span>

                            پاناسونیک

                        </span>

                        <input type="checkbox">

                    </label>


                    <label>

                        <span>

                            سامسونگ

                        </span>

                        <input type="checkbox">

                    </label>


                    <label>

                        <span>

                            توشیبا

                        </span>

                        <input type="checkbox">

                    </label>


                    <label>

                        <span>

                            فوجی فیلم

                        </span>

                        <input type="checkbox">

                    </label>


                </div>


            </div>


        </aside>


    </div>


    <!-- =====================================
         DESCRIPTION
    ====================================== -->

    <section class="category-description">


        <h2>

            <?php echo htmlspecialchars($category['name'] ?? 'محصولات'); ?>

        </h2>


        <p>

            <?php echo nl2br(htmlspecialchars($category_description)); ?>

        </p>


    </section>


    <!-- =====================================
         FAQ
    ====================================== -->

    <section class="category-faq">


        <div class="faq-heading">


            <h2>

                سؤالات متداول

            </h2>


            <span>

                ?

            </span>


        </div>


        <div class="faq-list">


            <?php if ($category_faqs && mysqli_num_rows($category_faqs) > 0) { ?>


                <?php while ($faq = mysqli_fetch_assoc($category_faqs)) { ?>


                    <div class="faq-item">


                        <button class="faq-question">


                            <?php echo htmlspecialchars($faq['question']); ?>


                            <span>

                                ⌄

                            </span>


                        </button>


                        <div class="faq-answer">


                            <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>


                        </div>


                    </div>


                <?php } ?>


            <?php } else { ?>


                <div class="faq-empty">

                    سؤالات متداولی برای این دسته‌بندی ثبت نشده است.

                </div>


            <?php } ?>


        </div>


    </section>


</div>


<!-- =========================================
     FOOTER
========================================= -->

<?php include("lib/footer.php"); ?>


<script>


/* =========================
   FAQ
========================= */

document.querySelectorAll(".faq-question").forEach(function(button) {

    button.addEventListener("click", function() {

        let item = this.parentElement;

        item.classList.toggle("faq-open");

    });

});


/* =========================
   فیلتر محدوده قیمت
========================= */

const priceMin = document.querySelector(".price-range-min");

const priceMax = document.querySelector(".price-range-max");


const priceMinText = document.querySelector(
    ".price-range-text span:first-child b"
);


const priceMaxText = document.querySelector(
    ".price-range-text span:last-child b"
);


function formatPrice(price) {

    return Number(price).toLocaleString("en-US");

}


function updatePriceText() {

    let min = parseInt(priceMin.value);

    let max = parseInt(priceMax.value);


    if (min > max) {

        if (this === priceMin) {

            priceMin.value = max;

            min = max;

        } else {

            priceMax.value = min;

            max = min;

        }

    }


    priceMinText.textContent = formatPrice(min);

    priceMaxText.textContent = formatPrice(max);

}


priceMin.addEventListener("input", updatePriceText);

priceMax.addEventListener("input", updatePriceText);


updatePriceText();


function applyPriceFilter() {

    const url = new URL(window.location.href);


    url.searchParams.set(
        "min_price",
        priceMin.value
    );


    url.searchParams.set(
        "max_price",
        priceMax.value
    );


    window.location.href = url.toString();

}


priceMin.addEventListener(
    "change",
    applyPriceFilter
);


priceMax.addEventListener(
    "change",
    applyPriceFilter
);


</script>


</body>

</html>