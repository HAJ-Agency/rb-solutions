(function ($) {

  $(function () {

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

  $(document).on("mouseleave", "header:has(.menu-dropdown.shown)", function () {
    $(".menu-dropdown-show").removeClass("active");
    $(".menu-dropdown").removeClass("shown");
  });
})(jQuery);


