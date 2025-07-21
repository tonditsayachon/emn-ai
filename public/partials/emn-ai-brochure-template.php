<?php
/**
 * Template for the Product Brochure PDF - v3 (New Design)
 *
 * @var array $products_data Contains an array of product data objects.
 */

// ใส่ URL เต็มๆ ของโลโก้บริษัทคุณตรงนี้
$company_logo_url = 'https://www.halalthai.com/wp-content/uploads/2024/12/halplus-directory-logo.png'; 
// ใส่ URL รูปภาพหน้าปกตรงนี้
$cover_image_url = 'https://www.yourcompany.com/path/to/cover-image.jpg';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Product Brochure</title>
    <style>
        @page {
            margin: 0; /* ลบ margin ของหน้ากระดาษ */
        }
        body { 
            font-family: "garuda", sans-serif; 
            font-size: 12px; 
            color: #333;
            margin: 0;
        }
        .page-break { 
            page-break-after: always; 
        }
        .cover-page {
            width: 100%;
            height: 100%;
            background-color: #005A9C; /* สีพื้นหลังหน้าปก */
            color: white;
            text-align: center;
            display: table;
        }
        .cover-content {
            display: table-cell;
            vertical-align: middle;
        }
        .cover-content h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .cover-content img.logo {
            max-height: 80px;
            margin-bottom: 40px;
        }
        .product-page {
            padding: 40px;
            box-sizing: border-box;
        }
        .product-header {
            display: block;
            margin-bottom: 30px;
            border-bottom: 2px solid #005A9C;
            padding-bottom: 15px;
        }
        .product-header .product-name {
            font-size: 28px;
            color: #005A9C;
            font-weight: bold;
            margin: 0;
        }
        .product-header .product-sku {
            font-size: 14px;
            color: #555;
            font-family: monospace;
            margin-top: 5px;
        }
        .product-main {
            height: 500px; /* กำหนดความสูงเพื่อให้จัด layout ง่าย */
        }
        .product-image {
            width: 45%;
            float: left;
            text-align: center;
        }
        .product-image img {
            max-width: 100%;
            max-height: 350px;
        }
        .product-info {
            width: 50%;
            float: right;
        }
        .product-price {
            font-size: 26px;
            font-weight: bold;
            color: #D32F2F;
            margin-bottom: 15px;
        }
        .product-short-desc {
            font-size: 14px;
            line-height: 1.7;
            text-align: justify;
            margin-top: 15px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .details-section {
            margin-top: 40px;
            clear: both; /* เคลียร์ float */
        }
        .details-section h2 {
            font-size: 18px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
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
            background-color: #f7f7f7;
            font-weight: bold;
            width: 35%;
        }
        .page-footer {
            position: fixed;
            bottom: 10px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="cover-page page-break">
        <div class="cover-content">
            <img src="<?php echo esc_url($company_logo_url); ?>" alt="Company Logo" class="logo">
            <h1>Product Catalog</h1>
            <p>Generated on: <?php echo date('F j, Y'); ?></p>
        </div>
    </div>


    <?php if (!empty($products_data)): ?>
        <?php foreach ($products_data as $product): ?>
        <div class="product-page">
            
            <div class="product-header">
                <h1 class="product-name"><?php echo esc_html($product->name); ?></h1>
                <?php if (!empty($product->sku)) : ?>
                    <div class="product-sku">SKU: <?php echo esc_html($product->sku); ?></div>
                <?php endif; ?>
            </div>

            <div class="product-main">
                <div class="product-image">
                    <?php if (!empty($product->featured_image)): ?>
                        <img src="<?php echo esc_url($product->featured_image); ?>" alt="<?php echo esc_attr($product->name); ?>">
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <?php if (!empty($product->regular_price)) : ?>
                        <div class="product-price">ราคา: ฿<?php echo number_format((float)$product->regular_price, 2); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($product->short_description)) : ?>
                        <div class="product-short-desc"><?php echo wp_strip_all_tags($product->short_description); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($product->acf_fields) && is_object($product->acf_fields)): ?>
                <div class="details-section">
                    <h2>ข้อมูลจำเพาะ</h2>
                    <table class="details-table">
                        <tbody>
                
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="page-footer">
                <p>เอกสารนี้จัดทำโดยระบบอัตโนมัติ | &copy; <?php echo date('Y'); ?> Your Company Name</p>
            </div>

        </div>
        <?php if (next($products_data)): // เช็คว่ามีสินค้าตัวต่อไปหรือไม่เพื่อใส่ page-break ?>
            <div class="page-break"></div>
        <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>