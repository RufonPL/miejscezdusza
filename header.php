<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <main id="main">
 *
 * @author Rafał Puczel
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php $favicon = get_field('_favicon','option'); ?>
<?php if($favicon) : ?>
<link rel="shortcut icon" href="<?php echo esc_url($favicon['url']); ?>" />
<?php endif; ?>


<?php $logo = get_field('_logo','option'); ?>

<?php wp_head(); ?>

</head>
<body <?php body_class(); ?> data-ng-app="rfsApp" data-ng-cloak data-site-url="<?php bloginfo('template_url'); ?>">

	<?php require_template_part('page-loader', 'misc'); ?>

	<div class="body-inner" data-ng-controller="PageLoadedController">
	
	<header>
    	<nav class="navbar navbar-default container-fluid">
			<div class="container">
				<div class="row">
					<div class="navbar-header">
                        <a href="<?php echo esc_url(get_bloginfo('url')); ?>" class="navbar-brand">
                            <?php if($logo) : ?>
                            <img src="<?php echo esc_url($logo['sizes']['medium']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>"/>
                            <h2 class="site-name"><?php echo esc_html(get_bloginfo('name')); ?></h2>
                            <?php else : ?>
                            <h2><?php echo esc_html(get_bloginfo('name')); ?></h2>
                            <?php endif; ?>
                        </a>
						<div class="account-links">
							<?php if( is_user_logged_in() ) : ?>
							<a href="<?php echo wp_logout_url(); ?>" class="text-uppercase nuh inline-block transition" id="logout-link"><i class="fa fa-power-off" aria-hidden="true"></i>
 Wyloguj się</a>
							<a href="<?php echo esc_url( rfs_redirect_to('profile') ); ?>" class="text-uppercase nuh inline-block transition" id="profile-link"><i class="fa fa-user" aria-hidden="true"></i>
 Moje konto</a>
							<?php else : ?>
							<a href="<?php echo esc_url( rfs_redirect_to('login') ); ?>" class="text-uppercase nuh inline-block transition" id="login-link"><i class="fa fa-lock" aria-hidden="true"></i>
 Zaloguj się</a>
							<a href="<?php echo esc_url( rfs_redirect_to('login') ); ?>#rejestracja" class="text-uppercase nuh inline-block transition" id="register-link"><i class="fa fa-users" aria-hidden="true"></i>
 Rejestracja</a>
 							<?php endif; ?>
						</div>
						<?php echo social_icons(); ?>
                    </div>
				</div>
			</div>
			<div class="menu-container <?php if(is_singular('post')) : ?>menu-container-shadow<?php endif; ?>">
				<div class="navbar-menu container">
					<div class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<div class="pull-left">
							MENU
						</div>
						<div class="pull-right">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</div>
					</div>
					<?php 
					$args = array(
						'theme_location' 	=> 'primary', 
						'container_class' 	=> 'navbar-collapse collapse', 
						'menu_class' 		=> 'nav navbar-nav',
						'fallback_cb'		=> '',
						'menu_id' 			=> 'main-menu',
						'walker' 			=> new Rfswp_Walker_Nav_Menu()); 
					wp_nav_menu($args);
					?>
				</div>
			</div>
		</nav>
	</header><!--end header-->
	<div id="content" class="site-content">