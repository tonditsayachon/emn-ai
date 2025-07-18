<?php
/**
 * Template for the Product Brochure PDF - v2 (Fixed Redeclare Error)
 *
 * @var array $products_data Contains an array of product data objects.
 */

if (empty($products_data)) {
    echo '<p>No product information available.</p>';
    return;
}

// *** แก้ปัญหา Redeclare: ประกาศฟังก์ชัน Helper ไว้นอก Loop ***
if (!function_exists('display_acf_fields_in_table')) {
    /**
     * Renders ACF fields in a table format.
     * @param array|object $fields The fields to display.
     */
    function display_acf_fields_in_table($fields) {
        foreach ($fields as $key => $value) {
            // ถ้าค่าที่ได้เป็น object หรือ array, ให้วนลูปเข้าไปข้างในอีกชั้น
            if (is_object($value) || is_array($value)) {
                display_acf_fields_in_table($value);
            } elseif (!empty($value)) {
                // ถ้าเป็นค่าปกติ, ให้แสดงผลในตาราง
                echo '<tr>';
                // แปลง key เช่น 'brand_name' เป็น 'Brand Name'
                echo '<td>' . esc_html(ucwords(str_replace('_', ' ', $key))) . '</td>';
                echo '<td>' . esc_html($value) . '</td>';
                echo '</tr>';
            }
        }
    }
}


// ใส่ URL เต็มๆ ของโลโก้บริษัทคุณตรงนี้
$company_logo_url = 'https://www.yourcompany.com/path/to/logo.png';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        body { font-family: "garuda", sans-serif; font-size: 11px; color: #444; }
        .product-page { page-break-after: always; }
        .product-page:last-child { page-break-after: auto; }
        .brochure-container { width: 100%; margin: 0 auto; padding: 10px; }
        .logo-header { text-align: right; border-bottom: 2px solid #005A9C; padding-bottom: 15px; margin-bottom: 20px; }
        .logo-header img { max-height: 60px; }
        .header h1 { font-size: 26px; color: #005A9C; margin-bottom: 5px; }
        .sku { font-size: 12px; font-family: monospace; color: #888; margin-bottom: 20px; }
        .price { font-size: 24px; font-weight: bold; color: #D32F2F; margin-bottom: 15px; }
        .description { font-size: 14px; line-height: 1.7; text-align: justify; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; }
        .details-section h2 { font-size: 18px; color: #333; border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .details-table td { border: 1px solid #e0e0e0; padding: 10px; font-size: 12px; vertical-align: top; }
        .details-table td:first-child { background-color: #f7f7f7; font-weight: bold; width: 35%; }
        .footer { text-align: center; margin-top: 40px; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <?php foreach ($products_data as $product): ?>
    <div class="product-page">
        <div class="brochure-container">

            <div class="logo-header">
                <img src="<?php echo esc_url($company_logo_url); ?>" alt="Company Logo">
            </div>

            <div class="header">
                <h1><?php echo esc_html($product->name); ?></h1>
                <?php if (!empty($product->sku)) : ?>
                    <div class="sku">SKU: <?php echo esc_html($product->sku); ?></div>
                <?php endif; ?>
            </div>

            <div class="main-content">
                <?php if (!empty($product->regular_price)) : ?>
                    <div class="price">ราคา: ฿<?php echo number_format((float)$product->regular_price, 2); ?></div>
                <?php endif; ?>

                <?php if (!empty($product->short_description)) : ?>
                    <div class="description"><?php echo wp_strip_all_tags($product->short_description); ?></div>
                <?php endif; ?>
            </div>

            <?php if (!empty((array)$product->acf_fields)): ?>
            <div class="details-section">
                <h2>ข้อมูลจำเพาะ</h2>
                <table class="details-table">
                    <tbody>
                        <?php display_acf_fields_in_table($product->acf_fields); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <div class="footer">
                <p>เอกสารนี้จัดทำโดยระบบอัตโนมัติ | &copy; <?php echo date('Y'); ?> Your Company Name</p>
            </div>

        </div>
    </div>
    <?php endforeach; ?>

</body>
</html>