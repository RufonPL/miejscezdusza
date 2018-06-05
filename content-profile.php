<div class="container-fluid page-container profile-page">

	<?php if(rfs_role('subscriber')) : //user ?> 
		<?php require_template_part('user', 'profile'); ?>
	<?php endif; ?>

	<?php if(rfs_role('contributor')) : //user premium ?>
		<?php require_template_part('premium', 'profile'); ?>
	<?php endif; ?>
	
</div>