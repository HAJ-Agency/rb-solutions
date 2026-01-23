<?php

namespace RBS\ASSETS\SHORTCODES;

use WP_Query;

add_shortcode('machines_archive_content', __NAMESPACE__ . '\machines_archive_content_callback');

function machines_archive_content_callback() {
   ob_start();
   /** @var array{basedir: string, baseurl: string} $upload_dir */
   $upload_dir = \wp_get_upload_dir();
   $file_path = $upload_dir['basedir'] . '/data/machines.json';
   $machines_json = json_decode(file_get_contents($file_path), true);
   $unique_machine_types = array_filter(array_unique(array_map(function ($arr) {
      return ucwords(strtolower($arr['meta_fields']['machine_type']));
   }, $machines_json)));
   sort($unique_machine_types);
?>

   <div class="all-machines">
      <form class="machines-filters-grid">
         <div class="column one">
            <fieldset class="status-group" data-filter-group="status">
               <div>
                  <button type="reset" data-sort="date:desc">Clear All</button>
               </div>
               <div class="filter-button">
                  <label>
                     <input type="radio" name="status" value=".for-sale" />
                     <span>For Sale</span>
                  </label>
               </div>
               <div class="filter-button">
                  <label>
                     <input type="radio" name="status" value=".for-rent" />
                     <span>For Rent</span>
                  </label>
               </div>
            </fieldset>
         </div>
         <div class="column two">
            <fieldset class="quality-group" data-filter-group="quality">
               <div class="filter-button">
                  <label>
                     <input type="radio" name="quality" value=".new" />
                     <span>New</span>
                  </label>
               </div>

               <div class="filter-button">
                  <label>
                     <input type="radio" name="quality" value=".pre-owned" />
                     <span>Pre-owned</span>
                  </label>
               </div>
            </fieldset>

            <fieldset class="machine_type-group" data-filter-group>
               <select>
                  <button>
                     <div>
                        <selectedcontent></selectedcontent>
                     </div>
                  </button>
                  <div>
                     <option value="">
                        <div class="custom-option">
                           <span class="option-text">Type of machine</span>
                        </div>
                     </option>
                     <?php
                     foreach ($unique_machine_types as $machine_type) :
                     ?>
                        <option value=".<?= \sanitize_title($machine_type) ?>">
                           <div class="custom-option">
                              <span class="option-text"><?= $machine_type ?></span>
                           </div>
                        </option>
                     <?php
                     endforeach;
                     ?>
                  </div>
               </select>
            </fieldset>
         </div>

         <div class="column three">
            <div class="machines-results-info">
               <div>Showing <span class="machines-results-total-matching"><?= sizeof($machines_json) ?> </span> results</div>

               <fieldset class="sorting-group">
                  <select>
                     <button>
                        <div>
                           <selectedcontent></selectedcontent>
                        </div>
                     </button>
                     <div>
                        <option>
                           Sort by
                        </option>
                        <option data-sort="date:desc">
                           Date Added (desc)
                        </option>
                        <option data-sort="date:asc">
                           Date added (asc)
                        </option>
                        <option data-sort="title:desc">
                           Title (desc)
                        </option>
                        <option data-sort="title:asc">
                           Title (asc)
                        </option>
                     </div>
                  </select>
               </fieldset>

            </div>
         </div>

      </form>
      <div class="machines-results-grid">
         <?php
         foreach ($machines_json as $machine) :
            $title = $machine['title'];
            $excerpt = $machine['excerpt'];
            $date = $machine['date'];
            $permalink = $machine['permalink'];

            $machine_type = $machine['meta_fields']['machine_type'];
            $quality = $machine['meta_fields']['quality'];
            $status = $machine['meta_fields']['status'];
            $year = $machine['meta_fields']['year'];
            $filters = ($machine_type ? \sanitize_title($machine_type) . " " : "") .
               ($quality ? \sanitize_title($quality) . " " : "") .
               ($status ? \sanitize_title($status) . " " : "") .
               ($year ? 'year-' . \sanitize_title($year) . " " : "");
            $filters = trim($filters);

            $image_url = $machine['featured_image']['src']['medium_large'];
         ?>
            <div class="machine <?= $filters ?>" data-date="<?= $date ?>" data-title="<?= $title ?>">
               <div class="machine-card columns">
                  <div class="machine-card-column image" style="background-image: url(<?= $image_url ?>);">
                     <div class="has-label-font-size machine-card-label"><?= $status ?></div>
                  </div>
                  <div class="machine-card-column content">
                     <?= gmdate("Y-m-d", $date) ?>
                     <div class="machine-card-quality"><?= $quality ?><?= $year ? ": <span>" . $year . "</span>" : "" ?></div>
                     <h4 class="machine-card-title"><?= $title ?></h4>
                     <?= $machine_type ? '<div class="machine-card-machine_type">' . $machine_type . '</div>' : '' ?>
                     <p class="machine-card-excerpt"><?= $excerpt ?></p>
                     <a class="machine-card-button" href="<?= $permalink ?>" target="_self">Read More<span class="screen-reader-text">: New Machine 1</span></a>
                  </div>
               </div>
            </div>
         <?php
         endforeach;
         ?>
      </div>
      <div class="mixitup-page-list machine-results-paging"></div>
   </div>

   <?php
   return ob_get_clean();
}
