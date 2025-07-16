<?php
/**
 * Template for the Product Brochure PDF.
 * * @var object $product_data ข้อมูลสินค้าทั้งหมด
 * @var string $image_url URL ของรูปภาพ
 * @var string $categories_string หมวดหมู่สินค้า
 */
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Brochure: <?php echo esc_html($product_data->name); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo esc_url(plugin_dir_url(__FILE__) . 'css/emn-ai-public.css'); ?>">
</head>
<body>
    <div class="brochure-container">
        <div class="header">
            <h1><?php echo esc_html($product_data->name); ?></h1>
            <?php if (!empty($categories_string)) : ?>
                <div class="category"><?php echo esc_html($categories_string); ?></div>
            <?php endif; ?>
        </div>

        <div class="main-content">
            <div class="product-image-container">
                <?php if (!empty($image_url)) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_data->name); ?>" class="product-image">
                <?php endif; ?>
            </div>
            <div class="product-info">
                <?php if (!empty($product_data->regular_price)) : ?>
                    <div class="price">฿<?php echo number_format((float)$product_data->regular_price, 2); ?></div>
                <?php endif; ?>

                <?php if (!empty($product_data->description)) : ?>
                    <p class="description"><?php echo nl2br(esc_html($product_data->description)); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php 
        // ตรวจสอบว่ามีข้อมูลใน acf_fields หรือไม่
        $has_attributes = false;
        if (!empty($product_data->acf_fields)) {
            foreach ($product_data->acf_fields as $group) {
                if(is_object($group)) {
                    foreach($group as $value) {
                        if(!empty($value)) {
                            $has_attributes = true;
                            break 2;
                        }
                    }
                }
            }
        }
        ?>

        <?php if ($has_attributes): ?>
        <div class="details-section">
            <h2>คุณสมบัติและข้อมูลจำเพาะ</h2>
            <table class="details-table">
                <tbody>
                    <?php foreach ($product_data->acf_fields as $group_data) : ?>
                        <?php if (is_object($group_data)) : ?>
                            <?php foreach ($group_data as $key => $value) : ?>
                                <?php if (!empty($value)) : ?>
                                    <tr>
                                        <td><?php echo esc_html(ucwords(str_replace('_', ' ', $key))); ?></td>
                                        <td><?php echo esc_html($value); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>