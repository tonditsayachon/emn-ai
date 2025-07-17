<?php
/**
 * Template for the Product Brochure PDF - Upgraded Design
 *
 * @var array $products_data Contains an array of product data objects.
 */

if (empty($products_data)) {
    echo '<p>No product information available.</p>';
    return;
}

// *** เพิ่มตัวแปรสำหรับโลโก้ ***
// ให้ใส่ URL เต็มๆ ของโลโก้บริษัทคุณตรงนี้
$company_logo_url = 'https://www.yourcompany.com/path/to/logo.png';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        /*
         * === ส่วนของการตกแต่ง (CSS) ===
         * แก้ไขค่าสี, ขนาด, ฟอนต์ ได้ทั้งหมดในนี้
        */
        body {
            font-family: "garuda", sans-serif; /* ฟอนต์หลัก (รองรับภาษาไทย) */
            font-size: 11px;
            color: #444; /* สีตัวอักษรหลัก */
        }
        .product-page {
            page-break-after: always;
        }
        .product-page:last-child {
            page-break-after: auto;
        }
        .brochure-container {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        .logo-header {
            text-align: right; /* จัดโลโก้ไปทางขวา */
            border-bottom: 2px solid #005A9C; /* สีเส้นใต้โลโก้ (สีน้ำเงิน) */
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-header img {
            max-height: 60px; /* ขนาดความสูงของโลโก้ */
        }
        .header h1 {
            font-size: 26px;
            color: #005A9C; /* สีชื่อสินค้า (สีน้ำเงิน) */
            margin-bottom: 5px;
        }
        .category {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .sku {
            font-size: 12px;
            font-family: monospace;
            color: #888;
            margin-bottom: 20px;
        }
        .main-content {
            /* เราจะใช้ Flexbox ในการแบ่ง 2 คอลัมน์ (แต่ mPDF ไม่รองรับดีนัก)
               ดังนั้นจะใช้ table แทนซึ่งแน่นอนกว่า */
        }
        .price {
            font-size: 24px;
            font-weight: bold;
            color: #D32F2F; /* สีราคา (สีแดงเข้ม) */
            margin-bottom: 15px;
        }
        .description {
            font-size: 14px;
            line-height: 1.7;
            text-align: justify;
            margin-top: 15px;
        }
        .details-section h2 {
            font-size: 18px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
            margin-top: 30px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .details-table td {
            border: 1px solid #e0e0e0;
            padding: 10px;
            font-size: 12px;
            vertical-align: top;
        }
        .details-table td:first-child {
            background-color: #f7f7f7; /* สีพื้นหลังของหัวข้อตาราง */
            font-weight: bold;
            width: 35%;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <?php foreach ($products_data as $product_data): ?>
    <div class="product-page">
        <div class="brochure-container">

            <div class="logo-header">
                <img src="<?php echo esc_url($company_logo_url); ?>" alt="Company Logo">
            </div>

            <div class="header">
                <h1><?php echo esc_html($product_data->name); ?></h1>

                <?php if (!empty($product_data->sku)) : ?>
                    <div class="sku">SKU: <?php echo esc_html($product_data->sku); ?></div>
                <?php endif; ?>

                <?php if (!empty($product_data->categories)) : ?>
                    <div class="category">หมวดหมู่: <?php echo esc_html(implode(', ', $product_data->categories)); ?></div>
                <?php endif; ?>
            </div>

            <div class="main-content">
                <div class="product-info">
                    <?php if (!empty($product_data->regular_price)) : ?>
                        <div class="price">ราคา: ฿<?php echo number_format((float)$product_data->regular_price, 2); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($product_data->short_description)) : ?>
                        <div class="description">
                            <?php echo wp_strip_all_tags($product_data->short_description); // ใช้ wp_strip_all_tags เพื่อลบ HTML tag ออก ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty((array)$product_data->acf_fields)): ?>
            <div class="details-section">
                <h2>ข้อมูลจำเพาะ</h2>
                <table class="details-table">
                    <tbody>
                        <?php
                        // Function เพื่อวนลูปแสดงผลข้อมูลในตาราง (รองรับข้อมูลซ้อนกัน)
                        function display_acf_fields_in_table($fields) {
                            foreach ($fields as $key => $value) {
                                if (is_object($value) || is_array($value)) {
                                    display_acf_fields_in_table($value);
                                } elseif (!empty($value)) {
                                    echo '<tr>';
                                    echo '<td>' . esc_html(ucwords(str_replace('_', ' ', $key))) . '</td>';
                                    echo '<td>' . esc_html($value) . '</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        display_acf_fields_in_table($product_data->acf_fields);
                        ?>
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