<?php

require_once "function/config.php";

require_valid_csrf();
require_once __DIR__ . "/function/function.php";


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
   دریافت مقاله
========================================================= */

$post = false;


/* =========================================================
   دریافت با slug
========================================================= */

if (
    isset($_GET['slug']) &&
    trim($_GET['slug']) !== ''
) {

    $slug = trim($_GET['slug']);

    $post = getBlogPostBySlug($slug);

}


/* =========================================================
   دریافت با id
========================================================= */

if (
    !$post &&
    isset($_GET['id'])
) {

    $id = (int) $_GET['id'];

    if ($id > 0) {

        $post = getBlogPost($id);

    }

}


/* =========================================================
   اگر مقاله پیدا نشد
========================================================= */

if (!$post) {

    http_response_code(404);

    $page_title = "مقاله پیدا نشد";

    require_once "lib/header.php";

    ?>

    <link
        rel="stylesheet"
        href="css/blog-single.css"
    >

    <main class="blog-single-page">

        <section class="blog-single-not-found">

            <div class="blog-single-not-found-box">

                <i class="fa-solid fa-file-circle-xmark"></i>

                <h1>
                    مقاله پیدا نشد
                </h1>

                <p>
                    مقاله مورد نظر وجود ندارد یا حذف شده است.
                </p>

                <a href="blog.php">
                    بازگشت به مجله
                </a>

            </div>

        </section>

    </main>

    <?php

    require_once "lib/footer.php";

    exit;
}


/* =========================================================
   اطلاعات پایه مقاله
========================================================= */

$post_id = (int) ($post['id'] ?? 0);

$post_title = trim(
    $post['title'] ?? ''
);


/* =========================================================
   متغیرهای دیدگاه
========================================================= */

$comment_success = false;

$comment_error = '';

$comment_name = '';

$comment_email = '';

$comment_text = '';


/* =========================================================
   ثبت دیدگاه
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['submit_comment'])
) {

    /* -----------------------------------------
       دریافت اطلاعات فرم
    ----------------------------------------- */

    $comment_name = trim(
        $_POST['name'] ?? ''
    );

    $comment_email = trim(
        $_POST['email'] ?? ''
    );

    $comment_text = trim(
        $_POST['comment'] ?? ''
    );


    /* -----------------------------------------
       بررسی شناسه مقاله
    ----------------------------------------- */

    if ($post_id <= 0) {

        $comment_error =
            'شناسه مقاله معتبر نیست.';

    }


    /* -----------------------------------------
       بررسی خالی نبودن فیلدها
    ----------------------------------------- */

    elseif (
        $comment_name === '' ||
        $comment_email === '' ||
        $comment_text === ''
    ) {

        $comment_error =
            'لطفاً همه فیلدها را کامل کنید.';

    }


    /* -----------------------------------------
       بررسی طول نام
    ----------------------------------------- */

    elseif (
        mb_strlen($comment_name) < 2
    ) {

        $comment_error =
            'لطفاً نام خود را به‌درستی وارد کنید.';

    }


    /* -----------------------------------------
       بررسی ایمیل
    ----------------------------------------- */

    elseif (
        !filter_var(
            $comment_email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $comment_error =
            'لطفاً یک ایمیل معتبر وارد کنید.';

    }


    /* -----------------------------------------
       بررسی متن دیدگاه
    ----------------------------------------- */

    elseif (
        mb_strlen($comment_text) < 3
    ) {

        $comment_error =
            'متن دیدگاه خیلی کوتاه است.';

    }


    /* -----------------------------------------
       ذخیره دیدگاه
    ----------------------------------------- */

    else {

        $result = addBlogComment(
            $post_id,
            $comment_name,
            $comment_email,
            $comment_text
        );


        if ($result) {

            $comment_success = true;

            $comment_name = '';

            $comment_email = '';

            $comment_text = '';

        } else {

            $comment_error =
                'ثبت دیدگاه با خطا مواجه شد. لطفاً دوباره تلاش کنید.';

        }

    }

}


/* =========================================================
   اطلاعات مقاله
========================================================= */

$post_description = trim(
    $post['description'] ?? ''
);

$post_content = $post['content'] ?? '';

$post_image = trim(
    $post['image'] ?? ''
);

$post_video = trim(
    $post['video_url'] ?? ''
);

$post_author = trim(
    $post['author_name'] ?? ''
);

$post_views = isset($post['views'])
    ? (int) $post['views']
    : 0;

$post_date = $post['published_at'] ?? '';

$post_slug = trim(
    $post['slug'] ?? ''
);


/* =========================================================
   دریافت بلوک‌های مقاله
========================================================= */

$blog_blocks = getBlogPostBlocks($post_id);


/* =========================================================
   افزایش بازدید
========================================================= */

if ($post_id > 0) {

    if (incrementBlogPostViews($post_id)) {

        $post_views++;

    }

}


/* =========================================================
   تصویر اصلی
========================================================= */

$post_image_url = '';

if ($post_image !== '') {

    $normalized_image = str_replace(
        '\\',
        '/',
        $post_image
    );


    if (
        strpos(
            $normalized_image,
            '/'
        ) === false
    ) {

        $post_image_url =
            'img/' .
            $normalized_image;

    } else {

        $post_image_url =
            ltrim(
                $normalized_image,
                '/'
            );

    }

}


/* =========================================================
   تاریخ مقاله
========================================================= */

$formatted_date = '';

if ($post_date !== '') {

    $timestamp = strtotime($post_date);

    if ($timestamp !== false) {

        $formatted_date = date(
            'Y/m/d',
            $timestamp
        );

    }

}


/* =========================================================
   مطالب مرتبط
========================================================= */

$related_posts = getRelatedBlogPosts(
    $post_id,
    3
);


/* =========================================================
   عنوان صفحه
========================================================= */

$page_title =
    $post_title .
    " | Digi24";


/* =========================================================
   Header
========================================================= */

require_once "lib/header.php";

?>

<link
    rel="stylesheet"
    href="css/blog-single.css"
>


<style>

/* =========================================================
   پیام موفقیت دیدگاه
========================================================= */

.blog-comment-success {

    width: 100%;

    box-sizing: border-box;

    margin: 0 0 25px 0;

    padding: 15px 20px;

    border-radius: 8px;

    background: #e9f8ef;

    border: 1px solid #a8dfba;

    color: #20753d;

    font-size: 14px;

    line-height: 1.9;

    text-align: right;

}


/* =========================================================
   پیام خطای دیدگاه
========================================================= */

.blog-comment-error {

    width: 100%;

    box-sizing: border-box;

    margin: 0 0 25px 0;

    padding: 15px 20px;

    border-radius: 8px;

    background: #fff0f0;

    border: 1px solid #efb0b0;

    color: #a52a2a;

    font-size: 14px;

    line-height: 1.9;

    text-align: right;

}

</style>


<!-- =====================================================
     نوار صورتی
===================================================== -->

<div class="blog-single-pink-line"></div>


<!-- =====================================================
     محتوای اصلی مقاله
===================================================== -->

<main class="blog-single-page">

    <div class="blog-single-container">


        <!-- =================================================
             عنوان مقاله
        ================================================== -->

        <header class="blog-single-header">

            <h1>

                <?= htmlspecialchars(
                    $post_title,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </h1>


            <div class="blog-single-meta">


                <?php if ($post_author !== ''): ?>

                    <span>

                        <i class="fa-regular fa-user"></i>

                        <?= htmlspecialchars(
                            $post_author,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </span>

                <?php endif; ?>


                <?php if ($formatted_date !== ''): ?>

                    <span>

                        <i class="fa-regular fa-calendar"></i>

                        <?= htmlspecialchars(
                            $formatted_date,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </span>

                <?php endif; ?>


                <span>

                    <i class="fa-regular fa-eye"></i>

                    <?= number_format($post_views); ?>

                    بازدید

                </span>


            </div>

        </header>


        <!-- =================================================
             مقاله
        ================================================== -->

        <article class="blog-single-article">


            <!-- =================================================
                 تصویر اصلی مقاله
            ================================================== -->

            <?php if ($post_image_url !== ''): ?>

                <div class="blog-single-main-image">

                    <img
                        src="<?= htmlspecialchars(
                            $post_image_url,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        alt="<?= htmlspecialchars(
                            $post_title,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>

            <?php endif; ?>


            <!-- =================================================
                 توضیحات کوتاه مقاله
            ================================================== -->

            <?php if ($post_description !== ''): ?>

                <div class="blog-single-description">

                    <?= nl2br(
                        htmlspecialchars(
                            $post_description,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ); ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 بلوک‌های محتوای مقاله
            ================================================== -->

            <?php

            $has_blocks = (
                $blog_blocks &&
                mysqli_num_rows($blog_blocks) > 0
            );

            ?>


            <?php if ($has_blocks): ?>


                <div class="blog-single-blocks">

<?php
$section2_text = '';
?>
                    <?php while (
                        $block =
                        mysqli_fetch_assoc($blog_blocks)
                    ): ?>


                        <?php

                        $block_type =
                            $block['type'] ?? '';

                        $block_content =
                            $block['content'] ?? '';

                        $block_image =
                            trim(
                                $block['image'] ?? ''
                            );

                        $block_video =
                            trim(
                                $block['video_url'] ?? ''
                            );
                            $block_sort_order =
    (int) ($block['sort_order'] ?? 0);

                        ?>


                        <!-- =========================================
                             بلوک عنوان
                        ========================================== -->

                        <?php if (
                            $block_type === 'heading'
                        ): ?>

                            <?php if (
                                trim($block_content) !== ''
                            ): ?>

                                <div
                                    class="blog-single-block-heading"
                                >

                                    <h2>

                                        <?= htmlspecialchars(
                                            $block_content,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </h2>

                                </div>

                            <?php endif; ?>


                        <!-- =========================================
                             بلوک متن
                        ========================================== -->

                       <?php elseif (
    $block_type === 'text'
): ?>

    <?php
    /*
     * متن بخش دوم فعلاً نگه داشته می‌شود
     * تا بعد از تصویر دوم نمایش داده شود.
     */
    if ($block_sort_order === 4) {

        $section2_text = $block_content;

    } else {

        if (trim($block_content) !== ''):
    ?>

            <div
                class="blog-single-content"
            >

                <?= nl2br(
                    htmlspecialchars(
                        $block_content,
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ); ?>

            </div>

        <?php
        endif;

    }
    ?>

                        <!-- =========================================
                             بلوک تصویر
                        ========================================== -->

                      <?php elseif (
    $block_type === 'image' &&
    $block_sort_order !== 2
): ?>


                            <?php

                            $block_image_url = '';

                            if (
                                $block_image !== ''
                            ) {

                                $normalized_block_image =
                                    str_replace(
                                        '\\',
                                        '/',
                                        $block_image
                                    );


                                if (
                                    strpos(
                                        $normalized_block_image,
                                        '/'
                                    ) === false
                                ) {

                                    $block_image_url =
                                        'img/' .
                                        $normalized_block_image;

                                } else {

                                    $block_image_url =
                                        ltrim(
                                            $normalized_block_image,
                                            '/'
                                        );

                                }

                            }

                            ?>


                            <?php if (
                                $block_image_url !== ''
                            ): ?>

                                <div
                                    class="blog-single-block-image"
                                >

                                    <img
                                        src="<?= htmlspecialchars(
                                            $block_image_url,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        alt="<?= htmlspecialchars(
                                            trim($block_content) !== ''
                                                ? $block_content
                                                : $post_title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >

                                </div>
                                <?php if (
    $block_sort_order === 5 &&
    trim($section2_text) !== ''
): ?>

    <div class="blog-single-content">

        <?= nl2br(
            htmlspecialchars(
                $section2_text,
                ENT_QUOTES,
                'UTF-8'
            )
        ); ?>

    </div>

<?php endif; ?>

                            <?php endif; ?>


                        <!-- =========================================
                             بلوک ویدئو
                        ========================================== -->

                        <?php elseif (
                            $block_type === 'video'
                        ): ?>


                            <?php if (
                                $block_video !== ''
                            ): ?>

                                <div
                                    class="blog-single-video"
                                >

                                    <?php

                                    $video_url =
                                        $block_video;

                                    $youtube_id = '';


                                    if (
                                        strpos(
                                            $video_url,
                                            'youtube.com'
                                        ) !== false ||
                                        strpos(
                                            $video_url,
                                            'youtu.be'
                                        ) !== false
                                    ) {

                                        if (
                                            preg_match(
                                                '~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([^&?/]+)~',
                                                $video_url,
                                                $matches
                                            )
                                        ) {

                                            $youtube_id =
                                                $matches[1];

                                        }

                                    }

                                    ?>


                                    <?php if (
                                        $youtube_id !== ''
                                    ): ?>

                                        <div
                                            class="blog-single-video-wrapper"
                                        >

                                            <iframe
                                                src="https://www.youtube.com/embed/<?= htmlspecialchars(
                                                    $youtube_id,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                                title="<?= htmlspecialchars(
                                                    $post_title,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen
                                            ></iframe>

                                        </div>

                                    <?php else: ?>

                                        <a
                                            href="<?= htmlspecialchars(
                                                $video_url,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="blog-single-video-link"
                                        >

                                            <i
                                                class="fa-solid fa-play"
                                            ></i>

                                            مشاهده ویدئو

                                        </a>

                                    <?php endif; ?>


                                </div>

                            <?php endif; ?>


                        <?php endif; ?>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <!-- =================================================
                     پشتیبانی از مقالات قدیمی
                ================================================== -->


                <?php if (
                    $post_content !== ''
                ): ?>

                    <div
                        class="blog-single-content"
                    >

                        <?= $post_content; ?>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     ویدئوی قدیمی مقاله
                ================================================== -->

                <?php if (
                    $post_video !== ''
                ): ?>

                    <div
                        class="blog-single-video"
                    >

                        <?php

                        $video_url =
                            trim(
                                $post_video
                            );

                        $youtube_id = '';


                        if (
                            strpos(
                                $video_url,
                                'youtube.com'
                            ) !== false ||
                            strpos(
                                $video_url,
                                'youtu.be'
                            ) !== false
                        ) {

                            if (
                                preg_match(
                                    '~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([^&?/]+)~',
                                    $video_url,
                                    $matches
                                )
                            ) {

                                $youtube_id =
                                    $matches[1];

                            }

                        }

                        ?>


                        <?php if (
                            $youtube_id !== ''
                        ): ?>

                            <div
                                class="blog-single-video-wrapper"
                            >

                                <iframe
                                    src="https://www.youtube.com/embed/<?= htmlspecialchars(
                                        $youtube_id,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    title="<?= htmlspecialchars(
                                        $post_title,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>

                            </div>

                        <?php else: ?>

                            <a
                                href="<?= htmlspecialchars(
                                    $video_url,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="blog-single-video-link"
                            >

                                <i
                                    class="fa-solid fa-play"
                                ></i>

                                مشاهده ویدئو

                            </a>

                        <?php endif; ?>


                    </div>

                <?php endif; ?>


                <!-- =================================================
                     اگر هیچ محتوایی وجود نداشت
                ================================================== -->

                <?php if (
                    $post_content === '' &&
                    $post_video === ''
                ): ?>

                    <div
                        class="blog-single-content-empty"
                    >

                        محتوای این مقاله هنوز ثبت نشده است.

                    </div>

                <?php endif; ?>


            <?php endif; ?>


        </article>


        <!-- =================================================
             اشتراک گذاری
        ================================================== -->

        <?php

        $current_url =
            'http://' .
            $_SERVER['HTTP_HOST'] .
            $_SERVER['REQUEST_URI'];

        $current_url =
            urlencode(
                $current_url
            );

        $share_title =
            urlencode(
                $post_title
            );

        ?>


        <div class="blog-single-share">


            <span class="blog-single-share-title">

                اشتراک گذاری:

            </span>


            <div class="blog-single-share-icons">


                <!-- X -->

                <a
                    href="https://twitter.com/intent/tweet?url=<?= $current_url; ?>&text=<?= $share_title; ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="اشتراک گذاری در X"
                >

                    <i
                        class="fa-brands fa-x-twitter"
                    ></i>

                </a>


                <!-- LinkedIn -->

                <a
                    href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $current_url; ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="اشتراک گذاری در LinkedIn"
                >

                    <i
                        class="fa-brands fa-linkedin-in"
                    ></i>

                </a>


                <!-- Facebook -->

                <a
                    href="https://www.facebook.com/sharer/sharer.php?u=<?= $current_url; ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="اشتراک گذاری در Facebook"
                >

                    <i
                        class="fa-brands fa-facebook-f"
                    ></i>

                </a>


                <!-- Pinterest -->

                <a
                    href="https://pinterest.com/pin/create/button/?url=<?= $current_url; ?>&description=<?= $share_title; ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="اشتراک گذاری در Pinterest"
                >

                    <i
                        class="fa-brands fa-pinterest-p"
                    ></i>

                </a>


            </div>

        </div>


        <!-- =================================================
             بخش دیدگاه‌ها
        ================================================== -->

        <section class="blog-single-comments">


            <div class="blog-section-title">

                <h2>

                    دیدگاه شما

                </h2>

            </div>


            <!-- =================================================
                 پیام موفقیت
            ================================================== -->

            <?php if (
                $comment_success
            ): ?>

                <div
                    class="blog-comment-success"
                >

                    دیدگاه شما با موفقیت ثبت شد و پس از تأیید نمایش داده می‌شود.

                </div>

            <?php endif; ?>


            <!-- =================================================
                 پیام خطا
            ================================================== -->

            <?php if (
                $comment_error !== ''
            ): ?>

                <div
                    class="blog-comment-error"
                >

                    <?= htmlspecialchars(
                        $comment_error,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 فرم دیدگاه
            ================================================== -->

            <form
                class="blog-comment-form"
                action=""
                method="post"
            >
<?php echo csrf_field(); ?>


                <div
                    class="blog-comment-form-row"
                >


                    <!-- نام -->

                    <div
                        class="blog-comment-field"
                    >

                        <input
                            type="text"
                            name="name"
                            placeholder="نام شما"
                            value="<?= htmlspecialchars(
                                $comment_name,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            required
                        >

                    </div>


                    <!-- ایمیل -->

                    <div
                        class="blog-comment-field"
                    >

                        <input
                            type="email"
                            name="email"
                            placeholder="ایمیل شما"
                            value="<?= htmlspecialchars(
                                $comment_email,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            required
                        >

                    </div>


                </div>


                <!-- متن دیدگاه -->

                <textarea
                    name="comment"
                    rows="6"
                    placeholder="دیدگاه خود را بنویسید..."
                    required
                ><?= htmlspecialchars(
                    $comment_text,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?></textarea>


                <!-- دکمه -->

                <button
                    type="submit"
                    name="submit_comment"
                >

                    ارسال دیدگاه

                </button>


            </form>


            <!-- =================================================
                 لیست دیدگاه‌ها
            ================================================== -->

            <?php

            $blog_comments =
                getBlogComments(
                    $post_id
                );

            ?>


            <?php if (
                $blog_comments &&
                mysqli_num_rows($blog_comments) > 0
            ): ?>

                <div
                    class="blog-comments-list"
                >

                    <h3
                        class="blog-comments-list-title"
                    >

                        دیدگاه‌های کاربران

                    </h3>


                    <?php while (
                        $comment =
                        mysqli_fetch_assoc(
                            $blog_comments
                        )
                    ): ?>


                        <div
                            class="blog-comment-item"
                        >


                            <div
                                class="blog-comment-item-header"
                            >

                                <strong>

                                    <?= htmlspecialchars(
                                        $comment['name'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </strong>


                                <?php if (
                                    !empty(
                                        $comment['created_at']
                                    )
                                ): ?>

                                    <span>

                                        <?= htmlspecialchars(
                                            date(
                                                'Y/m/d',
                                                strtotime(
                                                    $comment['created_at']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </span>

                                <?php endif; ?>


                            </div>


                            <div
                                class="blog-comment-item-text"
                            >

                                <?= nl2br(
                                    htmlspecialchars(
                                        $comment['comment'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ); ?>

                            </div>


                        </div>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <div
                    class="blog-comments-empty"
                >

                    هنوز دیدگاهی برای این مقاله ثبت نشده است.

                </div>


            <?php endif; ?>


        </section>


        <!-- =================================================
             مطالب مرتبط
        ================================================== -->

        <?php if (
            $related_posts &&
            mysqli_num_rows($related_posts) > 0
        ): ?>


            <section
                class="blog-related-section"
            >


                <div
                    class="blog-section-title"
                >

                    <h2>

                        دیگر مطالب مرتبط

                    </h2>

                </div>


                <div
                    class="blog-related-grid"
                >


                    <?php while (
                        $related =
                        mysqli_fetch_assoc(
                            $related_posts
                        )
                    ): ?>


                        <?php

                        $related_id =
                            (int) (
                                $related['id'] ?? 0
                            );

                        $related_title =
                            $related['title'] ?? '';

                        $related_description =
                            $related['description'] ?? '';

                        $related_image =
                            $related['image'] ?? '';

                        $related_slug =
                            trim(
                                $related['slug'] ?? ''
                            );


                        /* =====================================
                           تصویر مرتبط
                        ====================================== */

                        $related_image_url = '';

                        if (
                            $related_image !== ''
                        ) {

                            $related_image =
                                str_replace(
                                    '\\',
                                    '/',
                                    $related_image
                                );


                            if (
                                strpos(
                                    $related_image,
                                    '/'
                                ) === false
                            ) {

                                $related_image_url =
                                    'img/' .
                                    $related_image;

                            } else {

                                $related_image_url =
                                    ltrim(
                                        $related_image,
                                        '/'
                                    );

                            }

                        }


                        /* =====================================
                           لینک مرتبط
                        ====================================== */

                        if (
                            $related_slug !== ''
                        ) {

                            $related_url =
                                'blog-single.php?slug=' .
                                urlencode(
                                    $related_slug
                                );

                        } else {

                            $related_url =
                                'blog-single.php?id=' .
                                $related_id;

                        }

                        ?>


                        <article
                            class="blog-related-card"
                        >


                            <!-- تصویر -->

                            <a
                                href="<?= htmlspecialchars(
                                    $related_url,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                class="blog-related-image"
                            >


                                <?php if (
                                    $related_image_url !== ''
                                ): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                            $related_image_url,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        alt="<?= htmlspecialchars(
                                            $related_title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >

                                <?php else: ?>

                                    <div
                                        class="blog-related-no-image"
                                    >

                                        <i
                                            class="fa-regular fa-image"
                                        ></i>

                                    </div>

                                <?php endif; ?>


                            </a>


                            <!-- محتوا -->

                            <div
                                class="blog-related-content"
                            >


                                <h3>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $related_url,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $related_title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </a>

                                </h3>


                                <?php if (
                                    !empty(
                                        $related_description
                                    )
                                ): ?>

                                    <p>

                                        <?= htmlspecialchars(
                                            mb_substr(
                                                $related_description,
                                                0,
                                                110
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                        ...

                                    </p>

                                <?php endif; ?>


                                <a
                                    href="<?= htmlspecialchars(
                                        $related_url,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="blog-related-more"
                                >

                                    ادامه مطلب

                                    <i
                                        class="fa-solid fa-angle-left"
                                    ></i>

                                </a>


                            </div>


                        </article>


                    <?php endwhile; ?>


                </div>


            </section>


        <?php endif; ?>


    </div>

</main>


<?php

/* =========================================================
   Footer
========================================================= */

require_once "lib/footer.php";

?>