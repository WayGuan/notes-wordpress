<?php
 /*
 Template Name: bookingscanner
 */

?>

<?php get_header(); ?>

<div class="site-content">
   <div id="container" class="wrapper clear">
      <div class="primary">

         <?php while (have_posts()) : the_post(); ?>

            <?php get_template_part('calendar', 'bookingscanner'); ?>
            
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


    // Simulate the click event for demonstration purposes
        jQuery('#calendar_booking7').click(function () {
            // Assume your calendar plugin updates the date_booking5 textarea on selection
            var selectedDate = jQuery('#date_booking7').val(); // Replace this with the actual selected date

            // Extract the week and weekday from the selected date
      var selectedDateObject = parseDate(selectedDate);
            var weekday = selectedDateObject.getDay(); // Sunday is 0, Monday is 1, ..., Saturday is 6

            // Check if the weekday is between Monday and Thursday
            if (weekday >= 1 && weekday <= 4) {
                // If yes, update the timeslot options from 10:00 AM to 6:00 PM
                updateTimeslotOptions(8);
            } else {
                // If no, update the timeslot options from 10:00 AM to 2:00 PM
                updateTimeslotOptions(4);
            }
        });
    });

    // Function to parse date string and create a Date object
    function parseDate(dateString) {
        var parts = dateString.split(".");
        // Note: Month is zero-based in JavaScript Dates, so we subtract 1
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    function updateTimeslotOptions(counts) {
        jQuery('select[name="rangetime7"]').empty();
            
          var additionalOptions = [
            "10:00 AM - 1:00 PM",
            "1:00 PM - 4:00 PM",
            "4:00 PM - 6:00 PM",

        ];
   var additionalOptionsValues = [
            "10:00 - 13:00",
            "13:00 - 16:00",
            "16:00 - 18:00",

        ];

        // Append each additional option to the select element
        var selectElement = jQuery('select[name="rangetime7"]');
        for (var i = 0; i < counts; i++) {
            var optionText = additionalOptions[i];
       var optionTextValues = additionalOptionsValues[i];
            selectElement.append('<option value="' + optionTextValues + '">' + optionText + '</option>');
        }            
    }
</script>
