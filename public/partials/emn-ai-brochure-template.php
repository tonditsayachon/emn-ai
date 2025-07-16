<?php
/**
 * The template for displaying the brochure content.
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/public/partials
 */

// รับตัวแปร $product_ids ที่ถูกส่งมาจากฟังก์ชัน process_brochure_generation_job
global $product_ids; 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* ใส่ CSS สำหรับ PDF ของคุณที่นี่ */
        body { font-family: 'Garuda'; } /* mPDF รองรับฟอนต์ไทยชื่อ Garuda */
        .product-item { page-break-inside: avoid; border-bottom: 1px solid #ccc; padding-bottom: 20px; margin-bottom: 20px; }
        .product-image { max-width: 150px; float: left; margin-right: 20px; }
        .product-title { font-size: 24px; color: #333; }
        .product-price { font-size: 20px; color: #d63638; }
    </style>
</head>
<body>
    <h1>Product Brochure</h1>
    <?php foreach ($product_ids as $pid) : ?>
        <?php
            $product = wc_get_product($pid);
            if (!$product) continue;
        ?>
        <div class="product-item">
            <?php echo $product->get_image('thumbnail', array('class' => 'product-image')); ?>
            <h2 class="product-title"><?php echo esc_html($product->get_name()); ?></h2>
            <div class="product-price"><?php echo $product->get_price_html(); ?></div>
            <div><?php echo wp_kses_post($product->get_short_description()); ?></div>
        </div>
    <?php endforeach; ?>
</body>
</html>