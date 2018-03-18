<?php get_header(); ?>
    <div id="primary" class="content__area">
        <?php do_action('te-st-theme__theme-content-area-before');?>
        <div class="base__container">
            <?php do_action('te-st-theme__theme-main-before');?>
            <main id="main-content" class="site__main site__width" role="main">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();?>
                        <?php do_action('te-st-theme__theme-loop__start', get_the_ID() );?>
                        <?php get_template_part( 'templates/index' );?>
                        <?php do_action('te-st-theme__theme-loop__end', get_the_ID() );?>
                    <?php endwhile;
                else : ?>
                    <?php get_template_part( 'templates/empty' ); ?>
                <?php endif;
                ?>
            </main>
            <?php do_action('te-st-theme__theme-main-after');?>
        </div>
        <?php do_action('te-st-theme__theme-content-area-after');?>
    </div>
<?php get_footer(); ?>