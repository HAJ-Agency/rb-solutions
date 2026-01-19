(function ($) {
  $(function () {
    
    $(document).on("click", ".schema-faq-section:not(.open)", function (e) {
      e.preventDefault();
      $(this).addClass("open");
      $(this).find(".schema-faq-answer").show(150);
    });

    $(document).on("click", ".schema-faq-section.open", function (e) {
      e.preventDefault();
      $(this).removeClass("open");
      $(this).find(".schema-faq-answer").hide(150);
    });
  });
})(jQuery);
