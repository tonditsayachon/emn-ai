<?php
/**
 * Template for the Product Brochure PDF.
 * This template now loops through an array of product data.
 *
 * @var array $products_data Contains an array of product data objects.
 */

if (empty($products_data)) {
    echo '<p>No product information available.</p>';
    return;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        /* เพิ่ม CSS เข้าไปในเทมเพลตโดยตรงเพื่อให้ mPDF อ่านได้ */
        body { font-family: "garuda", sans-serif; font-size: 12px; }
        .product-page { page-break-after: always; /* ทำให้สินค้าแต่ละชิ้นขึ้นหน้าใหม่ */ }
        .product-page:last-child { page-break-after: auto; }
        .brochure-container { width: 100%; margin: 0 auto; padding: 10px; }
        .header h1 { font-size: 24px; color: #333; margin-bottom: 5px; }
        .category { font-size: 14px; color: #777; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;}
        .main-content { margin-bottom: 20px; }
        .product-info {}
        .price { font-size: 22px; font-weight: bold; color: #E53935; margin-bottom: 10px; }
        .description { font-size: 14px; line-height: 1.6; }
        .details-section h2 { font-size: 18px; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 25px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .details-table td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        .details-table td:first-child { background-color: #f9f9f9; font-weight: bold; width: 30%; }
    </style>
</head>
<body>

    <?php foreach ($products_data as $product_data): ?>
    <div class="product-page">
        <div class="brochure-container">
            <div class="header">
                <h1><?php echo esc_html($product_data->name); ?></h1>
                <?php if (!empty($product_data->categories)) : ?>
                    <div class="category"><?php echo esc_html(implode(', ', $product_data->categories)); ?></div>
                <?php endif; ?>
            </div>

            <div class="main-content">
                <div class="product-info">
                    <?php if (!empty($product_data->regular_price)) : ?>
                        <div class="price">฿<?php echo number_format((float)$product_data->regular_price, 2); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($product_data->description)) : ?>
                        <p class="description"><?php echo nl2br(esc_html($product_data->description)); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty((array)$product_data->acf_fields)): ?>
            <div class="details-section">
                <h2>คุณสมบัติและข้อมูลจำเพาะ</h2>
                <table class="details-table">
                    <tbody>
                        <?php foreach ($product_data->acf_fields as $group_name => $fields): ?>
                             <?php
                                if (is_object($fields) || is_array($fields)) {
                                    foreach ($fields as $key => $value) {
                                        if (!empty($value)) {
                                            echo '<tr>';
                                            echo '<td>' . esc_html(ucwords(str_replace('_', ' ', $key))) . '</td>';
                                            echo '<td>' . esc_html(is_array($value) ? implode(', ', $value) : $value) . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                } elseif (!empty($fields)) {
                                    echo '<tr>';
                                    echo '<td>' . esc_html(ucwords(str_replace('_', ' ', $group_name))) . '</td>';
                                    echo '<td>' . esc_html($fields) . '</td>';
                                    echo '</tr>';
                                }
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

</body>
</html>