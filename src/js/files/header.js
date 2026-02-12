(function ($) {
  // let modal_open = false;
  $(function () {
    // $('.wp-block-navigation__responsive-container-open').attr('data-wp-on--click', null);
    // $('.wp-block-navigation__responsive-container-open').attr('data-wp-on--keydown', null);
    // $('.wp-block-navigation__responsive-container-open').attr('aria-haspopup', null);
    // $('.wp-block-navigation__responsive-container-open').attr('aria-label', null);

    $(".menu-dropdown-show a").hover(function () {
      $(".menu-dropdown-show").addClass("active");
      $(".menu-dropdown").addClass("shown");
    });

  });

  $(document).on('click', '.wp-block-navigation__responsive-container-open', function (e) {
    e.preventDefault();
    console.log("Clown modal.");
    $(this).removeClass('wp-block-navigation__responsive-container-open');
    $(this).addClass('wp-block-navigation__responsive-container-close');
    $(this).attr('data-wp-on--click', "actions.closeMenuOnClick");
    $(this).attr('data-wp-on--keydown', "actions.closeMenuOnKeyDown");
    $(this).attr('aria-haspopup', null);
    $(this).attr('aria-label', "Close Menu");
  });

  $(document).on('click', 'nav > .wp-block-navigation__responsive-container-close', function (e) {
    e.preventDefault();
    $('#modal-1').find('.wp-block-navigation__responsive-container-close').click();
    $(this).removeClass('wp-block-navigation__responsive-container-close');
    $(this).addClass('wp-block-navigation__responsive-container-open');
    $(this).attr('data-wp-on--click', "actions.openMenuOnClick");
    $(this).attr('data-wp-on--keydown', "actions.handleMenuKeyDown");
    $(this).attr('aria-haspopup', "dialog");
    $(this).attr('aria-label', "OpenMenu");
  });

  /**
  var $div = $("#modal-1");
  var observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      var att = $(mutation.target).prop(mutation.attributeName);
      if (~att.indexOf('has-modal-open') && !modal_open) {
        modal_open = true;
        $('.wp-block-navigation__responsive-container-open').addClass('close-modal');
       
      } else if (!~att.indexOf('has-modal-open') && modal_open) {
        modal_open = false;
        $('.wp-block-navigation__responsive-container-open').removeClass('close-modal');
      }
    });
  });

  observer.observe($div[0], {
    attributes: true,
    attributeFilter: ['class']
  });
  $(document).on("mouseleave", "header:has(.menu-dropdown.shown)", function () {
    $(".menu-dropdown-show").removeClass("active");
    $(".menu-dropdown").removeClass("shown");
  });
  
  $(document).on('click', 'button', function (e) {
    let yeah = $('html').hasClass('has-modal-open');
    console.log("TWOTWO: " + yeah);
  });
  */
})(jQuery);
