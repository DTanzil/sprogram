<?php 

/*
Template Name: uuf-form


*/

	get_header();
?>
<script src="https://depts.washington.edu/sprogram/admin/assets/js/formMethods.js"></script>
<script src="https://depts.washington.edu/sprogram/admin/assets/js/jquery.validate.js"></script>
<script src="https://depts.washington.edu/sprogram/admin/assets/js/jquery.mask.js"></script>
<script>
var step = 0;
$(document).ready(function(){
	nextStep('form_controller');
});
function validate_form(){
	jQuery.validator.addMethod("uw_email", function(value, element) {
		  return this.optional(element) || /^[a-zA-Z0-9._%+-]+@uw.edu$/.test(value);
		}, "Please provide a valid UW email");
	return true;//$("#form").validate().form();
}
function formSubmit(){
	getLocationDetails(locations);
	return validate();
}
</script>
<div id="primary">
			<div id="content" role="main" class="container">
			
						
			<div class="row show-grid">
				<div class="span8">
					
					
			<?php while ( have_posts() ) : the_post(); ?>

      <span id="arrow-mark" <?php the_blogroll_banner_style(); ?> ></span>
				
      <?php uw_breadcrumbs(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php apply_filters('italics', get_the_title()); ?></h1>
				</header><!-- .entry-header -->
			
				<div class="entry-content">
					<!-- Start form HTML -->
                    
                    
                    <div class="row">
						<form class="custom" id="form" action="http://depts.washington.edu/sprogram/admin/form_controller/submit" method="POST" onsubmit="return formSubmit()"></form>
					</div>
                    
                    
                    <!-- End form html -->
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
				</div><!-- .entry-content -->
				<!--<footer class="entry-meta">
					<?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
				</footer><!-- .entry-meta -->
			</article><!-- #post-<?php the_ID(); ?> -->

			<?php endwhile; // end of the loop. ?>

				</div>
				<div id="secondary" class="span4 right-bar" role="complementary">
					<div class="stripe-top"></div><div class="stripe-bottom"></div>				
          <div id="sidebar">
          <?php if (is_active_sidebar('homepage-sidebar') && is_front_page()) : dynamic_sidebar('homepage-sidebar'); else: dynamic_sidebar('sidebar'); endif; ?>
          </div>
				</div>
 			 </div>
			</div><!-- #content -->
		</div><!-- #primary -->


<?php get_footer() ?>

