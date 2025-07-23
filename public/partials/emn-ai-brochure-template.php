<?php

/**
 * Template for the Product Brochure PDF (New Design - July 2025 v2)
 *
 * @var array $products_data Contains an array of product data objects.
 */

// ดึงข้อมูลบริษัทเริ่มต้นจากค่าคงที่ (Constants) ที่กำหนดไว้ใน class-emn-ai.php
$default_company_name = defined('EMN_AI_DEFAULT_COMPANY_NAME') ? EMN_AI_DEFAULT_COMPANY_NAME : 'Emonics Solution';
$default_logo_url     = defined('EMN_AI_DEFAULT_LOGO') ? EMN_AI_DEFAULT_LOGO : '';
$default_address      = defined('EMN_AI_DEFAULT_ADDRESS') ? EMN_AI_DEFAULT_ADDRESS : '';
$default_tel          = defined('EMN_AI_DEFAULT_TEL') ? EMN_AI_DEFAULT_TEL : '';
$default_email        = defined('EMN_AI_DEFAULT_EMAIL') ? EMN_AI_DEFAULT_EMAIL : '';



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
    $vendor_info = [
        'store_name' => $vendor_name,
        'logo_url'   => get_user_meta($vendor_id, 'marketking_profile_logo_image', true) ?: '',
        'address'    => get_user_meta($vendor_id, 'dokan_store_address', true) ?: 'N/A',
        'phone'      => get_user_meta($vendor_id, 'dokan_store_phone', true) ?: 'N/A',
        'email'      => !empty($vendor_data->user_email) ? $vendor_data->user_email : 'N/A',
    ];
    // หากใช้ปลั๊กอินร้านค้า (Store) ของ Vendor สามารถดึงลิงก์ได้ตามนี้ (ถ้ามี)
    $vendor_shop_url = function_exists('dokan_get_store_url') ? dokan_get_store_url($vendor_id) : '#';

    // เพิ่ม vendor_info เข้าไปใน object ของ product
    $products_data[$i]->vendor_info = $vendor_info;
    $products_data[$i]->vendor_shop_url = $vendor_shop_url;
}




?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        @page {
            margin-top: 50px;
            margin-left: 30px;
            margin-right: 30px;
            margin-bottom: 50px;
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
            padding-bottom: 15px;
            margin-bottom: 16px;

        }

        .page-header>* {
            display: block;
            width: 100%;

        }


        table {
            width: 100%;
            border-collapse: collapse;

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
        }


        .vendor-details>* {
            display: block;

        }

        .vendor-name {
            border-bottom: #000 1px solid;
            margin-bottom: 16px;
            font-weight: 500;
            font-size: 20px !important;
            padding-bottom: 8px;
        }

        .vendor-logo {

            

            margin-bottom: 8px;
           
        }

        .vendor-logo img {

            max-width: 100%;
            max-height: 20px;
        }

        .vendor-contact {
            color: #595959;
            font-weight: 300;
        }

        /* --- Main Content --- */
        .main-content {
            overflow: auto;
            margin-bottom: 16px;
        }

        .left-column {
            float: left;
            width: 48%;
        }

        .feature-image img {
            width: 100%;
            height: auto;
            /* border-width: 1px;
            border-style: solid;
            border-color: #ddd;
            border-radius: 4px; */
        }

        .right-column {
            float: right;
            width: 48%;
        }



        .description {
            color: #595959;
            margin-bottom: 16px;

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

            img {
                object-fit: cover;
            }


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
            border-bottom: 2px solid #666;

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
                    <table>
                        <tr>
                            <td>
                                <h1 class="product-name" style="text-transform: uppercase;"><?php echo esc_html($product->name); ?></h1>
                            </td>
                            <td style="text-align:end;float:right;">
                                <img class="hsc-logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 2))); ?>" alt="Halplus Directory Logo">
                            </td>
                        </tr>
                    </table>
                </div>


                <table>
                    <tr>
                        <td style="max-width:40%;width:40%;background-color:#ddd;">
                            <div >
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
                                    <div>
                                        <img
                                            style="width:450px;"
                                            src="<?php echo $image_url; ?>"
                                            alt="<?php echo esc_attr($product->name); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td style="max-width:60%;width:60%;padding-left:16px;vertical-align:top">
                            <div class="vendor-details">

                                <div class="vendor-logo">
                                    <?php if (!empty($logo_url)): ?>
                                        <img 
                                        src="<?php echo esc_url($logo_url); ?>" 
                                        alt="Vendor Logo"
                                        style="max-height: 150px;"
                                     
                                        >
                                    <?php endif; ?>

                                </div>
                                <div class="vendor-name"><?php echo esc_html($store_name); ?></div>
                                <div class="vendor-contact">
                                    <?php echo wp_kses_post('<strong>Address: </strong>' . nl2br(esc_html($address))); ?><br>

                                    <ul>
                                        <li> <strong>Tel:</strong> <?php echo esc_html($tel); ?></li>
                                        <li> <strong>Email:</strong> <?php echo esc_html($email); ?></li>
                                    </ul>


                                </div>


                            </div>
                            <div class="tier-prices" style="border: 1px solid #666; border-radius: 10px; overflow: hidden;">
                                <?php if (!empty($product->tiers_prices) && is_array($product->tiers_prices)): ?>
                                    <table class="tier-price-table">
                                        <thead>
                                            <tr>
                                                <th>Quantity</th>
                                                <th>Price</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($product->tiers_prices as $tier): ?>
                                                <tr>
                                                    <td><?php echo !empty($tier['quantity']) ? esc_html($tier['quantity']) : 'N/A'; ?></td>
                                                    <td>฿<?php echo isset($tier['price']) && $tier['price'] !== null ? number_format((float)$tier['price'], 2) : 'N/A'; ?></td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- <div class="main-content">

                    <div class="left-column">

                        <div class="feature-image">
                            < ?php if (!empty($product->featured_image)): ?>
                                < ?php
                                // Get image dimensions
                                $image_url = esc_url($product->featured_image);
                                $image_size = @getimagesize($product->featured_image);
                                $is_landscape = false;
                                if ($image_size && $image_size[0] > $image_size[1]) {
                                    $is_landscape = true;
                                }
                                ?>
                                <div style="border: 2px solid #E6E6E6;background: < ?php echo $is_landscape ? '#f0f0f0' : 'none'; ?>; display: flex; align-items: center; justify-content: center; width: 100%; height: auto;">
                                    <img
                                        style="max-width: 100%; max-height: auto; object-fit: fill; background: #e6e6e6;"
                                        src="< ?php echo $image_url; ?>"
                                        alt="< ?php echo esc_attr($product->name); ?>">
                                </div>
                            < ?php endif; ?>
                        </div>
                    </div>
                    <div class="right-column">

                        <div class="vendor-details">

                            <div class="vendor-logo">
                                < ?php if (!empty($logo_url)): ?>
                                    <img src="< ?php echo esc_url($logo_url); ?>" alt="Vendor Logo">
                                < ?php endif; ?>

                            </div>
                            <div class="vendor-name">< ?php echo esc_html($store_name); ?></div>
                            <div class="vendor-contact">
                                < ?php echo wp_kses_post('<strong>Address: </strong>' . nl2br(esc_html($address))); ?><br>

                                <ul>
                                    <li> <strong>Tel:</strong> < ?php echo esc_html($tel); ?></li>
                                    <li> <strong>Email:</strong> < ?php echo esc_html($email); ?></li>
                                </ul>


                            </div>


                        </div>
                        <div class="tier-prices" style="border: 1px solid #666; border-radius: 10px; overflow: hidden;">
                            < ?php if (!empty($product->tiers_prices) && is_array($product->tiers_prices)): ?>
                                <table class="tier-price-table">
                                    <thead>
                                        <tr>
                                            <th>Weight</th>
                                            <th>Price</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        < ?php foreach ($product->tiers_prices as $tier): ?>
                                            <tr>
                                                <td>< ?php echo !empty($tier['quantity']) ? esc_html($tier['quantity']) : 'N/A'; ?></td>
                                                <td>฿< ?php echo isset($tier['price']) && $tier['price'] !== null ? number_format((float)$tier['price'], 2) : 'N/A'; ?></td>

                                            </tr>
                                        < ?php endforeach; ?>
                                    </tbody>
                                </table>
                            < ?php endif; ?>
                        </div>
                    </div>
                </div> -->

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

</body>

</html>