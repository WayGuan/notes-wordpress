<?php
 /*
 Template Name: bookingdevice
 */

?>

<?php get_header(); ?>

<div class="site-content">
   <div id="container" class="wrapper clear">
      <div class="primary">

         <?php while (have_posts()) : the_post(); ?>

            <?php get_template_part('calendar', 'bookingdevice'); ?>
            
            <?php
            // If comments are open or we have at least one comment, load up the comment template
            if (comments_open() || get_comments_number()) :
               comments_template();
            endif;
            ?>

         <?php endwhile; // end of the loop. ?>
      </div>
      <?php get_sidebar(); ?>
   </div><!-- #container -->
</div><!-- .site-content -->

<?php get_footer(); ?>
<script>
jQuery('#lab_file_service').change(function () { 
    var cxc_form = new FormData();
    var file = jQuery(document).find('input[type="file"]');
    var cxc_individual_file = file[0].files[0];

    if (cxc_individual_file == undefined) {
        alert('Please include an image.');
    } else {
        var allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'image/svg+xml'];
        var maxSize = 5 * 1024 * 1024; // 5MB
        
        if (allowedTypes.includes(cxc_individual_file.type) && cxc_individual_file.size <= maxSize) {
            cxc_form.append("file", cxc_individual_file);
            cxc_form.append('action', 'cxc_upload_file_data');

            jQuery.ajax({
                type: 'POST',
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                data: cxc_form,
                contentType: false,
                processData: false,
                success: function( cxc_response ){
                    if (cxc_response != '' && cxc_response.success) {
                        jQuery( '#file_url' ).val(cxc_response.cxc_image_url);
                    } else {
                        alert('Image not uploaded');
                    }
                }
            });
        } else {
            alert('Please upload an image file (JPEG, PNG, PDF, or SVG) with a maximum size of 5MB.');
            jQuery('input[type="file"]').val('');
        }
    }
});
</script>
