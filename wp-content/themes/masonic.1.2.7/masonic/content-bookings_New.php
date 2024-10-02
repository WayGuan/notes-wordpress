<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */


require_once ($_SERVER['DOCUMENT_ROOT'] . "/phpmailer/class.phpmailer.php");

/*      variables for content-bookaservice   */


$booking_type = "";



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

$time_options = array("930am" => " 9:30am - 10:00am",
                      "10am" => "10:00am - 12:00pm",
                      "11am" => "11:00am - 2:00pm",
                      "12pm" => "12:00pm - 2:00pm",
                      "2pm"  => " 2:00pm - 4:00pm",
                      "4-5pm"  => " 4:00pm - 5:00pm",
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
                      "19h"  => " 7:00pm - 8:00pm",
                      // "930"  => " 9:30am - 10:00am",
                      "1000" => "10:00am - 1:00pm",
                      "1330" => " 1:30pm - 4:30pm",
                      "1700" => " 5:00pm - 8:00pm"
                    );



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



if(isset($_POST['submit_space_1'])){

    $booking_type = filter_var($_POST['booking_type_space'], FILTER_SANITIZE_STRING);
    //prep mail 
    $first_name_space = filter_var($_POST['lab_first_name_space'], FILTER_SANITIZE_STRING);
    $branch_space = filter_var($_POST['branch_space'], FILTER_SANITIZE_STRING);
    $lab_name_space = filter_var($_POST['lab_name_space'], FILTER_SANITIZE_STRING);
    $lab_date_space = filter_var($_POST['lab_date_space'], FILTER_SANITIZE_STRING);
    $lab_time_slot_space = filter_var($_POST['lab_time_space'], FILTER_SANITIZE_STRING);
    $last_name_space = filter_var($_POST['lab_last_name_space'], FILTER_SANITIZE_STRING);
    $lab_age_space = filter_var($_POST['lab_age_space'], FILTER_SANITIZE_STRING);
    $lab_agreement_space = filter_var($_POST['lab_agreement_space'], FILTER_SANITIZE_STRING);
    $lab_purpose_space = "N/A"; //filter_var($_POST['lab_purpose_space'], FILTER_SANITIZE_STRING);
    $lab_library_card_space = filter_var($_POST['lab_library_card_space'], FILTER_SANITIZE_STRING);
    $lab_email_space = filter_var($_POST['lab_email_space'], FILTER_SANITIZE_EMAIL);
    $lab_telephone_space = filter_var($_POST['lab_telephone_space'], FILTER_SANITIZE_STRING);
    $lab_orientation_space = "no";

    $branch_name = "Civic Centre Resource Library";
    $branch_short = "CCRL";
    if($branch_space == "7"){
      $branch_name = "Pierre Berton Resource Library";
      $branch_short = "PBRL";
    }


    $results = validate_booking_space($first_name_space, $last_name_space, $lab_age_space, $lab_purpose_space, $lab_agreement_space, $lab_library_card_space, $lab_telephone_space, $lab_email_space, $lab_orientation_space, $lab_time_slot_space, $lab_date_space, $lab_name_space, $branch_space);


    if(strlen($results) == 0){

      if($lab_email_space == "vplwebmaster@vaughan.ca"){
        $to = "vplwebmaster@vaughan.ca";  
      }
      else{
        $to = "Librarian.Librarian@vaughan.ca";
      }
      

      $subject = "Creation Spaces Booking " . $branch_short;           
  
      $message = "The following customer wants to book a space:\n".
      "\n".
      "Name:  ". $first_name_space . " " . $last_name_space . "\n\n" .
      "Age:  ". $lab_age_space . "\n\n".
      "Customer Agreement Form Signed?:  ". $lab_agreement_space . "\n\n".
      /*"Purpose:  ". $lab_purpose . "\n\n".*/
      "Telephone:  ". $lab_telephone_space . "\n\n".
      "Library Card Number:  ". $lab_library_card_space . "\n\n".
      "Email Address:  ". $lab_email_space . "\n\n".
      "Space:  ". $labs[$lab_name_space] . "\n\n".
      "Date:  ". $lab_date_space . "\n\n".
      "Time Slot:  ". $time_options[$lab_time_slot_space] . "\n\n";      
                  
      $content = $message;
      
      $headers[] = 'From: Vaughan Public Libraries <librarian.librarian@vaughan.ca>';            

      $mail = new PHPMailer();
      $mail->From = 'Librarian.Librarian@vaughan.ca';
      $mail->FromName = 'Vaughan Public Libraries';
      $mail->Subject = $subject;
      //$mail->addReplyTo($lab_email);
      $mail->Body = $message;
      $mail->AddAddress( $to );     
      $mailSuccess = $mail->Send();
      //$mailSuccess = true;

      if ($mailSuccess)
      {
          $lab_success = "Thank you. Your request will be processed and library staff will get back to you shortly.";  
          insertStatsData(52);  

          $lab_date_space = '';
          $lab_time_space = '';
          $first_name_space = '';
          $last_name_space = '';
          $lab_age_space = '';
          $lab_agreement_space = 'No';
          $lab_library_card_space = '';
          $lab_email_space = '';
          $lab_telephone_space = '';
      }
      else
      {
         $lab_message = "Something wrong with email server, please try again later.";
      }
    }
    else
    {
      $lab_message = $results;
    }            
  }


  if(isset($_POST['submit_equip_2'])){

    $booking_type = filter_var($_POST['booking_type_equip'], FILTER_SANITIZE_STRING);
    //prep mail 
    $first_name_equip = filter_var($_POST['lab_first_name_equip'], FILTER_SANITIZE_STRING);
    $branch_equip = filter_var($_POST['branch_equip'], FILTER_SANITIZE_STRING);
    $lab_name_equip = filter_var($_POST['lab_name_equip'], FILTER_SANITIZE_STRING);
    $lab_date_equip = filter_var($_POST['lab_date_equip'], FILTER_SANITIZE_STRING);
    $lab_time_slot_equip = filter_var($_POST['lab_time_equip'], FILTER_SANITIZE_STRING);
    $last_name_equip = filter_var($_POST['lab_last_name_equip'], FILTER_SANITIZE_STRING);
    $lab_age_equip = filter_var($_POST['lab_age_equip'], FILTER_SANITIZE_STRING);
    $lab_agreement_equip = filter_var($_POST['lab_agreement_equip'], FILTER_SANITIZE_STRING);
    $lab_purpose_equip = "N/A"; //filter_var($_POST['lab_purpose_equip'], FILTER_SANITIZE_STRING);
    $lab_library_card_equip = filter_var($_POST['lab_library_card_equip'], FILTER_SANITIZE_STRING);
    $lab_email_equip = filter_var($_POST['lab_email_equip'], FILTER_SANITIZE_EMAIL);
    $lab_telephone_equip = filter_var($_POST['lab_telephone_equip'], FILTER_SANITIZE_STRING);
    $lab_orientation_equip = "no";


    $branch_name = "Civic Centre Resource Library";
    $branch_short = "CCRL";
    if($branch_equip == "7"){
      $branch_name = "Pierre Berton Resource Library";
      $branch_short = "PBRL";
    }

    $results = validate_booking_equip($first_name_equip, $last_name_equip, $lab_age_equip, $lab_purpose_equip, $lab_agreement_equip, $lab_library_card_equip, $lab_telephone_equip, $lab_email_equip, $lab_orientation_equip, $lab_time_slot_equip, $lab_date_equip, $lab_name_equip, $branch_equip);
    if(strlen($results) == 0){

      $to = "Librarian.Librarian@vaughan.ca";
      if($lab_email_equip == "vplwebmaster@vaughan.ca"){
        $to = "vplwebmaster@vaughan.ca";  
      }
      
      
      /* email details */
      $subject = "Creation Equipment Booking " . $branch_short;           
  
      $message = "The following customer wants to book equipment:\n".
      "\n".
      "Name:  ". $first_name_equip . " " . $last_name_equip . "\n\n" .
      "Age:  ". $lab_age_equip . "\n\n".
      "Customer Agreement Form Signed?:  ". $lab_agreement_equip . "\n\n".
      /*"Purpose:  ". $lab_purpose . "\n\n".*/
      "Telephone:  ". $lab_telephone_equip . "\n\n".
      "Library Card Number:  ". $lab_library_card_equip . "\n\n".
      "Email Address:  ". $lab_email_equip . "\n\n".
      "Equipment:  ". $labs[$lab_name_equip] . "\n\n".
      "Date:  ". $lab_date_equip . "\n\n".
      "Time Slot:  ". $time_options[$lab_time_slot_equip] . "\n\n";
                  
      $content = $message;
      
      $mail = new PHPMailer();
      $mail->From = 'Librarian.Librarian@vaughan.ca';
      $mail->FromName = 'Vaughan Public Libraries';
      $mail->Subject = $subject;
      //$mail->addReplyTo($lab_email);
      $mail->Body = $message;
      $mail->AddAddress( $to );     
      $mailSuccess = $mail->Send();
      //$mailSuccess = true;

      if ($mailSuccess)
      {
          $lab_success = "Thank you. Your request will be processed and library staff will get back to you shortly.";  
          insertStatsData(52);  

          $lab_time_equip = '';
          $lab_date_equip = '';
          $first_name_equip = '';
          $last_name_equip = '';
          $lab_age_equip = '';
          $lab_agreement_equip = 'No';
          $lab_library_card_equip = '';
          $lab_email_equip = '';
          $lab_telephone_equip = '';

      }
      else
      {
         $lab_message = "Something wrong with email server, please try again later.";
      }
    }
    else{
      $lab_message = $results;
    }            
   
  }



  if(isset($_POST['submit_3D_printer_3']) || isset($_POST['submit_carvey_4'])){

      $booking_type = filter_var($_POST['booking_type_service'], FILTER_SANITIZE_STRING);
      //prep mail 
      $first_name_service = filter_var($_POST['lab_first_name_service'], FILTER_SANITIZE_STRING);
      $branch_service = filter_var($_POST['branch_service'], FILTER_SANITIZE_STRING);

      $lab_name_service = "";
      if(isset($_POST['submit_3D_printer'])) 
      {
          if($branch_service == 7)
          {
              $lab_name_service =  "3d_station_3";
          }
          elseif ($branch_service == 11) {
              $lab_name_service = "3d_station_1";
          }
      }
      else
      {
          $lab_name_service = "carvey";
      } 
      

      //$lab_date_service = filter_var($_POST['lab_date_service'], FILTER_SANITIZE_STRING);    
      $lab_date_service = "";
      if(isset($_POST['lab_date_service'])){
         $lab_date_service = filter_var($_POST['lab_date_service'], FILTER_SANITIZE_STRING);
      }
      
      $lab_time_service = "";

      if(isset($_POST['lab_time_service']))
         $lab_time_service = filter_var($_POST['lab_time_service'], FILTER_SANITIZE_STRING);    

      $last_name_service = filter_var($_POST['lab_last_name_service'], FILTER_SANITIZE_STRING);
      $lab_age_service = filter_var($_POST['lab_age_service'], FILTER_SANITIZE_STRING);
      $lab_agreement_service = filter_var($_POST['lab_agreement_service'], FILTER_SANITIZE_STRING);
      $lab_certification_service = filter_var($_POST['lab_certification_service'], FILTER_SANITIZE_STRING);
      $lab_first_print_with_staff = filter_var($_POST['lab_first_print_with_staff'], FILTER_SANITIZE_STRING);
      $lab_purpose_service = "N/A"; //filter_var($_POST['lab_purpose_service'], FILTER_SANITIZE_STRING);
      $lab_library_card_service = filter_var($_POST['lab_library_card_service'], FILTER_SANITIZE_STRING);
      $lab_email_service = filter_var($_POST['lab_email_service'], FILTER_SANITIZE_EMAIL);
      $lab_telephone_service = filter_var($_POST['lab_telephone_service'], FILTER_SANITIZE_STRING);

      $branch_name = "Civic Centre Resource Library";
      $branch_short = "CCRL";
      if($branch_service == "7"){
        $branch_name = "Pierre Berton Resource Library";
        $branch_short = "PBRL";
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

      $lab_material = "";
      if(isset($_POST['lab_material_service'])){
        $lab_material = filter_var($_POST['lab_material_service'], FILTER_SANITIZE_STRING);      
      }      


      $lab_orientation_service = "no";

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

      $results = validate_booking_service($first_name_service, $last_name_service, $lab_age_service, $lab_purpose_service, $lab_agreement_service, $lab_certification_service, $lab_library_card_service, $lab_telephone_service, $lab_email_service, $lab_orientation_service, $lab_name_service, $branch_service, $file_location_name, $file_error, $file_size);
      

      if(strlen($results) == 0){
        
        $subject = "Creation Services Booking " . $branch_short;           
    
        $message = "The following customer wants to book a service:\n"."\n".
        "Name: ". $first_name_service . " " . $last_name_service . "\n\n" .
        "Age: ". $lab_age_service . "\n\n".
        "Customer Agreement Form Signed?: ". $lab_agreement_service . "\n\n".
        "Have you taken the required certification?: ". $lab_certification_service . "\n\n";

        if(isset($_POST['submit_3D_printer']))
          $message .= "Have you completed your first print with staff?: " . $lab_first_print_with_staff . "\n\n";
        /*"Purpose: ". $lab_purpose . "\n\n".*/

        $message .= "Telephone: ". $lab_telephone_service . "\n\n".
                    "Library Card Number: ". $lab_library_card_service . "\n\n".
                    "Email Address: ". $lab_email_service . "\n\n".
                    "Branch: ". $branch_name . "\n\n".
                    "Service: ". $labs[$lab_name_service] . "\n\n";
        

        // if(!empty($lab_date) && strpos($lab_name, "3d_station_1") !== false){
        //   $message .= "Date: ". $lab_date . "\n\n";
        // }

        if(!empty($lab_date_service) && !empty($lab_time_service))
        {
           $message .= "Date: " . $lab_date_service . "\n\n";

           $message .= "Timeslot: " . $lab_time_service . "\n\n";
        }




         if(!empty($lab_infill_percentage) && strpos($lab_name_service, "3d_station_3") !== false){
          $message .= "Infill Percentage: ". $lab_infill_percentage . "\n\n";
        }

        if(!empty($lab_supports_required) && strpos($lab_name_service, "3d_station_3") !== false){
          $message .= "Supports Required: ". $lab_supports_required . "\n\n";
        }

        if(!empty($lab_filament_colour) && strpos($lab_name_service, "carvey") === false){
          $message .= "Filament Colour: ". filament_to_word($lab_filament_colour) . "\n\n";        
        }
        
        
        if(!empty($lab_resolution) && strpos($lab_name_service, "3d_station_3") !== false){
          $message .= "Filament Two Colour: ". filament_to_word($lab_filament_two_colour) . "\n\n";   
          $message .= "Resolution: ". $resolution_options[$lab_resolution] . "\n\n";        
        }      


        if(strpos($lab_name_service, "carvey") !== false){        
          $lab_material = filter_var($_POST['lab_material_service'], FILTER_SANITIZE_STRING);
          $material_name = $list_of_materials[$lab_material];
          
          $message .= "Material: ". $material_name . "\n\n";
          //$message .= "Comment: \n". $lab_comment . "\n\n";
        }

        if(!empty($lab_carvey_link) && strpos($lab_name_service, "carvey") !== false){                
          $message .= "Carvey Link: ". $lab_carvey_link . "\n\n";
        }  

        if(!empty($lab_comment) && strpos($lab_name_service, "3d_station_3") !== false){
          $message .= "Comment: \n". $lab_comment . "\n\n";
        }      
        

        $content = $message;
        
        //d($message);
        $headers[] = 'From: Vaughan Public Libraries <librarian.librarian@vaughan.ca>';            
        //$to = "david.heng@vaughan.ca";  
        $to = "Librarian.Librarian@vaughan.ca";      


        if($lab_email_service == "vplwebmaster@vaughan.ca"){
            $to = "vplwebmaster@vaughan.ca";  
        }                   
        
        //var_dump($message);

        $mail = new PHPMailer();
        $mail->From = 'Librarian.Librarian@vaughan.ca';
        $mail->FromName = 'Vaughan Public Libraries';
        $mail->Subject = $subject;
        //$mail->addReplyTo($lab_email);
        $mail->Body = $message;
        //$mail->AddAddress( 'Librarian.Librarian@vaughan.ca' );
        $mail->AddAddress( $to );     

        //only for 3d printers
        if(isset($file_location) && $lab_name_service != "carvey"){   
          $mail->AddAttachment($file_location, $file_location_name);        
        }
        $mailSuccess = $mail->Send();
        insertStatsData(52);  
        $lab_success = "Thank you. Your request will be processed and library staff will get back to you shortly.";

        $first_name_service = '';
        $last_name_service = '';
        $lab_age_service = '';
        $lab_agreement_service = 'No';
        $lab_certification_service = 'No';
        $lab_first_print_with_staff = 'No';
        $lab_library_card_service = '';
        $lab_email_service = '';
        $lab_telephone_service = '';
        $lab_material = '';
        $lab_infill_percentage = '';
        $lab_supports_required = 'No';
        $lab_comment = '';
        $lab_carvey_link = '';
      }
      else{
        $lab_message = $results;
      }            
    
}



/***************************************************/


/*      variables for content-bookaspace   */


/***************************************************/


?>


<?php 

function validate_booking_space($lab_first_name, $lab_last_name, $lab_age, $lab_purpose, $lab_waiver, $lab_card, $lab_phone, $lab_email, $lab_orientation, $lab_time, $lab_date, $lab_name, $lab_branch){
  $str_message = ""; 

  if (empty($lab_phone) || !preg_match('/^[(][0-9]{3}[)][ ][0-9]{3}[-][0-9]{4}$/', $lab_phone))
  {
    $str_message .= "Please enter a valid phone number. e.g. (416) 123-1234.\n" . "<br/>";
  }   


  if (empty($lab_time))
  {
    $str_message .= "Please select a time slot.\n" . "<br/>";
  }

 /* if (empty($lab_age) || !preg_match('/^\d*$/', $lab_age))
  {
    $str_message .= "Please enter a valid age.\n" . "<br/>";
  }
*/

  if (empty($lab_card) || !preg_match('/^\d{14}$/', $lab_card))
  {
    $str_message .= "Please enter a vaild Vaughan Library Card Number.\n" . "<br/>";
  }

  //check that the lab is available from the location 
  $check_lab_station = checkLabStation($lab_branch, $lab_name);

  if(count($check_lab_station) == 0){
    $str_message .= "An error has occurred. This lab isn't available from this branch. Please reload the page and try again.\n" . "<br/>";  
  }

  $date_today = new Datetime(); 
  $date_selected = new Datetime($lab_date); 
  $date_interval = ceil(($date_selected->format('U') - $date_today->format('U')) / (60*60*24));

  if($date_interval < 3 || $date_interval > 30){
    $str_message .= "Date Unavailable. Please select a later date.\n" . "<br/>";
  }  

  $check_library_card = checkLibraryCard($lab_name, $lab_card, $lab_date);

  if(count($check_library_card) > 0){
    $str_message .= "You can only book the same lab once per day with the same library card.\n" . "<br/>";
  }
  
  $check_name = checkName($lab_name, $first_name, $last_name, $lab_date);  

  if(count($check_name) > 0){
    $str_message .= "You have already booked this lab for the specified day.\n" . "<br/>";
  }

  $check_reservation = checkReservation_new($lab_name, $lab_date, $lab_time);

  if(count($check_reservation) > 0){
      $str_message .= "This lab has already been booked at the requested time.\n" . "<br/>" . count($check_reservation) . " " . $check_reservation[0]['date'] . " " . $check_reservation[0]['time_slot'];
  }
  

  return $str_message; 

}



function validate_booking_equip($lab_first_name, $lab_last_name, $lab_age, $lab_purpose, $lab_waiver, $lab_card, $lab_phone, $lab_email, $lab_orientation, $lab_time, $lab_date, $lab_name, $lab_branch){
  $str_message = ""; 


  if (empty($lab_phone) || !preg_match('/^[(][0-9]{3}[)][ ][0-9]{3}[-][0-9]{4}$/', $lab_phone))
  {
    $str_message .= "Please enter a valid phone number.\n" . "<br/>";
  }   

  if (empty($lab_card) || !preg_match('/^\d{14}$/', $lab_card))
  {
    $str_message .= "Please enter a vaild Vaughan Library Card Number.\n" . "<br/>";
  }

  //check that the lab is available from the location 
  $check_lab_station = checkLabStation($lab_branch, $lab_name);

  if(count($check_lab_station) == 0){
    $str_message .= "An error has occurred. This lab isn't available from this branch. Please reload the page and try again.\n" . "<br/>";  
  }

  $date_today = new Datetime(); 
  $date_selected = new Datetime($lab_date); 
  $date_interval = ceil(($date_selected->format('U') - $date_today->format('U')) / (60*60*24));
  if($date_interval < 3 || $date_interval > 30){
    $str_message .= "Date Unavailable. Please select a later date.\n" . "<br/>";
  }  

  $check_library_card = checkLibraryCard($lab_name, $lab_card, $lab_date);
  if(count($check_library_card) > 0){
    $str_message .= "You can only book the same lab once per day with the same library card.\n" . "<br/>";
  }
  
  $check_name = checkName($lab_name, $first_name, $last_name, $lab_date);  
  if(count($check_name) > 0){
    $str_message .= "You have already booked this lab for the specified day.\n" . "<br/>";
  }

  $check_reservation = checkReservation($lab_name, $lab_date, $lab_time);
  if(count($check_reservation) > 0){
    $str_message .= "This lab has already been booked at the requested time.\n" . "<br/>";
  }  
   

  return $str_message; 
}


function validate_booking_service($lab_first_name, $lab_last_name, $lab_age, $lab_purpose, $lab_waiver, $lab_certification, $lab_card, $lab_phone, $lab_email, $lab_orientation, $lab_name, $lab_branch, $file_location_name, $file_error, $file_size){


  $str_message = ""; 

  if (empty($lab_phone) || !preg_match('/^[(][0-9]{3}[)][ ][0-9]{3}[-][0-9]{4}$/', $lab_phone))
  {
      $str_message .= "Please enter a valid phone number.\n" . "<br/>";
  }

  if (empty($lab_card) || !preg_match('/^\d{14}$/', $lab_card))
  {
    $str_message .= "Please enter a vaild Vaughan Library Card Number.\n" . "<br/>";
  }

  //check that the lab is available from the location 
  $check_lab_station = checkLabStation($lab_branch, $lab_name);

  if(count($check_lab_station) == 0){
    $str_message .= "An error has occurred. This lab isn't available from this branch. Please reload the page and try again.\n" . "<br/>";  
  }

  // $date_today = new Datetime(); 
  // $date_selected = new Datetime($lab_date); 
  // $date_interval = ceil(($date_selected->format('U') - $date_today->format('U')) / (60*60*24));

  // //only apply to CCRL b/c it needs a date
  // if($lab_branch == 11 && ($date_interval < 3 || $date_interval > 30)){
  //   $str_message .= "Date Unavailable. Please select a later date.\n";
  // }  

  $check_library_card = checkLibraryCard($lab_name, $lab_card, $lab_date);
  if(count($check_library_card) > 0){
    $str_message .= "You can only book the same lab once per day with the same library card.\n" . "<br/>";
  }
  
  $check_name = checkName($lab_name, $first_name, $last_name, $lab_date);  
  if(count($check_name) > 0){
    $str_message .= "You have already booked this lab for the specified day.\n" . "<br/>";
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
    $str_message .= "The file size exceeds the 2MB limit.\n" . "<br/>";
  }   

  if(!empty($file_location_name) && 
      strpos($file_location_name, ".stl") === false && 
      strpos($file_location_name, ".obj") === false && 
      strpos($file_location_name, ".thing") === false && 
      strpos($file_location_name, ".zip") === false && 
      strpos($file_location_name, ".rar") === false)
  {
      $str_message .= "The file type must be stl, obj, thing, zip, or rar.\n" . "<br/>";
  }  

  return $str_message; 
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


?>


<article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>

  <div class="entry-content">
        
      <?php if(strlen($lab_message) > 0){ ?>
              <div class="notify-margin notify notify-red">Form Submission Error:<br><?php echo $lab_message; ?></div>
            <?php }else if(strlen($lab_success) > 0){ ?>
              <div class="notify-margin notify notify-green"><?php echo $lab_success; ?></div>
      <?php } ?>

      <style type="text/css">

      section {
        display: flex;
        flex-flow: row wrap;
      }

      section > div {
        flex: 1;
        padding: 0.5rem;
      }

      input[type="radio"]{
        display: none;
        &:not(:disabled) ~ label {
          cursor: pointer;
        }
        &:disabled ~ label {
          color: hsla(150, 5%, 75%, 1);
          border-color: hsla(150, 5%, 75%, 1);
          box-shadow: none;
          cursor: not-allowed;
        }
      }

      .rb_label {
        height: 100%;
        display: block;
        background: white;
        border: 2px solid hsla(180, 100%, 81%, 1);
        border-radius: 20px;
        padding: 1rem;
        margin-bottom: 1rem;
        text-align: center;
        box-shadow: 0px 3px 10px -2px hsla(150, 5%, 65%, 0.5);
        position: relative;
        font-weight: bolder;
      }

      input[type="radio"]:checked + label {
        background: hsla(186, 100%, 81%, 1);
        color: hsla(180, 0%, 100%, 1);
       /* box-shadow: 0px 0px 20px hsla(186, 100%, 50%, 0.75);*/
        &::after {
          color: hsla(180, 5%, 25%, 1);
          font-family: FontAwesome;
          border: 2px solid hsla(150, 75%, 45%, 1);
          content: "\f00c";
          font-size: 24px;
          position: absolute;
          top: -25px;
          left: 50%;
          transform: translateX(-50%);
          height: 50px;
          width: 50px;
          line-height: 50px;
          text-align: center;
          border-radius: 50%;
          background: white;
          box-shadow: 0px 2px 5px -2px hsla(0, 0%, 0%, 0.25);
        }
      }
      /*input[type="radio"]#control_05:checked + label {
        background: red;
        border-color: red;
      }*/
      p.rb_para {
        font-weight: bolder;
        text-align: left;
        font-size: 16px;
      }

      p.rb_branch, p.rb_branch_one {
        font-weight: bold;
        font-size: 15px;
        text-align: center;
        line-height: 16px;
        margin-bottom: 0px;
        color: black;
      }

      p.rb_branch_one {
        margin-top: 2px;
      }

      .labelForm {
          color: red;
          font-size: 1em;

      }

      img.card_img {
        height:180px;
        width: 320px;
      }

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

       input[type="radio"].show_radio {
          display: inline-block;
       }

</style>

    <!-- =========================================   Radio Button Selections ================================================================== -->

    <div id="selectOption">
          <div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <div style="background-color: #dff0d8; border-color: #dff0d8; padding: 10px, 10px, 10px, 10px;">
                <p style="color:blue;"><strong>Service Update:</strong> We are pleased to announce the re-opening of our creation spaces, and booking is now open for our 3D printing and Carvey machines. Some services may not be available due to public health guidelines.</p>
            </div>

          </div>
          <div>&nbsp;</div>
          <div>&nbsp;</div>
         <!--  <div><p><strong>Click to choose one of the four options to make a booking:</strong></p></div> -->
          <SECTION id="sec_rb_one">
              <div>
                  <input class="rb_type_one" type="radio" id="rb_space" name="select" value="1" onclick="changeForms()" checked>
                  <label class="rb_label" for="rb_space">
                      <h3>Green Room and Recording Studio</h3>
                      <p class="rb_branch_one">Civic Centre Resource Library</p>
                      <img class="img-responsive card_image alignnone card_img" src="http://www.vaughanpl.info/shareit/wp-content/uploads/Customers-using-Music-Recording-Studio.png" alt="Recording Studio" width="203">
                      <p class="rb_para">
                          Book time to independently create your project and use our editing software. 
                      </p>
                  </label>
              </div>
             
             <div>
                  <input class="rb_type_one" type="radio" id="rb_3d_printer" name="select" value="3" onclick="changeForms()">
                  <label class="rb_label" for="rb_3d_printer">
                      <h3>3D Printing</h3>
                      <p class="rb_branch_one">Civic Centre Resource Library</p>
                      <p class="rb_branch">Pierre Berton Resource Library</p>
                      <img class="img-responsive card_image card_img" src="http://www.vaughanpl.info/shareit/wp-content/uploads/New-3D-Printer-@PBRL.jpg" alt="New 3D Printer" width="203">
                      <p class="rb_para">
                         Book time to use our Ultimaker 3D printer. All customers must first take the online 3D Certification Course and complete their first print with a staff member.
                      </p>
                  </label>
              </div>
          </SECTION>
          <SECTION id="sec_rb_two">

             <div>
                  <input class="rb_type_one" type="radio" id="rb_equipment" name="select" value="2" onclick="changeForms()">
                  <label class="rb_label" for="rb_equipment">
                      <h3>Equipment</h3>
                      <p class="rb_branch_one">Civic Centre Resource Library</p>
                      <p class="rb_branch">Pierre Berton Resource Library</p>
                      <img class="img-responsive card_image card_img" src="http://www.vaughanpl.info/shareit/wp-content/uploads/Oculus-Rift-1.png" alt="Oculus-Rift" width="203">
                      <p class="rb_para">
                          Use our equipment independently in the library and explore robotics, coding, virtual reality, design and much more.
                      </p>
                  </label>
              </div>
             
               <div>
                  <input class="rb_type_one" type="radio" id="rb_carving" name="select" value="4" onclick="changeForms()">
                  <label class="rb_label" for="rb_carving">
                      <h3>Carving</h3>
                      <p class="rb_branch_one">Pierre Berton Resource Library</p>
                      <img class="img-responsive card_image card_img" src="http://www.vaughanpl.info/shareit/wp-content/uploads/Carving.jpg" alt="New 3D Printer" width="203">
                      <p class="rb_para">
                         For a small fee, staff will carve your design into wood or plastic using our Carvey machine. Only staff have access to the Carvey machine.
                      </p>
                  </label>
              </div>
          </SECTION>
      </div>
    <!-- =========================================   Radio Button Selections ================================================================== -->

    <!-- =========================================   Content : book a space ================================================================== -->
    
    <div id="formOne" style="display: block;">
          <div class="entry-content">
            <!-- <?php if(strlen($lab_message) > 0){ ?>
              <div class="notify-margin notify notify-red">Form Submission Error:<br><?php echo $lab_message; ?></div>
            <?php }else if(strlen($lab_success) > 0){ ?>
              <div class="notify-margin notify notify-green"><?php echo $lab_success; ?></div>
            <?php } ?>

            <?php //the_content(); ?> -->

            <div class="description">
              
              <div>&nbsp;</div>
             <!--  <p>Vaughan Public Libraries cardholders can book the Green Room or Recording Studio at the <a href="http://www.vaughanpl.info/libraries/view/11" target="_blank">Civic Centre Resource Library</a> for free.</p> -->
              <p><strong>To book a space:</strong></p>
              <ul>
              <li>Print and complete the Customer Agreement Form and return it on your first booking <a href="http://www.vaughanpl.info/shareit/wp-content/uploads/creation_spaces_customer_agreement.pdf" target="_blank">Download Customer Agreement Form</a>.</li>
              <li>Fill out the Booking Form at the bottom of this page.</li>
              <li>Read the rules and guidelines below:</li>
                <ul>
                <li>Bookings must be made with a valid Vaughan Public Libraries card in good standing.</li>
                <li>Bookings must be made at least 3 days in advance.</li>
                <li>Each space&nbsp;can be booked once per day with a valid library card.</li>
                <li>Each booking is limited to 3 hours per day, with the exception of:</li>
                <ul>
                <li>Music Lesson bookings for the Recording Studio  9:30 – 10:00 am (for instrument use only)</li>
                </ul>
                <li>Reservations will be held up to 30 minutes after the Booking start time. (unless previously arranged)</li>
                <li>Booking privileges may be suspended if too many appointments are missed without any notice. Please call or email the library to inform staff of a cancelled appointment so other customers can use the space.</li>
                <li>Children under 16 must be accompanied by an adult.</li>
                <li>Maximum occupancy is 6 per room.</li>
                <li>Covered beverages are allowed and food is not permitted.</li>
                </ul>
              </ul>

            </div>

            
            <!-- <form action="/shareit/bookings<?php if(!empty($choice)) echo '?choice=' . $choice; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateBooking_space();">     
                <fieldset>
                  <p><strong>All fields with * are required.</strong></p>
                  
                  <p><input name="booking_type_space" type="hidden" value="space"></p>
                    
                  <p><label for="lab_branch_space" class="label_text">Branch<sup>*</sup>:&nbsp;</label></p> 
                  <p>
                    <select name="branch_space" title="Local Branch" id="branch_space" onchange="changeLabs_space()" required>              
                      <option value="11">Civic Centre Resource Library</option>              
                    </select>
                  </p>

                   <p><label for="lab_name_space" class="label_text">Space<sup>*</sup>:&nbsp;</label></p> 
                   <p>
                    <select name="lab_name_space" id="lab_name_space" value="<?php echo $lab_name_space; ?>" onchange="limitTime_space(<?php echo $lab_time_space; ?>)">
                      <option value="">- Select Space -</option>                                      
                    </select>          
                  </p>

                  
                  <p><label for="lab_date_space" class="label_text">Date<sup>*</sup>:&nbsp;</label></p>
                  <p>
                    <input name="lab_date_space" size="50" maxlength="15" class="input_space" value="<?php echo $lab_date_space; ?>" onblur="limitTime_space()" id="lab_date_space"  type="text" required>
                  </p>

                  <p><label for="lab_time_space" class="label_text">Time Slot<sup>*</sup>:&nbsp;</label></p>
                  <p>
                    <select name="lab_time_space" id="lab_time_space" required>
                      <option value="">- Select Space and Date -</option>
                    </select>              
                  </p>
                  
                  <p><label for="lab_first_name_space" class="label_text">First Name<sup>*</sup>:&nbsp;</label></p>
                  <p>
                    <input type="text" name="lab_first_name_space" class="input_space" id="lab_first_name_space" value="<?php echo $first_name_space; ?>" size="50" required />
                  </p>

                  <p> <label for="lab_last_name_space" class="label_text">Last Name<sup>*</sup>:&nbsp;</label></p>
                  <p>
                   <input type="text" name="lab_last_name_space" class="input_space" id="lab_last_name_space" value="<?php echo $last_name_space; ?>" size="50" required />
                  </p>

                  <p><label for="lab_age_space" class="label_text">Age if under 18:&nbsp;</label></p>
                  <p>
                    <input type="number" name="lab_age_space" id="lab_age_space" class="input_space" value="<?php echo $lab_age_space; ?>" size="15" />
                  </p>

                  <p><label for="lab_agreement_space" class="label_text">Customer Agreement Form Signed?<sup>*</sup>:&nbsp;</label></p>
                  <p>
                    <input type="radio" id="lab_agreement_space" value="Yes" class="show_radio" name="lab_agreement_space" <?php echo ($lab_agreement_space == "Yes") ? "checked" : ""; ?>>&nbsp;Yes
                    <input type="radio" id="lab_agreement_space" value="No" class="show_radio" name="lab_agreement_space" <?php echo (empty($lab_agreement_space) || $lab_agreement_space == "No") ? "checked" : ""; ?>>&nbsp;No        
                  </p>

                  <p><label for="lab_library_card_space" class="label_text">Library Card<sup>*</sup>:&nbsp;</label></p>
                  <p>
                     <input type="text" name="lab_library_card_space" id="lab_library_card_space" class="input_space" value="<?php echo $lab_library_card_space; ?>" required />
                  </p>
             
                  <p><label for="lab_email_space" class="label_text">Email<sup>*</sup>:&nbsp;</label></p>
                  <p>
                    <input type="email" name="lab_email_space" id="lab_email_space" size="50" class="input_space" value="<?php echo $lab_email_space; ?>" required />
                  </p>

                  <p><label for="lab_telephone_space" class="label_text">Telephone<sup>*</sup>:&nbsp;</label></p>
                  <p>
                    <input type="text" name="lab_telephone_space" id="lab_telephone_space" class="input_space" placeholder="e.g. (416) 123-4567" value="<?php echo $lab_telephone_space; ?>" required />
                  </p>
                  <p style="color: darkblue;"><strong>The format of phone number should be (416) 123-4567.</strong></p>                                        

                  <div id="form_errors"></div> 

                  <div class="labs_email_reminder">
                    Please check your email and spam folder for confirmation of your reservation. Please allow 3 business days for processing.
                  </div>
                  <p class="text_center">
                    <input type="submit" name="submit_space" id="submit_space" value="Submit">
                  </p>                                    
              </fieldset>
            </form> -->

              <?php
              wp_link_pages(array(
                  'before' => '<div class="page-links">' . __('Pages:', 'masonic'),
                  'after' => '</div>',
              ));
              ?>
           </div><!-- .entry-content -->
    </div>

    <!-- =========================================   Content : book a space ================================================================== -->

    <!-- =========================================   Content : book equipment ================================================================== -->

    <div id="formTwo" style="display: none;">
       <div class="entry-content" id="form-entry-div">
        <!-- <?php if(strlen($lab_message) > 0){ ?>
          <div class="notify-margin notify notify-red">Form Submission Error:<br><?php echo $lab_message; ?></div>
        <?php }else if(strlen($lab_success) > 0){ ?>
          <div class="notify-margin notify notify-green"><?php echo $lab_success; ?></div>
        <?php } ?>

        <?php //the_content(); ?> -->

        <div class="description">
          
            <div>&nbsp;</div>
            <p>Vaughan Public Libraries cardholders can book time to use equipment independently in the <a href="http://www.vaughanpl.info/libraries/view/11" target="_blank">Civic Centre</a> and <a href="http://www.vaughanpl.info/libraries/view/7" target="_blank">Pierre Berton</a> Resource Libraries.</p>
            <p><strong>To book equipment:</strong></p>
            <ul>
            <li>Print and complete the Customer Agreement Form and return it on your first booking <a href="http://www.vaughanpl.info/shareit/wp-content/uploads/creation_spaces_customer_agreement.pdf" target="_blank">Download Customer Agreement Form</a>.</li>
            <li>Fill out the Booking Form at the bottom of this page.</li>
            <li>Read the rules and guidelines below:</li>
            <ul>
            <li>Bookings made at least 3 days in advance are strongly encouraged. Drop-in bookings will be accepted given the availability of equipment.</li>
            <li>Each booking is limited to 2 hours per day (with exception of 1 hour for the Oculus Rift).</li>
            <li>All users of the Oculus Rift must be at least 13 years of age and must read and adhere to all <a href="http://www.vaughanpl.info/shareit/wp-content/files/health_and_safety_rules_and_guidelines.pdf" target="_blank">Health &amp; Safety Guidelines</a>.</li>
            <li>Only material supplied by the library may be used with the Silhouette Cameo (vinyl cutter).</li>
            </ul>
            </ul>
        </div>

        
        <!-- <form action="/shareit/bookings<?php if(!empty($choice)) echo '?choice=' . $choice; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateBooking_equip();">     
            <fieldset>
              <p><strong>All fields with * are required.</strong></p>
              
              <p><input name="booking_type_equip" type="hidden" value="equip"></p>

              <p> <label for="branch_equip" class="label_text">Branch<sup>*</sup>:&nbsp;</label></p>
              <p> 
                <select name="branch_equip" title="Local Branch" id="branch_equip" onchange="changeLabs_equip()" required>              
                  <option value="11" <?php if($branch_equip == "11") echo "selected"; ?>>Civic Centre Resource Library</option>
                  <option value="7"  <?php if($branch_equip == "7") echo "selected"; ?>>Pierre Berton Resource Library</option>              
                </select>
              </p>

              <p> <label for="lab_name_equip" class="label_text">Equipment<sup>*</sup>:&nbsp;</label></p>
              <p> 
                <select name="lab_name_equip" id="lab_name_equip" onchange="limitTime_equip(<?php echo $lab_time_equip; ?>);">
                  <option value="">- Select Lab -</option>                                      
                </select>          
              </p>

              
              <p> <label for="lab_date_equip" class="label_text">Date<sup>*</sup>:&nbsp;</label></p>
              <p> 
               <input name="lab_date_equip" size="50" maxlength="15" class="input_space" value="<?php echo $lab_date_equip; ?>" onblur="limitTime_equip()" id="lab_date_equip" type="text" required>
              </p>

              <p> <label for="lab_time_equip" class="label_text">Time Slot<sup>*</sup>:&nbsp;</label></p>
              <p>
                <select name="lab_time_equip" id="lab_time_equip" required>
                  <option value="">- Select Branch and Date -</option>
                </select>              
              </p>
              
              <p> <label for="lab_first_name_equip" class="label_text">First Name<sup>*</sup>:&nbsp;</label></p>
              <p> 
                <input type="text" name="lab_first_name_equip" id="lab_first_name_equip" class="input_space" value="<?php echo $first_name_equip; ?>" size="50" required />
              </p>

              <p> <label for="lab_last_name_equip" class="label_text">Last Name<sup>*</sup>:&nbsp;</label></p>
              <p> 
               <input type="text" name="lab_last_name_equip" class="input_space" id="lab_last_name_equip" value="<?php echo $last_name_equip; ?>" size="50" required />
              </p>

              <p> <label for="lab_age_equip" class="label_text">Age if under 18:&nbsp;</label></p>
              <p> 
                <input type="number" name="lab_age_equip" class="input_space" id="lab_age_equip" value="<?php echo $lab_age_equip; ?>" size="15"/>
              </p>

              <p> <label for="lab_agreement_equip" class="label_text">Customer Agreement Form Signed?<sup>*</sup>:&nbsp;</label></p>
              <p> 
                <input type="radio" id="lab_agreement_equip" value="Yes" class="show_radio" name="lab_agreement_equip" <?php echo ($lab_agreement_equip == "Yes") ? "checked" : ""; ?>>&nbsp;Yes
                <input type="radio" id="lab_agreement_equip" value="No" class="show_radio" name="lab_agreement_equip" <?php echo (empty($lab_agreement_equip) || $lab_agreement_equip == "No") ? "checked" : ""; ?>>&nbsp;No        
              </p>

              <p> <label for="lab_library_card_equip" class="label_text">Library Card<sup>*</sup>:&nbsp;</label></p>
              <p> 
               <input type="text" name="lab_library_card_equip" maxlength="15" class="input_space" id="lab_library_card_equip" value="<?php echo $lab_library_card_equip; ?>" required />
              </p>
         
              <p> <label for="lab_email_equip" class="label_text">Email<sup>*</sup>:&nbsp;</label></p>
              <p> 
                <input type="email" name="lab_email_equip" maxlength="100" class="input_space" id="lab_email_equip" value="<?php echo $lab_email_equip; ?>" size="50" required />
              </p>

              <p> <label for="lab_telephone_equip" class="label_text">Telephone<sup>*</sup>:&nbsp;</label></p>
              <p> 
                <input type="text" name="lab_telephone_equip" class="input_space" id="lab_telephone_equip" value="<?php echo $lab_telephone_equip; ?>" placeholder=" e.g. (416) 123-4567" required />
              </p>
              <p style="color: darkblue;"><strong>The format of phone number should be (416) 123-4567.</strong></p>
                                      

              <div id="form_errors_equip"></div> 

              <div class="labs_email_reminder">
                Please check your email and spam folder for confirmation of your reservation. Please allow 3 business days for processing.
              </div>
              <p class="text_center">
                <input type="submit" name="submit_equip" id="submit_equip" value="Submit">
              </p>                                    
          </fieldset>
        </form> -->

          <?php
          wp_link_pages(array(
              'before' => '<div class="page-links">' . __('Pages:', 'masonic'),
              'after' => '</div>',
          ));
          ?>
       </div><!-- .entry-content -->
    </div>

    <!-- =========================================   Content : book equipment ================================================================== -->

    <!-- =========================================   Content : book a service ================================================================== -->

      <div id="formThree" style="display: block;">
        <div class="entry-content">
          <div class="description">
            <div>&nbsp;</div>
            <p><strong>Fees</strong> - $0.25/gram</p>
            <p><strong>To use the 3D Printer:</strong></p>
            <ul>
            <li>Print and complete the Customer Agreement Form and return it on your first booking.&nbsp;<a href="http://www.vaughanpl.info/shareit/wp-content/uploads/creation_spaces_customer_agreement.pdf">Download Customer Agreement Form</a>.</li>
            <li style="font-weight: inherit;">Become 3D Certified by taking VPL&rsquo;s online <a title="3D Certification Course" href="http://www.vaughanpl.info/shareit/courses/get-certified-online/" target="_blank">3D Certification Course</a></li>
            <li style="font-weight: inherit;">Fill out the Booking Form at the bottom of this page.</li>
            <li style="font-weight: inherit;">Read the rules and guidelines below:</li>
            <ul style="font-weight: inherit;">
            <li style="font-weight: inherit;">Bookings must be made with a valid Vaughan Public Libraries card in good standing.</li>
            <li style="font-weight: inherit;">Bookings must be made at least 3 days in advance.</li>
            <li style="font-weight: inherit;">Print jobs run for a maximum of 7 hours Mondays &ndash; Thursdays and 3 hours Fridays &ndash; Sundays.</li>
            <li style="font-weight: inherit;">Customers must arrive for their booking with a ready to print .STL or .obj file.</li>
            <li style="font-weight: inherit;">3D Printers are reserved for customers 13 +.</li>
            <li style="font-weight: inherit;">All customers must complete their first print with a staff member.</li>
            </ul>
            </ul>
          </div>
          <form action="/shareit/bookings<?php if(!empty($choice)) echo '?choice=' . $choice; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateBooking_service();">     
                <fieldset>
                  <p><strong>All fields with * are required.</strong></p>
                  
                  <p><input name="booking_type_service" type="hidden" value="3D_printer"></p>
                    
                  <p> <label for="lab_branch_service" class="label_text">Branch<sup>*</sup>:&nbsp;</label></p>
                  <p> 
                      <select name="branch_service" title="Local Branch" id="branch_service" required>              
                          <option value="11" <?php if($branch_service == "11") echo "selected"; ?>>Civic Centre Resource Library</option>
                          <option value="7"  <?php if($branch_service == "7") echo "selected"; ?>>Pierre Berton Resource Library</option>
                      </select>
                  </p>

                  <p><label for="lab_date_service" class="label_text">Date<sup>*</sup>:&nbsp;</label></p>
                    <p>
                      <input name="lab_date_service" size="50" maxlength="15" class="input_space" value="<?php echo $lab_date_service; ?>" onblur="limitTime_service()" id="lab_date_service"  type="text" required>
                    </p>

                    <p><label for="lab_time_service" class="label_text">Time Slot<sup>*</sup>:&nbsp;</label></p>
                    <p>
                      <select name="lab_time_service" id="lab_time_service" required>
                        <option value="">- Select Time Slot -</option>
                      </select>              
                    </p>
                  
                  <p> <label for="lab_first_name_service" class="label_text">First Name<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                    <input type="text" name="lab_first_name_service" maxlength="50" class="input_space" id="lab_first_name_service" value="<?php echo $first_name_service; ?>" size="50" required />
                  </p>

                  <p> <label for="lab_last_name_service" class="label_text">Last Name<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                      <input type="text" name="lab_last_name_service" maxlength="50" class="input_space" id="lab_last_name_service" size="50" value="<?php echo $last_name_service; ?>" required />
                  </p>

                  <p> <label for="lab_age_service" class="label_text">Age if under 18:&nbsp;</label></p>
                  <p>
                      <input type="number" name="lab_age_service" class="input_space" id="lab_age_service" value="<?php echo $lab_age_service; ?>" size="15"/>
                  </p>

                  <p> <label for="lab_agreement_service" class="label_text">Customer Agreement Form Signed?<sup>*</sup>:&nbsp;</label> </p>
                  <p> 
                      <input type="radio" id="lab_agreement_service" value="Yes" class="show_radio" name="lab_agreement_service" <?php echo ($lab_agreement_service == "Yes") ? "checked" : ""; ?>>&nbsp;Yes
                      <input type="radio" id="lab_agreement_service" value="No" class="show_radio" name="lab_agreement_service" <?php echo (empty($lab_agreement_service) || $lab_agreement_service == "No") ? "checked" : ""; ?>>&nbsp;No        
                  </p>

                  <p> <label for="lab_certification_service" class="label_text">Have you taken the required certification?<sup>*</sup>:</label> </p>
                  <p>
                      <input type="radio" id="lab_certification_service" value="Yes" class="show_radio" name="lab_certification_service" <?php echo ($lab_certification_service == "Yes") ? "checked" : ""; ?>>&nbsp;Yes
                      <input type="radio" id="lab_certification_service" value="No" class="show_radio" name="lab_certification_service" <?php echo (empty($lab_certification_service) || $lab_certification_service == "No") ? "checked" : ""; ?>>&nbsp;No        
                  </p>

                  <p> <label for="lab_first_print_with_staff" class="label_text">Have you completed your first print with staff?<sup>*</sup>:</label> </p>
                  <p>
                      <input type="radio" id="lab_first_print_with_staff" value="Yes" class="show_radio" name="lab_first_print_with_staff" <?php echo ($lab_first_print_with_staff == "Yes") ? "checked" : ""; ?>>&nbsp;Yes
                      <input type="radio" id="lab_first_print_with_staff" value="No" class="show_radio" name="lab_first_print_with_staff" <?php echo (empty($lab_first_print_with_staff) || $lab_first_print_with_staff == "No") ? "checked" : ""; ?>>&nbsp;No        
                  </p>

                  <p> <label for="lab_library_card_service" class="label_text">Library Card<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                      <input type="text" name="lab_library_card_service" maxlength="15" class="input_space" id="lab_library_card_service" value="<?php echo $lab_library_card_service; ?>" required />
                  </p>
             
                  <p> <label for="lab_email_service" class="label_text">Email<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                    <input type="email" name="lab_email_service" class="input_space" id="lab_email_service" size="50" maxlength="50" value="<?php echo $lab_email_service; ?>" required />
                  </p>

                  <p> <label for="lab_telephone_service" class="label_text">Telephone<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                      <input type="text" name="lab_telephone_service" class="input_space" id="lab_telephone_service" value="<?php echo $lab_telephone_service; ?>" placeholder=" e.g. (416) 123-4567" required />
                  </p>
                  <p style="color: darkblue;"><strong>The format of phone number should be (416) 123-4567.</strong></p>

                  <div id="form_errors"></div> 

                  <div class="labs_email_reminder">
                    Please check your email and spam folder for confirmation of your reservation. Please allow 3 business days for processing.
                  </div>
                  <p class="text_center">
                    <input type="submit" name="submit_3D_printer" id="submit_3D_printer" value="Submit">
                  </p>                                    
              </fieldset>
            </form>

        </div>
      </div>

      <div id="formFour" style="display: block;">
        <div class="entry-content">
          <div class="description">
            <div>&nbsp;</div>
                <p><strong>Fees</strong></p>
                <p>Carvey: $2.00/hour plus Material Cost. You can use your own material based on<a href="http://www.vaughanpl.info/shareit/guidelines-for-materials-used-with-carvey/" target="_blank"> the Material Guideline</a>.</p>
                <p><span id="id8997" class="collapseomatic colomat-close colomat-visited colomat-hover" tabindex="0" title="Material Cost Information">Material Cost Information</span></p>
                <div id="target-id8997" class="collapseomatic_content ">
                <table width="565">
                <tbody>
                <tr>
                <td width="274"><strong>Material</strong></td>
                <td width="69"><strong>Size (in)</strong></td>
                <td width="105"><strong>Thickness (in)</strong></td>
                <td width="117"><strong>Cost (C$)</strong></td>
                </tr>
                <tr>
                <td width="274">Plywood-G1S/Oak</td>
                <td width="69">8 x 12</td>
                <td width="105">1/4</td>
                <td width="117">$1.25</td>
                </tr>
                <tr>
                <td width="274">Plywood-G1S/Oak</td>
                <td width="69">8 x 12</td>
                <td width="105">1/2</td>
                <td width="117">$1.5</td>
                </tr>
                <tr>
                <td width="274">Two-Colour HDPE Yellow on Black (Plastic)</td>
                <td width="69">6 x 6</td>
                <td width="105">1/4</td>
                <td width="117">$3.25</td>
                </tr>
                <tr>
                <td width="274">Green Expanded PVC Sheet (Plastic)</td>
                <td width="69">8 x 12</td>
                <td width="105">1/8</td>
                <td width="117">$5.00</td>
                </tr>
                </tbody>
                </table>
                </div>
                <p><strong>To book a staff member to carve your design:</strong></p>
                <ul>
                    <li>Have a valid VPL card in good standing.</li>
                    <li>Print and sign a <a href="http://www.vaughanpl.info/shareit/wp-content/uploads/creation_spaces_customer_agreement.pdf" target="_blank">Customer Agreement Form</a> and return it on your first booking.</li>
                    <li>Read the rules and guidelines below:</li>
                    <ul>
                      <li>Bookings must be made at least 3 days in advance.</li>
                      <li>The Carvey can be booked for a maximum 2-hour carving job.</li>
                      <li>All designs must be uploaded as an Easel editor link.</li>
                    </ul>
                    <li>Request a booking in the form below:</li>
                </ul>
          </div>

          <form action="/shareit/bookings<?php if(!empty($choice)) echo '?choice=' . $choice; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateBooking_service();">     
                <fieldset>
                  <p><strong>All fields with * are required.</strong></p>
                  
                  <p><input name="booking_type_service" type="hidden" value="carving"></p>
                    
                  <p> <label for="lab_branch_service" class="label_text">Branch<sup>*</sup>:&nbsp;</label></p>
                  <p> 
                      <select name="branch_service" title="Local Branch" id="branch_service" required>             
                          <option value="7"  <?php if($branch_service == "7") echo "selected"; ?>>Pierre Berton Resource Library</option>
                      </select>
                  </p>
                  
                  <p> <label for="lab_first_name_service" class="label_text">First Name<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                    <input type="text" name="lab_first_name_service" class="input_space" id="lab_first_name_service" value="<?php echo $first_name_service; ?>" size="50" maxlength="50" required />
                  </p>

                  <p> <label for="lab_last_name_service" class="label_text">Last Name<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                      <input type="text" name="lab_last_name_service" class="input_space" id="lab_last_name_service" size="50" value="<?php echo $last_name_service; ?>" maxlength="50" required />
                  </p>

                  <p> <label for="lab_age_service" class="label_text">Age if under 18:&nbsp;</label></p>
                  <p>
                      <input type="number" name="lab_age_service" class="input_space" id="lab_age_service" value="<?php echo $lab_age_service; ?>" size="15"/>
                  </p>

                  <p> <label for="lab_agreement_service" class="label_text">Customer Agreement Form Signed?<sup>*</sup>:&nbsp;</label> </p>
                  <p> 
                      <input type="radio" id="lab_agreement_service" value="Yes" class="show_radio" name="lab_agreement_service" <?php echo ($lab_agreement_service == "Yes") ? "checked" : ""; ?>>&nbsp;Yes
                      <input type="radio" id="lab_agreement_service" value="No" class="show_radio" name="lab_agreement_service" <?php echo (empty($lab_agreement_service) || $lab_agreement_service == "No") ? "checked" : ""; ?>>&nbsp;No        
                  </p>

                  <p> <label for="lab_certification_service" class="label_text">Have you taken the required certification?<sup>*</sup>:</label> </p>
                  <p>
                      <input type="radio" id="lab_certification_service" value="Yes" class="show_radio" name="lab_certification_service" <?php echo ($lab_certification_service == "Yes") ? "checked" : ""; ?>>&nbsp;Yes
                      <input type="radio" id="lab_certification_service" value="No" class="show_radio" name="lab_certification_service" <?php echo (empty($lab_certification_service) || $lab_certification_service == "No") ? "checked" : ""; ?>>&nbsp;No        
                  </p>

                  <p> <label for="lab_library_card_service" class="label_text">Library Card<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                      <input type="text" name="lab_library_card_service" maxlength="15" class="input_space" id="lab_library_card_service" value="<?php echo $lab_library_card_service; ?>" required />
                  </p>
             
                  <p> <label for="lab_email_service" class="label_text">Email<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                    <input type="email" name="lab_email_service" class="input_space" maxlength="50" id="lab_email_service" size="50" value="<?php echo $lab_email_service; ?>" required />
                  </p>

                  <p> <label for="lab_telephone_service" class="label_text">Telephone<sup>*</sup>:&nbsp;</label> </p>
                  <p>
                      <input type="text" name="lab_telephone_service" class="input_space" id="lab_telephone_service" value="<?php echo $lab_telephone_service; ?>" placeholder=" e.g. (416) 123-4567" required />
                  </p>
                  <p style="color: darkblue;"><strong>The format of phone number should be (416) 123-4567.</strong></p>

                  <p id="p_material"> <label for="lab_material_service" class="label_text">Material<sup>*</sup>:&nbsp;</label><br/>
                    <select name="lab_material_service" id="lab_material_service" required>
                      <option value="">-- Select Material --</option>
                      <option value="plywood_8x12_1x4" <?php echo ($lab_material == "plywood_8x12_1x4") ? "selected" : ""; ?>>Plywood-G1S/Oak (1/4in Thickness)</option>
                      <option value="plywood_8x12_1x2" <?php echo (empty($lab_material) || ($lab_material == "plywood_8x12_1x2")) ? "selected" : ""; ?>>Plywood-G1S/Oak (1/2in Thickness)</option>              
                      <option value="hdpe_yellowblack_6x6_1x4" <?php echo ($lab_material == "hdpe_yellowblack_6x6_1x4") ? "selected" : ""; ?>>Two-Colour HDPE Yellow on Black (Plastic)</option>             
                      <option value="green_pvc_8x12_1x8" <?php echo ($lab_material == "green_pvc_8x12_1x8") ? "selected" : ""; ?>>Green Expanded PVC Sheet (plastic)</option>
                      <option value="use_my_own_material" <?php echo ($lab_material == "use_my_own_material") ? "selected" : ""; ?>>Use My Own Material</option>
                    </select>             
                  </p>

                  <p id="p_carvey_link_field"> <label for="lab_carvey_link_service" class="label_text">Carvey: Add Easel Editor Link:&nbsp;</label><br/>
                   <input type="text" name="lab_carvey_link_service" class="input_space" id="lab_carvey_link_service" value="<?php echo $lab_carvey_link; ?>" size="50" maxlength="150"/>
                  </p>

                  <div id="form_errors"></div> 

                  <div class="labs_email_reminder">
                    Please check your email and spam folder for confirmation of your reservation. Please allow 3 business days for processing.
                  </div>
                  <p class="text_center">
                    <input type="submit" name="submit_carvey" id="submit_carvey" value="Submit">
                  </p>                                    
              </fieldset>
            </form>

        </div>
      </div>



    <div style="color: #31708f;
                background-color: #d9edf7;
                border-color: #bce8f1;
                padding: 15px;
                margin-bottom: 20px;
                border: 1px solid transparent;
                border-radius: 4px;">
        The personal information collected from you on this form will only be used for the purpose of making the bookings. Your personal information will not be shared with outside organizations, except as indicated in the <a href="http://www.vaughanpl.info/about/website_privacy" target="_blank">Privacy Statement</a>.
    </div>

</article><!-- #post-## -->


<!--------------------------------------------------------------------->
<!--     javascript variables and functions for content-bookings     -->
<!--------------------------------------------------------------------->

<script type="text/javascript">

    function changeForms(default_lab)
    {

        var radioBtnSpace = document.getElementById("rb_space");
        var radioBtnEquip = document.getElementById("rb_equipment");
        var radioBtnPrinter = document.getElementById("rb_3d_printer");
        var radioBtnCarving = document.getElementById("rb_carving");

        document.getElementById("formOne").style.display="none";
        document.getElementById("formTwo").style.display="none";
        document.getElementById("formThree").style.display="none";
        document.getElementById("formFour").style.display="none";

        if (default_lab !== undefined)
        {
          if (default_lab == "space")
          {
              radioBtnSpace.checked = true;
          }
          else if (default_lab == "equip")
          {
              radioBtnEquip.checked = true;
          }
          else if (default_lab == "3D_printer")
          {
              radioBtnPrinter.checked = true;
          }
          else if (default_lab == "carving")
          {
              radioBtnCarving.checked = true;
          }
        }

        if (radioBtnSpace.checked){
            document.getElementById("formOne").style.display="block";
            /*if (jQuery('#lab_date_space') !== undefined)
              jQuery('#lab_date_space').data('Zebra_DatePicker').update();*/
        }
        else if (radioBtnEquip.checked){
            document.getElementById("formTwo").style.display="block";
            /*if (jQuery('#lab_date_equip') !== undefined)
              jQuery('#lab_date_equip').data('Zebra_DatePicker').update();*/
        }
        else if (radioBtnPrinter.checked){
            document.getElementById("formThree").style.display="block";
            /*jQuery('#lab_date_service').data('Zebra_DatePicker').update();*/
        }
        else if(radioBtnCarving.checked){
          document.getElementById("formFour").style.display="block";
        }
    }

</script>



<!--------------------------------------------------------------------->
<!--     javascript variables and functions for content-bookaspace   -->
<!--------------------------------------------------------------------->

<script type="text/javascript">
  
  var reserved_times_array = [];
  

  <?php 
      $ccrl_select_space = "";
      $pbrl_select_space = "";
      
      foreach ($special_list['11']['space'] as $key => $value){
          $ccrl_select_space .= "<option value='$key'>" . $value . "</option>";    
      }                    
            
      if(isset($special_list['7']) && isset($special_list['7']['space'])){         
          foreach ($special_list['7']['space'] as $key => $value){
              $pbrl_select_space .= "<option value='$key'>" . $value . "</option>";    
          }                                               
      }

      echo "var labs_ccrl_space =\"$ccrl_select_space\";";
      echo "var labs_pbrl_space =\"$pbrl_select_space\";";
  ?>
 
  ////////////////////////////////////////////////////////////////////////

  <?php 
      $ccrl_select_equip = "";
      $pbrl_select_equip = "";

      foreach ($special_list['11']['equipment'] as $key => $value){
          $ccrl_select_equip .= "<option value='$key'>" . $value . "</option>";    
      }                    
      
      if(isset($special_list['7']) && isset($special_list['7']['equipment'])){         
          foreach ($special_list['7']['equipment'] as $key => $value){
              $pbrl_select_equip .= "<option value='$key'>" . $value . "</option>";    
          }                                               
      }

      echo "var labs_ccrl_equip =\"$ccrl_select_equip\";";
      echo "var labs_pbrl_equip =\"$pbrl_select_equip\";";
  ?>

////////////////////////////////////////////////////////////////////////

  <?php 
      $ccrl_select_service = "";
      $pbrl_select_service = "";

      foreach ($special_list['11']['service'] as $key => $value){
          $ccrl_select_service .= "<option value='$key'>" . $value . "</option>"; 
      }                    
      
      if(isset($special_list['7']) && isset($special_list['7']['service'])){         
          foreach ($special_list['7']['service'] as $key => $value){
              $pbrl_select_service .= "<option value='$key'>" . $value . "</option>";    
          }                                               
      }

      echo "var labs_ccrl_service =\"$ccrl_select_service\";";
      echo "var labs_pbrl_service =\"$pbrl_select_service\";";
  ?>


  var filament_3d_printer = "<option value='White'>White</option>" + 
                            "<option value='Black'>Black</option>" + 
                            "<option value='Grey'>Grey</option>";  // +
                            // "<option value='Blue'>Blue</option>"   +
                            // "<option value='Red'>Red</option>";



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

////////////////////////////////////////////////////////////////////////





////////////////////////////////////////////////////////////////////////
////////                   Change Labs                             ///// 
////////////////////////////////////////////////////////////////////////

  function changeLabs_space(default_lab){
    var val = jQuery('#branch_space').val();
    var select = jQuery('#lab_name_space');

    if(val == 7){
        select.empty().append(labs_pbrl_space);
    }
    if(val == 11){
        select.empty().append(labs_ccrl_space);
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


  function changeLabs_equip(default_lab){
    //have to clear the date 
    //var clear_date = jQuery('#lab_date_equip').val("");

    var val = jQuery('#branch_equip').val();
    var select = jQuery('#lab_name_equip');

    if(val == 7){
        select.empty().append(labs_pbrl_equip);

        //pbrl has different available dates
        // date picker restriction
        jQuery('#lab_date_equip').Zebra_DatePicker({ 
          direction: [3, 27],
          disabled_dates: [
                            '24 12 2019',
                            '25 12 2019',
                            '26 12 2019',
                            '31 12 2019',
                            '01 01 2020',
                            '17 02 2020',
                            '10 04 2020',
                            '12 04 2020',
                            '13 04 2020',
                            '18 05 2020',
                            '01 07 2020',
                            '03 08 2020',
                            '07 09 2020',
                            '12 10 2020',
                            '24 12 2020',
                            '25 12 2020',
                            '26 12 2020',
                            '31 12 2020',
                            '01 01 2021',
                            '11 10 2021',
                            '24 12 2021',
                            '25 12 2021',
                            '26 12 2021',
                            '31 12 2021',
                            '01 01 2022']});
    }
    if(val == 11){
        select.empty().append(labs_ccrl_equip);



        //pbrl has different available dates
        // date picker restriction
        jQuery('#lab_date_equip').Zebra_DatePicker({ 
          direction: [3, 27],
          disabled_dates: [
                            '24 12 2019',
                            '25 12 2019',
                            '26 12 2019',
                            '31 12 2019',
                            '01 01 2020',
                            '17 02 2020',
                            '10 04 2020',
                            '12 04 2020',
                            '13 04 2020',
                            '18 05 2020',
                            '01 07 2020',
                            '03 08 2020',
                            '07 09 2020',
                            '12 10 2020',
                            '24 12 2020',
                            '25 12 2020',
                            '26 12 2020',
                            '31 12 2020',
                            '01 01 2021',
                            '11 10 2021',
                            '24 12 2021',
                            '25 12 2021',
                            '26 12 2021',
                            '31 12 2021',
                            '01 01 2022']});
    }

    

    //sets the default item as selected
    if (default_lab !== undefined) {
      
        //set the default lab field 
        jQuery('#lab_name_equip').find('option').each(function(i,e){
            if(jQuery(e).val() == default_lab){
                jQuery('#lab_name_equip').prop('selectedIndex',i);
            }
        });            
    }

    limitTime_equip();

  }


  function changeLabs_service(default_lab){
  
    var val = jQuery('#branch_service').val();
    var select = jQuery('#lab_name_service');

    var filament = jQuery('#lab_filament_colour_service');

    var filament_two = jQuery('#lab_filament_two_colour_service');

    if(val == 7){

        select.empty().append(labs_pbrl_service);
          
        filament.empty().append(filament_colour);
        filament_two.empty().append(filament_two_colour);
    }
    if(val == 11){
        //update the dates
        // var datepicker1 = jQuery('#lab_date_service').data('Zebra_DatePicker');

        // datepicker1.update({ 
        //   direction: [3, 27],
        //   disabled_dates: ['* * * 1',
        //     <?php 
        //     $reserved_times = getReservedTimes3DPrinter();
            
        //     foreach($reserved_times as $rt){
        //       $rdate = new DateTime($rt->date);
        //       echo "'" . $rdate->format("d m Y") . "',";
        //     }
        //     ?>

        //     '01 01 2019',
        //     '18 02 2019',
        //     '19 04 2019',
        //     '21 04 2019',
        //     '22 04 2019',
        //     '20 05 2019',
        //     '01 07 2019',
        //     '05 08 2019',
        //     '02 09 2019',
        //     '14 10 2019',
        //     '24 12 2019',
        //     '25 12 2019',
        //     '26 12 2019',
        //     '31 12 2019',
        //     '01 01 2020']
        // });        

        select.empty().append(labs_ccrl_service);

        filament.empty().append(filament_3d_printer);
    }


    //sets the default item as selected
    // if (default_lab !== undefined) {
    //     //set the default lab field 
    //     jQuery('#lab_name_service').find('option').each(function(i,e){
    //         if(jQuery(e).val() == default_lab){
    //             jQuery('#lab_name_service').prop('selectedIndex',i);
    //         }
    //     });            
    // }

    //changeFields(default_lab);
  }


  ////////////////////////////////////////////////////////////////////////
  ////////            validate Booking                               ///// 
  ////////////////////////////////////////////////////////////////////////

  function validateBooking_space(){

    var error_message = "";
    // var age_field = jQuery("#lab_age_space").val();
    // //console.log(age_field);
    // if(isNaN(age_field)){
    //   error_message += "Age must be an number.\n";     
    // }

    //check date has been filled in: 
    var date_field = jQuery('#lab_date_space').val();
    if(date_field.length == 0){
      error_message += "Please fill in the date field.\n";
    }
    
    if(error_message.length > 0){
      alert("Errors:\n" + error_message);
      return false;
    }
    
    return true;
  }


  function validateBooking_equip(){

    var error_message = "";
    // var age_field = jQuery("#lab_age_equip").val();

    // if(isNaN(age_field)){
    //   error_message += "Age must be an number.\n";     
    // }

    //check date has been filled in: 
    var date_field = jQuery('#lab_date_equip').val();


    if(date_field.length == 0){
      error_message += "Please fill in the date field.\n";
    }
    
    
    if(error_message.length > 0){
      alert("Errors:\n" + error_message);
      return false;
    }
    
    return true;
  }


  function validateBooking_service(){
  
    var error_message = "";
    var branch = jQuery('#branch_service').val();   

    if(branch == 11) 
    {
        // var age_field = jQuery("#lab_age_service").val();

        // if(isNaN(age_field)){
        //   error_message += "Age must be an number.\n";     
        // }

        //check date has been filled in: 
        // var date_field = jQuery('#lab_date_service').val();
        // if(date_field.length == 0){
        //   error_message += "Please fill in the date field.\n";
        // }

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


  function changeFields(default_value){
    var select = jQuery('#lab_name_service').val();
    var filament = jQuery('#lab_filament_colour_service');
    var filament_two = jQuery('#lab_filament_two_colour_service');
    
    
    if((select == "carvey") || ((default_value !== undefined) && (default_value == "carvey"))){

      //jQuery('#p_date_field').addClass("hide_field");
      //jQuery('#lab_date_service').removeAttr("required");  

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
    else if((select =="3d_station_3") || ((default_value !== undefined) && (default_value == "3d_station_3"))){    
      
      //jQuery('#p_date_field').addClass("hide_field");
      //jQuery('#lab_date_service').removeAttr("required");  

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

       /////////////////////////////////////////////////////////////////////////

        <?php 
              echo "var filament_selected =\"$lab_filament_colour\";";
              echo "var filament_two_selected =\"$lab_filament_two_colour\";";
        ?>


        if (filament_selected !== undefined) {
                  //set the default lab field 
                  jQuery('#lab_filament_colour_service').find('option').each(function(i,e){
                      if(jQuery(e).val() == filament_selected){
                          console.log("filament");
                          console.log(filament_selected);
                          console.log(jQuery(e).val());
                          jQuery('#lab_filament_colour_service').prop('selectedIndex',i);
                      }});            
        }


        if (filament_two_selected !== undefined) {
                  //set the default lab field 
                  jQuery('#lab_filament_two_colour_service').find('option').each(function(i,e){
                      if(jQuery(e).val() == filament_two_selected){
                          console.log("filament_two");
                          console.log(filament_two_selected);
                          console.log(jQuery(e).val());
                          jQuery('#lab_filament_two_colour_service').prop('selectedIndex',i);
                      }});            
        } 

        /////////////////////////////////////////////////////////////////////////
 
      
      jQuery('#p_carvey_link_field').addClass("hide_field");
    }
    else{
      //jQuery('#p_date_field').removeClass("hide_field");
      //jQuery('#lab_date_service').attr("required", true);      
              
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

      //jQuery('#lab_date_service').data('Zebra_DatePicker').update();       
    }
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


  function checkForReservedTime_new(lab_name, lab_date, lab_time){

    var num_times = reserved_times_array.length;
    var found = false;
    for(var i = 0; i < num_times; i++){
      if (lab_time == "1000") 
      {
          if(reserved_times_array[i].date == lab_date && 
             reserved_times_array[i].lab == lab_name && 
             (reserved_times_array[i].time == lab_time ||
              reserved_times_array[i].time == "10am" ||
              reserved_times_array[i].time == "12pm")){
              found = true;
              break;
          }   
      }
      else if (lab_time == "1330") 
      {
          if(reserved_times_array[i].date == lab_date && 
             reserved_times_array[i].lab == lab_name && 
             (reserved_times_array[i].time == lab_time ||
              reserved_times_array[i].time == "12pm" ||
              reserved_times_array[i].time == "2pm" ||
              reserved_times_array[i].time == "4pm" ||
              reserved_times_array[i].time == "4-5pm")){
              found = true;
              break;
          }
      }  
      else if (lab_time == "1700") 
      {
          if(reserved_times_array[i].date == lab_date && 
             reserved_times_array[i].lab == lab_name && 
             (reserved_times_array[i].time == lab_time ||
              reserved_times_array[i].time == "6pm")){
              found = true;
              break;
          }
      }
      else
      {
          if(reserved_times_array[i].date == lab_date && reserved_times_array[i].lab == lab_name && reserved_times_array[i].time == lab_time){
              found = true;
              break;
          }
      }
    }
    return found;
  }
 
  

  ////////////////////////////////////////////////////////////////////////
  ////////                   limit Time                              ///// 
  ////////////////////////////////////////////////////////////////////////

  function limitTime_service(default_value)
  {
     console.log(default_value);

     var datepicker = jQuery('#lab_date_service').data('Zebra_DatePicker');

     datepicker.update({ 
            direction: [3, 27],
            disabled_dates:  [//'* * * 1',
                              '24 12 2019',
                              '25 12 2019',
                              '26 12 2019',
                              '31 12 2019',
                              '01 01 2020',
                              '17 02 2020',
                              '10 04 2020',
                              '12 04 2020',
                              '13 04 2020',
                              '18 05 2020',
                              '01 07 2020',
                              '03 08 2020',
                              '07 09 2020',
                              '12 10 2020',
                              '24 12 2020',
                              '25 12 2020',
                              '26 12 2020',
                              '31 12 2020',
                              '01 01 2021',
                              '11 10 2021',
                              '24 12 2021',
                              '25 12 2021',
                              '26 12 2021',
                              '31 12 2021',
                              '01 01 2022']});

     //For weekend 3D Printer
    var weekendTime = [{value: "10am", text: "10:00am"},
                       {value: "2pm",  text: " 2:00pm"}];
    
    //For weekday 3D Printer
    var weekdayTime = [{value: "10am", text: "10:00am"}];

    //console.log(jQuery('#LabDate').val());   
    var chosen_date = jQuery('#lab_date_service').val();

    //if the date hasn't been chosen yet, we can just exit immediately.
    if(chosen_date == ""){
      return; 
    }

    var chosen_date_obj = new Date(chosen_date); 
    var day = chosen_date_obj.getDay();
    var isWeekend = (day == 4) || (day == 5) || (day == 6); 

    //clear all options from the start time select box 
    var select_time_slot_erase = document.getElementById("lab_time_service");
    var length = select_time_slot_erase.options.length;
    for (i = 0; i < length; i++) {
      select_time_slot_erase.options[0] = null;
    }

    var select_time_slot = document.getElementById("lab_time_service");
    var branch_choice = jQuery("#branch_service");  
    var lab_name = (branch_choice.val() == 7) ? "3d_station_3" : "3d_station_1"; 
    var option = "";
    var i = 0;

    if(isWeekend)
    {
        il = weekendTime.length;
        for (; i < il; i += 1) 
        {
            if(!checkForReservedTime(lab_name, chosen_date, weekendTime[i].value))
            {
                option = document.createElement('option');
                option.setAttribute('value', weekendTime[i].value);
                if (default_value !== undefined)
                {
                  console.log(default_value);
                  console.log(" 27");
                  if (default_value == option.value)
                      option.selected = true;
                }
                option.appendChild(document.createTextNode(weekendTime[i].text));
                select_time_slot.appendChild(option);
            }
        }
    }
    else
    {
        il = weekdayTime.length;
        for (; i < il; i += 1) 
        {
            if(!checkForReservedTime(lab_name, chosen_date, weekdayTime[i].value))
            {
                option = document.createElement('option');
                option.setAttribute('value', weekdayTime[i].value);
                if (default_value !== undefined)
                {
                  console.log(default_value);
                  console.log(" 28");
                  if (default_value == option.value)
                      option.selected = true;
                }
                option.appendChild(document.createTextNode(weekdayTime[i].text));
                select_time_slot.appendChild(option);
            }
        }
    }

  if (select_time_slot.length <= 0)
    {
        option = document.createElement('option');
        option.setAttribute('value', '');
        option.appendChild(document.createTextNode("It is fully booked. Please choose a different date."));
        select_time_slot.appendChild(option);
    }

  }


  function limitTime_space(default_value)
  {

    console.log("Testing");

    console.log(default_value);

    var chosen_lab = jQuery("#lab_name_space").val();

    var datepicker = jQuery('#lab_date_space').data('Zebra_DatePicker');

    
    // limit date 
    if(chosen_lab == "green_room")
    {    
        datepicker.update({ 
            direction: [3, 27],
            disabled_dates:  [//'* * * 1',
                              '24 12 2019',
                              '25 12 2019',
                              '26 12 2019',
                              '31 12 2019',
                              '01 01 2020',
                              '17 02 2020',
                              '10 04 2020',
                              '12 04 2020',
                              '13 04 2020',
                              '18 05 2020',
                              '01 07 2020',
                              '03 08 2020',
                              '07 09 2020',
                              '12 10 2020',
                              '24 12 2020',
                              '25 12 2020',
                              '26 12 2020',
                              '31 12 2020',
                              '01 01 2021',
                              '11 10 2021',
                              '24 12 2021',
                              '25 12 2021',
                              '26 12 2021',
                              '31 12 2021',
                              '01 01 2022']});
    }
    else
    { 
        datepicker.update({ 
          direction: [3, 27],
          disabled_dates:[//'* * * 1',
                          '24 12 2019',
                          '25 12 2019',
                          '26 12 2019',
                          '31 12 2019',
                          '01 01 2020',
                          '17 02 2020',
                          '10 04 2020',
                          '12 04 2020',
                          '13 04 2020',
                          '18 05 2020',
                          '01 07 2020',
                          '03 08 2020',
                          '07 09 2020',
                          '12 10 2020',
                          '24 12 2020',
                          '25 12 2020',
                          '26 12 2020',
                          '31 12 2020',
                          '01 01 2021',
                          '11 10 2021',
                          '24 12 2021',
                          '25 12 2021',
                          '26 12 2021',
                          '31 12 2021',
                          '01 01 2022']});
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


    var recording_Friday = [{value: "10am", text: "10:00am - 12:00pm"},
                            {value: "12pm", text: "12:00pm - 2:00pm"},
                            {value: "2pm",  text: " 2:00pm - 4:00pm"},
                            {value: "4-5pm",  text: " 4:00pm - 5:00pm"}];

    var recording_weekendTime =  [{value: "930am", text: " 9:30am - 10:00am"},
                                  {value: "10am", text: "10:00am - 12:00pm"},
                                  {value: "12pm", text: "12:00pm - 2:00pm"},
                                  {value: "2pm",  text: " 2:00pm - 4:00pm"}];
      
    var recording_weekdayTime =   [{value: "930am", text: " 9:30am - 10:00am"},
                                   {value: "10am", text: "10:00am - 12:00pm"},
                                   {value: "12pm", text: "12:00pm - 2:00pm"},
                                   {value: "2pm",  text: " 2:00pm - 4:00pm"},
                                   {value: "4pm",  text: " 4:00pm - 6:00pm"},
                                   {value: "6pm",  text: " 6:00pm - 8:00pm"}];
      

    var space_monday_time = [{value: "930am",  text: " 9:30am - 10:00am"},
                             {value: "1000", text: "10:00am - 1:00pm"}];

    var space_tue_thur_time = [{value: "930am",  text: " 9:30am - 10:00am"},
                               {value: "1000", text: "10:00am - 1:00pm"},
                               {value: "1330", text: " 1:30pm - 4:30pm"},
                               {value: "1700", text: " 5:00pm - 8:00pm"}];

    var space_fri_sun_time = [{value: "930am",  text: " 9:30am - 10:00am"},
                               {value: "1000", text: "10:00am - 1:00pm"},
                               {value: "1330", text: " 1:30pm - 4:30pm"}];


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
    var lab_choice = jQuery("#lab_name_space");   
    var option = "";
    var i = 0;
      

    //Populate the time slot options. Different for 3D Printing Stations. 

    var new_times_date = new Date("2019-12-06");

    if(chosen_date_obj >= new_times_date)
    {
        if(day == 0)
        {
            il = space_monday_time.length;
            for (; i < il; i += 1) 
            {
                if(!checkForReservedTime_new(lab_choice.val(), chosen_date, space_monday_time[i].value))
                {
                    option = document.createElement('option');
                    option.setAttribute('value', space_monday_time[i].value);
                    if (default_value !== undefined)
                    {
                      console.log(default_value);
                      console.log(" 27");
                      if (default_value == option.value)
                          option.selected = true;
                    }
                    option.appendChild(document.createTextNode(space_monday_time[i].text));
                    select_time_slot.appendChild(option);
                }
            }
        }
        else if (day == 1 || day == 2 || day == 3)
        {
            il = space_tue_thur_time.length;
            for (; i < il; i += 1) 
            {
                if(!checkForReservedTime_new(lab_choice.val(), chosen_date, space_tue_thur_time[i].value))
                {
                    option = document.createElement('option');
                    option.setAttribute('value', space_tue_thur_time[i].value);
                    if (default_value !== undefined)
                    {
                      console.log(default_value);
                      console.log(" 28");
                      if (default_value == option.value)
                          option.selected = true;
                    }
                    option.appendChild(document.createTextNode(space_tue_thur_time[i].text));
                    select_time_slot.appendChild(option);
                }
            }
        }
        else
        {
            il = space_fri_sun_time.length;
            for (; i < il; i += 1) 
            {
                if(!checkForReservedTime_new(lab_choice.val(), chosen_date, space_fri_sun_time[i].value))
                {
                    option = document.createElement('option');
                    option.setAttribute('value', space_fri_sun_time[i].value);
                    if (default_value !== undefined)
                    {
                      console.log(default_value);
                      console.log(" 29");
                      if (default_value == option.value)
                          option.selected = true;
                    }
                    option.appendChild(document.createTextNode(space_fri_sun_time[i].text));
                    select_time_slot.appendChild(option);
                }
            }
        }

        if (select_time_slot.length <= 0)
        {
            option = document.createElement('option');
            option.setAttribute('value', '');
            option.appendChild(document.createTextNode("It is fully booked. Please choose a different date."));
            select_time_slot.appendChild(option);
        }
    }
    else
    {
            if(lab_choice.val() == "recording")
            {
                      if(isWeekend)
                      {

                          if(day == 4)
                          {
                              il = recording_Friday.length;
                              for (; i < il; i += 1) 
                              {
                                  if(!checkForReservedTime(lab_choice.val(), chosen_date, recording_Friday[i].value))
                                  {
                                      option = document.createElement('option');
                                      option.setAttribute('value', recording_Friday[i].value);
                                      if (default_value !== undefined)
                                      {
                                        console.log(default_value);
                                        console.log(" 17");
                                        if (default_value == option.value)
                                            option.selected = true;
                                      }
                                      option.appendChild(document.createTextNode(recording_Friday[i].text));
                                      select_time_slot.appendChild(option);
                                  }
                              }
                          }
                          else
                          {
                              il = recording_weekendTime.length;
                              for (; i < il; i += 1) 
                              {
                                  if(!checkForReservedTime(lab_choice.val(), chosen_date, recording_weekendTime[i].value))
                                  {
                                      option = document.createElement('option');
                                      option.setAttribute('value', recording_weekendTime[i].value);
                                      if (default_value !== undefined)
                                      {
                                        console.log(default_value);
                                        console.log(" 15");
                                        if (default_value == option.value)
                                            option.selected = true;
                                      }
                                      option.appendChild(document.createTextNode(recording_weekendTime[i].text));
                                      select_time_slot.appendChild(option);
                                  }
                              }
                          }

                          if (select_time_slot.length <= 0)
                          {
                              option = document.createElement('option');
                              option.setAttribute('value', '');
                              option.appendChild(document.createTextNode("It is fully booked. Please choose a different date."));
                              select_time_slot.appendChild(option);
                          }
                      }
                      else
                      {  
                          if(day == 0)
                          {
                              option = document.createElement('option');
                              option.setAttribute('value', '');
                              option.appendChild(document.createTextNode("It is fully booked. Please choose a different date."));
                              select_time_slot.appendChild(option);
                          } 
                          else
                          {
                              il = recording_weekdayTime.length;
                              for (; i < il; i += 1) {
                                  if(!checkForReservedTime(lab_choice.val(), chosen_date, recording_weekdayTime[i].value)){
                                    option = document.createElement('option');
                                    option.setAttribute('value', recording_weekdayTime[i].value);
                                    if (default_value !== undefined)
                                    {
                                        console.log(default_value);
                                        console.log(" 16");
                                        if (default_value == option.value)
                                            option.selected = true;
                                    }
                                    option.appendChild(document.createTextNode(recording_weekdayTime[i].text));   
                                    select_time_slot.appendChild(option);
                                  }
                             }
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
                          if (default_value !== undefined)
                          {
                            console.log(default_value);
                            console.log(" 5");
                            if (default_value == option.value)
                                option.selected = true;
                          }
                          option.appendChild(document.createTextNode(weekendTime[i].text));
                          select_time_slot.appendChild(option);
                      }
                  }

                  if (select_time_slot.length <= 0)
                  {
                      option = document.createElement('option');
                      option.setAttribute('value', '');
                      option.appendChild(document.createTextNode("It is fully booked. Please choose a different date."));
                      select_time_slot.appendChild(option);
                  }
              }
              else
              {   
                  if(day == 0)
                  {
                      option = document.createElement('option');
                      option.setAttribute('value', '');
                      option.appendChild(document.createTextNode("It is fully booked. Please choose a different date."));
                      select_time_slot.appendChild(option);
                  }
                  else
                  {
                      il = weekdayTime.length;
                      for (; i < il; i += 1) {
                          if(!checkForReservedTime(lab_choice.val(), chosen_date, weekdayTime[i].value)){
                            option = document.createElement('option');
                            option.setAttribute('value', weekdayTime[i].value);
                            if (default_value !== undefined)
                            {
                                console.log(default_value);
                                console.log(" 6");
                                if (default_value == option.value)
                                    option.selected = true;
                            }
                            option.appendChild(document.createTextNode(weekdayTime[i].text));   
                            select_time_slot.appendChild(option);
                          }   
                      }            
                  }
              }
          }
    }
}


  function limitTime_equip(default_value){
      //PBRL weekday times after Sep 18, 2017
      var pbrl_weekday_times =   [{value: "10am", text: "10:00am - 12:00pm"},
                                  {value: "12pm", text: "12:00pm - 2:00pm"},
                                  {value: "2pm",  text: " 2:00pm - 4:00pm"},
                                  {value: "4pm",  text: " 4:00pm - 6:00pm"},
                                  {value: "6pm",  text: " 6:00pm - 8:00pm"}];

      //PBRL weekend times after Sep 18, 2017
      var pbrl_weekend_times = [{value: "10am", text: "10:00am - 12:00pm"},
                                {value: "12pm", text: "12:00pm - 2:00pm"},
                                {value: "2pm",  text: " 2:00pm - 4:00pm"}];  


      //For non 3D Printing stations CCRL
      var weekendTime =  [{value: "10am", text: "10:00am - 12:00pm"},
                          {value: "12pm", text: "12:00pm - 2:00pm"},
                          {value: "2pm",  text: " 2:00pm - 4:00pm"}];
      
      //For non 3D Printing Stations CCRL
      var weekdayTime =  [{value: "10am", text: "10:00am - 12:00pm"},
                          {value: "12pm", text: "12:00pm - 2:00pm"},
                          {value: "2pm",  text: " 2:00pm - 4:00pm"},
                          {value: "4pm",  text: " 4:00pm - 6:00pm"},
                          {value: "6pm",  text: " 6:00pm - 8:00pm"}];
        
      //For Oculus Rift CCRL
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
      var chosen_date = jQuery('#lab_date_equip').val();

      //if the date hasn't been chosen yet, we can just exit immediately.
      if(chosen_date == ""){
        return; 
      }

      var chosen_date_obj = new Date(chosen_date); 
      var day = chosen_date_obj.getDay();

      var chosen_month = chosen_date_obj.getMonth();
      var chosen_date = chosen_date_obj.getDate();


      
      

      //we are considering weekdays as tues - thurs and weekends as fri - sun as per Kristin 
      var isWeekend = (day == 4) || (day == 5) || (day == 6);  
      
      //clear all options from the start time select box 
      var select_time_slot_erase = document.getElementById("lab_time_equip");
      var length = select_time_slot_erase.options.length;
      for (i = 0; i < length; i++) {
        select_time_slot_erase.options[0] = null;
      }
      
      var select_time_slot = document.getElementById("lab_time_equip");
      var lab_choice = jQuery("#lab_name_equip"); 
      var option = "";
      var i = 0;
        
      
      if(jQuery("#branch_equip").val() == "7"){
          //Populate the time slot options for different days
          if(day >= 0 && day <= 3){
              il = pbrl_weekday_times.length;
              for (; i < il; i += 1) {
                if(!checkForReservedTime(lab_choice.val(), chosen_date, pbrl_weekday_times[i].value)){
                  option = document.createElement('option');
                  option.setAttribute('value', pbrl_weekday_times[i].value);
                  if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 1");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                  option.appendChild(document.createTextNode(pbrl_weekday_times[i].text));
                  select_time_slot.appendChild(option);
                }
              }               
          }
          else{
              il = pbrl_weekend_times.length;
              for (; i < il; i += 1) {
                if(!checkForReservedTime(lab_choice.val(), chosen_date, pbrl_weekend_times[i].value)){
                  option = document.createElement('option');
                  option.setAttribute('value', pbrl_weekend_times[i].value);
                  if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 2");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                  option.appendChild(document.createTextNode(pbrl_weekend_times[i].text));
                  select_time_slot.appendChild(option);
                }
              }    

          }
      }
      else{
        //Populate the time slot options. Different for 3D Printing Stations. 
        if(lab_choice.val() == "3d_station_1" || lab_choice.val() == "3d_station_2" || lab_choice.val() == "3d_station_3" || lab_choice.val() == "carving_station"){
          if(isWeekend){
            il = weekendTime_3d_stations.length;
            for (; i < il; i += 1) {
                option = document.createElement('option');
                option.setAttribute('value', weekendTime_3d_stations[i].value);
                if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 3");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                option.appendChild(document.createTextNode(weekendTime_3d_stations[i].text));
                select_time_slot.appendChild(option);
            }      
          }
          else{
            il = weekdayTime_3d_stations.length;
            for (; i < il; i += 1) {
                option = document.createElement('option');
                option.setAttribute('value', weekdayTime_3d_stations[i].value);
                if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 4");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                option.appendChild(document.createTextNode(weekdayTime_3d_stations[i].text));
                select_time_slot.appendChild(option);
            }
          }
              
        }
        else if(lab_choice.val() == "oculus_rift"){
          if(isWeekend){
            il = weekendTime_oculus_stations.length;
            for (; i < il; i += 1) {
                if(!checkForReservedTime(lab_choice.val(), chosen_date, weekendTime_oculus_stations[i].value)){
                  option = document.createElement('option');
                  option.setAttribute('value', weekendTime_oculus_stations[i].value);
                  if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 5");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                  option.appendChild(document.createTextNode(weekendTime_oculus_stations[i].text));
                  select_time_slot.appendChild(option);
                }
            }      
          }
          else{
            il = weekdayTime_oculus_stations.length;
            for (; i < il; i += 1) {
                if(!checkForReservedTime(lab_choice.val(), chosen_date, weekdayTime_oculus_stations[i].value)){
                  option = document.createElement('option');
                  option.setAttribute('value', weekdayTime_oculus_stations[i].value);
                  if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 6");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                  option.appendChild(document.createTextNode(weekdayTime_oculus_stations[i].text));
                  select_time_slot.appendChild(option);
                }
            }
          }       
        }
        else{ 
          if(isWeekend){
            il = weekendTime.length;
            for (; i < il; i += 1) {
                if(!checkForReservedTime(lab_choice.val(), chosen_date, weekendTime[i].value)){
                  option = document.createElement('option');
                  option.setAttribute('value', weekendTime[i].value);
                  if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 7");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                  option.appendChild(document.createTextNode(weekendTime[i].text));
                  select_time_slot.appendChild(option);
                }
            }
          }
          else{   
            il = weekdayTime.length;
            for (; i < il; i += 1) {
                if(!checkForReservedTime(lab_choice.val(), chosen_date, weekdayTime[i].value)){
                  option = document.createElement('option');
                  option.setAttribute('value', weekdayTime[i].value);
                  if (default_value !== undefined)
                  {
                      console.log(default_value);
                      console.log(" 8");
                      if (default_value == option.value)
                          option.selected = true;
                  }
                  option.appendChild(document.createTextNode(weekdayTime[i].text));   
                  select_time_slot.appendChild(option);
                }
            }   
          }
        }    
      } 
}



</script>


<script type="text/javascript">
  
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



    // date picker restriction
    // these include dates due to repairs and general unavailability.
    jQuery('#lab_date_space').Zebra_DatePicker({ 
      direction: [3, 27],
      disabled_dates:[
                      '24 12 2019',
                      '25 12 2019',
                      '26 12 2019',
                      '31 12 2019',
                      '01 01 2020',
                      '17 02 2020',
                      '10 04 2020',
                      '12 04 2020',
                      '13 04 2020',
                      '18 05 2020',
                      '01 07 2020',
                      '03 08 2020',
                      '07 09 2020',
                      '12 10 2020',
                      '24 12 2020',
                      '25 12 2020',
                      '26 12 2020',
                      '31 12 2020',
                      '01 01 2021',
                      '11 10 2021',
                      '24 12 2021',
                      '25 12 2021',
                      '26 12 2021',
                      '31 12 2021',
                      '01 01 2022']});

    // date picker restriction
  jQuery('#lab_date_equip').Zebra_DatePicker({ 
    direction: [3, 27],
    disabled_dates:[  
                      '24 12 2019',
                      '25 12 2019',
                      '26 12 2019',
                      '31 12 2019',
                      '01 01 2020',
                      '17 02 2020',
                      '10 04 2020',
                      '12 04 2020',
                      '13 04 2020',
                      '18 05 2020',
                      '01 07 2020',
                      '03 08 2020',
                      '07 09 2020',
                      '12 10 2020',
                      '24 12 2020',
                      '25 12 2020',
                      '26 12 2020',
                      '31 12 2020',
                      '01 01 2021',
                      '11 10 2021',
                      '24 12 2021',
                      '25 12 2021',
                      '26 12 2021',
                      '31 12 2021',
                      '01 01 2022']});  

    // date picker restriction
  jQuery('#lab_date_service').Zebra_DatePicker({ 
    direction: [3, 27],
    disabled_dates:[
                      '24 12 2019',
                      '25 12 2019',
                      '26 12 2019',
                      '31 12 2019',
                      '01 01 2020',
                      '17 02 2020',
                      '10 04 2020',
                      '12 04 2020',
                      '13 04 2020',
                      '18 05 2020',
                      '01 07 2020',
                      '03 08 2020',
                      '07 09 2020',
                      '12 10 2020',
                      '24 12 2020',
                      '25 12 2020',
                      '26 12 2020',
                      '31 12 2020',
                      '01 01 2021',
                      '11 10 2021',
                      '24 12 2021',
                      '25 12 2021',
                      '26 12 2021',
                      '31 12 2021',
                      '01 01 2022']});                        


  changeForms("<?php echo $booking_type; ?>");

  //update the lab options
     
  //changeLabs_space("<?php echo $lab_name_space;?>");

  var date_space = <?php echo empty($lab_date_space) ? "\"\"" : "\"$lab_date_space\"";?>;

  //console.log(date_space);

  if (date_space !== undefined)
  {
   //   limitTime_space("<?php echo $lab_time_slot_space;?>");
  }

  //changeLabs_equip("<?php echo $lab_name_equip; ?>");

  var date_equip = <?php echo empty($lab_date_equip) ? "\"\"" : "\"$lab_date_equip\"";?>;


  if (date_equip !== undefined)
  {
   //   limitTime_equip("<?php echo $lab_time_slot_equip;?>");
  }

  changeLabs_service("<?php echo $lab_name_service; ?>");
    
});



</script>



<!--------------------------------------------------------------------->
<!--     javascript variables and functions for content-bookequipment-->
<!--------------------------------------------------------------------->




<!--------------------------------------------------------------------->
<!--     javascript variables and functions for content-bookaservice -->
<!--------------------------------------------------------------------->