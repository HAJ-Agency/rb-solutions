(function ($) {
  $(function () {
    if (!$(".machines-results-grid").length) {
      return;
    }
    var mixer = mixitup(".machines-results-grid", {
      selectors: {
        target: ".machine",
      },
      animation: {
        duration: 300,
      },
      pagination: {
        limit: 3,
      },
      multifilter: {
        enable: true, // enable the multifilter extension for the mixer
      },
      callbacks: {
        onMixEnd: function (state, futureState) {
          if (state.totalPages === 1) {
            $(".mixitup-page-list").css("display", "none");
          } else {
            $(".mixitup-page-list").css("display", "flex");
          }
          $(".machines-results-total-matching").text(state.totalMatching);
        },
      },
    });

    $(".sorting-group select").on("change", function (e) {
      mixer.sort($(this).find(":selected").attr("data-sort"));
    });
    // $(document).on("change", ".sorting-group", function () {
    //   console.log("heya");
    // });
    // $("select").on("change", function () {
    //   console.log(this.value);
    // });
    // $(".banana").on("change", function (e) {
    //   e.preventDefault();
    //   let sortOn = $(this).find(":selected").attr("data-sort");
    //   console.log(sortOn);
    //   // $(".machines-results-grid").mixItUp("sort", sortOn);
    // });
  });
})(jQuery);
