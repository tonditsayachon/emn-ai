<?php

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Brochure: {{product_name}}</title>
    <link rel="stylesheet" href="/wp-content/plugins/emn-ai/public/css/emn-ai-public.css">
</head>
<body>
    <div class="brochure-container">
        <h1>{{product_name}}</h1>
        <div class="sku">SKU: {{sku}}</div>
        <div class="price">Price: ฿{{product_price}}</div>
        <div class="category">Category: {{product_categories}}</div>

        <h2>Description</h2>
        <p>{{product_description}}</p>

        <h2>Product Details</h2>
        <ul>
            <li>Product Volume: {{product_volume}}</li>
            <li>Manufacturer: {{manufacturer}}</li>
            <li>Ingredients: {{ingredients}}</li>
            <li>Place of Origin: {{place_of_origin}}</li>
            <li>Product Type: {{product_type}}</li>
        </ul>
    </div>
</body>
</html>
