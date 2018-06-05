<?php
/**
 * @author Rafał Puczel
 */
?>

<article>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-item">
		<header>
    		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		</header>
        
        <?php the_excerpt(); ?> 
       
        <a class="btn btn-primary" href="<?php the_permalink(); ?>">Czytaj więcej</a>
    </div>
</div>
</article>
