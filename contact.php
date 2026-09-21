 <?php


require_once __DIR__ . "/function/function.php";

require_valid_csrf();

/* =========================
   اطلاعات سایت
========================= */

$settings = getSiteSettings();
$menus = getMenus();
$contact_page = getContactPage();
$contact_info = getContactInfo();
$contact_addresses = [];
$contact_phones = [];
$contact_hours = [];

if ($contact_info) {
    while ($item = mysqli_fetch_assoc($contact_info)) {

        if ($item['type'] === 'address') {
            $contact_addresses[] = $item;
        }

        if ($item['type'] === 'phone') {
            $contact_phones[] = $item;
        }

        if ($item['type'] === 'working_hours') {
            $contact_hours[] = $item;
        }
    }
}


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
   ارسال فرم
========================= */

$message_sent = isset($_GET['sent']);
$message_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $message === ''
    ) {

        $message_error = true;

    } else {

        /*
         * موضوع را به ابتدای پیام اضافه می‌کنیم
         * چون جدول contact_messages ستون subject ندارد.
         */

        if ($subject !== '') {
            $message = "موضوع: " . $subject . "\n\n" . $message;
        }

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO contact_messages (name, email, message)
             VALUES (?, ?, ?)"
        );

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $email,
                $message
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                /*
                 * جلوگیری از ارسال مجدد فرم هنگام Refresh
                 */
                header("Location: contact.php?sent=1");
                exit;

            } else {

                $message_error = true;
            }

            mysqli_stmt_close($stmt);

        } else {

            $message_error = true;
        }
    }
}
/* =========================
   Header
========================= */

require_once "lib/header.php";

?>

<link rel="stylesheet" href="css/contact.css">

<main class="contact-page">

    <!-- =========================
         HERO
    ========================= -->

    <section class="contact-hero">

        <div class="contact-hero-shape"></div>

       
            <img
    src="img/<?= htmlspecialchars($contact_page['hero_image'] ?? '101.png') ?>"
    class="contact-hero-camera"
    alt="دوربین"

        >

    </section>


    <!-- =========================
         ABOUT
    ========================= -->

    <section class="contact-about">

        <div class="contact-about-circle"></div>

        <div class="container">

            <div class="contact-about-content">

             <h1>
    <?= htmlspecialchars($contact_page['title'] ?? 'درباره Digi24') ?>
</h1>

<div class="contact-about-text">
    <?php
    echo nl2br(
        htmlspecialchars(
            $contact_page['description'] ?? ''
        )
    );
    ?>
</div>

            </div>

        </div>

    </section>


    <!-- =========================
         CONTACT CONTENT
    ========================= -->

    <section class="contact-content">

        <div class="container">

            <div class="contact-grid">

                <!-- =========================
                     راه های ارتباطی
                ========================= -->

                <div class="contact-info-column">

                    <div class="contact-column">

                        <h2>
                            راه های ارتباطی
                        </h2>

                        <div class="contact-title-line"></div>


                       <div class="contact-info">

    <?php foreach ($contact_addresses as $item): ?>

        <div class="contact-info-item">

            <span class="contact-icon">
                📍
            </span>

            <span>
                <?php if (!empty($item['title'])): ?>
                    <?= htmlspecialchars($item['title']) ?>:
                <?php endif; ?>

                <?= htmlspecialchars($item['content']) ?>
            </span>

        </div>

    <?php endforeach; ?>


    <?php foreach ($contact_phones as $item): ?>

        <div class="contact-info-item">

            <span class="contact-icon">
                ☎
            </span>

            <span>
                <?php if (!empty($item['title'])): ?>
                    <?= htmlspecialchars($item['title']) ?>:
                <?php endif; ?>

                <?= htmlspecialchars($item['content']) ?>
            </span>

        </div>

    <?php endforeach; ?>

</div>
</div>
      </div>
                <!-- =========================
                     ساعت کاری
                ========================= -->

                <div class="contact-hours-column">

                    <div class="contact-column contact-working-hours">

                        <h2>
                            ساعت کاری
                        </h2>

                        <div class="contact-title-line"></div>


                        <?php
$working_hours_image = '1.jpg';

foreach ($contact_hours as $item) {
    if (!empty($item['image'])) {
        $working_hours_image = $item['image'];
        break;
    }
}
?>

<img
    src="img/<?= htmlspecialchars($working_hours_image) ?>"
    class="working-hours-image"
    alt="ساعت کاری"
>


                        <div class="working-hours-text">

    <?php foreach ($contact_hours as $item): ?>

        <p>
            <?= htmlspecialchars($item['title'] ?? '') ?>
            <?= htmlspecialchars($item['content'] ?? '') ?>
        </p>

    <?php endforeach; ?>

</div>

                    </div>

                </div>


                <!-- =========================
                     فرم
                ========================= -->

                <div class="contact-form-column">

                    <div class="contact-column">

                        <h2>
                            فرم ارتباط با ما
                        </h2>

                        <div class="contact-title-line"></div>


                        <?php if ($message_sent): ?>

                            <div class="contact-success">
                                پیام شما با موفقیت ارسال شد.
                            </div>

                        <?php endif; ?>


                        <?php if ($message_error): ?>

                            <div class="contact-error">
                                لطفاً اطلاعات فرم را بررسی کنید.
                            </div>

                        <?php endif; ?>


                        <form
                            action="contact.php"
                            method="post"
                            class="contact-form"
                        >
<?php echo csrf_field(); ?>


                            <div class="contact-form-row">
<input
                                    type="text"
                                    name="name"
                                    placeholder="نام"
                                    required
                                >

                                <input
                                    type="email"
                                    name="email"
                                    placeholder="آدرس ایمیل"
                                    required
                                >

                            </div>


                            <input
                                type="text"
                                name="subject"
                                placeholder="موضوع"
                            >


                            <textarea
                                name="message"
                                placeholder="متن پیام"
                                required
                            ></textarea>


                            <button
                                type="submit"
                                name="send_message"
                            >
                                ارسال
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         DECORATION
    ========================= -->

    <div class="contact-dots"></div>

</main>


<?php

require_once "lib/footer.php";

?>