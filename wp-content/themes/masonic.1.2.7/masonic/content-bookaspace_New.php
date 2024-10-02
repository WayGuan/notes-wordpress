<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */

/*********************************************************************************/

/*$choice = ""; 
if(isset($_GET['choice'])){
  $choice = filter_var($_GET['choice'], FILTER_SANITIZE_STRING);
}


//only show the form when a valid item was selected 
if(empty($choice)){
  $check_lab = array();
}
else{
  $check_lab = checkLabs($choice);      
} 

$get_branch_name = "";
$get_item_name = "";

foreach($check_lab as $each_result){
  $get_branch_id = $each_result->branch_id;
  $get_item_name  = $each_result->short_name;  
}*/

/********************************************************************************/

?>





<?php

function validate_booking_space($lab_first_name, $lab_last_name, $lab_age, $lab_purpose, $lab_waiver, $lab_card, $lab_phone, $lab_email, $lab_orientation, $lab_time, $lab_date, $lab_name, $lab_branch){
  $str_message = ""; 

  if (empty($lab_phone) || !preg_match('/^[(][0-9]{3}[)][ ][0-9]{3}[-][0-9]{4}$/', $lab_phone))
  {
    $str_message .= "Please enter a valid phone number. e.g. (416) 123-1234\n";
  }   


  if (empty($lab_age) || !preg_match('/^\d*$/', $lab_age))
  {
    $str_message .= "Please enter a valid age.\n";
  }

  //check that the lab is available from the location 
  $check_lab_station = checkLabStation($lab_branch, $lab_name);

  if(count($check_lab_station) == 0){
    $str_message .= "An error has occurred. This lab isn't available from this branch. Please reload the page and try again.\n";  
  }

  $date_today = new Datetime(); 
  $date_selected = new Datetime($lab_date); 
  $date_interval = ceil(($date_selected->format('U') - $date_today->format('U')) / (60*60*24));

  if($date_interval < 3 || $date_interval > 30){
    $str_message .= "Date Unavailable. Please select a later date.\n";
  }  

  $check_library_card = checkLibraryCard($lab_name, $lab_card, $lab_date);

  if(count($check_library_card) > 0){
    $str_message .= "You can only book the same lab once per day with the same library card.\n";
  }
  
  $check_name = checkName($lab_name, $first_name, $last_name, $lab_date);  

  if(count($check_name) > 0){
    $str_message .= "You have already booked this lab for the specified day.\n";
  }

  $check_reservation = checkReservation($lab_name, $lab_date, $lab_time);

  if(count($check_reservation) > 0){
      $str_message .= "This lab has already been booked at the requested time.\n";
  }
  
  

    //extra issue if date is 06-26, 06-27, 06-28 and green room because that is restricted 
    //also 09-15 09-16
    //$date_selected_as_string = $date_selected->format("Y-m-d");
    //if($lab_name == "green_room" && ($date_selected_as_string == "2017-06-26" || $date_selected_as_string == "2017-06-27" || $date_selected_as_string == "2017-06-28" || $date_selected_as_string == "2017-09-15" || $date_selected_as_string == "2017-09-16")){
    //  $str_message .= "The date is unavailable for the Green Room. Please select a different date.\n";
    //}
  

    //if(strlen($lab_phone) != 10 || !is_numeric($lab_phone)){
    //  $str_message .= "Please enter a valid phone number.\n";
    //}
     

  return $str_message; 

}



$time_options = array("10am" => "10:00am - 12:00pm",
                      "11am" => "11:00am - 2:00pm",
                      "12pm" => "12:00pm - 2:00pm",
                      "2pm"  => " 2:00pm - 4:00pm",
                      "4pm"  => " 4:00pm - 6:00pm",
                      "5pm"  => " 5:00pm - 8:00pm",
                      "6pm"  => " 6:00pm - 8:00pm",
                      "10h"  => "10:00am - 11:00am",		    
                      "11h"  => "11:00am - 12:00pm",
                      "12h"  => "12:00pm - 1:00pm",
                      "13h"  => " 1:00pm - 2:00pm",
                      "14h"  => " 2:00pm - 3:00pm",
                      "15h"  => " 3:00pm - 4:00pm",
                      "16h"  => " 4:00pm - 5:00pm",
                      "17h"  => " 5:00pm - 6:00pm",
                      "18h"  => " 6:00pm - 7:00pm",
                      "19h"  => " 7:00pm - 8:00pm");		

$lab_message = "";
$lab_success = "";
$all_labs = getLabs();

$labs = array();
$ccrl_list = array();
$pbrl_list = array();
$special_list = array();
foreach($all_labs as $each_lab){
    $labs[$each_lab->short_name] = $each_lab->full_name;
    if($each_lab->branch_id == 11){
        $special_list['11'][$each_lab->type][$each_lab->short_name] = $each_lab->full_name;
    }

    if($each_lab->branch_id == 7){
        $special_list['7'][$each_lab->type][$each_lab->short_name] = $each_lab->full_name;
    }    
}







if(isset($_POST['submit_space'])){
    //prep mail 
    $first_name = filter_var($_POST['lab_first_name_space'], FILTER_SANITIZE_STRING);
    $branch = filter_var($_POST['branch_space'], FILTER_SANITIZE_STRING);
    $lab_name = filter_var($_POST['lab_name_space'], FILTER_SANITIZE_STRING);
    $lab_date = filter_var($_POST['lab_date_space'], FILTER_SANITIZE_STRING);
    $lab_time_slot = filter_var($_POST['lab_time_space'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['lab_last_name_space'], FILTER_SANITIZE_STRING);
    $lab_age = filter_var($_POST['lab_age_space'], FILTER_SANITIZE_STRING);
    $lab_agreement = filter_var($_POST['lab_agreement_space'], FILTER_SANITIZE_STRING);
    $lab_purpose = filter_var($_POST['lab_purpose_space'], FILTER_SANITIZE_STRING);
    $lab_library_card = filter_var($_POST['lab_library_card_space'], FILTER_SANITIZE_STRING);
    $lab_email = filter_var($_POST['lab_email_space'], FILTER_SANITIZE_EMAIL);
    $lab_telephone = filter_var($_POST['lab_telephone_space'], FILTER_SANITIZE_STRING);
    $lab_orientation = "no";

    $branch_name = "Civic Centre Resource Library";
    $branch_short = "CCRL";
    if($branch == "7"){
      $branch_name = "Pierre Berton Resource Library";
      $branch_short = "PBRL";
    }

    $results = validate_booking_space($first_name, $last_name, $lab_age, $lab_purpose, $lab_agreement, $lab_library_card, $lab_telephone, $lab_email, $lab_orientation, $lab_time_slot, $lab_date, $lab_name, $branch);
    if(strlen($results) == 0){

      if($lab_email == "vplwebmaster@vaughan.ca"){
        $to = "vplwebmaster@vaughan.ca";  
      }
      else{
        $to = "Librarian.Librarian@vaughan.ca";
      }
      
      /* email details */
      $subject = "Request to Reserve a Lab " . $branch_short;						
  
      $message = "The following customer wants to reserve a lab:\n".
      "\n".
      "Name:  ". $first_name . " " . $last_name . "\n\n" .
      "Age:  ". $lab_age . "\n\n".
      "Customer Agreement Form Signed?:  ". $lab_agreement . "\n\n".
      "Purpose:  ". $lab_purpose . "\n\n".
      "Telephone:  ". $lab_telephone . "\n\n".
      "Library Card Number:  ". $lab_library_card . "\n\n".
      "Email Address:  ". $lab_email . "\n\n".
      "Lab:  ". $labs[$lab_name] . "\n\n".
      "Date:  ". $lab_date . "\n\n".
      "Time Slot:  ". $time_options[$lab_time_slot] . "\n\n";      
                  
      $content = $message;
      
      $headers[] = 'From: Vaughan Public Libraries <librarian.librarian@vaughan.ca>';            

    
      wp_mail($to, $subject, $content, $headers);
      insertStatsData(52);  
      
      $lab_success = "Thank you. Your request will be processed and library staff will get back to you shortly.";
    }
    else{
      $lab_message = $results;
    }            
   
/*
          $mail = new PHPMailer();
					$mail->From = 'Librarian.Librarian@vaughan.ca';
					$mail->FromName = 'Vaughan Public Libraries';
					$mail->Subject = $subject;
					$mail->addReplyTo($lab_email);
					$mail->Body = $message;
					//$mail->AddAddress( 'Librarian.Librarian@vaughan.ca' );
          $mail->AddAddress( 'david.heng@vaughan.ca' );					
					$mail->Send();
          echo "mail sent";*/



}


?>

<style type="text/css">
  
 input.input_space {
    padding-top: 0px;
    padding-right: 0px;
    padding-left: 0px;
    padding-bottom: 0px;
    line-height:16px;
  }

 label.label_text {
    font-weight: bold;
 }

</style>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>
  <div class="entry-content">
    <?php if(strlen($lab_message) > 0){ ?>
      <div class="notify-margin notify notify-red">Form Submission Error:<br><?php echo $lab_message; ?></div>
    <?php }else if(strlen($lab_success) > 0){ ?>
      <div class="notify-margin notify notify-green"><?php echo $lab_success; ?></div>
    <?php } ?>

    <?php the_content(); ?>

		
    <form action="/shareit/bookings<?php if(!empty($choice)) echo '?choice=' . $choice; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateBooking();">			
				<fieldset>
          <p>Please complete all fields with an asterisk (*). </p>
					
						
          <p><label for="lab_branch_space" class="label_text">Branch<sup>*</sup>:&nbsp;</label></p> 
          <p>
            <select name="branch_space" title="Local Branch" id="branch_space" onchange="changeLabs()" required>              
              <option value="11">Civic Centre Resource Library</option>              
            </select>
          </p>

           <p><label for="lab_name_space" class="label_text">Space<sup>*</sup>:&nbsp;</label></p> 
           <p>
            <select name="lab_name_space" id="lab_name_space" onchange="limitTime();">
              <option value="">- Select Space -</option>                                      
            </select>          
          </p>

          
          <p><label for="lab_date_space" class="label_text">Date<sup>*</sup>:&nbsp;</label></p>
          <p>
            <input name="lab_date_space" size="50" maxlength="15" class="input_space" onblur="limitTime()" id="lab_date_space"  type="text" required>
          </p>

          <p><label for="lab_time_space" class="label_text">Time Slot<sup>*</sup>:&nbsp;</label></p>
          <p>
            <select name="lab_time_space" id="lab_time_space" required>
              <option value="">- Select Space and Date -</option>
            </select>              
          </p>
          
          <p><label for="lab_first_name_space" class="label_text">First Name<sup>*</sup>:&nbsp;</label></p>
          <p>
            <input type="text" name="lab_first_name_space" class="input_space" id="lab_first_name_space" size="50" required />
          </p>

          <p> <label for="lab_last_name_space" class="label_text">Last Name<sup>*</sup>:&nbsp;</label></p>
          <p>
           <input type="text" name="lab_last_name_space" class="input_space" id="lab_last_name_space" size="50" required />
          </p>

          <p><label for="lab_age_space" class="label_text">Age<sup>*</sup>:&nbsp;</label></p>
          <p>
            <input type="number" name="lab_age_space" id="lab_age_space" class="input_space" size="15" required />
          </p>

          <p><label for="lab_agreement_space" class="label_text">Customer Agreement Form Signed?<sup>*</sup>:&nbsp;</label></p>
          <p>
            <input type="radio" id="lab_agreement_space" value="Yes" name="lab_agreement_space">&nbsp;Yes
            <input type="radio" id="lab_agreement_space" value="No" name="lab_agreement_space" checked>&nbsp;No        
          </p>

          <p><label for="lab_purpose_space" class="label_text">Purpose<sup>*</sup>:&nbsp;</label></p>
          <p>
              <select name="lab_purpose_space" id="lab_purpose_space" required>
                  <option value=""></option>
                  <option value="work">Work</option>
                  <option value="school">School</option>
                  <option value="hobby">Hobby</option>
              </select>              
          </p>

          <p><label for="lab_library_card_space" class="label_text">Library Card<sup>*</sup>:&nbsp;</label></p>
          <p>
             <input type="text" name="lab_library_card_space" id="lab_library_card_space" class="input_space" required />
          </p>
     
          <p><label for="lab_email_space" class="label_text">Email<sup>*</sup>:&nbsp;</label></p>
          <p>
            <input type="email" name="lab_email_space" id="lab_email_space" size="50" class="input_space" required />
          </p>

          <p><label for="lab_telephone_space" class="label_text">Telephone<sup>*</sup>:&nbsp;</label></p>
          <p>
            <input type="text" name="lab_telephone_space" id="lab_telephone_space" class="input_space" placeholder="e.g. (416) 123-1234" required />
          </p>          								              

          <div id="form_errors"></div> 

          <div class="labs_email_reminder">
            Please check your email and spam folder for confirmation of your reservation. Please allow 3 business days for processing.
          </div>
          <p class="text_center">
            <input type="submit" name="submit_space" id="submit_space" value="Submit Request">
          </p>											              
      </fieldset>
    </form>

      <?php
      wp_link_pages(array(
          'before' => '<div class="page-links">' . __('Pages:', 'masonic'),
          'after' => '</div>',
      ));
      ?>
   </div><!-- .entry-content -->


</article><!-- #post-## -->


<script type="text/javascript">
var reserved_times_array = [];
<?php 
    $ccrl_select = "";
    $pbrl_select = "";

    

    foreach ($special_list['11']['space'] as $key => $value){
        $ccrl_select .= "<option value='$key'>" . $value . "</option>";    
    }                    
    
    
    if(isset($special_list['7']) && isset($special_list['7']['space'])){         
        foreach ($special_list['7']['space'] as $key => $value){
            $pbrl_select .= "<option value='$key'>" . $value . "</option>";    
        }                                               
    }

    echo "var labs_ccrl =\"$ccrl_select\";";
    echo "var labs_pbrl =\"$pbrl_select\";";
?>

function changeLabs(default_lab){
    var val = jQuery('#branch_space').val();
    var select = jQuery('#lab_name_space');

    if(val == 7){
        select.empty().append(labs_pbrl);

        
    }
    if(val == 11){
        select.empty().append(labs_ccrl);



    }

    
    //sets the default item as selected
    if (default_lab !== undefined) {
        //set the default lab field 
        jQuery('#lab_name_space').find('option').each(function(i,e){
            if(jQuery(e).val() == default_lab){
                jQuery('#lab_name_space').prop('selectedIndex',i);
            }
        });            
    }    
}

function validateBooking(){
  var error_message = "";


  var age_field = document.getElementById("lab_age_space").val();
  //console.log(age_field);
  if(isNaN(age_field)){
    error_message += "Age must be an number.\n";     
  }

  //check date has been filled in: 
  var date_field = document.getElementById('lab_date_space').val();
  if(date_field.length == 0){
    error_message += "Please fill in the date field.\n";
     
  }

  /*
  if(document.getElementById('lab_file').files.length > 0){
    size = document.getElementById('lab_file').files[0].size; 
    if (size > 2096000) { 
      error_message += 'Your file size exceeds the limit allowed and cannot be uploaded.\n'; 
      
    } 
  }*/
  
  
  if(error_message.length > 0){
    alert("Errors:\n" + error_message);
    return false;
  }
  
  return true;
}


function checkForReservedTime(lab_name, lab_date, lab_time){
  var num_times = reserved_times_array.length;
  var found = false;
  for(var i = 0; i < num_times; i++){
    if(reserved_times_array[i].date == lab_date && reserved_times_array[i].lab == lab_name && reserved_times_array[i].time == lab_time){
      found = true;
      break;
    }
  }
  return found;
}			



function limitTime()
{
  var chosen_lab = jQuery("#lab_name_space").val();

  var datepicker = jQuery('#lab_date_space').data('Zebra_DatePicker');

  
  // limit date 
  if(chosen_lab == "green_room")
  {    
      datepicker.update({ 
          direction: [3, 27],
          disabled_dates:  ['* * * 1',
                            '01 01 2019',
                            '18 02 2019',
                            '19 04 2019',
                            '21 04 2019',
                            '22 04 2019',
                            '20 05 2019',
                            '01 07 2019',
                            '05 08 2019',
                            '02 09 2019',
                            '14 10 2019',
                            '24 12 2019',
                            '25 12 2019',
                            '26 12 2019',
                            '31 12 2019',
                            '01 01 2020']});
  }
  else
  { 
      datepicker.update({ 
        direction: [3, 27],
        disabled_dates:['* * * 1',
                        '01 01 2019',
                        '18 02 2019',
                        '19 04 2019',
                        '21 04 2019',
                        '22 04 2019',
                        '20 05 2019',
                        '01 07 2019',
                        '05 08 2019',
                        '02 09 2019',
                        '14 10 2019',
                        '24 12 2019',
                        '25 12 2019',
                        '26 12 2019',
                        '31 12 2019',
                        '01 01 2020']});
  }

	//For non 3D Printing stations
	var weekendTime = [{value: "10am", text: "10:00am - 12:00pm"},
	                   {value: "12pm", text: "12:00pm - 2:00pm"},
	                   {value: "2pm",  text: " 2:00pm - 4:00pm"}];
	
	//For non 3D Printing Stations
	var weekdayTime = [{value: "10am", text: "10:00am - 12:00pm"},
	                   {value: "12pm", text: "12:00pm - 2:00pm"},
	                   {value: "2pm",  text: " 2:00pm - 4:00pm"},
	                   {value: "4pm",  text: " 4:00pm - 6:00pm"},
	                   {value: "6pm",  text: " 6:00pm - 8:00pm"}];
    
    
  //special green room times for august documentary
  var augustGreenRoomTimes = [{value: "10am", text: "10:00am - 12:00pm"},
	                            {value: "12pm", text: "12:00pm - 2:00pm"}];

	//For 3D Printing Stations 	
	var weekdayTime_3d_stations = [{value: "11am", text: "11:00am - 2:00pm"},		    
		                             {value: "5pm",  text: " 5:00pm - 8:00pm"}];

	var weekendTime_3d_stations = [{value: "11am", text: "11:00am - 2:00pm"}];
  
  //For Oculus Rift
	var weekdayTime_oculus_stations =  [{value: "10h", text: "10:00am - 11:00am"},		    
                                      {value: "11h", text: "11:00am - 12:00pm"},
                                      {value: "12h", text: "12:00pm - 1:00pm"},
                                      {value: "13h", text: " 1:00pm - 2:00pm"},
                                      {value: "14h", text: " 2:00pm - 3:00pm"},
                                      {value: "15h", text: " 3:00pm - 4:00pm"},
                                      {value: "16h", text: " 4:00pm - 5:00pm"},
                                      {value: "17h", text: " 5:00pm - 6:00pm"},
                                      {value: "18h", text: " 6:00pm - 7:00pm"},
                                      {value: "19h", text: " 7:00pm - 8:00pm"}];

	var weekendTime_oculus_stations =  [{value: "10h", text: "10:00am - 11:00am"},		    
                                      {value: "11h", text: "11:00am - 12:00pm"},
                                      {value: "12h", text: "12:00pm - 1:00pm"},
                                      {value: "13h", text: " 1:00pm - 2:00pm"},
                                      {value: "14h", text: " 2:00pm - 3:00pm"},
                                      {value: "15h", text: " 3:00pm - 4:00pm"}];  


	//console.log(jQuery('#LabDate').val());	 
	var chosen_date = jQuery('#lab_date_space').val();

	//if the date hasn't been chosen yet, we can just exit immediately.
	if(chosen_date == ""){
		return; 
	}

	var chosen_date_obj = new Date(chosen_date); 
	var day = chosen_date_obj.getDay();
	//console.log(day);
	//we are considering weekdays as tues - thurs and weekends as fri - sun as per Kristin 
	var isWeekend = (day == 4) || (day == 5) || (day == 6);  
	
	//clear all options from the start time select box 
	var select_time_slot_erase = document.getElementById("lab_time_space");
	var length = select_time_slot_erase.options.length;
	for (i = 0; i < length; i++) {
		select_time_slot_erase.options[0] = null;
	}
	
	var select_time_slot = document.getElementById("lab_time_space");
  var	lab_choice = jQuery("#lab_name_space");		
	var option = "";
  var i = 0;
  	

	//Populate the time slot options. Different for 3D Printing Stations. 
	if(lab_choice.val() == "3d_station_1" || lab_choice.val() == "3d_station_2" || lab_choice.val() == "3d_station_3" || lab_choice.val() == "carving_station")
  {
  		if(isWeekend)
      {
          il = weekendTime_3d_stations.length;
          for (; i < il; i += 1) 
          {
              option = document.createElement('option');
              option.setAttribute('value', weekendTime_3d_stations[i].value);
              option.appendChild(document.createTextNode(weekendTime_3d_stations[i].text));
              select_time_slot.appendChild(option);
          }      
      }
      else
      {
          il = weekdayTime_3d_stations.length;
          for (; i < il; i += 1) 
          {
              option = document.createElement('option');
              option.setAttribute('value', weekdayTime_3d_stations[i].value);
              option.appendChild(document.createTextNode(weekdayTime_3d_stations[i].text));
              select_time_slot.appendChild(option);
          }
      }
	}
  else if(lab_choice.val() == "oculus_rift")
  {
  		if(isWeekend)
      {
          il = weekendTime_oculus_stations.length;
          for (; i < il; i += 1) {
              option = document.createElement('option');
              option.setAttribute('value', weekendTime_oculus_stations[i].value);
              option.appendChild(document.createTextNode(weekendTime_oculus_stations[i].text));
              select_time_slot.appendChild(option);
          }      
      }
      else
      {
          il = weekdayTime_oculus_stations.length;
          for (; i < il; i += 1) {
              option = document.createElement('option');
              option.setAttribute('value', weekdayTime_oculus_stations[i].value);
              option.appendChild(document.createTextNode(weekdayTime_oculus_stations[i].text));
              select_time_slot.appendChild(option);
          }
      }    		
	}
	else
  {	
    if(isWeekend)
    {
  			il = weekendTime.length;
  			for (; i < il; i += 1) 
        {
  					if(!checkForReservedTime(lab_choice.val(), chosen_date, weekendTime[i].value))
            {
      					option = document.createElement('option');
      					option.setAttribute('value', weekendTime[i].value);
      					option.appendChild(document.createTextNode(weekendTime[i].text));
      					select_time_slot.appendChild(option);
            }
  			}
		}
		else
    {		
  			il = weekdayTime.length;
  			for (; i < il; i += 1) {
  					if(!checkForReservedTime(lab_choice.val(), chosen_date, weekdayTime[i].value)){
  					option = document.createElement('option');
  					option.setAttribute('value', weekdayTime[i].value);
  					option.appendChild(document.createTextNode(weekdayTime[i].text));		
  					select_time_slot.appendChild(option);
  					}
  			}		
		}
  }
}

jQuery(document).ready(function() {
  <?php 
    $reserved_times = getReservedTimes();
    foreach($reserved_times as $rt){
  ?>
    var dict = {date: '<?php echo $rt->date;?>', lab: '<?php echo $rt->lab_name;?>', time: '<?php echo $rt->time_slot;?>'};
    reserved_times_array.push(dict);
  <?php    
    }
  ?>  
    //update the lab options
   
    changeLabs("<?php echo $get_item_name; ?>");

	// date picker restriction
  // these include dates due to repairs and general unavailability.
	jQuery('#lab_date_space').Zebra_DatePicker({ 
		direction: [3, 27],
		disabled_dates:['* * * 1',
                    '01 01 2019',
                    '18 02 2019',
                    '19 04 2019',
                    '21 04 2019',
                    '22 04 2019',
                    '20 05 2019',
                    '01 07 2019',
                    '05 08 2019',
                    '02 09 2019',
                    '14 10 2019',
                    '24 12 2019',
                    '25 12 2019',
                    '26 12 2019',
                    '31 12 2019',
                    '01 01 2020']});       
});


window.onload = function() {
    //if they inputted a choice, we have to focus on the form and the date field in particular 
 
    if (window.location.search.indexOf('choice=') > -1) {

      //focus on the farthest field down then back to the date field 
      document.getElementById('lab_telephone_space').focus();      
 
      var lab_date_field = document.getElementById('lab_date_space').focus();      
 
      
    }     
}
</script>