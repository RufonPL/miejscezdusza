<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to rfswp_comment() which is
 * located in the inc/template-tags.php file.
 *
 * @author Rafał Puczel
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

	<div id="comments" class="comments-area row">

	<?php	
	$args = array(
		'id_form'           => 'commentform',
		'id_submit'         => 'submit',
		'class_submit'      => 'btn btn-primary text-uppercase btn-md',
		'title_reply'       => '',
		'title_reply_to'    => __( 'Odpowiedz na %s' ),
		'cancel_reply_link' => __( '<div class="btn btn-danger relative btn-sm pull-right text-uppercase">Anuluj</div>' ),
		'label_submit'      => 'Dodaj Komentarz',
	
		'comment_field' =>  '<div class="form-group comment-form-comment"><textarea id="comment" class="form-control" name="comment" aria-required="true" placeholder="Wpisz treść komentarza">' .
		'</textarea></div>',
	
		'must_log_in' => '<p class="must-log-in f18 bold color3">' .
		sprintf(
			'Musisz być <a href="%s">zalogowany</a>, aby komentować.',
			wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
		) . '</p>',
	
		'logged_in_as' => '',
	
		'comment_notes_before' => '<p class="comment-notes color3 no-margin text-left f15 medium">' .
		__( 'Twój e-mail nie zostanie opublikowany' ) . ( $req ? $required_text : '' ) .
		'</p>',
	
		'comment_notes_after' => '',
	
		'fields' => apply_filters( 'comment_form_default_fields', array(
	
				'author' =>
				'<div class="form-group comment-form-author col-sm-6 fg-left">' .
				'<input id="author" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
				'" ' . $aria_req . ' placeholder="Imię i nazwisko"/></div>',
	
				'email' =>
				'<div class="form-group comment-form-email col-sm-6 fg-right"> ' .
				'<input id="email" class="form-control" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
				'" ' . $aria_req . ' placeholder="Adres email"/></div>'
			)
		),
	);
	
	?>
	<p class="comments-header f36 color2 font1"><strong><?php echo page_header( 'option', get_field('_page_title_text_add_comment', 'option'), false, '_add_comment', false ); ?></strong></p>
	<div class="comments-form-container"><?php comment_form($args); ?></div>

	<?php if ( have_comments() ) : ?>
		<p class="comments-title f36 color2 font1 bold"><?php echo page_header( 'option', get_field('_page_title_text_comments', 'option'), false, '_comments', false ); ?> <?php //echo get_comments_number() ?></p>

		<ol class="comments-list list-unstyled">
			<?php wp_list_comments( array( 
				'callback' => 'rfswp_comment'
			) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="comment-nav-below" class="comment-navigation" role="navigation">
			<div class="row">
				<div class="col-sm-6">
					<div class="nav-previous btn btn-primary btn-sm"><?php previous_comments_link( '<i class="fa fa-angle-left"></i> Starsze Komentarze' ); ?></div>
				</div>
				<div class="col-sm-6 text-right">
					<div class="nav-next btn btn-primary btn-sm"><?php next_comments_link( 'Nowsze Komentarze <i class="fa fa-angle-right"></i>' ); ?></div>
				</div>
			</div>
		</nav>
		<?php endif;  ?>

	<?php else : ?>
	<p class="comments-title f36 color2 font1 bold"><?php echo page_header( 'option', get_field('_page_title_text_comments', 'option'), false, '_comments', false ); ?></p>
	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
	<?php endif; ?>
	
	

</div><!-- #comments -->

<?php  
// comment item template
function rfswp_comment( $comment, $args, $depth ) { ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body row">

			<?php if( $args['avatar_size'] != 0 ) : ?>
			<div class="comment-avatar pull-left overflow img-circle">
				<?php echo get_avatar( $comment, 120 ); ?>
			</div>
			<?php endif; ?>

			<div class="comment-content overflow">
				<div class="row comment-content-top">
					<div class="col-sm-6">
						<p class="f18 color2 font2 no-margin"><strong><?php echo esc_html( ucfirst( get_comment_author( $comment_ID ) ) ); ?></strong></p>
					</div>
					<div class="col-sm-6 text-right">
						<p class="f14 color2 no-margin">Dodano: <strong><?php echo get_comment_date('d.m.Y'); ?> <?php echo get_comment_time('H:i'); ?></strong></p>
					</div>
				</div>
				<div class="row comment-content-bottom">
					<?php if( $comment->comment_approved == 0 ) : ?>
					<p class="margin-md bold color5">Twój komentarz czeka na zatwierdzenie</p>
					<?php else : ?>
					<p class="margin-md"><?php echo wp_kses(get_comment_text(), array('br'=>array())); ?></p>
					<?php endif; ?>
				</div>
				<?php if( $comment->comment_approved != 0 ) : ?>
				<div class="row text-right">
					<?php
						comment_reply_link( array_merge( $args, array(
							'add_below' => 'div-comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<div class="reply-btn text-uppercase btn btn-primary btn-sm">',
							'after'     => '</div>',
						) ) );
					?>
				</div>
				<?php endif; ?>
			</div>

		</article><!--end comment-body-->

	<?php
	
}
?>
