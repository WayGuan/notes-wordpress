<?php
 /*
 Template Name: bookingspace
 */

?>

<?php get_header(); ?>

<div class="site-content">
   <div id="container" class="wrapper clear">
      <div class="primary">

         <?php while (have_posts()) : the_post(); ?>

            <?php get_template_part('calendar', 'bookingspace'); ?>
            
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
    jQuery('select[name="rangetime1"] option[value="14:00 - 16:00"]').remove();
    jQuery('select[name="rangetime6"] option[value="15:00 - 16:00"]').remove();    
    jQuery('#calendar_booking6').click(function () {
        updateTimeslotOptions(6, parseDate(jQuery('#date_booking6').val()).getDay());
    });
    jQuery('#calendar_booking1').click(function () {
        updateTimeslotOptions(1, parseDate(jQuery('#date_booking1').val()).getDay());
    });

});

function parseDate(dateString) {
    var [day, month, year] = dateString.split(".");
    return new Date(year, month - 1, day);
}

function updateTimeslotOptions(spaceSelection, dayOfWeek) {
    var optionsMap = {
        6: {
            1: ["11:00 AM - 2:00 PM", "3:00 PM - 6:00 PM", "7:00 PM - 8:00 PM"],
            0: ["11:00 AM - 2:00 PM", "3:00 PM - 4:00 PM"]
        },
        default: {
            1: ["10:00 AM - 1:00 PM", "2:00 PM - 5:00 PM", "6:00 PM - 8:00 PM"],
            0: ["10:00 AM - 1:00 PM", "2:00 PM - 4:00 PM"]
        }
    };

    var options = optionsMap[spaceSelection] || optionsMap.default;
    var timeslots = dayOfWeek >= 1 && dayOfWeek <= 4 ? options[1] : options[0];
    var spaceName = "rangetime" + spaceSelection;
    var selectElement = jQuery('select[name=' + spaceName + ']').empty();
    timeslots.forEach(function (slot) {
    var [startTime, endTime] = slot.split(" - ");
    var start24h = convertTo24HourFormat(startTime);
    var end24h = convertTo24HourFormat(endTime);
    var optionValue = start24h + " - " + end24h;
    selectElement.append('<option value="' + optionValue + '">' + slot + '</option>');
});
}

function convertTo24HourFormat(time12h) {
    var [time, period] = time12h.split(" ");
    var [hours, minutes] = time.split(":").map(Number);
    
    if (period === "PM" && hours !== 12) {
        hours += 12;
    } else if (period === "AM" && hours === 12) {
        hours = 0;
    }

    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}

</script>
