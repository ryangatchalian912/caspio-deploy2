=== Caspio Deploy2 ===
Contributors: Caspio Inc.
Tags: caspio, bridge, datapage, deployment, seo, php, javascript, ajax, database
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.6

Enables Shortcodes for SEO or embedded deployment of Caspio cloud computing database applications.

== Description ==

Caspio is an online database featuring web-based tools for designing database tables, forms, reports, charts and graphs, and more complicated applications like store locators with Google Maps mashups. Applications can be created without programming, and embedding them in your website is as easy as copying and pasting a code snippet.

The Caspio Deploy2 plugin enables Shortcode placeholders that further streamline the deployment of Caspio applications. The shortcodes can be used for SEO deployment of content, as well as embedding the AJAX widget used to display Caspio forms. Caspio Deploy2 replaces the earlier Caspio Deployment Control plugin (which did not use shortcodes).

Every Caspio application has a unique address at caspio.com, but most Caspio users want to embed these applications in their own websites. Caspio provides code snippets you can paste into any web page, but pasting JavaScript or PHP code into the WordPress editor is awkward. The Caspio Deploy2 plugin lets you instead paste the application URL into a `[caspio]` shortcode format, and the plugin generates the required JavaScript code or calls the PHP functions.

See the Screenshots tab for an illustration of how to obtain the Caspio URL for an application by toggling the DataPage deployment wizard to "Direct from Caspio".

You can then use any of the following Shortcode formats:

= Embedded Deployment / AJAX Widget =

`[caspio]http://b4.caspio.com/dp.asp?AppKey=5F422000c3f2d8h4d3c9f4b9g9e0[/caspio]`

or

`[caspio embed="https://b4.caspio.com" key="5F422000i9f9a0h4e2i7c9g6c9c3"]`

= SEO Deployment =

`[caspio seo="http://b4.caspio.com/dp.asp?AppKey=5F422000c3f2d8h4d3c9f4b9g9e0" style="l"]`

This can also be formatted as:

`[caspio url="http://b4.caspio.com/" key="5F422000c3f2d8h4d3c9f4b9g9e0" style="l"]`

Or you can embed the whole PHP code snippet for SEO Deployment:

`[caspio]
<!-- Begin Caspio Deploy Code (for inserting in body) -->
<?php require_once('http://b4.caspio.com/scripts/dpload.txt');dpload('http://b4.caspio.com/','5F422000c3f2d8h4d3c9f4b9g9e0','l');?>
<!-- End Caspio Deploy Code -->
[/caspio]`

Instead of having WordPress execute the PHP code, WordPress extracts the parameters from the dpload function call. You can also delete everything except those parameters:

`[caspio]
'http://b4.caspio.com/','5F422000c3f2d8h4d3c9f4b9g9e0','l'
[/caspio]`

The style parameter you would use with the first two examples is the third parameter in the dpload function call. If you omit the style parameter, the plugin uses "l" (lower case "L") as the default.

In order to deploy a Caspio Bridge SEO DataPage in WordPress, you must have:

* Caspio Bridge account with activated SEO deployment and properly configured DataPage
* Installed and activated the Caspio Deploy2 plugin for WordPress. 

For information about Caspio Bridge see the Caspio official site at [http://www.caspio.com](http://www.caspio.com "Caspio official site")

== Installation ==

If you have previously installed a WordPress plugin, you will find this installation fairly straightforward:

1. Download the Caspio Deploy2 plugin archive and extract the files
1. Copy the resulting caspio-deploy2 directory into /wp-content/plugins/
1. Activate the plugin through the 'Plugins' menu of WordPress

== Frequently Asked Questions ==

= Where do I get support and other information about Caspio Bridge? =

For support and other information about Caspio Bridge, see the Caspio official site at [http://www.caspio.com](http://www.caspio.com "Caspio official site"), Caspio Bridge overview at [http://www.caspio.com/bridge/](http://www.caspio.com/bridge/ "Caspio Bridge Overview") and Caspio support page at [http://www.caspio.com/support/](http://www.caspio.com/support/ "Link to Caspio support page").

== Screenshots ==

1. To extract the Caspio URL for an application, toggle the DataPage deployment wizard to "Direct from Caspio". Paste the deployment code snippet in between the `[caspio][/caspio]` tags for embedded deployment (AJAX widget). Example: `[caspio]http://b4.caspio.com/dp.asp?AppKey=5F422000c3f2d8h4d3c9f4b9g9e0[/caspio]` / Or, for SEO Deployment include the URL as the shortcode attribute seo, Example: `[caspio seo="http://b4.caspio.com/dp.asp?AppKey=5F422000c3f2d8h4d3c9f4b9g9e0" style="l"]`.
2. Option #2: Paste the deployment code snippet in between the `[caspio][/caspio]` tags for smooth deployment in WordPress. Shown here is the standard PHP code snippet for SEO Deployment. For security reasons, the PHP code is not executed directly, but the plugin extracts the server url and application key parameters it needs to function. The same technique works for pasting a JavaScript snippet between `[caspio][/caspio]` tags to avoid having the code scrambled by the WordPress editor in Visual mode. 
3. Option #3: You can use the more concise format `[caspio url="CaspioServerURL" key="CaspioAppKey" style="l"]` (for SEO Deployment) or `[caspio embed="CaspioServerURL" key="CaspioAppKey"]` (embed JavaScript widget, note the parameter name `embed` instead of `url`). This shows where you can find the server url variable by toggling the Deployment wizard to Direct from Caspio.
4. The location of the application key parameter is shown here.

== Changelog ==

= 1.6 =

Fix an issue with HTTPS

= 1.5 =

White Label

= 1.2, 1.3, 1.4 =

Updates to Caspio PHP library.

= 1.1 =

Simplifies model for using Shortcodes.

= 1.0 =

Initial Public Release. Replaces earlier Caspio Deployment Control plugin.


== Upgrade Notice ==

= 1.0 =
Recommended replacement for earlier Caspio Deployment Control plugin.