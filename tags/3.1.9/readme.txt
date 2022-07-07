=== Map Multi Marker ===
Contributors: Austoni Mickael
Donate link: https://www.paypal.me/0ze
Tags: map, maps, marker, markers, map marker, google map, google map plugin, multiple marker map, multi marker google map, easy map, ajax marker, location, map multi marker, plugin map wordpress, wp plugin map, map responsive, unlimited marker
Tested up to: 5.2.1
Stable tag: 3.1.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html


The easiest, useful and powerful google map plugin ! Easily create an unlimited number of google map and marker.

== Description ==
The easiest, useful and powerful google map plugin ! Easily create an unlimited number of google map and marker.. Many options are available to fully customize your map and marker.

* Create and display unlimited number of maps
* Possibility to create an unlimited number of markers on your map
* Responsive map
* Display tooltip when you click a marker
* Customise tooltip
* Customize each marker
* Choose your maps types: roadmap, terrain, satellite or hybrid
* Add Image, Title, description, address, phone, web link into each marker tooltip
* Many options map and marker available
* Very easy to use and fast
* Content tooltip AJAX loading
* Import unlimited number of markers from csv file

= Translations available =
* English
* French
* Bulgarian
* Italian
* Japanese

= Installation =
1. Upload the entire `map-multi-marker` folder(unzip) to the `/wp-content/plugins/` directory or use dashboard (Plugins > Add new) and search `map multi marker`.
2. Activate the plugin through the `/Plugins`/ menu in WordPress.
3. Add the shortcode [map-multi-marker id="YOUR_MAP_ID"] where you want display the map.
4. IMPORTANT : Add your Google API key in setting page Map Multi Marker

= Requirement =
* php5.6 or +


== Screenshots ==
1. Front office screenshot-1.png
2. Admin panel marker screenshot-2.png
3. Admin panel option screenshot-3.png


== Changelog ==

= 3.1.9 =
* Improve compatibility with other plugin and theme

= 3.1.8 =
* Fix clipboard copy
* Fix bug that prevents set default image marker and image description

= 3.1.7 =
* Minor fix

= 3.1.6 =
* Add version register script

= 3.1.5 =
* Fix product data tab conflict with WooCommerce plugin
* Minor fix some wording alert

= 3.1.4 =
* IMPORTANT : Fix bug with map api language
* Improve front-end compatibility with other plugin and theme
* Improve loading script/style if shortcode isn't present in page

= 3.1.3 =
* Fix bug prevent delete marker
* Fix warning display when shortcode not match with map id

= 3.1.2 =
* Add some conditional script loading

= 3.1.1 =
* Fix render buffer output

= 3.1 =
* Improve source code and performance
* Add japanese translate
* Fix empty tooltip first click on marker
* Improve compatibility with other plugin and theme
* Fix translate title when new map is created

= 3.0.2 =
* Fix jquery dependency

= 3.0 =
* Add maps name on the detail maps page
* Add button to copy shortcode on the detail maps page
* Optimize loading script & css
* Displays an alert message if api key is not register
* Better compatibility with other plugin
* Bug fixes

= 2.9.6 =
* Bug fixes
* Better image helps

= 2.9.5 =
* Map center dynamically at resize
* Improves function csv file, security and optimize class CsvReader

= 2.9.4 =
* Fix HTML entity in title, description and address

= 2.9.3 =
* Improves function csv file

= 2.9.2 =
* Allow line break in description field

= 2.9.1 =
* Optimize upload function csv file
* Upgrade clipboard.js to version 1.5.16
* Upgrade featherlight.js to version 1.7.0
* Strip all space in phone number link (href only)

= 2.9 =
* Fix dismiss notice message
* Fix notice message translation
* Add section to help page
* Add the possibility to import CSV file
* Bug fixes
* Change phone number format for more flexibility
* Optimized tooltip UI for less conflict with theme wordpress

= 2.8 =
* Fixed problem with options in tooltip
* Optimized plugin performance
* Optimized style front-end

= 2.7 =
* It's now possible to add an unlimited number of maps on the same page !

= 2.6 =
* Fix fatal error when you try active the plugin caused by php version inferior to 5.4
* Optimized style front-end
* Fix conflict with the plugin "The event calendar"
* Add Italian translation, thank chitarrista85

= 2.5 =
* Add return button for map list
* Fix conflict with other plugin
* Add French translate for Belgium & Canada
* Optimized style into tooltip
* Fix content marker tooltip AJAX when a lot of clicks was done

= 2.4 =
* Add an option to open image into tooltip in a lightbox
* Update library clipboard.js to v1.5.15
* Add Bulgarian translation (Beta), thank ettaniel !

= 2.3 =
* Fix street view option
* Optimizing the interface to create/edit a marker
* Now use media library wordpress to add image marker and image tooltip
* Add the possibility to set a default marker and image tooltip when a new markeur is created
* Minor text fixes

= 2.2 =
* Add scrollwheel option
* Add streetview option
* Change user interface map option
* Minor text fixes
* Fixed error php version earlier to 5.4
* Add link donation
* Support Worpress 4.6

= 2.1 =
* Add the possibility to create several map
* Many bug fixes
* Fixed translation and bug with font awesome on admin page

= 2.0 =
* Fixed calling core loading files directly
* Upgrade clipboard.js v1.5.10 to v1.5.12
* Optimized plugin security (Sanitize, escape, and validate POST calls) & using Nonces

= 1.9 =
* Bug fixes
* Optimized style front-end & back-end
* Change shortcode name and plugin name

= 1.8 =
* Add option google API key and change language
* Optimized interface to add new markers
* Automatic phone call on click
* It is possible to display adsress, phone and web link fields
* Optimized details markers

= 1.7 =
* Bug fixes

= 1.6 =
* Improved image preview and upload function
* Updated default marker icon
* Add new options to display on desired field

= 1.5 =
* Bug fixes to avoid duplicated content for plugin activation
* Add possibility to customize the picture and icon marker already created

= 1.4 =
* Optimized admin interface
* When the plugin is installed a marker is automatically added as an example
* Add an option to resize the map

= 1.3 =
* Add a popup (modal) to confirm when you delete a marker
* French translation added
* Added options to customize the pictures & markers icon
* Added help page to use the plugin

= 1.2 =
* Bug fixes for the requiered field

= 1.1 =
* Add possibility to upload an image in marker details
* Add message alert