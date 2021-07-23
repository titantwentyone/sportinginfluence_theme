jQuery('.toggle').on('click', function(e) {
    e.preventDefault();
    jQuery(this).toggleClass('active');
    jQuery('#offcanvas').toggleClass('open');
   });