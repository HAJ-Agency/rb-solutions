(function ($) {
   $(function () {
      /**
       * @see https://jqueryui.com/accordion/
       */
      if ($(".wtc-accordion")[0]) {
         $(".wtc-accordion").each(function (index, item) {
            createAccordion(item);
         });
      }

      /**
       * Check if we're on the FAQ page
       *
       */
      if ($(".rank-math-list-item")[0]) {
         $(".rank-math-list-item").each(function (index, item) {
            const headerTag = $(item).find(".rank-math-question ")[0].tagName;
            createAccordion(item, headerTag);
         });
      }

      if ($("#wtc-tabs--parent")[0]) {
         createTabs();
      }

      /**
       * @see https://jqueryui.com/tabs/
       */
      if ($(".wtc-tabs--child")[0]) {
         $(".wtc-tabs--child").each(function (i, value) {
            $(value).tabs();
         });
      }
   });

   $(document).on("click", ".wtc-accordion.chunk", function () {
      $(this).siblings(".wtc-accordion.chunk").accordion({ active: false });
   });

   const createTabs = () => {
      $("#wtc-tabs--parent").tabs();
   };

   const createAccordion = (element, headerTag = "h5") => {
      $(element).accordion({
         active: false,
         classes: {
            "ui-accordion-header": "wtc-accordion--header",
            "ui-accordion-header-collapsed": "wtc-accordion--header-collapsed",
            "ui-accordion-content": "wtc-accordion--content",
         },
         collapsible: true,
         header: " > " + headerTag,
         heightStyle: "content",
         icons: { header: "ui-icon-plus", activeHeader: "ui-icon-minus" },
         beforeActivate: function (event, ui) {
            /**
             * If there is a form with the id #form_anmalan-event inside the accordion,
             * then we want to populate the input field with the class .event-sign-up-form-event-name
             * with the title of the accordion ie the event name.
             */
            const isThereAForm = $(this).find("#form_anmalan-event").length;
            if (isThereAForm) {
               const currentAccordionId = event.currentTarget.id;
               const currentTitleElement = $(headerTag + "#" + currentAccordionId);
               const currentTitleText = currentTitleElement.text();
               const currentFormElement = currentTitleElement.next("div").find("#form_anmalan-event");
               currentFormElement.find('.event-sign-up-form-event-name input[type="text"]').val(currentTitleText);
            }
         },
      });
   };
})(jQuery);
