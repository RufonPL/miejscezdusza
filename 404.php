<?php
wp_redirect(get_bloginfo('url'),'301'); exit();
?>
<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @author Rafał Puczel
 */

get_header(); ?>

<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 content">
            <section class="error-404">
                <header class="page-header">
                    <h1 class="page-title"><?php _e( 'Strona, której szukasz nie istnieje!', 'rfswp' ); ?></h1>
                </header><!--end page-header-->
                
            </section><!--end error-404-->
        </div><!--end content-->
    </div><!--end row-->
</div><!--end container-->               

<?php get_footer(); ?>