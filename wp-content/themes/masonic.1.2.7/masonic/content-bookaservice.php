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

$choice = ""; 
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
}

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

function validate_booking($lab_first_name, $lab_last_name, $lab_age, $lab_purpose, $lab_waiver, $lab_certification, $lab_card, $lab_phone, $lab_email, $lab_orientation, $lab_time, $lab_date, $lab_name, $lab_branch, $file_location_name, $file_error, $file_size){
  $str_message = ""; 

  if(strlen($lab_phone) != 10 || !is_numeric($lab_phone)){
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

  
  if($lab_name == "3d_station_1"){
    $check_reservation = checkReservation($lab_name, $lab_date, $lab_time);
    if(count($check_reservation) > 0){
        $str_message .= "This lab has already been booked at the requested time.\n";
    }  
  }
  
  

  if($file_error == 1 || $file_size > 2000000){
    $str_message .= "The file size exceeds the 2MB limit.\n";
  }   

  if(!empty($file_location_name) && strpos($file_location_name, ".stl") === false && strpos($file_location_name, ".obj") === false && strpos($file_location_name, ".thing") === false && strpos($file_location_name, ".zip") === false && strpos($file_location_name, ".rar") === false){
    $str_message .= "The file type must be stl, obj, thing, zip, or rar.\n";
  }  

  return $str_message; 

}


$list_of_materials = array(
  "plywood_8x12_1x4" => "Plywood-G1S/Oak (8x12 1/4in Thickness)",
  "plywood_8x12_1x2" => "Plywood-G1S/Oak (8x12 1/2in Thickness)",
  "hdpe_yellowblack_6x6_1x4" => "Two-Colour HDPE Yellow on Black (Plastic)",
  "green_pvc_8x12_1x8" => "Green Expanded PVC Sheet (Plastic)",
  "use_my_own_material" => "Use My Own Material"
);     

$resolution_options = array(
  "0.2mm" => "Standard (0.2mm)",
  "0.15mm" => "High (0.15mm)",
  "0.1mm" => "Perfect (0.1mm)"
);

$time_options = array(
  "10am"=>"10:00am - 12:00pm",
  "11am"=>"11:00am - 2:00pm",
  "12pm"=>"12:00pm - 2:00pm",
  "2pm"=>"2:00pm - 4:00pm",
  "4pm"=>"4:00pm - 6:00pm",
  "5pm"=>"5:00pm - 8:00pm",
  "6pm"=>"6:00pm - 8:00pm",
  "10h"=>"10:00am - 11:00am",		    
  "11h"=>"11:00am - 12:00pm",
  "12h"=>"12:00pm - 1:00pm",
  "13h"=>"1:00pm - 2:00pm",
  "14h"=>"2:00pm - 3:00pm",
  "15h"=>"3:00pm - 4:00pm",
  "16h"=>"4:00pm - 5:00pm",
  "17h"=>"5:00pm - 6:00pm",
  "18h"=>"6:00pm - 7:00pm",
  "19h"=>"7:00pm - 8:00pm");		

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


if(isset($_POST['submit'])){
    //prep mail 
    $first_name = filter_var($_POST['lab_first_name'], FILTER_SANITIZE_STRING);
    $branch = filter_var($_POST['branch'], FILTER_SANITIZE_STRING);
    $lab_name = filter_var($_POST['lab_name'], FILTER_SANITIZE_STRING);    
    $lab_time_slot = "N/A";
    $last_name = filter_var($_POST['lab_last_name'], FILTER_SANITIZE_STRING);
    $lab_age = filter_var($_POST['lab_age'], FILTER_SANITIZE_STRING);
    $lab_agreement = filter_var($_POST['lab_agreement'], FILTER_SANITIZE_STRING);
    $lab_certification = filter_var($_POST['lab_certification'], FILTER_SANITIZE_STRING);
    $lab_purpose = filter_var($_POST['lab_purpose'], FILTER_SANITIZE_STRING);
    $lab_library_card = filter_var($_POST['lab_library_card'], FILTER_SANITIZE_STRING);
    $lab_email = filter_var($_POST['lab_email'], FILTER_SANITIZE_EMAIL);
    $lab_telephone = filter_var($_POST['lab_telephone'], FILTER_SANITIZE_STRING);

    $branch_name = "Civic Centre Resource Library";
    $branch_short = "CCRL";
    if($branch == "7"){
      $branch_name = "Pierre Berton Resource Library";
      $branch_short = "PBRL";
    }


         

    $lab_date = "";
    if(isset($_POST['lab_date'])){
      $lab_date = filter_var($_POST['lab_date'], FILTER_SANITIZE_STRING);
    }

    $lab_infill_percentage = "";
    if(isset($_POST['lab_infill_percentage'])){
      $lab_infill_percentage = filter_var($_POST['lab_infill_percentage'], FILTER_SANITIZE_STRING);      
    }
    
    $lab_supports_required = "";
    if(isset($_POST['lab_supports_required'])){
      $lab_supports_required = filter_var($_POST['lab_supports_required'], FILTER_SANITIZE_STRING);      
    }

    $lab_filament_colour = "";
    if(isset($_POST['lab_filament_colour'])){
      $lab_filament_colour = filter_var($_POST['lab_filament_colour'], FILTER_SANITIZE_STRING);      
    }


    $lab_filament_two_colour = "";
    if(isset($_POST['lab_filament_two_colour'])){
      $lab_filament_two_colour = filter_var($_POST['lab_filament_two_colour'], FILTER_SANITIZE_STRING);      
    }
    
    
    $lab_carvey_link = "";
    if(isset($_POST['lab_carvey_link'])){
      $lab_carvey_link = filter_var($_POST['lab_carvey_link'], FILTER_SANITIZE_STRING);      
    }

    $lab_resolution = "";
    if(isset($_POST['lab_resolution'])){
      $lab_resolution = filter_var($_POST['lab_resolution'], FILTER_SANITIZE_STRING);      
    }

    $lab_comment = "";
    if(isset($_POST['lab_comment'])){
      $lab_comment = filter_var($_POST['lab_comment'], FILTER_SANITIZE_STRING);      
    }



    $lab_orientation = "no";

    $file_location_name = "";
    $file_error = "";
    $file_size = "";

    if(!empty($_FILES['lab_file']['name'])){

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $uploadedfile = $_FILES['lab_file'];
        $file_location = $_FILES['lab_file']['tmp_name'];
        $file_location_name = $_FILES['lab_file']['name'];
        $file_error = $_FILES['lab_file']['error'];
        $file_size = $_FILES['lab_file']['size'];
    }

    $results = validate_booking($first_name, $last_name, $lab_age, $lab_purpose, $lab_agreement, $lab_certification, $lab_library_card, $lab_telephone, $lab_email, $lab_orientation, $lab_time_slot, $lab_date, $lab_name, $branch, $file_location_name, $file_error, $file_size);
    if(strlen($results) == 0){
      /* email details */
      $subject = "Request to Reserve a Lab " . $branch_short;						
  
      $message = "The following customer wants to reserve a lab:\n".
      "\n".
      "Name: ". $first_name . " " . $last_name . "\n" .
      "Age: ". $lab_age . "\n".
      "Customer Agreement Form Signed?: ". $lab_agreement . "\n".
      "Have you taken the required certification?: ". $lab_certification . "\n".
      "Purpose: ". $lab_purpose . "\n".
      "Telephone: ". $lab_telephone . "\n".
      "Library Card Number: ". $lab_library_card . "\n".
      "Email Address: ". $lab_email . "\n".
      "Branch: ". $branch_name . "\n".
      "Lab: ". $labs[$lab_name] . "\n";
      

      if(!empty($lab_date) && strpos($lab_name, "3d_station_1") !== false){
        $message .= "Date: ". $lab_date . "\n";
      }

       if(!empty($lab_infill_percentage) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Infill Percentage: ". $lab_infill_percentage . "\n";
      }

      if(!empty($lab_supports_required) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Supports Required: ". $lab_supports_required . "\n";
      }

      if(!empty($lab_filament_colour) && strpos($lab_name, "carvey") === false){
        $message .= "Filament Colour: ". filament_to_word($lab_filament_colour) . "\n";        
      }
      
      
      if(!empty($lab_resolution) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Filament Two Colour: ". filament_to_word($lab_filament_two_colour) . "\n";   
        $message .= "Resolution: ". $resolution_options[$lab_resolution] . "\n";        
      }      


      if(strpos($lab_name, "carvey") !== false){        
        $lab_material = filter_var($_POST['lab_material'], FILTER_SANITIZE_STRING);
        $material_name = $list_of_materials[$lab_material];
        
        $message .= "Material: ". $material_name . "\n";
        $message .= "Comment: \n". $lab_comment . "\n";
      }

      if(!empty($lab_carvey_link) && strpos($lab_name, "carvey") !== false){                
        $message .= "Carvey Link: ". $lab_carvey_link . "\n";
      }  

      if(!empty($lab_comment) && strpos($lab_name, "3d_station_3") !== false){
        $message .= "Comment: \n". $lab_comment . "\n";
      }      
      

      $content = $message;
      
      //d($message);
      $headers[] = 'From: Vaughan Public Libraries <librarian.librarian@vaughan.ca>';            
      //$to = "david.heng@vaughan.ca";  
      $to = "Librarian.Librarian@vaughan.ca";      

      if($lab_email == "david.heng@vaughan.ca"){
        $to = "david.heng@vaughan.ca";  
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
      insertStatsData(52);  
      $lab_success = "Thank you. Your request will be processed and a librarian will get back to you shortly.";
    }
    else{
      $lab_message = $results;
    }            
  
}


?>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>
  <div class="entry-content">
    <?php if(strlen($lab_message) > 0){ ?>
      <div class="notify-margin notify notify-red">Form Submission Error:<br><?php echo $lab_message; ?></div>
    <?php }else if(strlen($lab_success) > 0){ ?>
      <div class="notify-margin notify notify-green"><?php echo $lab_success; ?></div>
    <?php } ?>

    <?php the_content(); ?>
    
		
    <form action="/shareit/book-a-service<?php if(!empty($choice)) echo '?choice=' . $choice; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateBooking();">			
				<fieldset>
          <p>Please complete all fields with an asterisk (*). </p>
					
						
          <p> <label for="lab_branch">Branch<sup>*</sup>:&nbsp;</label> 
            <select name="branch" title="Local Branch" id="branch" onchange="changeLabs()" required>              
              <option value="11" <?php if($get_branch_id == "11") echo "selected"; ?>>Civic Centre Resource Library</option>
              <option value="7"  <?php if($get_branch_id == "7") echo "selected"; ?>>Pierre Berton Resource Library</option>
            </select>
          </p>

           <p> <label for="lab_name">Equipment<sup>*</sup>:&nbsp;</label> 
            <select name="lab_name" id="lab_name" onchange="changeFields()">
              <option value="">- Select Lab -</option>                                      
            </select>          
          </p>
          
           <p id="p_date_field"> <label for="lab_date">Date<sup>*</sup>:&nbsp;</label> 
           <input name="lab_date" size="15" maxlength="15" id="lab_date"  type="text" required>
          </p>           
          
           <p> <label for="lab_first_name">First Name<sup>*</sup>:&nbsp;</label> 
            <input type="text" name="lab_first_name" id="lab_first_name" size="50" required />
          </p>

           <p> <label for="lab_last_name">Last Name<sup>*</sup>:&nbsp;</label> 
           <input type="text" name="lab_last_name" id="lab_last_name" size="50" required />
          </p>

           <p> <label for="lab_age">Age<sup>*</sup>:&nbsp;</label> 
            <input type="text" name="lab_age" id="lab_age" size="50" required />
          </p>

          <p> <label for="lab_agreement">Customer Agreement Form Signed?<sup>*</sup>:&nbsp;</label> 
            <input type="radio" id="lab_agreement" value="Yes" name="lab_agreement">&nbsp;Yes
            <input type="radio" id="lab_agreement" value="No" name="lab_agreement" checked>&nbsp;No        
          </p>

          <p> <label for="lab_certification">Have you taken the required certification?<sup>*</sup>:</label> 
            <input type="radio" id="lab_certification" value="Yes" name="lab_certification">&nbsp;Yes
            <input type="radio" id="lab_certification" value="No" name="lab_certification" checked>&nbsp;No        
          </p>

           <p> <label for="lab_purpose">Purpose<sup>*</sup>:&nbsp;</label> 
            <select name="lab_purpose" id="lab_purpose" required>
              <option value=""> </option>

              <option value="work">Work</option>

              <option value="school">School</option>
              
              <option value="hobby">Hobby</option>
            </select>              
          </p>

           <p> <label for="lab_library_card">Library Card<sup>*</sup>:&nbsp;</label> 
           <input type="text" name="lab_library_card" id="lab_library_card" required />
          </p>
     
           <p> <label for="lab_email">Email<sup>*</sup>:&nbsp;</label> 
            <input type="email" name="lab_email" id="lab_email" size="50" required />
          </p>

           <p> <label for="lab_telephone">Telephone<sup>*</sup>:&nbsp;</label> 
            <input type="text" name="lab_telephone" id="lab_telephone" required />
          </p>

          <p id="p_material"> <label for="lab_material">Material<sup>*</sup>:&nbsp;</label> 
            <select name="lab_material" id="lab_material" required>
              <option value="">-- Select Material --</option>
              <option value="plywood_8x12_1x4">Plywood-G1S/Oak (1/4in Thickness)</option>
              <option value="plywood_8x12_1x2">Plywood-G1S/Oak (1/2in Thickness)</option>              
              <option value="hdpe_yellowblack_6x6_1x4">Two-Colour HDPE Yellow on Black (Plastic)</option>             
              <option value="green_pvc_8x12_1x8">Green Expanded PVC Sheet (plastic)</option>
              <option value="use_my_own_material">Use My Own Material</option>
            </select>             
          </p>

           <p id="p_infill_percentage"> <label for="lab_infill_percentage">Infill Percentage<sup>*</sup>:&nbsp;</label> 
            <select name="lab_infill_percentage" id="lab_infill_percentage" required>
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

           <p id="p_supports_required"> <label for="lab_supports_required">Supports required?<sup>*</sup>:&nbsp;</label>           
            <input type="radio" id="lab_supports_required" value="Yes" name="lab_supports_required">&nbsp;Yes
            <input type="radio" id="lab_supports_required" value="No" name="lab_supports_required" checked>&nbsp;No                              
          </p>

           <p id="p_filament_colour"> <label for="lab_filament_colour">Filament Colour<sup>*</sup>:&nbsp;</label> 
            <select name="lab_filament_colour" id="lab_filament_colour" required>
              <option value=""> Select Colour </option>
            </select>              
          </p>                    

           <p id="p_filament_two_colour"> <label for="lab_filament_two_colour">Filament Two Colour<sup>*</sup>:&nbsp;</label> 
            <select name="lab_filament_two_colour" id="lab_filament_two_colour" required>
              <option value=""> Select Colour </option>
            </select>              
          </p>        

           <p id="p_resolution"> <label for="lab_resolution">Resolution<sup>*</sup>:&nbsp;</label> 
            <select name="lab_resolution" id="lab_resolution" required>              
              <option value="0.2mm">Standard (0.2mm)</option>

              <option value="0.15mm" selected>High (0.15mm)</option>
              
              <option value="0.1mm">Perfect (0.1mm)</option>
            </select>              
          </p>

           <p id="p_comment"> <label for="lab_comment">Comments / Instructions<sup></sup></label> 
              <textarea id="lab_comment" name="lab_comment" rows="3" cols="50"></textarea>
            </p>

           <p id="p_lab_file"> <label for="lab_file">For 3D Printing bookings. You can upload either a .stl, .obj, or .thing. You can upload multiple files in .zip, or .rar format (Max size: 2MB)&nbsp;</label> 
            <br><input type="file" name="lab_file" id="lab_file">
          </p>											              

          <p id="p_carvey_link_field"> <label for="lab_carvey_link">Carvey: Add Easel Editor Link:&nbsp;</label> 
           <input type="text" name="lab_carvey_link" id="lab_carvey_link" size="50" />
          </p>

          <div id="form_errors"></div> 

          <div class="labs_email_reminder">
            Please check your email and spam folder for confirmation of your reservation. Please allow 3 business days for processing.
          </div>
          <p class="text_center">
            <input type="submit" name="submit" id="submit" value="Submit Request">
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

var filament_3d_printer = "<option value='White'>White</option><option value='Black'>Black</option><option value='Grey'>Grey</option><option value='Blue'>Blue</option><option value='Red'>Red</option>";

//var filament_colour = "<option value='white'>White</option><option value='black'>Black</option><option value='grey'>Grey</option><option value='blue'>Blue</option><option value='red'>Red</option><option value='brown'>Brown</option><option value='yellow'>Yellow</option><option value='orange'>Orange</option><option value='pink'>Pink</option><option value='purple'>Purple</option><option value='green'>Green</option><option value='translucent_yellow'>Translucent Yellow</option><option value='translucent_purple'>Translucent Purple</option><option value='translucent_red'>Translucent Red</option><option value='translucent_blue'>Translucent Blue</option><option value='translucent_orange'>Translucent Orange</option>";
//var filament_two_colour = "<option value='white'>White</option><option value='black'>Black</option><option value='grey'>Grey</option><option value='blue'>Blue</option><option value='red'>Red</option><option value='brown'>Brown</option><option value='yellow'>Yellow</option><option value='orange'>Orange</option><option value='pink'>Pink</option><option value='purple'>Purple</option><option value='green'>Green</option><option value='translucent_yellow'>Translucent Yellow</option><option value='translucent_purple'>Translucent Purple</option><option value='translucent_red'>Translucent Red</option><option value='translucent_blue'>Translucent Blue</option><option value='translucent_orange'>Translucent Orange</option><option value='pva_only_for_support'>PVA - Only for Support</option>";

var filament_colour = "<option value='White'>White</option><option value='Black'>Black</option><option value='Silver'>Silver</option><option value='Blue'>Blue</option><option value='Red'>Red</option><option value='Green'>Green</option><option value='Yellow'>Yellow</option><option value='Translucent'>Translucent</option>";
var filament_two_colour = "<option value='N/A'>N/A</option><option value='White'>White</option><option value='Black'>Black</option><option value='Silver'>Silver</option><option value='Blue'>Blue</option><option value='Red'>Red</option><option value='Green'>Green</option><option value='Yellow'>Yellow</option><option value='Translucent'>Translucent</option><option value='pva_only_for_support'>PVA - Only for Support</option>";


function changeLabs(default_lab){
  
    var val = jQuery('#branch').val();
    var select = jQuery('#lab_name');

    var filament = jQuery('#lab_filament_colour');

    var filament_two = jQuery('#lab_filament_two_colour');

    if(val == 7){

        select.empty().append(labs_pbrl);
        

        filament.empty().append(filament_colour);
        filament_two.empty().append(filament_two_colour);
 
    }
    if(val == 11){
        //update the dates
        var datepicker1 = jQuery('#lab_date').data('Zebra_DatePicker');

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

            '24 12 2017',       
            '25 12 2017',  
            '26 12 2017', 
            '31 12 2017',
            '01 01 2018',
            '07 02 2018',
            '08 02 2018',
            '09 02 2018',
            '10 02 2018',
            '11 02 2018',
            '12 02 2018',
            '13 02 2018',
            '14 02 2018',
            '15 02 2018',
            '16 02 2018',
            '17 02 2018',
            '18 02 2018',
            '19 02 2018',
            '20 02 2018',
            '21 02 2018',
            '22 02 2018',
            '23 02 2018',
            '24 02 2018',
            '25 02 2018',
            '26 02 2018',
            '27 02 2018',            
            '19 02 2018',
            '07 03 2018',
            '30 03 2018',
            '01 04 2018',
            '02 04 2018',
            '24 04 2018',            
            '20 05 2018',
            '21 05 2018',
            '01 07 2018',            
            '06 08 2018',
            '02 09 2018',
            '08 10 2018',
            '24 12 2018',
            '25 12 2018',
            '26 12 2018',
            '31 12 2018',
            '01 01 2019'      
          ],
        });        

        select.empty().append(labs_ccrl);




        filament.empty().append(filament_3d_printer);


    }


    //sets the default item as selected
    if (default_lab !== undefined) {
        //set the default lab field 
        jQuery('#lab_name').find('option').each(function(i,e){
            if(jQuery(e).val() == default_lab){
                jQuery('#lab_name').prop('selectedIndex',i);
            }
        });            
    }

    changeFields();
        

}

function changeFields(){
  var select = jQuery('#lab_name').val();
  var filament = jQuery('#lab_filament_colour');
  var filament_two = jQuery('#lab_filament_two_colour');
  
  
  if(select == "carvey"){

    jQuery('#p_date_field').addClass("hide_field");
    jQuery('#lab_date').removeAttr("required");  

    jQuery('#p_infill_percentage').addClass("hide_field");
    jQuery('#lab_infill_percentage').removeAttr("required");

    jQuery('#p_supports_required').addClass("hide_field");        
    jQuery('#lab_supports_required').removeAttr("required");  

    jQuery('#p_resolution').addClass("hide_field");
    jQuery('#lab_resolution').removeAttr("required");  


    jQuery('#p_material').removeClass("hide_field");
    jQuery('#lab_material').attr("required", true);

    jQuery('#p_lab_file').addClass("hide_field");    
    jQuery('#p_filament_colour').addClass("hide_field");        
    jQuery('#p_filament_two_colour').addClass("hide_field");   
    jQuery('#lab_filament_two_colour').removeAttr("required");
    jQuery('#p_carvey_link_field').removeClass("hide_field");       

  }
  else if(select =="3d_station_3"){    
    
    jQuery('#p_date_field').addClass("hide_field");
    jQuery('#lab_date').removeAttr("required");  

    jQuery('#p_infill_percentage').removeClass("hide_field");
    jQuery('#lab_infill_percentage').attr("required", true);

    jQuery('#p_supports_required').removeClass("hide_field");        
    jQuery('#lab_supports_required').attr("required", true);    
             
    jQuery('#p_resolution').removeClass("hide_field");
    jQuery('#lab_resolution').attr("required", true);  

    jQuery('#p_comment').removeClass("hide_field");
         

    jQuery('#p_material').addClass("hide_field");
    jQuery('#lab_material').removeAttr("required");  

    jQuery('#p_lab_file').removeClass("hide_field");
    jQuery('#p_filament_colour').removeClass("hide_field");
    jQuery('#p_filament_two_colour').removeClass("hide_field");

    filament.empty().append(filament_colour);
    filament_two.empty().append(filament_two_colour);
    
    jQuery('#p_carvey_link_field').addClass("hide_field");
  }
  else{
    jQuery('#p_date_field').removeClass("hide_field");
    jQuery('#lab_date').attr("required", true);      
            
    jQuery('#p_infill_percentage').addClass("hide_field");    
    jQuery('#lab_infill_percentage').removeAttr("required");
    
    jQuery('#p_supports_required').addClass("hide_field"); 
    jQuery('#lab_supports_required').removeAttr("required");  

    jQuery('#p_resolution').addClass("hide_field");
    jQuery('#lab_resolution').removeAttr("required");  

    jQuery('#p_comment').addClass("hide_field");
    jQuery('#lab_comment').removeAttr("required");

    jQuery('#p_material').addClass("hide_field");
    jQuery('#lab_material').removeAttr("required");  

    jQuery('#p_lab_file').removeClass("hide_field");    
    jQuery('#p_filament_colour').removeClass("hide_field");        
    jQuery('#p_filament_two_colour').addClass("hide_field");     
    jQuery('#lab_filament_two_colour').removeAttr("required");
    filament.empty().append(filament_3d_printer);
    
    jQuery('#p_carvey_link_field').addClass("hide_field");             
  }
  
  
}

function validateBooking(){
  
  var error_message = "";


  var age_field = document.getElementById("lab_age").val();
  //console.log(age_field);
  if(isNaN(age_field)){
    error_message += "Age must be an number.\n";     
  }

  //check date has been filled in: 
  var date_field = document.getElementById('lab_date').val();
  if(date_field.length == 0){
    error_message += "Please fill in the date field.\n";
     
  }

  if(document.getElementById('lab_file').files.length > 0){
    size = document.getElementById('lab_file').files[0].size; 
    if (size > 2096000) { 
      error_message += 'Your file size exceeds the limit allowed and cannot be uploaded.\n'; 
      
    } 
  }
  
  
  if(error_message.length > 0){
    alert("Errors:\n" + error_message);
    return false;
  }
  
  return true;
}


function limitTime(){

	//For non 3D Printing stations
	var weekendTime = [
		{value: "10am", text: "10:00am - 12:00pm"},
		{value: "12pm", text: "12:00pm - 2:00pm"},
		{value: "2pm", text: "2:00pm - 4:00pm"},
	];
	
	//For non 3D Printing Stations
	var weekdayTime = [
		{value: "10am", text: "10:00am - 12:00pm"},
		{value: "12pm", text: "12:00pm - 2:00pm"},
		{value: "2pm", text: "2:00pm - 4:00pm"},
		{value: "4pm", text: "4:00pm - 6:00pm"},
		{value: "6pm", text: "6:00pm - 8:00pm"},
	];
		
	//For 3D Printing Stations 	
	var weekdayTime_3d_stations = [
		{value: "11am", text: "11:00am - 2:00pm"},		    
		{value: "5pm", text: "5:00pm - 8:00pm"}    
	];

	var weekendTime_3d_stations = [
		{value: "11am", text: "11:00am - 2:00pm"}		
	];
  
  //For Oculus Rift
	var weekdayTime_oculus_stations = [
		{value: "10h", text: "10:00am - 11:00am"},		    
    {value: "11h", text: "11:00am - 12:00pm"},
    {value: "12h", text: "12:00pm - 1:00pm"},
    {value: "13h", text: "1:00pm - 2:00pm"},
    {value: "14h", text: "2:00pm - 3:00pm"},
    {value: "15h", text: "3:00pm - 4:00pm"},
    {value: "16h", text: "4:00pm - 5:00pm"},
    {value: "17h", text: "5:00pm - 6:00pm"},
    {value: "18h", text: "6:00pm - 7:00pm"},
    {value: "19h", text: "7:00pm - 8:00pm"}
	];

	var weekendTime_oculus_stations = [
		{value: "10h", text: "10:00am - 11:00am"},		    
    {value: "11h", text: "11:00am - 12:00pm"},
    {value: "12h", text: "12:00pm - 1:00pm"},
    {value: "13h", text: "1:00pm - 2:00pm"},
    {value: "14h", text: "2:00pm - 3:00pm"},
    {value: "15h", text: "3:00pm - 4:00pm"}	
	];  


	//console.log(jQuery('#LabDate').val());	 
	var chosen_date = jQuery('#lab_date').val();

	//if the date hasn't been chosen yet, we can just exit immediately.
	if(chosen_date == ""){
		return; 
	}

	var chosen_date_obj =new Date(chosen_date); 
	var day = chosen_date_obj.getDay();
	//console.log(day);
	//we are considering weekdays as tues - thurs and weekends as fri - sun as per Kristin 
	var isWeekend = (day == 4) || (day == 5) || (day == 6);  
	
	//clear all options from the start time select box 
	var select_time_slot_erase = document.getElementById("lab_time");
	var length = select_time_slot_erase.options.length;
	for (i = 0; i < length; i++) {
		select_time_slot_erase.options[0] = null;
	}
	
	var select_time_slot = document.getElementById("lab_time"),
			lab_choice = jQuery("#lab_name"),		
	option,
  i = 0;
  	

	//Populate the time slot options. Different for 3D Printing Stations. 
	if(lab_choice.val() == "3d_station_1" || lab_choice.val() == "3d_station_2" || lab_choice.val() == "3d_station_3" || lab_choice.val() == "carving_station"){
		if(isWeekend){
      il = weekendTime_3d_stations.length;
      for (; i < il; i += 1) {
          option = document.createElement('option');
          option.setAttribute('value', weekendTime_3d_stations[i].value);
          option.appendChild(document.createTextNode(weekendTime_3d_stations[i].text));
          select_time_slot.appendChild(option);
      }      
    }
    else{
      il = weekdayTime_3d_stations.length;
      for (; i < il; i += 1) {
          option = document.createElement('option');
          option.setAttribute('value', weekdayTime_3d_stations[i].value);
          option.appendChild(document.createTextNode(weekdayTime_3d_stations[i].text));
          select_time_slot.appendChild(option);
      }
    }
    		
	}
  else if(lab_choice.val() == "oculus_rift"){
		if(isWeekend){
      il = weekendTime_oculus_stations.length;
      for (; i < il; i += 1) {
          option = document.createElement('option');
          option.setAttribute('value', weekendTime_oculus_stations[i].value);
          option.appendChild(document.createTextNode(weekendTime_oculus_stations[i].text));
          select_time_slot.appendChild(option);
      }      
    }
    else{
      il = weekdayTime_oculus_stations.length;
      for (; i < il; i += 1) {
          option = document.createElement('option');
          option.setAttribute('value', weekdayTime_oculus_stations[i].value);
          option.appendChild(document.createTextNode(weekdayTime_oculus_stations[i].text));
          select_time_slot.appendChild(option);
      }
    }    		
	}
	else{	
		if(isWeekend){
			il = weekendTime.length;
			for (; i < il; i += 1) {
					option = document.createElement('option');
					option.setAttribute('value', weekendTime[i].value);
					option.appendChild(document.createTextNode(weekendTime[i].text));
					select_time_slot.appendChild(option);
			}
		}
		else{		
			il = weekdayTime.length;
			for (; i < il; i += 1) {
					option = document.createElement('option');
					option.setAttribute('value', weekdayTime[i].value);
					option.appendChild(document.createTextNode(weekdayTime[i].text));		
					select_time_slot.appendChild(option);
			}		
		}
	}
}

jQuery(document).ready(function() {

	// date picker restriction
	jQuery('#lab_date').Zebra_DatePicker({ 
		direction: [3, 27],
		disabled_dates: ['* * * 1',      
            '24 12 2017',       
            '25 12 2017',  
            '26 12 2017', 
            '31 12 2017',
            '01 01 2018',
            '07 02 2018',
            '08 02 2018',
            '09 02 2018',
            '10 02 2018',
            '11 02 2018',
            '12 02 2018',
            '13 02 2018',
            '14 02 2018',
            '15 02 2018',
            '16 02 2018',
            '17 02 2018',
            '18 02 2018',
            '19 02 2018',
            '20 02 2018',
            '21 02 2018',
            '22 02 2018',
            '23 02 2018',
            '24 02 2018',
            '25 02 2018',
            '26 02 2018',
            '27 02 2018',            
            '19 02 2018',
            '07 03 2018',
            '30 03 2018',
            '01 04 2018',
            '02 04 2018',
            '24 04 2018',            
            '20 05 2018',
            '21 05 2018',
            '01 07 2018',            
            '06 08 2018',
            '02 09 2018',
            '08 10 2018',
            '24 12 2018',
            '25 12 2018',
            '26 12 2018',
            '31 12 2018',
            '01 01 2019'   
    ],
  });

    //update the lab options
    changeLabs("<?php echo $get_item_name; ?>");  
});


window.onload = function() {
    var is_post = <?php echo isset($_POST['lab_name']) ? "true;" : "false;"; ?>
    //if they inputted a choice, we have to focus on the form and the date field in particular 
 
    if(!is_post){
      if (window.location.search.indexOf('choice=14') > -1 || window.location.search.indexOf('choice=27') > -1) {

        //focus on the farthest field down then back to the first name field for the PBRL choices 
        document.getElementById('lab_telephone').focus();       
        document.getElementById('lab_first_name').focus();             
      }     
      else if (window.location.search.indexOf('choice=') > -1) {

        //focus on the farthest field down then back to the date field for all others 
        document.getElementById('lab_telephone').focus();       
        document.getElementById('lab_date').focus();             
      }
    }
}
</script>