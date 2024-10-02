<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */
//require_once("C:\\xampp\\htdocs\\kint\\kint.class.php");
//require_once ('/var/www/vaughan/PHPMailer-master/class.phpmailer.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . "/phpmailer/class.phpmailer.php");


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

function filament_to_word($filament){

  //the pva option is slightly different so this is an exception
  if($filament == "pva_only_for_support"){
    $word_array = "PVA - Only For Support";
    return $word_array;
  }

  //this function just makes the output of the filament more readable (removing the underscore)
  $word_array = explode("_", $filament);
  
  return implode(" ", $word_array);
}

function validate_booking_service($lab_first_name, $lab_last_name, $lab_age, $lab_purpose, $lab_waiver, $lab_certification, $lab_card, $lab_phone, $lab_email, $lab_orientation, $lab_time, $lab_date, $lab_name, $lab_branch, $file_location_name, $file_error, $file_size){
  $str_message = ""; 

  // if(strlen($lab_phone) != 10 || !is_numeric($lab_phone)){
  //   $str_message .= "Please enter a valid phone number.\n";
  // }

  if (empty($lab_phone) || !preg_match('/^[(][0-9]{3}[)][ ][0-9]{3}[-][0-9]{4}$/', $lab_phone))
  {
      $str_message .= "Please enter a valid phone number.\n";
  }


  //check that the lab is available from the location 
  $check_lab_station = checkLabStation($lab_branch, $lab_name);

  if(count($check_lab_station) == 0){
    $str_message .= "An error has occurred. This lab isn't available from this branch. Please reload the page and try again.\n";  
  }

  $date_today = new Datetime(); 
  $date_selected = new Datetime($lab_date); 
  $date_interval = ceil(($date_selected->format('U') - $date_today->format('U')) / (60*60*24));

  //only apply to CCRL b/c it needs a date
  if($lab_branch == 11 && ($date_interval < 3 || $date_interval > 30)){
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

  /////////////////////////////////////////////////////////////////////////
  // There is no $lab_time being used.
  // J Yang, 01-07-2019
  // Comment out the following if condition.
  ////////////////////////////////////////////////////////////////////////

  // if($lab_name == "3d_station_1"){
  //   $check_reservation = checkReservation($lab_name, $lab_date, $lab_time);
  //   if(count($check_reservation) > 0){
  //       $str_message .= "This lab has already been booked at the requested time.\n";
  //   }  
  // }
  
  
  if($file_error == 1 || $file_size > 2000000){
    $str_message .= "The file size exceeds the 2MB limit.\n";
  }   

  if(!empty($file_location_name) && 
      strpos($file_location_name, ".stl") === false && 
      strpos($file_location_name, ".obj") === false && 
      strpos($file_location_name, ".thing") === false && 
      strpos($file_location_name, ".zip") === false && 
      strpos($file_location_name, ".rar") === false)
  {
      $str_message .= "The file type must be stl, obj, thing, zip, or rar.\n";
  }  

  return $str_message; 

}


$list_of_materials =  array("plywood_8x12_1x4" => "Plywood-G1S/Oak (8x12 1/4in Thickness)",
                            "plywood_8x12_1x2" => "Plywood-G1S/Oak (8x12 1/2in Thickness)",
                            "hdpe_yellowblack_6x6_1x4" => "Two-Colour HDPE Yellow on Black (Plastic)",
                            "green_pvc_8x12_1x8" => "Green Expanded PVC Sheet (Plastic)",
                            "use_my_own_material" => "Use My Own Material");     


$resolution_options = array("0.2mm"  => "Standard (0.2mm)",
                            "0.15mm" => "High (0.15mm)",
                            "0.1mm"  => "Perfect (0.1mm)");


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


if(isset($_POST['submit_service'])){
    //prep mail 
    $first_name = filter_var($_POST['lab_first_name_service'], FILTER_SANITIZE_STRING);
    $branch = filter_var($_POST['branch_service'], FILTER_SANITIZE_STRING);
    $lab_name = filter_var($_POST['lab_name_service'], FILTER_SANITIZE_STRING);    
    $lab_time_slot = "N/A";
    $last_name = filter_var($_POST['lab_last_name_service'], FILTER_SANITIZE_STRING);
    $lab_age = filter_var($_POST['lab_age_service'], FILTER_SANITIZE_STRING);
    $lab_agreement = filter_var($_POST['lab_agreement_service'], FILTER_SANITIZE_STRING);
    $lab_certification = filter_var($_POST['lab_certification_service'], FILTER_SANITIZE_STRING);
    $lab_purpose = filter_var($_POST['lab_purpose_service'], FILTER_SANITIZE_STRING);
    $lab_library_card = filter_var($_POST['lab_library_card_service'], FILTER_SANITIZE_STRING);
    $lab_email = filter_var($_POST['lab_email_service'], FILTER_SANITIZE_EMAIL);
    $lab_telephone = filter_var($_POST['lab_telephone_service'], FILTER_SANITIZE_STRING);

    $branch_name = "Civic Centre Resource Library";
    $branch_short = "CCRL";
    if($branch == "7"){
      $branch_name = "Pierre Berton Resource Library";
      $branch_short = "PBRL";
    }


         

    $lab_date = "";
    if(isset($_POST['lab_date_service'])){
      $lab_date = filter_var($_POST['lab_date_service'], FILTER_SANITIZE_STRING);
    }

    $lab_infill_percentage = "";
    if(isset($_POST['lab_infill_percentage_service'])){
      $lab_infill_percentage = filter_var($_POST['lab_infill_percentage_service'], FILTER_SANITIZE_STRING);      
    }
    
    $lab_supports_required = "";
    if(isset($_POST['lab_supports_required_service'])){
      $lab_supports_required = filter_var($_POST['lab_supports_required_service'], FILTER_SANITIZE_STRING);      
    }

    $lab_filament_colour = "";
    if(isset($_POST['lab_filament_colour_service'])){
      $lab_filament_colour = filter_var($_POST['lab_filament_colour_service'], FILTER_SANITIZE_STRING);      
    }


    $lab_filament_two_colour = "";
    if(isset($_POST['lab_filament_two_colour_service'])){
      $lab_filament_two_colour = filter_var($_POST['lab_filament_two_colour_service'], FILTER_SANITIZE_STRING);      
    }
    
    
    $lab_carvey_link = "";
    if(isset($_POST['lab_carvey_link_service'])){
      $lab_carvey_link = filter_var($_POST['lab_carvey_link_service'], FILTER_SANITIZE_STRING);      
    }

    $lab_resolution = "";
    if(isset($_POST['lab_resolution_service'])){
      $lab_resolution = filter_var($_POST['lab_resolution_service'], FILTER_SANITIZE_STRING);      
    }

    $lab_comment = "";
    if(isset($_POST['lab_comment_service'])){
      $lab_comment = filter_var($_POST['lab_comment_service'], FILTER_SANITIZE_STRING);      
    }



    $lab_orientation = "no";

    $file_location_name = "";
    $file_error = "";
    $file_size = "";

    if(!empty($_FILES['lab_file_service']['name'])){

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $uploadedfile = $_FILES['lab_file_service'];
        $file_location = $_FILES['lab_file_service']['tmp_name'];
        $file_location_name = $_FILES['lab_file_service']['name'];
        $file_error = $_FILES['lab_file_service']['error'];
        $file_size = $_FILES['lab_file_service']['size'];
    }

    $results = validate_booking_service($first_name, $last_name, $lab_age, $lab_purpose, $lab_agreement, $lab_certification, $lab_library_card, $lab_telephone, $lab_email, $lab_orientation, $lab_time_slot, $lab_date, $lab_name, $branch, $file_location_name, $file_error, $file_size);
    if(strlen($results) == 0){
      /* email details */
      $subject = "Request to Reserve a Lab " . $branch_short;						
  
      $message = "The following customer wants to reserve a lab:\n"."\n".
      "Name: ". $first_name . " " . $last_name . "\n\n" .
      "Age: ". $lab_age . "\n\n".
      "Customer Agreement Form Signed?: ". $lab_agreement . "\n\n".
      "Have you taken the required certification?: ". $lab_certification . "\n\n".
      "Purpose: ". $lab_purpose . "\n\n".
      "Telephone: ". $lab_telephone . "\n\n".
      "Library Card Number: ". $lab_library_card . "\n\n".
      "Email Address: ". $lab_email . "\n\n".
      "Branch: ". $branch_name . "\n\n".
      "Lab: ". $labs[$lab_name] . "\n\n";
      

      if(!empty($lab_date) && strpos($lab_name, "3d_station_1") !== false){
        $message .= "Date: ". $lab_date . "\n\n";
      }

       if(!empty($lab_infill_percentage) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Infill Percentage: ". $lab_infill_percentage . "\n\n";
      }

      if(!empty($lab_supports_required) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Supports Required: ". $lab_supports_required . "\n\n";
      }

      if(!empty($lab_filament_colour) && strpos($lab_name, "carvey") === false){
        $message .= "Filament Colour: ". filament_to_word($lab_filament_colour) . "\n\n";        
      }
      
      
      if(!empty($lab_resolution) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Filament Two Colour: ". filament_to_word($lab_filament_two_colour) . "\n\n";   
        $message .= "Resolution: ". $resolution_options[$lab_resolution] . "\n\n";        
      }      


      if(strpos($lab_name, "carvey") !== false){        
        $lab_material = filter_var($_POST['lab_material_service'], FILTER_SANITIZE_STRING);
        $material_name = $list_of_materials[$lab_material];
        
        $message .= "Material: ". $material_name . "\n\n";
        $message .= "Comment: \n". $lab_comment . "\n\n";
      }

      if(!empty($lab_carvey_link) && strpos($lab_name, "carvey") !== false){                
        $message .= "Carvey Link: ". $lab_carvey_link . "\n\n";
      }  

      if(!empty($lab_comment) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Comment: \n". $lab_comment . "\n\n";
      }      
      

      $content = $message;
      
      //d($message);
      $headers[] = 'From: Vaughan Public Libraries <librarian.librarian@vaughan.ca>';            
      //$to = "david.heng@vaughan.ca";  
      $to = "Librarian.Librarian@vaughan.ca";      

      if($lab_email == "vplwebmaster@vaughan.ca"){
          $to = "vplwebmaster@vaughan.ca";  
      }										
      
      //var_dump($message);

      $mail = new PHPMailer();
      $mail->From = 'Librarian.Librarian@vaughan.ca';
      $mail->FromName = 'Vaughan Public Libraries';
      $mail->Subject = $subject;
      $mail->addReplyTo($lab_email);
      $mail->Body = $message;
      //$mail->AddAddress( 'Librarian.Librarian@vaughan.ca' );
      $mail->AddAddress( $to );			

      //only for 3d printers
      if(isset($file_location) && $lab_name != "carvey"){		
        $mail->AddAttachment($file_location, $file_location_name);        
      }
      $mailSuccess = $mail->Send();
      //insertStatsData(52);  
      $lab_success = "Thank you. Your request will be processed and library staff will get back to you shortly.";
    }
    else{
      $lab_message = $results;
    }            
  
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
					
						
          <p> <label for="lab_branch_service" class="label_text">Branch<sup>*</sup>:&nbsp;</label></p>
          <p> 
              <select name="branch_service" title="Local Branch" id="branch_service" onchange="changeLabs()" required>              
                  <option value="11" <?php if($get_branch_id == "11") echo "selected"; ?>>Civic Centre Resource Library</option>
                  <option value="7"  <?php if($get_branch_id == "7") echo "selected"; ?>>Pierre Berton Resource Library</option>
              </select>
          </p>

          <p> <label for="lab_name_service" class="label_text">Services<sup>*</sup>:&nbsp;</label></p>

          <p>
              <select name="lab_name_service" id="lab_name_service" onchange="changeFields()">
                  <option value="">- Select Lab -</option>                                      
              </select>          
          </p>
          
          <p id="p_date_field"> <label for="lab_date_service" class="label_text">Date<sup>*</sup>:&nbsp;</label><br/>
              <input name="lab_date_service" size="15" maxlength="15" class="input_space" id="lab_date_service"  type="text" required>
          </p>           
          
          <p> <label for="lab_first_name_service" class="label_text">First Name<sup>*</sup>:&nbsp;</label> </p>
          <p>
            <input type="text" name="lab_first_name_service" class="input_space" id="lab_first_name_service" size="50" required />
          </p>

          <p> <label for="lab_last_name_service" class="label_text">Last Name<sup>*</sup>:&nbsp;</label> </p>
          <p>
              <input type="text" name="lab_last_name_service" class="input_space" id="lab_last_name_service" size="50" required />
          </p>

          <p> <label for="lab_age_service" class="label_text">Age<sup>*</sup>:&nbsp;</label> </p>
          <p>
              <input type="number" name="lab_age_service" class="input_space" id="lab_age_service" size="50" required />
          </p>

          <p> <label for="lab_agreement_service" class="label_text">Customer Agreement Form Signed?<sup>*</sup>:&nbsp;</label> </p>
          <p> 
              <input type="radio" id="lab_agreement_service" value="Yes" name="lab_agreement_service">&nbsp;Yes
              <input type="radio" id="lab_agreement_service" value="No" name="lab_agreement_service" checked>&nbsp;No        
          </p>

          <p> <label for="lab_certification_service" class="label_text">Have you taken the required certification?<sup>*</sup>:</label> </p>
          <p>
              <input type="radio" id="lab_certification_service" value="Yes" name="lab_certification_service">&nbsp;Yes
              <input type="radio" id="lab_certification_service" value="No" name="lab_certification_service" checked>&nbsp;No        
          </p>

          <p> <label for="lab_purpose_service" class="label_text">Purpose<sup>*</sup>:&nbsp;</label> </p>
          <p>
              <select name="lab_purpose_service" id="lab_purpose_service" required>
                  <option value=""> </option>

                  <option value="work">Work</option>

                  <option value="school">School</option>
                  
                  <option value="hobby">Hobby</option>
              </select>              
          </p>

          <p> <label for="lab_library_card_service" class="label_text">Library Card<sup>*</sup>:&nbsp;</label> </p>
          <p>
              <input type="text" name="lab_library_card_service" class="input_space" id="lab_library_card_service" required />
          </p>
     
          <p> <label for="lab_email_service" class="label_text">Email<sup>*</sup>:&nbsp;</label> </p>
          <p>
            <input type="email" name="lab_email_service" class="input_space" id="lab_email_service" size="50" required />
          </p>

          <p> <label for="lab_telephone_service" class="label_text">Telephone<sup>*</sup>:&nbsp;</label> </p>
          <p>
              <input type="text" name="lab_telephone_service" class="input_space" id="lab_telephone_service" placeholder=" e.g. (416) 123-4567" required />
          </p>

          <p id="p_material"> <label for="lab_material_service" class="label_text">Material<sup>*</sup>:&nbsp;</label><br/>
            <select name="lab_material_service" id="lab_material_service" required>
              <option value="">-- Select Material --</option>
              <option value="plywood_8x12_1x4">Plywood-G1S/Oak (1/4in Thickness)</option>
              <option value="plywood_8x12_1x2">Plywood-G1S/Oak (1/2in Thickness)</option>              
              <option value="hdpe_yellowblack_6x6_1x4">Two-Colour HDPE Yellow on Black (Plastic)</option>             
              <option value="green_pvc_8x12_1x8">Green Expanded PVC Sheet (plastic)</option>
              <option value="use_my_own_material">Use My Own Material</option>
            </select>             
          </p>

           <p id="p_infill_percentage"> <label for="lab_infill_percentage_service" class="label_text">Infill Percentage<sup>*</sup>:&nbsp;</label><br/>
            <select name="lab_infill_percentage_service" id="lab_infill_percentage_service" required>
              <option value=""> </option>
              <option value="5">5</option>
              <option value="10">10</option>              
              <option value="15">15</option>
              <option value="20" selected>20</option>
              <option value="25">25</option>              
              <option value="30">30</option>
              <option value="35">35</option>
              <option value="40">40</option>              
              <option value="45">45</option>
              <option value="50">50</option>
              <option value="55">55</option>              
              <option value="60">60</option>              
            </select>              
          </p>

           <p id="p_supports_required"> <label for="lab_supports_required_service" class="label_text">Supports required?<sup>*</sup>:&nbsp;</label><br/>           
            <input type="radio" id="lab_supports_required_service" value="Yes" name="lab_supports_required_service">&nbsp;Yes
            <input type="radio" id="lab_supports_required_service" value="No" name="lab_supports_required_service" checked>&nbsp;No                              
          </p>

           <p id="p_filament_colour"> <label for="lab_filament_colour_service" class="label_text">Filament Colour<sup>*</sup>:&nbsp;</label><br/> 
            <select name="lab_filament_colour_service" id="lab_filament_colour_service" required>
              <option value=""> Select Colour </option>
            </select>              
          </p>                    

           <p id="p_filament_two_colour"> <label for="lab_filament_two_colour_service" class="label_text">Filament Two Colour<sup>*</sup>:&nbsp;</label><br/>
            <select name="lab_filament_two_colour_service" id="lab_filament_two_colour_service" required>
              <option value=""> Select Colour </option>
            </select>              
          </p>        

           <p id="p_resolution"> <label for="lab_resolution_service" class="label_text">Resolution<sup>*</sup>:&nbsp;</label><br/>
            <select name="lab_resolution_service" id="lab_resolution_service" required>              
              <option value="0.2mm">Standard (0.2mm)</option>

              <option value="0.15mm" selected>High (0.15mm)</option>
              
              <option value="0.1mm">Perfect (0.1mm)</option>
            </select>              
          </p>

           <p id="p_comment"> <label for="lab_comment_service" class="label_text">Comments / Instructions<sup></sup></label><br/> 
              <textarea id="lab_comment_service" name="lab_comment_service" rows="3" cols="50"></textarea>
            </p>

           <p id="p_lab_file"> <label for="lab_file_service" class="label_text">For 3D Printing bookings. You can upload either a .stl, .obj, or .thing. You can upload multiple files in .zip, or .rar format (Max size: 2MB)&nbsp;</label> 
            <br><input type="file" name="lab_file_service" class="input_space" id="lab_file_service">
          </p>											              

          <p id="p_carvey_link_field"> <label for="lab_carvey_link_service" class="label_text">Carvey: Add Easel Editor Link:&nbsp;</label><br/>
           <input type="text" name="lab_carvey_link_service" class="input_space" id="lab_carvey_link_service" size="50" />
          </p>

          <div id="form_errors"></div> 

          <div class="labs_email_reminder">
            Please check your email and spam folder for confirmation of your reservation. Please allow 3 business days for processing.
          </div>
          <p class="text_center">
            <input type="submit" name="submit_service" id="submit_service" value="Submit Request">
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


<?php 
    $ccrl_select = "";
    $pbrl_select = "";

    

    foreach ($special_list['11']['service'] as $key => $value){
        $ccrl_select .= "<option value='$key'>" . $value . "</option>"; 


    }                    
    
    
    if(isset($special_list['7']) && isset($special_list['7']['service'])){         
        foreach ($special_list['7']['service'] as $key => $value){
            $pbrl_select .= "<option value='$key'>" . $value . "</option>";    
        }                                               
    }

    echo "var labs_ccrl =\"$ccrl_select\";";
    echo "var labs_pbrl =\"$pbrl_select\";";
?>

var filament_3d_printer = "<option value='White'>White</option>" + 
                          "<option value='Black'>Black</option>" + 
                          "<option value='Grey'>Grey</option>"   +
                          "<option value='Blue'>Blue</option>"   +
                          "<option value='Red'>Red</option>";

//var filament_colour = "<option value='white'>White</option><option value='black'>Black</option><option value='grey'>Grey</option><option value='blue'>Blue</option><option value='red'>Red</option><option value='brown'>Brown</option><option value='yellow'>Yellow</option><option value='orange'>Orange</option><option value='pink'>Pink</option><option value='purple'>Purple</option><option value='green'>Green</option><option value='translucent_yellow'>Translucent Yellow</option><option value='translucent_purple'>Translucent Purple</option><option value='translucent_red'>Translucent Red</option><option value='translucent_blue'>Translucent Blue</option><option value='translucent_orange'>Translucent Orange</option>";
//var filament_two_colour = "<option value='white'>White</option><option value='black'>Black</option><option value='grey'>Grey</option><option value='blue'>Blue</option><option value='red'>Red</option><option value='brown'>Brown</option><option value='yellow'>Yellow</option><option value='orange'>Orange</option><option value='pink'>Pink</option><option value='purple'>Purple</option><option value='green'>Green</option><option value='translucent_yellow'>Translucent Yellow</option><option value='translucent_purple'>Translucent Purple</option><option value='translucent_red'>Translucent Red</option><option value='translucent_blue'>Translucent Blue</option><option value='translucent_orange'>Translucent Orange</option><option value='pva_only_for_support'>PVA - Only for Support</option>";

var filament_colour = "<option value='White'>White</option>"   + 
                      "<option value='Black'>Black</option>"   + 
                      "<option value='Silver'>Silver</option>" + 
                      "<option value='Blue'>Blue</option>"     + 
                      "<option value='Red'>Red</option>"       +
                      "<option value='Green'>Green</option>"   +
                      "<option value='Yellow'>Yellow</option>" +
                      "<option value='Translucent'>Translucent</option>";

var filament_two_colour = "<option value='N/A'>N/A</option>"                 +
                          "<option value='White'>White</option>"             +
                          "<option value='Black'>Black</option>"             +
                          "<option value='Silver'>Silver</option>"           +
                          "<option value='Blue'>Blue</option>"               +
                          "<option value='Red'>Red</option>"                 +
                          "<option value='Green'>Green</option>"             +
                          "<option value='Yellow'>Yellow</option>"           + 
                          "<option value='Translucent'>Translucent</option>" + 
                          "<option value='pva_only_for_support'>PVA - Only for Support</option>";


function changeLabs(default_lab){
  
    var val = jQuery('#branch_service').val();
    var select = jQuery('#lab_name_service');

    var filament = jQuery('#lab_filament_colour_service');

    var filament_two = jQuery('#lab_filament_two_colour_service');

    if(val == 7){

        select.empty().append(labs_pbrl);
          
        filament.empty().append(filament_colour);
        filament_two.empty().append(filament_two_colour);
 
    }
    if(val == 11){
        //update the dates
        var datepicker1 = jQuery('#lab_date_service').data('Zebra_DatePicker');

        datepicker1.update({ 
          direction: [3, 27],
          disabled_dates: ['* * * 1',
            <?php 
            $reserved_times = getReservedTimes3DPrinter();
            
            foreach($reserved_times as $rt){
              $rdate = new DateTime($rt->date);
              echo "'" . $rdate->format("d m Y") . "',";
            }
            ?>

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
            '01 01 2020']
        });        

        select.empty().append(labs_ccrl);

        filament.empty().append(filament_3d_printer);
    }


    //sets the default item as selected
    if (default_lab !== undefined) {
        //set the default lab field 
        jQuery('#lab_name_service').find('option').each(function(i,e){
            if(jQuery(e).val() == default_lab){
                jQuery('#lab_name_service').prop('selectedIndex',i);
            }
        });            
    }

    changeFields();
}

function changeFields(){
  var select = jQuery('#lab_name_service').val();
  var filament = jQuery('#lab_filament_colour_service');
  var filament_two = jQuery('#lab_filament_two_colour_service');
  
  
  if(select == "carvey"){

    jQuery('#p_date_field').addClass("hide_field");
    jQuery('#lab_date_service').removeAttr("required");  

    jQuery('#p_infill_percentage').addClass("hide_field");
    jQuery('#lab_infill_percentage_service').removeAttr("required");

    jQuery('#p_supports_required').addClass("hide_field");        
    jQuery('#lab_supports_required_service').removeAttr("required");  

    jQuery('#p_resolution').addClass("hide_field");
    jQuery('#lab_resolution_service').removeAttr("required");  


    jQuery('#p_material').removeClass("hide_field");
    jQuery('#lab_material_service').attr("required", true);

    jQuery('#p_lab_file').addClass("hide_field");    
    jQuery('#p_filament_colour').addClass("hide_field");        
    jQuery('#p_filament_two_colour').addClass("hide_field");   
    jQuery('#lab_filament_two_colour_service').removeAttr("required");
    jQuery('#p_carvey_link_field').removeClass("hide_field");       

  }
  else if(select =="3d_station_3"){    
    
    jQuery('#p_date_field').addClass("hide_field");
    jQuery('#lab_date_service').removeAttr("required");  

    jQuery('#p_infill_percentage').removeClass("hide_field");
    jQuery('#lab_infill_percentage_service').attr("required", true);

    jQuery('#p_supports_required').removeClass("hide_field");        
    jQuery('#lab_supports_required_service').attr("required", true);    
             
    jQuery('#p_resolution').removeClass("hide_field");
    jQuery('#lab_resolution_service').attr("required", true);  

    jQuery('#p_comment').removeClass("hide_field");
         

    jQuery('#p_material').addClass("hide_field");
    jQuery('#lab_material_service').removeAttr("required");  

    jQuery('#p_lab_file').removeClass("hide_field");
    jQuery('#p_filament_colour').removeClass("hide_field");
    jQuery('#p_filament_two_colour').removeClass("hide_field");

    filament.empty().append(filament_colour);
    filament_two.empty().append(filament_two_colour);
    
    jQuery('#p_carvey_link_field').addClass("hide_field");
  }
  else{
    jQuery('#p_date_field').removeClass("hide_field");
    jQuery('#lab_date_service').attr("required", true);      
            
    jQuery('#p_infill_percentage').addClass("hide_field");    
    jQuery('#lab_infill_percentage_service').removeAttr("required");
    
    jQuery('#p_supports_required').addClass("hide_field"); 
    jQuery('#lab_supports_required_service').removeAttr("required");  

    jQuery('#p_resolution').addClass("hide_field");
    jQuery('#lab_resolution_service').removeAttr("required");  

    jQuery('#p_comment').addClass("hide_field");
    jQuery('#lab_comment_service').removeAttr("required");

    jQuery('#p_material').addClass("hide_field");
    jQuery('#lab_material_service').removeAttr("required");  

    jQuery('#p_lab_file').removeClass("hide_field");    
    jQuery('#p_filament_colour').removeClass("hide_field");        
    jQuery('#p_filament_two_colour').addClass("hide_field");     
    jQuery('#lab_filament_two_colour_service').removeAttr("required");
    filament.empty().append(filament_3d_printer);
    
    jQuery('#p_carvey_link_field').addClass("hide_field");      

    jQuery('#lab_date_service').data('Zebra_DatePicker').update();       
  }
  
  
}

function validateBooking(){
  
  var error_message = "";

  var branch = jQuery('#branch_service').val();   

  if(branch == 11) 
  {
      var age_field = jQuery("#lab_age_service").val();

      if(isNaN(age_field)){
        error_message += "Age must be an number.\n";     
      }

      //check date has been filled in: 
      var date_field = jQuery('#lab_date_service').val();
      if(date_field.length == 0){
        error_message += "Please fill in the date field.\n";
         
      }

      //console.log(jQuery('#lab_file_service').files[0].size);
      if(jQuery('#lab_file_service').get(0).files.length > 0){
        size = jQuery('#lab_file_service').get(0).files[0].size; 
        if (size > 2096000) { 
          error_message += 'Your file size exceeds the limit allowed and cannot be uploaded.\n'; 
          
        } 
      }  
  }
    
  
  if(error_message.length > 0){
    alert("Errors:\n" + error_message);
    return false;
  }
  

  return true;
}

jQuery(document).ready(function() {

	// date picker restriction
	jQuery('#lab_date_service').Zebra_DatePicker({ 
		direction: [3, 27],
		disabled_dates: ['* * * 1',      
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
            '01 01 2020']
  });

    //update the lab options
    changeLabs("<?php echo $get_item_name; ?>");  
});


window.onload = function() {
    var is_post = <?php echo isset($_POST['lab_name_service']) ? "true;" : "false;"; ?>
    //if they inputted a choice, we have to focus on the form and the date field in particular 
 
    if(!is_post){
      if (window.location.search.indexOf('choice=14') > -1 || window.location.search.indexOf('choice=27') > -1) {

        //focus on the farthest field down then back to the first name field for the PBRL choices 
        document.getElementById('lab_telephone_service').focus();       
        document.getElementById('lab_first_name_service').focus();             
      }     
      else if (window.location.search.indexOf('choice=') > -1) {

        //focus on the farthest field down then back to the date field for all others 
        document.getElementById('lab_telephone_service').focus();       
        document.getElementById('lab_date_service').focus();             
      }
    }
}
</script>