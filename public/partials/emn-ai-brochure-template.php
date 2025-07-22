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
                display: block;
                margin-left: auto;
                margin-right: 0;
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

            width: 150px;
            height: auto;
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
            border-width: 1px;
            border-style: solid;
            border-color: #ddd;
            border-radius: 4px;
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
                margin-top: 8px;
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
            border: 1px solid #ddd;
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
                    <table>
                        <tr>
                            <td>
                                <h1 class="product-name" style="text-transform: uppercase;"><?php echo esc_html($product->name); ?></h1>
                            </td>
                            <td style="vertical-align: right;">
                                <img class="hsc-logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 2))); ?>" alt="Halplus Directory Logo">
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="main-content">

                    <div class="left-column">

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
                                <div style="background: <?php echo $is_landscape ? '#f0f0f0' : 'none'; ?>; display: flex; align-items: center; justify-content: center; width: 100%; height: auto;">
                                    <img
                                        style="border: 1px solid #E6E6E6; max-width: 100%; max-height: auto; object-fit: contain; background: transparent;"
                                        src="<?php echo $image_url; ?>"
                                        alt="<?php echo esc_attr($product->name); ?>">
                                </div>
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
                                <ul>
                                    <li> <strong>Tel:</strong> <?php echo esc_html($tel); ?></li>
                                    <li> <strong>Email:</strong> <?php echo esc_html($email); ?></li>
                                </ul>


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
                    <p class="title">Description</p>
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