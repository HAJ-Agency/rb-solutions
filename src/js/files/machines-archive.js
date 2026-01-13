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

    var params = parseQueryString();
    $.each(params, function (index, param) {
      mixer.setFilterGroupSelectors(param[0], "." + param[1]);
    });
    mixer.parseFilterGroups();

    $(".sorting-group select").on("change", function (e) {
      mixer.sort($(this).find(":selected").attr("data-sort"));
    });
  });

  $(document).on("click", ".machine-card", function(e) {
    e.preventDefault();
    e.stopPropagation();
    const link = $(this).find(".machine-card-button").attr("href");
    window.location.href = link;
  })

  function parseQueryString() {
    var parsedParameters = [],
      uriParameters = location.search.substr(1).split("&");

    for (var i = 0; i < uriParameters.length; i++) {
      var parameter = uriParameters[i].split("=");
      if (parameter[0] && parameter[1]) {
        parsedParameters.push([parameter[0], parameter[1]]);
      }
    }

    return parsedParameters;
  }
})(jQuery);
