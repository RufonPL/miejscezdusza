<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @author Rafał Puczel
 */

get_header(); ?>

<main>
<div class="container-fluid page-container">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 page-content posts-index">
            <?php if(have_posts()) : ?>
            <?php while(have_posts()) : the_post(); ?>
                <?php get_template_part('content', get_post_format()); ?>
            <?php endwhile; ?>
            	<?php rfs_pagination(); ?>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</main>

<?php get_footer(); ?>
