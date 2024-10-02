/*!
 * Contact Buttons Plugin Demo 0.1.0
 * https://github.com/joege/contact-buttons-plugin
 *
 * Copyright 2015, José Gonçalves
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */


// Initialize Share-Buttons
jQuery.contactButtons({
  effect  : '',
  buttons : {
    'facebook':   { class: 'facebook',  use: true, icon: 'facebook',  link: 'https://www.facebook.com/vaughanpl',              title: 'Like on Facebook', alt: 'Blog Logo'},
    'twitter':    { class: 'twitter',   use: true, icon: 'twitter',   link: 'https://twitter.com/vaughanpl',                   title: 'Follow on Twitter', alt: 'Blog Logo' },
    'youtube':    { class: 'youtube',   use: true, icon: 'youtube',   link: 'http://www.youtube.com/user/VaughanPL',           title: 'Catch us on Youtube', alt: 'Blog Logo' },
    'pinterest':  { class: 'pinterest', use: true, icon: 'pinterest', link: 'https://www.pinterest.com/vaughanpl/',            title: 'Check us out on Pinterest', alt: 'Blog Logo' },
    'instagram':  { class: 'instagram', use: true, icon: 'instagram', link: 'https://www.instagram.com/vaughanpubliclibraries/',            title: 'Join us on Instagram', alt: 'Instagram Logo' },
    'Blogs': { class: 'blog', use: true, icon: 'book', link: 'http://www.vaughanpl.info/news_and_events/blogs', title: 'VPL Blog', alt: 'Blog Logo' },
    'Eventbrite': { class: 'eventbrite', use: true, icon: 'eventbrite', link: 'https://www.eventbrite.ca/o/vaughan-public-libraries-13595422412', title: 'VPL on Eventbrite', alt: 'Register programs on Eventbrite' }
  }
});


/*jQuery(".site-content").on("click", function(){
  console.log("Hello world");
});*/



jQuery(document).ready(function () {
  /*jQuery("#menu-item-100").hover(function(){
    jQuery("#dropdown-content").css("display", "block");
    }, function(){
    jQuery('#dropdown-content').css("display", "none");
  });*/
  //console.log("here");

  jQuery(".site-content").on("click", function(){
    //check if the sidebar is visible. 
    //if the sidebar is visible, close it 
    if(jQuery("#masonic-toggle").is(":checked")){
      jQuery("#masonic-toggle").prop( "checked", false );    
    }    
  });


});
/*
function hoverLink(){
  console.log("hover");
}

function leaveLink(){
  console.log("leave");
}*/