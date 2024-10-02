<?php
 /*
 Template Name: bookingprint
 */

?>

<?php get_header(); ?>

<div class="site-content">
   <div id="container" class="wrapper clear">
      <div class="primary">

         <?php while (have_posts()) : the_post(); ?>

            <?php get_template_part('calendar', 'bookingprint'); ?>
            
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
        "10:00 AM - 1:00 PM", "1:00 PM - 4:00 PM", "4:00 PM - 6:00 PM"
    ];
    var additionalOptionsValues = [
        "10:00 - 13:00", "13:00 - 16:00", "16:00 - 18:00"
    ];

    for (var i = 0; i < counts; i++) {
        selectElement.append('<option value="' + additionalOptionsValues[i] + '">' + additionalOptions[i] + '</option>');
    }
}

</script>
