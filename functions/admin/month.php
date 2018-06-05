<?php 
$currentYear 	= absint(date('Y'));
$currentDay 	= absint(date('d'));
$page 			= 'admin.php?page=md_month_contest';
$active 		= isset($_GET['mdsub']) ? sanitize_text_field($_GET['mdsub']) : 'latest';

$winnersList = winners_list('month', -1, 1);
?>

<div class="wrap md-contests">
	<h2>Miejsce miesiąca</h2>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-1">
			<div id="post-body-content">
				<div class="row md-month">
					<div class="md-nav pull-left">
						<ul class="list-unstyled">
							<li<?php if($active == 'latest') : ?> class="active"<?php endif; ?>><a class="btn btn-primary" href="<?php echo $page ?>&amp;mdsub=latest">Aktualny konkurs - <?php echo monthName( placeMonth(true) ); ?></a></li>
							<li<?php if($active == 'older') : ?> class="active"<?php endif; ?>><a class="btn btn-primary" href="<?php echo $page ?>&amp;mdsub=older">Poprzednie miesiące</a></li>
						</ul>
					</div>
					<div class="md-content overflow">
						<?php if($active == 'latest') : ?>
							<?php $contestPlaces = getContestPlaces($currentYear, placeMonth()); ?>
							<?php if($contestPlaces) : ?>
								<?php $places = $contestPlaces->all; ?>
								<?php if($places) : ?>
								<table class="table table-striped" cellpadding="0" cellspacing="0" border="0">
									<thead>
										<tr>
											<th>Nazwa</th>
											<th>Ilość głosów</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($places as $place) : ?>
										<?php 
										$pid 	= $place['id']; 
										$votes 	= $place['votes'];
										?>
										<tr>
											<td width="70%"><a href="post.php?post=<?php echo absint($pid); ?>&amp;action=edit"><?php echo esc_html(get_the_title($pid)); ?></a></td>
											<td><?php echo absint( $votes ); ?></td>
										</tr>
										<?php endforeach; ?>
										<tr>
											<td colspan="2" class="text-center">
											<p><strong><?php if($currentDay > 14) : ?>Zwycięzca:<?php else : ?>Aktualnie wygrywa:<?php endif; ?></strong></p>
											<a href="post.php?post=<?php echo absint($contestPlaces->winner); ?>&amp;action=edit"><strong><?php echo esc_html(get_the_title($contestPlaces->winner)); ?></strong></a>
											</td>
										</tr>
									</tbody>
								</table>
								<?php endif; ?>
							<?php endif; ?>
						<?php else : ?>
							
							<?php if( $winnersList['years'] ) : ?>
								<?php foreach($winnersList['years'] as $year) : ?>
									<div class="md-older">
										<h2><?php echo $year; ?></h2>
										<?php if( $winnersList['winners'] ) : ?>
											<ul>
											<?php foreach($winnersList['winners'] as $winner) : ?>
												<?php if( $winner->year == $year ) : ?>
												<li><strong><?php echo monthName( $winner->month ); ?></strong> - <?php echo esc_html( get_the_title( $winner->winner ) ) ?></li>
												<?php endif; ?>
											<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>

						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>