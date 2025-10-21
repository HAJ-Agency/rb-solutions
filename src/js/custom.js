(function ($) {
   $(function () {
      if ($(".ticker").length) {
         $(".ticker").slick({
            prevArrow: null,
            nextArrow: null,
            autoplay: true,
            autoplaySpeed: 0,
            cssEase: "linear",
            dots: false,
            draggable: false,
            infinite: true,
            slidesToScroll: 1,
            speed: 4000,
            variableWidth: true,
         });
      }

      if ($("form").length) {
         $(document).on("change", ".accept-form-terms input", function (e) {
            if (this.checked) {
               $(".frm_submit").parent().removeClass("button-disabled");
            } else {
               $(".frm_submit").parent().addClass("button-disabled");
            }
         });
      }
   });

   /**
    * When clicking on one of them image switcher items under the "Varför sitta hos oss?"
    * section.
    */
   $(document).on("click", ".wtcgbg-image-switcher__items > .wp-block-group", function (e) {
      e.preventDefault();
      const $url = $(this).find(".wp-block-button__link").attr("href");
      console.log("clicked: ", $url);
      window.location.href = $url;
   });

   /**
    * When hovering over one of the image switcher items under the "Varför sitta hos oss?"
    * section, we want to show the corresponding image.
    * We also want to hide the other images.
    * We use the index of the item to find the corresponding image.
    */
   $(document).on("mouseenter", ".wtcgbg-image-switcher__items > .wp-block-group", function (e) {
      e.preventDefault();
      let $index = $(this).index();

      $(".wtcgbg-image-switcher__images .wp-block-gallery figure").each(function (index) {
         if (index != $index) {
            $(this).fadeTo(250, 0);
         } else {
            $(this).fadeTo(250, 1);
         }
      });
   });

   /**
    * When clicking on the white puff icon, we want to scroll to the section with
    * anchortag that's in the white puffs href attribute.
    */
   $(document).on("click", ".white-puff", function (e) {
      e.preventDefault();
      const target = $(this).find("a").attr("href") ?? "#visning";
      $("html, body").animate(
         {
            scrollTop: $(target).offset().top,
         },
         "slow"
      );
   });

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
})(jQuery);
