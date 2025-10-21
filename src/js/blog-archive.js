(function ($) {
   toggle_load_more_button();

   $(document).on("click", ".posts__filter > li:not(.active)", function (e) {
      e.preventDefault();
      const self = $(this);
      const queryEl = $(".wp-block-query.posts__filter-result");
      const postTemplateEl = queryEl.find(".wp-block-post-template");

      // Toggle active class on category filter item
      self.addClass("active").siblings().removeClass("active");

      let catId = this.className.match(/cat-item-(\d+)/);
      catId = catId ? catId[1] : null;

      if (queryEl.length && postTemplateEl.length) {
         const block = JSON.parse(queryEl.attr("data-attrs"));

         if (parseInt(catId) !== 0) {
            block.attrs.query.taxQuery = {
               category: [parseInt(catId)],
            };
         } else {
            block.attrs.query.taxQuery = null;
         }

         let data = {
            action: "query_render_more_pagination",
            attrs: JSON.stringify(block),
            paged: queryEl.attr("data-paged"),
            type: "filter",
         };

         $.ajax({
            url: ajax.url,
            dataType: "html",
            data: data,
            error: function (xhr, status, error) {
               console.log("Error!");
            },
            complete: function (posts) {
               const htmlEl = $(posts.responseText);

               let attrs = JSON.stringify(block);
               $(".posts__filter-result").attr("data-attrs", attrs);

               if (htmlEl.length) {
                  const html = htmlEl.find(".wp-block-post-template").html() || "";

                  if (html.length) {
                     postTemplateEl.html("");
                     postTemplateEl.append(html);
                     toggle_load_more_button();
                     return;
                  }
               }
            },
         });
      }
   });

   $(document).on("click", ".posts__show-more-button", function (e) {
      e.preventDefault();

      const self = $(this);
      const queryEl = $(".wp-block-query.posts__filter-result");
      const postTemplateEl = queryEl.find(".wp-block-post-template");

      if (queryEl.length && postTemplateEl.length) {
         const block = JSON.parse(queryEl.attr("data-attrs"));
         const maxPages = block.attrs.query.pages || 0;

         let data = {
            action: "query_render_more_pagination",
            attrs: queryEl.attr("data-attrs"),
            paged: queryEl.attr("data-paged"),
            type: "load_more",
         };

         $.ajax({
            url: ajax.url,
            dataType: "html",
            data: data,
            error: function (xhr, status, error) {
               console.log("Error");
               console.log(xhr);
               console.log(status);
               console.log(error);
            },
            complete: function (posts) {
               const nextPage = Number(queryEl.attr("data-paged")) + 1;

               if (maxPages > 0 && nextPage >= maxPages) {
                  self.remove();
               }

               queryEl.attr("data-paged", nextPage);

               const htmlEl = $(posts.responseText);

               if (htmlEl.length) {
                  const html = htmlEl.find(".wp-block-post-template").html() || "";

                  if (html.length) {
                     postTemplateEl.append(html);
                     toggle_load_more_button();
                     return;
                  }
               }
            },
         });
      }
   });

   function toggle_load_more_button() {
      const activeFilter = $(".posts__filter > li.active");
      const activeFilterCount = parseInt(activeFilter.attr("data-cat-count"));
      const visiblePosts = $(".posts__filter-result ul > li").length;
      const button_class = visiblePosts >= activeFilterCount ? "wtcgbg-hide" : "wtcgbg-show";

      $(".posts__show-more-button").removeClass("wtcgbg-hide wtcgbg-show").addClass(button_class);
   }
})(jQuery);
