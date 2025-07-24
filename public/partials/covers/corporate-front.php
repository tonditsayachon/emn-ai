<?php
/**
 * Front Cover - Corporate Style
 */
?>
<style>
    .corporate-front-cover {
        width: 100%;
        height: 100%;
        background-color: #003366; /* Deep Blue */
        color: white;
        text-align: left;
        padding: 50px;
        box-sizing: border-box;
    }
    .corporate-front-cover .logo {
        max-width: 220px;
        margin-bottom: 250px;
    }
    .corporate-front-cover h1 {
        font-size: 52px;
        font-weight: bold;
        margin: 0;
        color: #FFFFFF;
    }
    .corporate-front-cover p {
        font-size: 22px;
        font-weight: 300;
        color: #E0E0E0;
        border-left: 3px solid #FFD700; /* Gold Accent */
        padding-left: 15px;
        margin-top: 15px;
    }
</style>
<div class="corporate-front-cover" style="page: cover-page;">
    <img class="logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 3))); ?>" alt="Logo">
    <h1>Product Catalog</h1>
    <p>Generated on <?php echo date_i18n('j F Y'); ?></p>
</div>