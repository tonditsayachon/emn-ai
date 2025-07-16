<?php

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Brochure: {{product_name}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/wp-content/plugins/emn-ai/public/css/emn-ai-public.css">
</head>
<body>
    <div class="brochure-container">
        <div class="header">
            <h1>{{product_name}}</h1>
            <div class="category">{{product_categories}}</div>
        </div>

        <div class="main-content">
            <div class="product-image-container">
                <img src="{{product_image_url}}" alt="{{product_name}}" class="product-image">
            </div>
            <div class="product-info">
                <div class="price">฿{{product_price}}</div>
                <p class="description">{{product_description}}</p>
            </div>
        </div>

        <div class="details-section">
            <h2>คุณสมบัติและข้อมูลจำเพาะ</h2>
            <table class="details-table">
                <tbody>
                    {{product_attributes_table}}
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>