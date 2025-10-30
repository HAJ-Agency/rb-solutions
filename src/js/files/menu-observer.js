(function ($) {
   $(function () {
      checkMenuPosition();
   });
   $(window).scroll(function () {
      checkMenuPosition();
   });

   function checkMenuPosition() {
      if($("header").length == 0) return; 
      const menuPosition = $("header").offset().top;

      $("[menumode]").each(function () {
         const sectionTop = $(this).offset().top;
         const sectionHeight = $(this).outerHeight();
         const sectionBottom = sectionTop + sectionHeight;

         if (menuPosition >= sectionTop && menuPosition < sectionBottom) {
            $mode = $(this).attr("menumode");
            if (!$("header").hasClass($mode)) {
               $("header").hasClass("dark") ? $("header").removeClass("dark").addClass("light") : $("header").removeClass("light").addClass("dark");
            }            
         }
      });
   }
})(jQuery);
