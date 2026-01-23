import Splide, { SplideTrack } from "@splidejs/splide";

(function ($) {
  $(function () {
    if (!$(".wp-block-gallery").length) {
      return;
    }

    let counter = 1;
    $(".wp-block-gallery")
      .each(function () {
        const gallery = $(this);
        const className = counter === 1 ? "main-carousel" : "thumbnails-carousel";
        // wrap all figure tags in gallery.html in <li> tag
        $(this).find("figure").wrap("<li class='splide__slide'></li>");
        $(this).replaceWith($('<div class="splide ' + className + '"><div class="splide__track"><ul class="splide__list">' + gallery.html() + "</ul></div></div>"));

        $(".main-carousel img").featherlightGallery();
        $(".main-carousel img").each(function () {
          const altText = $(this).attr("alt") || "";
          $(this).attr("data-featherlight", '<img src="' + $(this).attr("src") + '" alt="' + altText + '">');
        });
        counter++;
      })
      .promise()
      .done(function () {
        var main = new Splide(".main-carousel", {
          pagination: false,
          heightRatio: 0.6,
          cover: true,
        });
        if ($(".thumbnails-carousel").length) {
          var thumbnails = new Splide(".thumbnails-carousel", {
            dragMinThreshold: 40,
            fixedWidth: 159,
            gap: 12,
            cover: true,
            rewind: true,
            pagination: false,
            isNavigation: true,
            focus: "center",
            breakpoints: {
              782: {
                fixedWidth: "25%",
                heightRatio: 0.25,
              },
            },
          });

          main.sync(thumbnails);
          main.mount();
          thumbnails.mount();
        } else {
          main.mount();
        }
      });
  });
})(jQuery);
