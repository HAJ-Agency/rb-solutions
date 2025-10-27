(function ($) {
  $(function () {
    $(".menu-dropdown-show a").hover(function () {
      $('.menu-dropdown-show').addClass('active');
      $(".menu-dropdown").addClass("shown");
    });
  });
  $(document).on("mouseleave", "header:has(.menu-dropdown.shown)", function () {
    $('.menu-dropdown-show').removeClass('active');
    $(".menu-dropdown").removeClass("shown");
  });
})(jQuery);
