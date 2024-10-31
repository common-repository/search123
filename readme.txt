=== Search123 ===
Contributors: VCLK, TimZ, Steffi Brandstätter
Tags:  search123, S123, ad, ads, banner, monetization, cpc, link, ad insertion, ad manager, page, posts, admin
Requires at least: 2.0.2
Tested up to: 3.1
Stable tag: 2.4.1

Search123 is a WordPress plugin to manage Search123 pay-per-click ads on your blog. The appearance of the ads (color, font and font size) can be customised.

== Description ==

This WordPress plugin manages Search123 ads on your blog. Search123 bundles ads of several feed-partner (Yahoo!, MIVA, Mirago, Webfinder,...) in one metafeed. Payment is based on CPC. 

The appearance of the ads (color, font and font size) can be customised and easily integrated with WordPress widgets. 

The keyword, passed to the search feed, is taken from the title of either the category, page or single post or the keyword added to the WordPress search box.

When you active the option "use keyword input", the Search123 Plugin will use the keywords, which you can specifiy for every post. The plugin will try to read the special field "s123keywords". If this field does not exist or is empty, the plugin tries to read the field "keywords" next. The special field "keywords" is used by the very popular WordPress Plugin "All-In-One-SEO". If this fields doesn't exist, too, the plugin will use the category name or post title as written above.

To integrate Search123 ads in your existing WordPress design, you can use the sidebar widget or template tags. You can integrate Search123 ads without any JavaScript on your website.

The plugin can be translated to any language. A German translation is already included.

**Important**

To use this plugin you must participate in the [Search123 Publisher Program](http://www.search-123.de/signup/) and become a Search123 traffic partner.



== Installation ==

1. Download and unzip the files
1. Upload the plugin  to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress and 'Search123' will now be displayed in your 'Settings' menu
1. Enter your Search123 Publisher ID and customise color, font, font size, number of ads and alignment of your ads
1. Display ads by widget: 'Appearance'->'Widgets. Add the 'Search123' widget to your sidebar.
1. Display ads by template functions: 
 1. Place `<?php if (function_exists('searchFeed_searchpage')) { searchFeed_searchpage();} ?>` in your templates.
 1. The function `searchFeed_searchpage()` can be called with or without parameters. If no parameters are passed, the functions takes the customised number of ads and alignment.
 1. You have the possibility to pass three parameters (`example: searchFeed_searchpage(5, "Laptop", "vertical")`):
	* number of ads
	* keyword
	* alignment (horizontal, vertical)
1. If you are familiar with CSS styles, you can check "use CSS file" and define the appearance of your ads in the s123.css file included css subdirectory. 

= Keywordselection =

One of the most important features of Search123 is the possibility to select one or more keywords on your own, which are used to select relevant Keywords.
You can select certain keywords for every post or page in WordPress. The plugin uses the WordPress special fields to do this. The very popular plugin "All-In-One-SEO" is using those special fields, too. 
The plugin can use the article tags as keywords. If there is more than one tag, one tag is randomly selected.

The Search123 plugin reads the contents of the All-In-One-SEO plugin keyword field and uses this information to select relevant ads. If you use a large number or very different keywords, it might be possible that Search123 could not select relevant ads and will display nothing. In this case, you can add an additional special field called "s123keywords" and provide another keywords for your post. The keywords in "s123keywords" will be used just for your ads. The keywords of the All-In-One-SEO plugin will still be used for your SEO purposes.

Sequence for keywordselection: Article tags -> special field "s123keywords" -> All in One SEO Pack keywords -> title/category name

== Screenshots ==

1. Search123 Settings
2. Search123 ad example

== Changelog ==

= Version 2.4.1 =

* Bugfix: Widget settings fixed

= Version 2.4 =

* Bugfix: Small bug in adblock layout generation fixed

= Version 2.3 = 

* New: Keyword selection via article tags
* Bugfix: Fixed settings dialog (horizontal/vertical radio buttons).
* Some source refactoring and cleanup


= Version 2.2 = 

* New: French language added.

= Version 2.1 = 

* New: PHP5 is now required - the plugin is using constructors, static functions etc.
* New: Decoupled the functions and classes. It is now quite easy to write a plugin with a child class of search123. If you do this, you can overwrite every function, for example to add your own click tracking.
* Bugfix: Sequence of keyword selection changed

= Version 2.0 =

* Tested up to WordPress 3.0 beta 2
* New: Support for multiple widgets with parameters
* New: New modern style for settings page
* New: Added notifaction when settings were updated
* New: Shortcut link to plugin settings in plugin list
* Bugfix: short php tag in search123.class.php file fixed
* Update: Plugin sourcecode is now object oriented and using a class structure
* Update: Removed old unused code
* Update: Restructured the code to load scripts only when necessary
* Update: Changed process of finding the right keyword. If no keyword is passed to the searchFeed_searchpage function, the plugins tries to read the keyword from the WordPress internal search. If there is no search keyword, the plugins tries to read the typical keyword fields. If there is still no keyword found, the category name or post title is used as a keyword.
* Update: Changed default font size values from pt to px

= Version 1.7 =
* Tested up to WordPress 2.8.2
* Bugfix: Now the plugin is using selected keywords on posts - in previous versions, the entered keywords where only used on pages
* Bugfix: The keyword field of the All-In-One-SEO plugin changed, now the new field "_aioseop_keywords" is used now. Tested with All-In-One-SEO 1.6.4
* Bugfix: If you pass the number of ads by function call searchFeed_searchpage(), it was possible to display more than 8 or less then 2 ads. Now you are allowed to pass a minimum of 2 and a maximum of 8 to the function. Otherwise the value stored in the plugin configuration is used.
* Bugfix: If you specify an alignment argument in the searchFeed_searchpage() function call, this argument is used now.

= Version 1.6 =
* Keywords can be specified by WordPress special fields, thus using data from SEO Plugin All-In-One-SEO

= Version 1.5 =
* Layout can be specified by CSS styles; Admin interface redesign; Bugfixes

= Version 1.0.1 =
* Bugfix

= Version 1.0 =
* First release