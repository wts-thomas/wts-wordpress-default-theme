=== WTS Elementor ===

Contributors: Thomas Rainer
Requires at least: 5.0
Tested up to: 6.1
Stable tag: 1.7
Version: 1.7.1
Requires PHP: 7.4
JQuery: 3.6.3
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A lightweight, plain-vanilla theme for Elementor page builder.

== Description ==

A basic, plain-vanilla, lightweight theme, best suited for building your site using Elementor page builder.
This theme resets the WordPress environment and prepares it for smooth operation of Elementor.

Screenshot's images & icons are licensed under: Creative Commons (CC0), https://creativecommons.org/publicdomain/zero/1.0/legalcode

= 1.7.1 - 06-08-2023 =
* Additional styles added to hide AI buttons within page builder
* Dashboard Overrides for Elementor AI buttons

= 1.7.0 - 05-23-2023 =
* Adds a checkbox in Settings/General that hides the Edit with Elementor buttons and links for Pages/Posts
* Revised margin start for paragraph widgets
* Hides AI buttons in Elementor Editor panel
* Renames Elementor Overrides stylesheet to reflect that not just color edits are being made

= 1.6.1 - 05-12-2023 =
* Adjustments to theme name for update checker

= 1.6.0 - 04-25-2023 =
* Breadcrumb styling adjustments to padding and margin
* Refactor Hidden Dashboard items
* Adds Widget and Sidebar Functionality to Appearance - adds a single default sidebar

= 1.5.7 - 04-19-2023 =
* Removes Wordpress' and Elementors Dashboard Meta Widget Boxes

= 1.5.6 - 04-10-2023 =
* Default Wordpress Image Text Wrap styles, when used with TinyMCE

= 1.5.5 - 04-10-2023 =
* Selection color overrides for Elementor editor widgets
* Added additional global variant style options for possible future edits

= 1.5.4 - 04-06-2023 =
* Color Adjustments to Editor drop shadows

= 1.5.3 - 04-06-2023 =
* Adjustment of main Editor Background color

= 1.5.2 - 04-06-2023 =
* Relocated stylesheet of Elementor editor overrides

= 1.5.1 - 04-06-2023 =
* Refactor and Refine style overrides to Elementor Editor

= 1.5.0 - 04-06-2023 =
* Overrides Elementor's Dark Mode editor styling; fixes issue of too highcontrast colors

= 1.4.4 - 03-23-2023 =
* Fixes errors with minifing JS code

= 1.4.3 - 03-23-2023 =
* Minified JS Code for MatchHeight, Responsive Tables, and Viewport Height

= 1.4.2 - 03-07-2023 =
* Peformance update for Google PageSpeed
* Updated JQuery to 3.6.3
* Minor Style sheet adjustments

= 1.4.1 - 03-02-2023 =
* Added additional performance functions, e.g. Lazy Load, Fix to Width and Height, and loading of Custom Fonts
* Refactored the functions.php file for improved organization and notes

= 1.4.0 - 02-27-2023 =
* Added Breadcrumb shortcode [breadcrumbs] and default breadcrumb stylings
* Breadcrumb styles uses Global Font size and Global Colors e.g. Global Text Font Size, Global Text Color, and Global Primary Color

= 1.3.18 - 02-21-2023 =
* Created WP Admin Style Sheet to assist with custom styles for Admin Dashboard
* Added styles to hide unwanted SEO windows in some Admin screens

= 1.3.17 - 01-14-2023 =
* Refactored ACF function to show commas and decimals
* Refactored body class in header.php to remove error in finding $ variable for class

= 1.3.16 - 01-14-2023 =
* Fixed Critical Error for ACF number field - was using a name in the New Homes Plugin that was causing the conflict
* Renamed ACF number field name in funcitons to avoid conflict (property_listing_price)

= 1.3.15 - 01-13-2023 =
* Filter to add commas to ACF number field (name=price)

= 1.3.14 - 12-03-2022 =
* Fixed Theme Support for Featured Images

= 1.3.13 - 11-28-2022 =
* Removed conflicting full-height declaration
* Added the class(es) drop shadows for svg's 50%, 75%, and 85% opacity

= 1.3.12 - 11-08-2022 =
* Reduced the responsive media screen break for Tables from 1024px to 960px

= 1.3.11 - 11-04-2022 =
* Additional table styles using the classes (.vertical-lines and .no-lines) when used for Tablepress
* Added abililty to add up to an additional four alternate menus

= 1.3.10 - 11-04-2022 =
* Added the necessary javascript for Responsive Tables

= 1.3.9 - 11-02-2022 =
* Refined styles for divider shadow and changed class name

= 1.3.8 - 11-02-2022 =
* Revised horizontal line shadows

= 1.3.7 - 11-02-2022 =
* Revised paragraph margin settings for text widgets
* Added a horiz-divider class for shadows under horizontal lines
* Updated style declarations for Full Height
* Added responsive Tables via the added class ".table-responsive-stack"

= 1.3.6 - 11-01-2022 =
* Added leading styles to UL's that have a white background. Add the class ".leaders" to the UL
* Added the .full-height class for div's that need to fill parent containers; needed for iPhones
* Refactored a default declaration for the Elementor class .swiper-slide to line-height: 0

= 1.3.5 - 10-31-2022 =
* Minor refactoring of the paragraph styles

= 1.3.4.1 - 10-31-2022 =
* Adjusted paragraph default settings (p) to 0em for margin-block-start and margin-block-end

= 1.3.4 - 10-31-2022 =
* Fixed missing Featured Image option to Page(s) and Post(s)

= 1.3.3 - 10-28-2022 =
* Added a btn-block class to make Buttons, display: block; instead of inline-block

= 1.3.2 - 10-27-2022 =
* Removed custom Lazy Load functions in favor a more lightweight option coming soon 
* Note: lazyload is not included in this verion - currently working on solution

= 1.3.1 - 10-12-2022 =
* Hides Dashboard Screen Option Meta Boxes to show WTS Support card by default
* Misc refactoring of Functions

= 1.3.0 - 10-12-2022 =
* Added Support Contact Card to Admin Dashboard
* Added Function to hide Admin Edit to Tables if using the Tablepress plugin
* Minor Refactoring and Organizations to Functions

= 1.2.0 - 10-11-2022 =
* Remove Plugin menus from users' dashboard excluding WTS users

= 1.1.2 - 10-07-2022 =
* Removed a commented out Enqueued script for lazy load
* Style sheet formating adjustments

= 1.1.1 - 10-07-2022 =
* Refactoring of the lazy load functions 
* Fixes missing add action enqeue script for lazy load
* Removed redundant filter that removes Elementor's default lazy loader

= 1.1.0 - 10-07-2022 =
* Margin adjustments (top and bottom) for h tags
* Function to remove Gutenberg Block CSS Library
* Functions to remove Gutenberg Block elements
* Updated JQuery to 3.6.1
* Lazy Load function and styles added

= 1.0.1 - 09-29-2022 =
* Added function for an alternate menu to be created via Appearance/Menus

= 1.0.0 - 09-29-2022 =
* Initial Release
