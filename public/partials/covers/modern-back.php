<?php
/**
 * Back Cover - Minimalist Style (Replaces Modern)
 */
?>
<style>
    .minimalist-back-cover {
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        color: #111111;
        position: relative;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
        padding: 30px;
    }
    .minimalist-back-cover .contact-block {
        position: absolute;
        bottom: 50px;
        right: 50px;
        text-align: right;
    }
    .minimalist-back-cover .contact-block .logo {
        max-width: 120px;
        margin-bottom: 20px;
        
    }
    .minimalist-back-cover .contact-block p {
        font-size: 14px;
        font-weight: 300;
        margin: 4px 0;
    }
    .minimalist-back-cover .contact-block .website {
        font-weight: 500;
        color: #111111;
    }

</style>
<div class="minimalist-back-cover" style="page: back-cover-page;">
    
    <div class="contact-block">
        <img style="width:120px;" class="logo" src="<?php echo esc_url(plugins_url('public/images/halplus-directory-logo.png', dirname(__FILE__, 3))); ?>" alt="Halplus Directory Logo">
        <p>A platform for halal products and services.</p>
        <p class="website">www.halalplus.com</p>
    </div>

</div>