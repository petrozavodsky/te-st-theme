<!DOCTYPE html >
<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1.0,user-scalable=no">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> >
<div class="container">
    <header class="site__header header clearfix">


        <h3 class=" display-2 text-center">
            <a href="<?php echo esc_url(home_url()); ?>" rel="home">
                <?php bloginfo('name'); ?>
            </a>
        </h3>

</header>



