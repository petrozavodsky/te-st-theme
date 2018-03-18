<article <?php post_class(); ?> >
    <header>
		<?php the_title('<h1>','</h1>'); ?>
    </header>

    <div class="article__content">
        <?php the_content();?>
    </div>

</article>
