<article <?php post_class(); ?> >
    <header>
		<?php the_title('<h1 class="mb-0">','</h1>'); ?>
    </header>

    <div class="article__content card-text lead mb-auto">
        <?php the_excerpt();?>
    </div>

</article>
