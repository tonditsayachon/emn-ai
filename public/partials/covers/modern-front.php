<?php
/**
 * Front Cover - Minimalist Style (Replaces Modern)
 */
?>
<style>
    .minimalist-front-cover {
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        color: #111111;
        position: relative; /* For positioning child elements */
        font-family: 'Inter', sans-serif; /* Make sure this font is loaded */
        box-sizing: border-box;
    }
    .minimalist-front-cover .side-accent {
        position: absolute;
        top: 100px;
        left: 0;
        width: 12px;
        height: 80px;
        background-color: #111111;
    }
    .minimalist-front-cover .title-block {
        position: absolute;
        top: 100px;
        left: 50px;
    }
    .minimalist-front-cover h1 {
        font-size: 48px;
        font-weight: bold;
        margin: 0 0 5px 0;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .minimalist-front-cover p.subtitle {
        font-size: 20px;
        font-weight: 300; /* Light font */
        margin: 0;
        color: #555555;
    }
    .minimalist-front-cover .footer-block {
        position: absolute;
        bottom: 50px;
        left: 50px;
        width: 200px;
    }
    .minimalist-front-cover .footer-block hr {
        border: 0;
        border-top: 1.5px solid #111111;
        margin-bottom: 8px;
    }
    .minimalist-front-cover .footer-block .logo-text {
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

</style>
<div class="minimalist-front-cover" style="page: cover-page;">
    
    <div class="side-accent"></div>

    <div class="title-block">
        <h1>Product Catalog</h1>
        <p class="subtitle">Last Updated: <?php echo date_i18n('F Y'); ?></p>
    </div>

    <div class="footer-block">
        <hr />
        <p class="logo-text">Halal Plus Directory</p>
    </div>

</div>