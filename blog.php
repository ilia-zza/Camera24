<?php

require_once 'function/config.php';
require_once 'function/function.php';


/* =========================================================
   اطلاعات سایت
========================================================= */

$settings = getSiteSettings();

$menus = getMenus();

$brands = getBrands();


/* =========================================================
   اطلاعات فوتر
========================================================= */

$footer = getFooter();

$footer_related_links = getFooterItems('related_link');

$footer_latest_posts = getFooterItems('latest_post');

$footer_contacts = getFooterItems('contact');

$footer_socials = getFooterSocials();


/* =========================================================
   تعداد سبد خرید
========================================================= */

$cart_count = 0;


/* =========================================================
   تنظیمات صفحه Blog
========================================================= */

$blog_title = 'مجله Digi24';

$blog_description = '
طراحی گرافیک از این متن به عنوان عنصری از ترکیب بندی برای پر کردن صفحه و ارائه اولیه شکل ظاهری و کلی طرح سفارش گرفته شده استفاده می نماید. تا از نظر گرافیکی نشانگر چگونگی نوع و اندازه فونت و ظاهر متن باشد. معمولاً طراحان گرافیک برای صفحه آرایی نخست از متن های آزمایشی و بی معنی استفاده می کنند تا صرفاً با مشتری یا صاحب کار خود
نشان دهند که محصول نهایی چگونه خواهد بود.
';


/* =========================================================
   تنظیمات Pagination
========================================================= */

$per_page = 9;


/* =========================================================
   صفحه فعلی
========================================================= */

$current_page = isset($_GET['page'])
    ? (int) $_GET['page']
    : 1;


/* =========================================================
   جلوگیری از صفحه صفر و منفی
========================================================= */

if ($current_page < 1) {
    $current_page = 1;
}


/* =========================================================
   تعداد کل مقالات فعال
========================================================= */

$total_posts = getBlogPostsCount();


/* =========================================================
   تعداد کل صفحات
========================================================= */

$total_pages = $total_posts > 0
    ? (int) ceil($total_posts / $per_page)
    : 1;


/* =========================================================
   جلوگیری از صفحه بیشتر از تعداد صفحات موجود
========================================================= */

if ($current_page > $total_pages) {
    $current_page = $total_pages;
}


/* =========================================================
   محاسبه Offset
========================================================= */

$offset = ($current_page - 1) * $per_page;


/* =========================================================
   دریافت مقالات صفحه فعلی
========================================================= */

$articles = getBlogPosts(
    $per_page,
    $offset
);


/* =========================================================
   Header
========================================================= */

require_once 'lib/header.php';

?>

<link rel="stylesheet" href="css/blog.css">


<main class="blog-page">


    <!-- =====================================================
         DECORATIVE BACKGROUND
    ====================================================== -->

    <div class="blog-decoration blog-decoration-right">

        <span></span>
        <span></span>
        <span></span>

    </div>


    <div class="blog-decoration blog-decoration-left">

        <span></span>
        <span></span>
        <span></span>

    </div>


    <!-- =====================================================
         BLOG INTRO
    ====================================================== -->

    <section class="blog-intro">

        <div class="blog-container">

            <div class="blog-intro-content">

                <h1>

                    مجله

                    <strong>Digi24</strong>

                </h1>


                <p>

                    <?= nl2br(
                        htmlspecialchars(
                            $blog_description,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ); ?>

                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ARTICLES
    ====================================================== -->

    <section class="blog-articles">

        <div class="blog-container">


            <!-- =================================================
                 BLOG GRID
            ================================================== -->

            <div class="blog-grid">


                <?php if ($articles && mysqli_num_rows($articles) > 0): ?>


                    <?php while ($article = mysqli_fetch_assoc($articles)): ?>


                        <?php

                        /* =================================================
                           اطلاعات مقاله
                        ================================================== */

                        $article_id = (int) ($article['id'] ?? 0);

                        $article_title = trim(
                            $article['title'] ?? ''
                        );

                        $article_description = trim(
                            $article['description'] ?? ''
                        );

                        $article_slug = trim(
                            $article['slug'] ?? ''
                        );

                        $article_image = trim(
                            $article['image'] ?? ''
                        );


                        /* =================================================
                           مسیر تصویر
                        ================================================== */

                        if (!empty($article_image)) {

                            /*
                             * اگر فقط نام فایل در دیتابیس ذخیره شده باشد
                             * مسیر img/ به آن اضافه می‌شود.
                             */

                            if (
                                strpos($article_image, '/') === false &&
                                strpos($article_image, '\\') === false
                            ) {

                                $article_image = 'img/' . $article_image;

                            }

                        }


                        /* =================================================
                           لینک مقاله
                        ================================================== */

                        if (!empty($article_slug)) {

                            $article_url =
                                'blog-single.php?slug=' .
                                urlencode($article_slug);

                        } else {

                            /*
                             * اگر slug وجود نداشت،
                             * فعلاً با id لینک می‌سازیم.
                             * در blog-single.php این حالت را هم
                             * مدیریت خواهیم کرد.
                             */

                            $article_url =
                                'blog-single.php?id=' .
                                $article_id;

                        }

                        ?>


                        <!-- =================================================
                             ARTICLE CARD
                        ================================================== -->

                        <article class="blog-card">


                            <!-- IMAGE -->

                            <a
                                href="<?= htmlspecialchars(
                                    $article_url,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                class="blog-card-image"
                            >

                                <?php if (!empty($article_image)): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                            $article_image,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        alt="<?= htmlspecialchars(
                                            $article_title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >

                                <?php endif; ?>


                                <span class="blog-image-overlay"></span>

                            </a>


                            <!-- CONTENT -->

                            <div class="blog-card-content">


                                <h2>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $article_url,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $article_title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </a>

                                </h2>


                                <?php if (!empty($article_description)): ?>

                                    <p>

                                        <?= htmlspecialchars(
                                            $article_description,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </p>

                                <?php endif; ?>


                                <a
                                    href="<?= htmlspecialchars(
                                        $article_url,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="blog-read-more"
                                >

                                    ادامه مطلب

                                    <span>«</span>

                                </a>


                            </div>


                        </article>


                    <?php endwhile; ?>


                <?php else: ?>


                    <!-- =================================================
                         EMPTY STATE
                    ================================================== -->

                    <div class="blog-empty">

                        <p>
                            هنوز مقاله‌ای منتشر نشده است.
                        </p>

                    </div>


                <?php endif; ?>


            </div>


            <!-- =====================================================
                 PAGINATION
            ====================================================== -->

            <?php if ($total_posts > 0 && $total_pages > 1): ?>


                <nav
                    class="blog-pagination"
                    aria-label="صفحه‌بندی مقالات"
                >


                    <!-- =================================================
                         PREVIOUS
                    ================================================== -->

                    <?php if ($current_page > 1): ?>

                        <a
                            href="?page=<?= $current_page - 1; ?>"
                            class="pagination-arrow"
                            aria-label="صفحه قبلی"
                        >

                            ‹

                        </a>

                    <?php endif; ?>


                    <!-- =================================================
                         FIRST PAGE
                    ================================================== -->

                    <a
                        href="?page=1"
                        class="pagination-number <?= $current_page === 1 ? 'active' : ''; ?>"
                    >

                        1

                    </a>


                    <!-- =================================================
                         LEFT DOTS
                    ================================================== -->

                    <?php if ($current_page > 3): ?>

                        <span class="pagination-dots">

                            ...

                        </span>

                    <?php endif; ?>


                    <!-- =================================================
                         MIDDLE PAGES
                    ================================================== -->

                    <?php

                    $start_page = max(
                        2,
                        $current_page - 1
                    );

                    $end_page = min(
                        $total_pages - 1,
                        $current_page + 1
                    );

                    ?>


                    <?php for (
                        $i = $start_page;
                        $i <= $end_page;
                        $i++
                    ): ?>


                        <a
                            href="?page=<?= $i; ?>"
                            class="pagination-number <?= $i === $current_page ? 'active' : ''; ?>"
                        >

                            <?= $i; ?>

                        </a>


                    <?php endfor; ?>


                    <!-- =================================================
                         RIGHT DOTS
                    ================================================== -->

                    <?php if (
                        $current_page <
                        $total_pages - 2
                    ): ?>

                        <span class="pagination-dots">

                            ...

                        </span>

                    <?php endif; ?>


                    <!-- =================================================
                         LAST PAGE
                    ================================================== -->

                    <?php if ($total_pages > 1): ?>

                        <a
                            href="?page=<?= $total_pages; ?>"
                            class="pagination-number <?= $current_page === $total_pages ? 'active' : ''; ?>"
                        >

                            <?= $total_pages; ?>

                        </a>

                    <?php endif; ?>


                    <!-- =================================================
                         NEXT
                    ================================================== -->

                    <?php if ($current_page < $total_pages): ?>

                        <a
                            href="?page=<?= $current_page + 1; ?>"
                            class="pagination-arrow"
                            aria-label="صفحه بعدی"
                        >

                            ›

                        </a>

                    <?php endif; ?>


                </nav>


            <?php endif; ?>


        </div>

    </section>


</main>


<?php

/* =========================================================
   Footer
========================================================= */

require_once 'lib/footer.php';

?>