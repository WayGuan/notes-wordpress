<?php
 /*
 Template Name: bookingequipment
 */

?>

<?php get_header(); ?>

<div class="site-content">
   <div id="container" class="wrapper clear">
      <div class="primary">

         <?php while (have_posts()) : the_post(); ?>

            <?php get_template_part('calendar', 'bookingequipment'); ?>
            
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
jQuery(document).ready(function () {
    jQuery('.hasDatepick').click(function () {
        var id = jQuery(this).attr('id');
        var number = id.match(/\d+/)[0];
        var selectedDate = parseDate(jQuery('#date_booking' + number).val());
        var weekday = selectedDate.getDay();
        updateTimeslotOptions(number, weekday >= 1 && weekday <= 4 ? 8 : 4);
    });
});

function parseDate(dateString) {
    var [day, month, year] = dateString.split(".");
    return new Date(year, month - 1, day);
}

function updateTimeslotOptions(equipSelection, counts) {
    var equipName = "rangetime" + equipSelection;
    var selectElement = jQuery('select[name=' + equipName + ']').empty();

    var additionalOptions = [
        "10:00 AM - 11:00 AM", "11:00 AM - 12:00 PM", "12:00 PM - 1:00 PM", "1:00 PM - 2:00 PM",
        "2:00 PM - 3:00 PM", "3:00 PM - 4:00 PM", "4:00 PM - 5:00 PM", "5:00 PM - 6:00 PM"
    ];
    var additionalOptionsValues = [
        "10:00 - 11:00", "11:00 - 12:00", "12:00 - 13:00", "13:00 - 14:00",
        "14:00 - 15:00", "15:00 - 16:00", "16:00 - 17:00", "17:00 - 18:00"
    ];

    for (var i = 0; i < counts; i++) {
        selectElement.append('<option value="' + additionalOptionsValues[i] + '">' + additionalOptions[i] + '</option>');
    }
}


jQuery('#lab_file_service').change(function () { 
    var cxc_form = new FormData();
    var file = jQuery(document).find('input[type="file"]');
    var cxc_individual_file = file[0].files[0];

    if (cxc_individual_file == undefined) {
        alert('Please include an file.');
    } else {
	var fileName = cxc_individual_file.name;
        var validExtensions = ['stl','obj']; 
	var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1).toLowerCase();        
        
        var maxSize = 5 * 1024 * 1024; // 5MB
        
        if (validExtensions.indexOf(fileNameExt) !== -1 && cxc_individual_file.size <= maxSize) {
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
            alert('Please upload an STL or OBJ model file with a maximum size of 5MB.');
            jQuery('input[type="file"]').val('');
        }
    }
});


</script>
