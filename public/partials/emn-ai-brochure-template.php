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
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: "garuda", sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 16px;
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
            margin-bottom: 20px;

        }

        .page-header>* {
            display: block;
            width: 100%;

        }

        .hsc-logo {

            float: right;
            width: 120px;
            height: auto;
        }

        .product-name {
            float: left;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }

        .vendor-details>* {
            display: block;

        }

        .vendor-name {
            border-bottom: #000 1px solid;
            margin-bottom: 8px;
            font-weight: 400;
            font-size: 20px !important;
        }

        .vendor-logo {

            width: 150px;
            height: auto;
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
            margin-bottom: 20px;
        }

        .left-column {
            float: left;
            width: 48%;
        }

        .right-column {
            float: right;
            width: 48%;
        }

        .feature-image img {
            width: 100%;
            height: auto;
            border: 1px solid #ddd;
        }

        .description {
            color: #595959;

            .product-description {
                font-size: 12px;
                line-height: 1.5;
                font-weight: 300;
            }
        }
         .gallery-section {
             margin-top: 20px;
        }
        .gallery-grid {
            overflow: auto; /* To contain floated elements */
            width: 100%;
        }
        .gallery-image {
        
            width: 31.33%; /* (100% / 3 columns) - margins */
            margin: 1%;
            box-sizing: border-box;
            
            text-align: center;
         
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
                    <div class="header-logo">

                        <img class="hsc-logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 2))); ?>" alt="Halplus Directory Logo">

                    </div>

                    <div class="product-name">
                        <?php echo esc_html($product->name); ?>
                    </div>




                </div>

                <div class="main-content">

                    <div class="left-column">

                        <div class="feature-image">
                            <?php if (!empty($product->featured_image)): ?>
                                <img src="<?php echo esc_url($product->featured_image); ?>" alt="<?php echo esc_attr($product->name); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="right-column">

                        <div class="vendor-details">

                            <div class="vendor-logo">
                                <?php if (!empty($logo_url)): ?>
                                    <img src="<?php echo esc_url($logo_url); ?>" alt="Vendor Logo">
                                <?php endif; ?>

                            </div>
                            <div class="vendor-name"><?php echo esc_html($store_name); ?></div>
                            <div class="vendor-contact">
                                <?php echo nl2br(esc_html($address)); ?><br>
                                <strong>Tel:</strong> <?php echo esc_html($tel); ?> | <strong>Email:</strong> <?php echo esc_html($email); ?>
                            </div>


                        </div>
                        <div class="tier-prices">
                            <?php if (!empty($product->tiers_prices) && is_array($product->tiers_prices)): ?>
                                <table class="tier-price-table">
                                    <thead>
                                        <tr>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Discount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($product->tiers_prices as $tier): ?>
                                            <tr>
                                                <td><?php echo !empty($tier['quantity']) ? esc_html($tier['quantity']) : 'N/A'; ?></td>
                                                <td>฿<?php echo isset($tier['price']) && $tier['price'] !== null ? number_format((float)$tier['price'], 2) : 'N/A'; ?></td>
                                                <td><?php echo isset($tier['discount']) && $tier['discount'] !== null ? esc_html($tier['discount']) . '%' : 'N/A'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="description">
                    <h1>Description</h1>
                    <?php if (!empty($product->description)) : ?>
                        <div class="product-description">
                            <?php echo wp_strip_all_tags($product->description); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($product->product_gallery) && is_array($product->product_gallery)): ?>
                    <div class="gallery-section">
                        <table class="gallery-grid" BORDER="0" CELLPADDING="8" CELLSPACING="8">
                            <tr>
                                <?php foreach ($product->product_gallery as $i => $image_url): ?>
                                    <td class="gallery-image">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="Gallery Image <?php echo $i + 1; ?>">
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