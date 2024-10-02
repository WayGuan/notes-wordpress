/*!
 * Contact Buttons Plugin Demo 0.1.0
 * https://github.com/joege/contact-buttons-plugin
 *
 * Copyright 2015, José Gonçalves
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

(function (jQuery) {
  'use strict';

  // Main function
  jQuery.contactButtons = function( options ){

    // Define the defaults
    var defaults = {
      effect  : 'slide-on-scroll', // slide-on-scroll
      buttons : {
        'facebook':   { class: 'facebook',  use: false, icon: 'facebook',    link: '', title: 'Follow on Facebook' },
        'google':     { class: 'gplus',     use: false, icon: 'google-plus', link: '', title: 'Visit on Google Plus' },
        'linkedin':   { class: 'linkedin',  use: false, icon: 'linkedin',    link: '', title: 'Visit on LinkedIn' },
        'twitter':    { class: 'twitter',   use: false, icon: 'twitter',     link: '', title: 'Follow on Twitter' },
        'pinterest':  { class: 'pinterest', use: false, icon: 'pinterest',   link: '', title: 'Follow on Pinterest' },
        'phone':      { class: 'phone',     use: false, icon: 'phone',       link: '', title: 'Call us', type: 'phone' },
        'email':      { class: 'email',     use: false, icon: 'envelope',    link: '', title: 'Send us an email', type: 'email' }
      }
    };

    // Merge defaults and options
    var s,
        settings = options;
    for (s in defaults.buttons) {
      if (options.buttons[s]) {
        settings.buttons[s] = jQuery.extend( defaults.buttons[s], options.buttons[s] );
      }
    }

    // Define the container for the buttons
    var oContainer = jQuery("#contact-buttons-bar");

    // Check if the container is already on the page
    if ( oContainer.length === 0 ) {

      // Insert the container element
      jQuery('body').append('<div id="contact-buttons-bar" class="equilibrium">');

      // Get the just inserted element
      oContainer = jQuery("#contact-buttons-bar");

      // Add class for effect
      oContainer.addClass(settings.effect);

      // Add show/hide button
      var sShowHideBtn = '<button class="contact-button-link show-hide-contact-bar"><span class="fa fa-angle-right"></span></button>';
      oContainer.append(sShowHideBtn);

      var i;
      for ( i in settings.buttons ) {
        var bs = settings.buttons[i],
            sLink = bs.link,
            active = bs.use;

        // Check if element is active
        if (active) {

          // Change the link for phone and email when needed
          if (bs.type === 'phone') {
            sLink = 'tel:' + bs.link;
          } else if (bs.type === 'email') {
            sLink = 'mailto:' + bs.link;
          }

          // Insert the links
          /* this code is so that we can put the correct blog logo as our link icon. There is no 
          blog logo from the font-awesome catalog (at this time) so we are doing this to use the one
          in our repository.*/
          if (bs.icon == "book"){
            var sIcon = '<img src="/img/bloglogo_shareit.png" class="img-responsive" alt="blog logo">',
              sButton = '<a href="' + sLink +
                          '" class="contact-button-link cb-ancor ' + bs.class + '" ' +
                          (bs.title ? 'title="' + bs.title + '"' : '') +
                          (bs.extras ? bs.extras : '') +
                          ' alt="' + bs.class + ' logo">' + sIcon + '</a>';
            oContainer.append(sButton);
          }
          else if (bs.icon == "eventbrite") {
            var sIcon = '<img src="/img/eventbrite_logo.jpg" class="img-responsive" alt="Eventbrite logo" aria-hidden="true">',
              sButton = '<a href="' + sLink +
                '" class="contact-button-link cb-ancor ' + bs.class + '" ' +
                (bs.title ? 'title="' + bs.title + '"' : '') +
                (bs.extras ? bs.extras : '') +
                ' alt="' + bs.class + ' logo" aria-label="See Events happening at VPL on ">' + sIcon + '</a>';
            oContainer.append(sButton);
          }          
          else{
            var sIcon = '<span class="fa fa-' + bs.icon + '"></span>',
              sButton = '<a href="' + sLink +
                          '" class="contact-button-link cb-ancor ' + bs.class + '" ' +
                          (bs.title ? 'title="' + bs.title + '"' : '') +
                          (bs.extras ? bs.extras : '') +
                          ' alt="' + bs.class + ' logo">' + sIcon + '</a>';
            oContainer.append(sButton);
          }
          
        }
      }

      // Make the buttons visible
      setTimeout(function(){
        oContainer.animate({ right : 0 });
      }, 200);

      
      // Show/hide buttons
      jQuery('#contact-buttons-bar').on('click', '.show-hide-contact-bar', function(e){        
        e.preventDefault();
        e.stopImmediatePropagation();
        jQuery('.show-hide-contact-bar').find('.fa').toggleClass('fa-angle-right fa-angle-left');
        oContainer.find('.cb-ancor').toggleClass('cb-hidden');
      });
    }
  };


  // Slide on scroll effect
  jQuery(function(){

    // Define element to slide
    var el = jQuery("#contact-buttons-bar.slide-on-scroll");

    // Load top default
    el.attr('data-top', el.css('top'));

    // Listen to scroll
    jQuery(window).scroll(function() {
      clearTimeout( jQuery.data( this, "scrollCheck" ) );
      jQuery.data( this, "scrollCheck", setTimeout(function() {
        var nTop = jQuery(window).scrollTop() + parseInt(el.attr('data-top'));
        el.animate({
          top : nTop
        }, 500);
      }, 250) );
    });
  });

 }(jQuery));

function toggleClassDisplay(className){

}

function abc(){  
  var elements = document.getElementsByClassName("cb-ancor");
  for(var i = 0, length = elements.length; i < length; i++) {
    if( elements[i].style.display == ''){
      elements[i].style.display = 'none';

      if(i == 0){
        //document.getElementById("contact-bar-span-id").addClass('fa-angle-right');
        document.getElementById("contact-bar-span-id").classList.add("fa-angle-left");
        document.getElementById("contact-bar-span-id").classList.remove("fa-angle-right");
        //document.getElementById("contact-bar-span-id").remove('fa-angle-left');
      }      
    }
    else{
      elements[i].style.display = '';      
      if(i == 0){
        //document.getElementById("contact-bar-span-id").addClass('fa-angle-left');
        //document.getElementById("contact-bar-span-id").remove('fa-angle-right');
        document.getElementById("contact-bar-span-id").classList.add("fa-angle-right");
        document.getElementById("contact-bar-span-id").classList.remove("fa-angle-left");
      }      
    } 
  }
        
}

