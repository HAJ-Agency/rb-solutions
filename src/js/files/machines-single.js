(function ($) {
  $(function () {
    if (!$(".wp-block-gallery").length) {
      return;
    }

    /**
     * When a user clicks on any image in the gallery except the first one,
     * it swaps the clicked image with the first image in the gallery.
     */
    $(document).on("click", ".wp-block-gallery figure:not(:first-child)", function (e) {
      e.preventDefault();
      const firstFigure = $(".wp-block-gallery figure:first-child");
      const clickedFigure = $(this);
      let clickedImageIndex = $(this).index();
      $(firstFigure).insertAfter($(".wp-block-gallery figure:nth-child(" + (clickedImageIndex + 1) + ")"));
      $(this).parent().prepend(clickedFigure);
    });
  });
})(jQuery);
