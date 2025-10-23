(function ($) {
   $(function () {
      $(".menu-dropdown-show a").hover(function () {
         $(".menu-dropdown").addClass("shown");
      });
   });
   $(document).on("mouseleave", "header:has(.menu-dropdown.shown)", function () {
      $(".menu-dropdown").removeClass("shown");
   });
})(jQuery);
