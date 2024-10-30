=== mu ===
Contributors: chewbocka
Tags: social, twitter, plurk, identica, friendfeed, jaiku
Requires at least: ?
Tested up to: 2.6
Stable tag: 1.0

mu (Microblog Updater) is a plugin that farms out links to your posts to your microblogs.

== Description ==

mu works using PHP's cURL libraries and supplied user data to send out updates to whatever social networks you've
signed up for.  When you publish a post, mu steps in and grabs your post's permalink and whatever short description
you've supplied and sends it off.  The only setup required by a user is the initial choices for default services, 
when to send updates, and credentials for the user's accounts.

== Installation ==

This section describes how to install the plugin and get it working.

Installing from source:
1. Extract the archive to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set your defaults and specify account details in Manage -> mu Options

Installing from SVN:
1.  in wp-content/plugins, run command
	svn checkout http://mb-mu.googlecode.com/svn/trunk/ mu
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set your defaults and specify account details in Manage -> mu Options

== Upgrading ==

This section describes how to upgrade from a previous version.

From source:
 * Extract the archive to the `/wp-content/plugins/` directory, overwriting the previous version

From SVN:
* in wp-content/plugins, run command
	svn checkout http://mb-mu.googlecode.com/svn/trunk/ mu

