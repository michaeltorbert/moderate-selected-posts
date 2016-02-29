=== Moderate Selected Posts ===
Contributors: hallsofmontezuma, sivel
Tags: post, posts, moderate, moderated, selected
Requires at least: 2.5
Tested up to: 3.7.1
Stable tag: trunk

Force comment moderation on selected posts but allow others to remain open.

== Description ==

Force comment moderation on selected posts but allow others to remain open.

Simple admin interface to select your posts by title.  This does not work for pages at this point, only posts.

== Installation ==

1. Upload the `moderate-selected-posts` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

NOTE: See "Other Notes" for Upgrade and Usage Instructions as well as other pertinent topics.

== Upgrade ==

1. Delete the previous `moderate-selected-posts` folder from the `/wp-content/plugins/` directory
1. Upload the new `moderate-selected-posts` folder to the `/wp-content/plugins/` directory

== Usage ==

1. Visit Settings>Moderate Selected Posts in the admin area of your blog.
1. Select the posts you wish to moderate all comments on.

OR

1. Visit the post you want to moderate all comments on.
1. Check or uncheck the checkcbox in the Moderation meta box.

Enjoy!

== Changelog ==

= 1.4 (2012-12-13): =
* Fix PHP Notices
* Tested against WordPress 3.5

= 1.3 (2009-02-27): =
* Fix PHP Notices
* Update admin styling
* Added protection agains value returned from get_option not being an array

= 1.2 (2008-12-12): =
* Admin Styling updates
* Meta box for add/edit post pages
* Localization support
* Various enhancements for 2.7

= 1.1.1 (2008-09-09): =
* Small update to support PHP4

= 1.1 (2008-09-03): =
* Hook into comment process earlier so that a moderation email sent
* Hook into approval process earlier than Akismet so that Akismet can override with spam
* Re-wrote functions around new hooks
* The comment author, admins and users with moderation capabilitites are now not moderated

= 1.0 (2008-08-27): =
* Initial Public Release
