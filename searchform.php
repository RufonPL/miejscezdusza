<?php
/**
 * The template for displaying search forms in upBootWP
 *
 * @author Rafał Puczel
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
	<div class="search-inner relative">
		<input type="search" class="search-field color4" placeholder="Szukaj..." value="<?php echo esc_attr(get_search_query()); ?>" name="s"/>
    <button type="submit" class="transition" id="search_submit"><i class="fa fa-search transition color4"></i></button>
	</div>
</form>
