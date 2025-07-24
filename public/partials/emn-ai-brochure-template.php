<?php

/**
 * Template for the Product Brochure PDF (New Design - July 2025 v2)
 *
 * @var array $products_data Contains an array of product data objects.
 */



// =================================================================
// ส่วนที่ 1: ดึงข้อมูล Vendor (ผู้ขาย)
// =================================================================

// Loop ผ่าน products_data เพื่อเตรียมข้อมูล vendor สำหรับแต่ละ product
foreach ($products_data as $i => $product) {
    $vendor_id = get_post_field('post_author', $product->id);
    $vendor_data = get_userdata($vendor_id);
    //debug $vendor_data

    // หากไม่พบข้อมูล vendor หรือไม่มีชื่อที่แสดงได้ ให้กำหนดค่าเป็น 'N/A'
    $vendor_name = (!empty($vendor_data) && !empty($vendor_data->display_name)) ? $vendor_data->display_name : 'N/A';

    // --- START: ส่วนที่แก้ไข ---
    // ดึงข้อมูลที่อยู่และเบอร์โทรจาก meta key ของ MarketKing
    $address1 = get_user_meta($vendor_id, 'billing_address_1', true);
    $address2 = get_user_meta($vendor_id, 'billing_address_2', true);
    $city = get_user_meta($vendor_id, 'billing_city', true);
    $state = get_user_meta($vendor_id, 'billing_state', true);
    $postcode = get_user_meta($vendor_id, 'billing_postcode', true);
    $country = get_user_meta($vendor_id, 'billing_country', true);

    // รวมข้อมูลที่อยู่เป็นข้อความเดียว
    $full_address_parts = array_filter([$address1, $address2, $city, $state, $postcode, $country]);
    $full_address = !empty($full_address_parts) ? implode(', ', $full_address_parts) : 'N/A';

    $vendor_info = [
        'store_name' => $vendor_name,
        'logo_url'   => get_user_meta($vendor_id, 'marketking_profile_logo_image', true) ?: '',
        'address'    => $full_address, // ใช้ที่อยู่ที่รวมแล้ว
        'phone'      => get_user_meta($vendor_id, 'billing_phone', true) ?: 'N/A', // ใช้ billing_phone
        'email'      => !empty($vendor_data->user_email) ? $vendor_data->user_email : 'N/A',
    ];
    // --- END: ส่วนที่แก้ไข ---

    // หากใช้ปลั๊กอินร้านค้า (Store) อื่นๆ สามารถดึงลิงก์ได้ตามนี้ (ถ้ามี)
    // สำหรับ MarketKing อาจต้องตรวจสอบฟังก์ชันเฉพาะของปลั๊กอิน
    $vendor_shop_url = function_exists('marketking') ? marketking()->get_store_link($vendor_id) : '#';

    // เพิ่ม vendor_info เข้าไปใน object ของ product
    $products_data[$i]->vendor_info = $vendor_info;
    $products_data[$i]->vendor_shop_url = $vendor_shop_url;
}

// ... (โค้ดส่วนล่าง)




?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        @page {
            margin-top: 50px;
            margin-left: 50px;
            margin-right: 30px;
            margin-bottom: 50px;
            footer: html_myFooter;


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
                                                                <td style="text-align: right;">$<?php echo isset($tier['price']) && $tier['price'] !== null ? number_format((float)$tier['price'], 2) : 'N/A'; ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
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
                    <div class="page-break"></div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
        <htmlpagefooter name="myFooter">
            <div id="footer">
                Page {PAGENO} of {nb}
            </div>
        </htmlpagefooter>

</body>

</html>