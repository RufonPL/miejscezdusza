<?php if(is_page('paypal-execute-payment')) : ?>
<?php get_template_part('content', 'paypal-execute-payment'); ?>
<?php endif; ?><?php
/**
 * The Template for displaying all single posts.
 *
 * @author Rafał Puczel
 */
get_header(); ?>

<?php if( is_page('payu-notifications') ) : ?>
    <?php get_template_part('content', 'payu-notifications'); ?>
<?php endif; ?>

<?php if( is_page('paypal-notifications') ) : ?>
    <?php get_template_part('content', 'paypal-notifications'); ?>
<?php endif; ?>

<main>
    <?php if(is_profile_page()) : ?>
        <?php get_template_part('content', 'profile'); ?>
    <?php else : ?>

    <div class="container-fluid page-container places-bg-image">
        <div class="container place-single shadowed bgcolor1 <?php if( get_post_type() == 'post' ) : ?>place-has-comments<?php endif; ?>">
            <div class="row">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php if( get_post_type() == 'regiony' ) : ?>
                        <?php get_template_part('content', 'region'); ?>
                    <?php else : ?>
                        <?php if(is_login_page()) : ?>
                            <?php get_template_part('content', 'login'); ?>
                        <?php elseif(is_page('payment-confirmation')) : ?>
                            <?php get_template_part('content', 'payment-confirmation'); ?>
                        <?php else : ?>
                            <?php get_template_part('content', 'single'); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>

        <?php if( get_post_type() == 'post' && is_place_active(get_the_ID()) ) : ?>
        <div class="container place-comments">
            <div class="row">
                <?php comments_template(); ?> 
            </div>
        </div>
        <?php endif; ?>

    </div>
    
    <?php if( get_post_type() == 'regiony' ) : ?>
        <?php require_template_part('recommended-from-region', 'misc', array('region_id' => get_the_ID())); ?>
    <?php endif; ?>
    
    <?php endif; ?>
</main>

<?php get_footer(); ?>
