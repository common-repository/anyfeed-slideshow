=== Anyfeed Slideshow ===
Contributors: solei
Donate link: 
Tags: slideshow, widget, rss, feed, picasa, flickr, slide show
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 1.0.12

Anyfeed Slideshow is a fully customizable slide show widget which displays a feed of images from any RSS or Atom feed provided to it.

== Description ==
Anyfeed Slideshow is a plugin for Wordpress which adds a slide show widget to your library. This widget is fully customizable and will display a feed of images from any RSS or Atom feed provided to it.

[youtube http://www.youtube.com/watch?v=CountjTELgQ]

= About =
The widget was designed from the ground up to be both very simple to use, while also being excessively customizable. The more complicated features are hidden by default, keeping the widget customization panel simple and obvious, while more advanced customization remains available at the click of a button.

This plugin was originally written to be compatible with Wordpress from version 2.8 and up, though it may be compatible with older versions as the amount of interaction with Wordpress is fairly minimal. Wordpress' stock version of jQuery is employed for all animations and interactions.

To see a demo of Anyfeed Slideshow in use, check [the blog on the creator's site](http://tixen.net/blog/). 

== Installation ==

1. Unzip and upload the resulting folder to your hosting provider in the following directory:

` &lt;wordpress root&gt;/wp-content/plugins/ `

2. Activate the Plugin in your Wordpress admin plugins page.
3. Add the Widget to your sidebar in the Appearance > Widgets section.

== Frequently Asked Questions ==

= What feeds does Anyfeed Slideshow support? =

It should support any feed- thus the name!  If you find one it doesn't support, please let us know.

= Why can't I enable the Cache option? =

The Caching option in the Anyfeed Slideshow makes use of the system defined temp directory to store the cached feed files.  Without access to that directory, the script can't store the file!

= Where can I find support? =

Check our [forum here on Wordpress](http://wordpress.org/tags/anyfeed-slideshow?forum_id=10).

== Screenshots ==

1. This should be the first thing you see when you add the widget.
2. The full widget control panel, including advanced configuration features.
3. By default, this is what the widget looks like when it's loading.
4. Images are centered, and stick to the top of the frame by default.
5. Mouseover pauses the slideshow, and provides Title information & navigation buttons. Clicking the image directs a user to the URL associated with the feed item (in a new window).

== Changelog ==

= 1.0.12 =
# Bug Fixes #
* Corrected an issue with jQuery in recent versions of Wordpress which prevented some feeds from displaying (fix by [Jfreake](http://wordpress.org/support/profile/jfreake))
* Corrected an issue with Picasa feeds caused by URL structure changes at Picasa (fix by [Jfreake](http://wordpress.org/support/profile/jfreake))


= 1.0.11 =
# Bug Fixes #
* Corrected an issue with Flickr gallery RSS URLS ([Bug Report](http://wordpress.org/support/topic/plugin-anyfeed-slideshow-flickr-feed-not-working) by [barnabya](http://wordpress.org/support/profile/barnabya))
* Corrected an issue with SmugMug RSS duplicate photos showing  ([Bug Report](http://wordpress.org/support/topic/plugin-anyfeed-slideshow-image-sizes) by [beck.osborne](http://wordpress.org/support/profile/beckosborne))

# New Features #
* Redesigned the Configuration page
* Added ability to select if links go to same or new window
* Added maximum image limit with a default of 50 (makes large feeds MUCH faster)
* Added option to keep Title text up permanently
* All Javascript moved into an already included file for SEO best practices compliance


= 1.0.10 =
* Added some regex to prevent malformed XML from causing the slide show to fail!

= 1.0.9 =
* Corrected an issue caused by 1.0.8, which prevented Vimeo style media feeds from working properly.

= 1.0.8 =
* Resolved a few issues causing some feeds not to work (Picasa album feeds and a few others).

= 1.0.7 =
* Resolved an issue causing Opera browsers to never successfully finish loading xmlns media feeds\
* Re-wrote the photo caching engine to check for broken or missing images
* Added an error display system in the feed itself to help users diagnose problems
* Made modifications to the feed caching engine that should help the system be more compatible
* Added a mod_curl function to pull down images when fopen is not allowed to
* Cleaned up and consolidated code.

= 1.0.6 =
* Resolved an issue where blogs accessible by URLs outside of what was defined in the WP Config would result in a neverending loading screen
* Tweaked CSS to ensure titles larger then the space allowed clipped gracefully.

= 1.0.5 =
* Corrected an issue preventing Webkit based browsers (Safari & Chrome) from loading images within &gt;content:encoded&lt;` tags.

= 1.0.4 =
* Fixed the second half of the bug fixed in 1.0.2.  Oops! 

= 1.0.3 =
* Consolidated and cleaned up some of the Javascript
* Fixed a bug that caused the slide show to fail in Safari and Chrome
* Fixed the thumbnail resizing system for Flickr and Picasa, which was not working outside of Cached mode.

= 1.0.2 =
* Changed the Feed downloader engine to use a passthrough proxy by default ([Bug Report](http://wordpress.org/support/topic/plugin-anyfeed-slideshow-trying-to-use-slide-show-plugin-with-picasa-feed?replies=4#post-1763795)  by [ohippyday](http://wordpress.org/support/profile/ohippyday))
* Updated the widget controls to disable the caching option for Host configurations in which it will not work.

= 1.0.1 =
* Fixed a bug which caused single image RSS feeds not to display. ([Bug Report](http://wordpress.org/support/topic/plugin-anyfeed-slideshow-newbie-help-with-slideshow?replies=10) by [nlee](http://wordpress.org/support/profile/nnlee))
* Adjusted the loading timer to ensure the Loading text does not temporarily overlap images.

= 1.0 =
* Released.


== Upgrade Notice ==

= 1.0.11 =
Please upgrade to the latest version! 

* Compatibility with latest versions of Wordpress
* Fixes issues with most recent version of jQuery
* Fixes issues with Picasa feeds


