<?php
/**
 * Back Cover - Corporate Style
 */
?>
<style>
    .corporate-back-cover {
        width: 100%;
        height: 100%;
        background-color: #F0F0F0;
        color: #333;
        padding: 50px;
        text-align: center;
        box-sizing: border-box;
    }
    .corporate-back-cover .logo {
        max-width: 150px;
        margin-bottom: 20px;
    }
    .corporate-back-cover .contact-info {
        margin-top: 40px;
        color: #003366; /* Deep Blue Text */
    }
     .corporate-back-cover p {
        font-size: 16px;
    }
</style>
<div class="corporate-back-cover" style="page: back-cover-page;">
    <img class="logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 3))); ?>" alt="Logo">
    <div class="contact-info">
        <p><strong>Halal Plus Directory</strong></p>
        <p>A platform for halal products and services.</p>
        <p>www.halalplus.com</p>
    </div>
    <p style="margin-top: 100px; font-style: italic;">Thank you for your business.</p>
</div>