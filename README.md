# RB Solutions

Tiburon child theme for RB Solutions.

## Installation

Clone theme in to a WP installation that also uses the Tiburon theme. 

## Development

### Plugins

Get from either staging or live.

### Localhost 
```bash
npm start
```
Compiles scss and js files into `/dev` folder.

This folder is only used for localhost development.

### Staging and Live
```bash
npm run build
```
Compiles scss and js files into `/build` folder.

Then push to either staging or live.

## Machines Archive Page

### Machines data

The data for displaying machines on this site is gathered from a file in `/wp-content/uploads/data/machines.json`.

This file is updated every time a single machine is updated. 

Functionality for this is found in the class `includes/classes/Metaboxes.php`.

### Filtering

Content for both the filters and the data being filtered is from a shortcode in `includes/shortcodes/machines-archive-content.php`.

Code for filtering is in `src/js/files/machines-archive.js`.

## Machine Single Page

Slideshow(s) are made with splidejs.

Lightbox is made with Featherlight.

Code for the slideshow(s) and lightbox is in `src/js/files/machines-single.js`.

## Functionality

### Composer

```bash
composer dumpautoload -o
```

### GitHub Actions

One for staging and one for live.

## Links

[Staging Site](https://rb.hajagency.com/) - at hajagency.com

[Live Site](https://rb-solutions.com/) - at our WHM

[MixItUp](https://github.com/HAJ-Agency/mixitup) - forked on our GitHub

[MixItUp Pagination](https://github.com/HAJ-Agency/mixitup-pagination) - forked on our GitHub

[MixItUp Multifilter](https://github.com/HAJ-Agency/mixitup-multifilter) - forked on our GitHub

[Featherlight](https://noelboss.github.io/featherlight/)

[splidejs](https://splidejs.com/)

## Misc

Domain registered at [Netstat](https://netsite.app/domains/)