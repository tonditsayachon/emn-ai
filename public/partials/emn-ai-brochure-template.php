<?php

/**
 * Template for the Product Brochure PDF (New Design - July 2025 v2)
 *
 * @var array $products_data Contains an array of product data objects.
 */
$cover_style = isset($cover_style) ? absint($cover_style) : 1;

// --- [เพิ่ม] หา path ไปยังโฟลเดอร์ partials/covers ---
$covers_path = plugin_dir_path(__FILE__) . 'covers/';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        /* --- Page Layout Definitions --- */
        @page cover-page {
            footer: none;
            /* ไม่มี footer ที่หน้าปก */
            margin: 0;
            /* ไม่มีขอบเลยเพื่อให้เต็มจอ */
        }

        @page content-page {
            margin-top: 50px;
            margin-left: 50px;
            margin-right: 30px;
            margin-bottom: 50px;
            footer: html_myFooter;


        }

        @page back-cover-page {
            footer: none;
            /* ไม่มี footer ที่หน้าหลัง */
            margin: 0;
            /* ไม่มีขอบเลยเพื่อให้เต็มจอ */
        }

        /* --- Styling for Cover Content --- */
        .front-cover {
            width: 100%;
            height: 100%;
            background-color: #f0f0f0;

            /* สีพื้นหลัง (ตัวอย่าง) */
            color: white;
            text-align: center;
            padding-top: 200px;
        }

        .front-cover .logo {
            max-width: 250px;
            margin-bottom: 30px;
        }

        .front-cover h1 {
            font-size: 48px;
            font-weight: bold;
            margin: 0;
            color: #000;

        }

        .front-cover p {
            font-size: 24px;
            font-weight: 300;
            color: #000;
        }


        /* --- Styling for Back Cover Content --- */
        .back-cover {
            width: 100%;
            height: 100%;
            background-color: #f0f0f0;
            /* สีพื้นหลังหน้าหลัง */
            text-align: center;
            padding-top: 250px;
        }

        .back-cover .logo {
            max-width: 180px;
            margin-bottom: 20px;
        }

        .back-cover p {
            color: #333;
            font-size: 16px;
        }

        .back-cover .contact-info {
            margin-top: 40px;
        }

        body {
            font-family: 'Inter', sans-serif;
            padding: 26px;
            line-height: 1.4 !important;

        }

        .page-break {
            page-break-after: always;
        }

        .brochure-page {
            width: 100%;
            height: 100%;
        }

        /* --- Header --- */
        .page-header {

            overflow: auto;
            padding-bottom: 8px;
            margin-bottom: 8px;

        }

        .page-header>* {
            display: block;
            width: 100%;

        }

        .header-table {
            width: 100%;

        }

        .header-table .left-col {
            width: 80%;

        }

        .header-table .right-col {
            width: 20%;
            text-align: right;
        }


        h1.product-name {

            font-size: 28px;
            margin: 0;
            font-weight: bold;
            color: #333;
            float: left;


        }

        .hsc-logo {

            width: 120px;
            height: auto;


        }



        .vendor-name {
            border-bottom: #000 1px solid;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 20px !important;

        }

        .vendor-logo {
            margin-bottom: 16px;

        }


        .vendor-contact {
            color: #000;
            font-weight: 300;
        }

        .feature-image img {
            width: 100%;
            height: auto;

        }

        .description {
            color: #000;
            margin-bottom: 16px;



        }

        p.title {
            font-weight: 400;
            font-size: 20px !important;
            margin-bottom: 8px;
        }

        .product-description {
            font-size: 14px;
            font-weight: 300;
            margin: 8px 0 16px 0;
        }

        .gallery-section {
            margin-top: 20px;
            padding: 0;
        }

        .gallery-grid {
            overflow: auto;
            /* To contain floated elements */
            width: 100%;


        }

        .gallery-image {

            width: 31.33%;
            /* (100% / 3 columns) - margins */
            margin: 1%;
            box-sizing: border-box;

            text-align: center;




        }

        .gallery-image img {
            object-fit: cover;
        }

        div.tier-prices {
            border-radius: 10px;

        }

        .tier-price-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;

            overflow: hidden;
            font-family: Arial, sans-serif;

        }

        .tier-price-table thead th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #666;

        }

        .tier-price-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #ddd;

        }

        .tier-price-table tbody tr:last-child td {
            border-bottom: none;
        }

        .tier-price-table th,
        .tier-price-table td {
            border-right: none;
        }

        .tier-price-table {
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
        }

        #footer {
            font-size: 10px;
            color: #666;
            width: 100%;
            border-top: 1px solid #ccc;
            text-align: right;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <?php
    // --- [แก้ไข] ส่วนของ Front Cover ---
    switch ($cover_style) {
        case 2: // Corporate Style
            include $covers_path . 'corporate-front.php';
            break;
        case 3: // Minimalist (Modern) Style
            include $covers_path . 'modern-front.php';
            break;
        case 1: // Default Style
        default:
            // โค้ดหน้าปกเดิมของคุณ (Style 1)
    ?>
            <div class="front-cover" style="page: cover-page;">
                <img class="logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 2))); ?>" alt="Halplus Directory Logo">
                <h1>Product Catalog</h1>
                <p>Generated on <?php echo date_i18n('j F Y'); ?></p>
            </div>
    <?php
            break;
    }
    ?>


    <div class="content-pages" style="page: content-page;">
        <?php if (!empty($products_data)): ?>
            <?php foreach ($products_data as $index => $product): ?>
                <?php
                // เตรียมข้อมูล Vendor และค่า Default
                $vendor_info = $product->vendor_info ?? null;
                $logo_url    = !empty($vendor_info['logo_url']) ? $vendor_info['logo_url'] : $default_logo_url;
                $store_name  = !empty($vendor_info['store_name']) ? $vendor_info['store_name'] : $default_company_name;
                $address     = !empty($vendor_info['address']) ? $vendor_info['address'] : $default_address;
                $tel         = !empty($vendor_info['phone']) ? $vendor_info['phone'] : $default_tel;
                $email       = !empty($vendor_info['email']) ? $vendor_info['email'] : $default_email;
                ?>

                <div class="brochure-page">

                    <div class="page-header">
                        <table class="header-table">
                            <tr>
                                <td class="left-col">
                                    <h1 class="product-name" style="text-transform: uppercase;"><?php echo esc_html($product->name); ?></h1>
                                </td>
                                <td class="right-col">
                                    <img class="hsc-logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 2))); ?>" alt="Halplus Directory Logo">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table class="main-content-table" width="100%" cellspacing="4" cellpadding="4">
                        <tr>
                            <td class="left-column" style="width:48%;border:2px solid #ddd;padding: 5px;">
                                <div class="feature-image">
                                    <?php if (!empty($product->featured_image)): ?>
                                        <?php
                                        // Get image dimensions
                                        $image_url = esc_url($product->featured_image);
                                        $image_size = @getimagesize($product->featured_image);
                                        $is_landscape = false;
                                        if ($image_size && $image_size[0] > $image_size[1]) {
                                            $is_landscape = true;
                                        }
                                        ?>
                                        <div style="background: <?php echo $is_landscape ? '#f0f0f0' : 'none'; ?>;">
                                            <img
                                                style="max-width:450px;"
                                                src="<?php echo $image_url; ?>"
                                                alt="<?php echo esc_attr($product->name); ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="right-column" width="48%" style="vertical-align:top;">
                                <table width="100%">
                                    <tr>
                                        <td>
                                            <div class="vendor-logo">
                                                <?php if (!empty($logo_url)): ?>
                                                    <img src="<?php echo esc_url($logo_url); ?>" alt="Vendor Logo" style="max-width: 120px;">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="16"></td>
                                    </tr> <!-- spacing -->

                                    <tr>
                                        <td>
                                            <div class="vendor-name"><?php echo esc_html($store_name); ?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="8"></td>
                                    </tr> <!-- spacing -->

                                    <tr>
                                        <td>
                                            <div class="vendor-contact">
                                                <p><?php echo wp_kses_post('<strong>Address: </strong>' . nl2br(esc_html($address))); ?></p>

                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="8"></td>
                                    </tr> <!-- spacing -->
                                    <tr>
                                        <td>

                                            <strong>Tel:</strong> <a href="tel:<?php echo $tel ?>"><?php echo esc_html($tel); ?></a>


                                        </td>

                                    </tr>
                                    <tr>
                                        <td>


                                            <strong>Email:</strong> <a href="mailto:<?php echo $email ?>"><?php echo esc_html($email); ?></a>

                                        </td>

                                    </tr>
                                    <tr>
                                        <td height="10"></td>
                                    </tr> <!-- spacing -->

                                    <tr>
                                        <td>
                                            <!-- tier price -->
                                            <div class="tier-prices">
                                                <?php if (!empty($product->tiers_prices) && is_array($product->tiers_prices)): ?>

                                                    <table class="tier-price-table" style="border:1px solid #ddd;">
                                                        <thead>
                                                            <tr>
                                                                <th>Quantity</th>
                                                                <th style="text-align: right;">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($product->tiers_prices as $tier): ?>
                                                                <tr>
                                                                    <td><?php echo isset($tier['quantity']) && $tier['quantity'] !== null ? number_format((int)$tier['quantity']) : 'N/A'; ?></td>
                                                                    <td style="text-align: right;"><?php echo isset($tier['price']) && $tier['price'] !== null ? wc_price($tier['price']) : 'N/A'; ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>

                                                <?php else: // ถ้าไม่มี Tier Price ให้แสดงราคาปกติ/ลดราคาแทน 
                                                ?>

                                                    <table class="regular-price-table" width="100%" style="border:1px solid #ddd; border-collapse: separate; border-spacing: 0;">
                                                        <tbody>
                                                            <?php if (!empty($product->regular_price)): ?>
                                                                <tr style="background-color: #f5f5f5;">
                                                                    <th style="text-align: left; font-weight: bold; padding: 12px; border-bottom: 1px solid #ddd;">
                                                                        <?php echo !empty($product->sale_price) ? 'Sale Price' : 'Regular Price'; ?>
                                                                    </th>
                                                                    <td style="text-align: right; padding: 12px; border-bottom: 1px solid #ddd; <?php echo !empty($product->sale_price) ? 'text-decoration: line-through; color: #777;' : ''; ?>">
                                                                        <?php echo wc_price($product->regular_price); ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if (!empty($product->sale_price)): ?>
                                                                <tr>
                                                                    <th style="text-align: left; font-weight: bold; padding: 12px;">Sale price</th>
                                                                    <td style="text-align: right; font-weight: bold; color: #d63638; padding: 12px;">
                                                                        <?php echo wc_price($product->sale_price); ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>

                                                <?php endif; // สิ้นสุดการเช็คเงื่อนไขราคา 
                                                ?>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                        </tr>
                    </table>

                    <div class="description">
                        <p class="title">Description</p>
                        <?php if (!empty($product->description)) : ?>
                            <div class="product-description">
                                <?php echo wp_strip_all_tags($product->description); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($product->product_gallery) && is_array($product->product_gallery)): ?>
                        <div class="gallery-section">
                            <table class="gallery-grid" style="border-spacing: 8px 8px;">
                                <tr>
                                    <?php foreach ($product->product_gallery as $i => $image_url): ?>
                                        <td class="gallery-image" style="padding-left:5px;padding-right:5px;">
                                            <img
                                                style="max-height: 220px;"
                                                src="<?php echo esc_url($image_url); ?>"
                                                alt="Gallery Image <?php echo $i + 1; ?>">
                                        </td>
                                        <?php if (($i + 1) % 3 === 0 && ($i + 1) < count($product->product_gallery)): ?>
                                </tr>
                                <tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                                </tr>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if ($index < count($products_data) - 1): ?>

                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>


    <?php
    // --- [แก้ไข] ส่วนของ Back Cover ---
    switch ($cover_style) {
        case 2: // Corporate Style
            include $covers_path . 'corporate-back.php';
            break;
        case 3: // Minimalist (Modern) Style
            include $covers_path . 'modern-back.php';
            break;
        case 1: // Default Style
        default:
            // โค้ดปกหลังเดิมของคุณ (Style 1)
    ?>
            <div class="back-cover" style="page: back-cover-page;">
                <img class="logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 2))); ?>" alt="Halplus Directory Logo">
                <div class="contact-info">
                    <p><strong>Halal Plus Directory</strong></p>
                    <p>A platform for halal products and services.</p>
                    <p>www.halalplus.com</p>
                </div>
                <p style="margin-top: 50px;">Thank you for your interest in our products.</p>
            </div>
    <?php
            break;
    }
    ?>


    <htmlpagefooter name="myFooter">
        <div id="footer">
            Page {PAGENO} of {nbpg}
        </div>
    </htmlpagefooter>

</body>

</html>