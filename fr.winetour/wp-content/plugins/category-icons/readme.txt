=== Category Icons ===
Contributors: submarine
Donate link: http://www.category-icons.com/support-the-plugin/
Tags: category, categories, icons, icon, image, images, post, posts, list, lists, manage, plugin, plugins, sidebar, horizontally, vertically, widget, admin, photo, photos, formatting, rss
Requires at least: 2.3
Tested up to: 2.8.4
Stable tag: 2.1.1

The No. 1 plugin to assign icons to categories.

== Description ==

Assigns icons to categories with WordPress 2.3 or higher. This wonderful plugin :

* displays one or more icons in front of your post title or wherever you want  
* displays icons in the sidebar, with or without the category name, with or without the help of the widget
* includes a widget. You can : use multiple Category Icons widgets (for WordPress 2.5 and higher), include or exclude some categories, display only icons (i.e. without the category name)...
* you can make rollover with the icons in the sidebar if you follow [this tutorial](http://www.category-icons.com/2008/05/how-to-make-rollovers-with-jquery/)
* is able to display category icons in your RSS
* will either show you where to place the little code needed in your themes, or patch them automatically
* generates pure html code of the img tag, so it gives you the freedom of doing what you want to with the icons (customizing via CSS or not)
* generates valid XHTML (and Strict XHTML)
* can prioritize the icons display (by Oliver Weichhold)
* is translated in 20 languages (all the .po files are included, in case you want to update your language) : 
Arabic (ar) by Ahmad Bekdash, 
Belarusian (be\_BY) by Fat Cower, 
Brazilian Portuguese (pt\_BR) : 3 versions (!) by Pedro Germani, Helder Vieira & Rodrigo Vieira Alves,
Bulgarian (bg\_BG) by Kalin Dimitrov,  
Chinese (zh\_CN) by ?, 
Chinese Taïwan (zh\_TW) by Hugo Chen, 
Czech (cs\_CZ) by Spazi, 
Danish (da\_DK) by Henrik Schack, 
Dutch (nl\_NL) by Vincent Sparreboom, 
French (fr\_FR) by Brahim Machkouri, 
German (de\_DE) by Kristian Bollnow, 
Italian (it\_IT) by Gianni Diurno, 
Japanese (ja) by Tenderfeel, 
Polish (pl\_PL) by Shlizer,
Russian (ru\_RU) by Dimox, 
Slovak (sk) by Samuel Kroslak, 
Spanish (es\_ES) by TechnopodMan, 
Swedish (sv\_SE) by P-C, 
Turkish (tr\_TR) by Selim Basak,
Ukrainian (uk\_UA) by Andrew Senyshyn
* is compatible with these powerful plugins : 
[My Category Order](http://wordpress.org/extend/plugins/my-category-order/),
[qTranslate](http://wordpress.org/extend/plugins/qtranslate/),
[Recent Posts](http://wordpress.org/extend/plugins/recent-posts-plugin/),
[SEO Friendly Images](http://wordpress.org/extend/plugins/seo-image/),
[Similar Posts](http://wordpress.org/extend/plugins/similar-posts/)
* is compatible with WordPress MU

You can have a look to [the changelog](http://www.category-icons.com/category/changelog/ "Category Icons'changelog").

Or maybe you were looking for [Category Icons Module](http://www.awpcp.com/premium-modules/category-icons-module/) ?

== Frequently Asked Questions ==

Please visit [the official website](http://www.category-icons.com/category/faq/ "Category Icons'FAQ") and [How-To](http://www.category-icons.com/category/howto/ "Category Icons' How-Tos"). Have a look at the how-to, you'll see that there're some tricks to do with it (rollovers, display specific icon, icon by default...). 
And don't miss the languages updates.

== Installation ==

1. Very important ! Deactivate the Category Icons plugin if it was previously installed.
2. Delete the old `category-icons` directory and upload the new `category-icons` directory into the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu.
4. Go to the 'Posts' menu (or 'Manage' menu in previous versions of WordPress) and select Category Icons to use the plugin.
5. Please visit [the official website](http://www.category-icons.com/ "Category Icons") for further details and the latest information on this plugin.

== Translations ==

I'm looking for volunteers to [translate the plugin](http://www.category-icons.com/translate-the-plugin/ "Translations") and maintain the translations up to date. It's easy to translate it if you use the plugin [Easy Translator](http://wordpress.org/extend/plugins/easy-translator/ "Easy Translator"). All the .po files are included (and the pot file too, of course), in case you want to update a language with poEdit.

== Screenshots ==

[Please visit the category icons plugin page for screenshots & sample](http://www.category-icons.com/screenshots/)

== Donate ==

Any amount is welcome to support the development of the plugin.

== I've found a bug, how can I tell you about it so you'll fix it for me? ==

Go to http://www.category-icons.com/contact and fill out the contact form. Make sure you let me know the following:

* What version of WordPress you're using
* What other plugins you're running
* Exactly what problem you're seeing

Unfortunately, I do have a life outside of this plugin, so don't be sad if I don't fix the bug within 5 minutes.

== Changelog ==

= 2.1.1 =
* Added 2 new tabs : How To (WP 2.8+ only) & Support the plugin

= 2.1 =
* jQuery scripts bugs with WordPress 2.8 resolved.
* The admin interface is successfully checked as XHTML 1.0 Transitional. 
* Bug in checkboxes in Options Tab resolved.
* Better visual integration with WordPress 2.7+
* You can now add multiples Category Icons widgets.
* You can include just some categories thank to the new "Include" field in the widget.
* Two options were missing from the start : maximum icon width & maximum icon height.
* You can choose the access type to the database : an access for every icon (a query for each icon) which is the default option, or one access for all the icons (one query for all).
* Added compatiblity with Rob Marsh's plugins (http://rmarsh.com/) : recent posts, recent comments & similar posts. Just add in output filter : {caticons} to see the first category icon.
* Compatible with WordPress MU 2.8.1

= 2.0.7 =
* Compatibility with My Category Order added.
* I've added some links to Category Icons's blog.
* You can now sort a column of the category icons table by clicking its header. If you want to come back to the initial sort state, refresh the page.
* The widget is now included in category-icons.php. The file category-icons\_widget.php is not needed anymore, so you just have to activate only the plugin itself (the widget is automatically activated when you activate the plugin).
* The check for the field priority is removed. May be it will avoid some issues with MySQL... 
* New languages added : Arabic (ar) by Selim Basak, Brazilian (pt\_BR) by Pedro Germani, Helder Vieira & Rodrigo Vieira Alves, Czech (cs\_CZ) by Spazi, Swedish by P-C, Turkish by Selim Basak. Many thanks to them.

= 2.0.6 =
* Compatibility with qTranslate updated.
* Compatibility with Admin Drop Down Menus added.
* Compatibility with SEO Friendly Images improved : when you used SEO plugin, the ALT and TITLE attributes in the sidebar contained the last post title instead of the category names.
* New language added : Russian (translation by Dimox).

= 2.0.5 =
* I've found a plugin : SEO Friendly Images. As I've removed the SEO thing since v2.0, I've added the compatibility with that plugin. (I use a filter : category\_icons...)

= 2.0.4 =
* A new parameter to get\_cat\_icon() function is available : hierarchical. It displays icons in a hierarchical order (horizontally). 
* A new language is included : japanese, thanks to Tenderfeel.
* The plugin is now compatible with Lighter Menus plugin : it displays the links Icons, Options, Template Code.

= 2.0.2 =
* You can display the sidebar icons as a block again, to do some rollover effect with CSS, for example.

= 2.0.1 =
* I've put back the link=false default parameter for the put\_cat\_icons() function

= 2.0 =
* The plugin generates valid XHTML and Strict XHTML if you don't use the border parameter.
* You have the choice to use CSS or an image to define the margins of the icons in the sidebar.
* The ALT descriptions are removed. They're empty, now.

= 1.9.9 =
* A little bug in the widget is corrected (I forgot to not localize the widget id)

= 1.9.8 =
* Now the icons are more "SEO friendly" : the description of the ALT attribute of the icon is the category name.
* A new parameter is available : align, in order to align the icon when necessary.
* A little bug in the widget has been corrected by Jean-Christophe Marie (merci!).
* A new language is available : Danish, thank to Henrik Schack.
* The languages are not up to date (except Danish & French), but I'm sure the translators will do their best. I promise to add strings as less as possible. :-)

= 1.9.6 =
* If you use a plugin to set category to pages, you can use put\_cat\_icons() to display icon(s) next to the name of the page in the sidebar.
* Better integration in WordPress 2.5 : in order to have only one location of Category Icons, it's now located under the Manage menu, and the Category Icons 'Manage' tab is now replaced by 'Icons'.
* An information is displayed if there's a new version of the plugin (it can be disabled)
* New parameter : border. If true, the result will be a border around the icon. If false (by default) no border around it.
* There's no more link Select and remove. Instead, there is a checkbox to select the icon(s) to be removed. Now, to edit icons, you must click on the category name, like in WordPress 2.5. So the plugin takes the 'look & feel' of WordPress 2.5.
* The problem with no select or remove links should be fixed : I use another way to check the user role. (Ivan tested levels to check the role)
* Displays informations in the footer in WordPress 2.5 : 
	- displays the current version of Category Icons plugin
	- links to the following pages : FAQ, troubleshoot, and contact page
	- displays the name of the translator which is linked to his website if he provided an url. (The translator just have to enter these informations in the .po file, under the string translator\_name and translator\_url) 
* The category\_icons.pot file is updated (there are now 66 strings). (@Translators : I need your help, please) 
* The language files are now in a subfolder named languages
* Detects if the default upload folder is readable
* The icons are searched recursively
* Minors corrections of the code (no influence on the behaviour of the plugin)
* The widget has now the 'look & feel' of WordPress 2.5 and the localization works correctly now. 
* The widget has now the same version as the plugin, because of a weird behaviour of WordPress server update information.
* The RSS feeds can display the category icons.

= 1.8.6 =
* Compatibility with qtranslate added.

= 1.8.5 =
* A bug is corrected.

= 1.8.4 =
* Chinese & Italian translation added.
* Template Code improved a bit.
* WordPress 2.5 RC-1 friendly.

= 1.8.3 =
German translation file updated (Thanks Kristian !)

= 1.8.2 =
* New option : size of the space before and/or after the icons in the sidebar.
* A widget is included to display icons in the sidebar.
* French translation is up to date. Other language than english & french need to be updated. (thanks in advance to the translators)

= 1.8.1 =
* A little bug is corrected.

= 1.8 =
* The plugin is now compatible with WordPress Mu.

= 1.7.9 =
* Just a little 'bug' corrected.

= 1.7.8 =
* Bulgarian language added. Thanks to Kalin Dimitrov.
* Dutch language added. Thanks to Vincent Sparreboom.
* German language added. Thanks to Kristian Bollnow.
* New functionnality : in the parameters of put\_cat\_icons() function, if you put icons\_only=1, then only the icons will be displayed in the sidebar, instead of the icon AND the category name.

example : 

if (function\_exists('put\_cat\_icons')) 
   put\_cat\_icons( wp\_list\_categories('title\_li=&echo=0') ,'icons\_only=1');
else 
   wp\_list\_categories('title\_li='); 

= 1.7.7 =
* The plugin is now compatible with the 'Top Level Categories' plugin.
* Now, if no class is given in the parameters, the class attribute is not added to the img tag.

= 1.7.6 =
* Added Ukrainian language by Andrew S. (http://airman.myphotos.cc/)
* Bug resolved : I said to put the category\_icons.php file into the category\_icons directory instead of category-icons directory.
So, to be clear, you must put (if it's not done yet) category\_icons.php file in the category-icons directory.

= 1.7.5a =
* Added Slovak (sk) language. Author : Samuel K. (http://blog.samopal.eu/)
* Added Spanish (es\_ES) languages. Author : TechnopodMan (http://simplezaweb.com/)

= 1.7.5 =
* The Code Template process has been improved.
* Added an option to automatically patch or not the template files.

= 1.7.4 =
* now, the plugin is in a directory named 'category\_icons'.
* a bug is resolved
* get\_settings() (deprecated) replaced by get\_option()
* new functionnality : 
Through The Template Code panel, your template files are scanned and patched if they're writable. If not, informations about where you should paste get\_cat\_icon() in your files are displayed.

= 1.7.2 =
* a bug is resolved

= 1.7.1 =
* new feature : you can now display the icons vertically, it's like a 'stack' of icons, thank to a new parameter for the function get\_cat\_icon() : vertical\_display. Set it to true if you want to use that feature. 

Example : 

get\_cat\_icon('vertical\_display=true');

The 'stack' is generated thank to a div tag. That div tag has a class : caticons. So if you want to extend the style of the div tag, you can use css in style.css.

Example of generated code :

<div class="caticons" style="text-align:center;float:left;width: 32px;">[the\_icons\_are\_here]</div>

Example of extending the class in style.css :

.caticons,img {
	border:0 !important;
}

= 1.7 =
* A few bugs are resolved.
* Links & images are now W3 compliant.
* New functionnality : you can prioritize the display of icons. 
From the author (Oliver):
You can now assign numeric priorities to categories. The priority is used to select a single category icon for a post with multiple categories.

Example: imagine we have a post with two categories:

- Companies -> ACME Corp. (Icon: ACME.gif, Priority 10)
- Products -> Milk (Icon: Milk.gif, Priority 20)

If you now pass the new boolean parameter “use\_priority” to the get\_cat\_icon() function, it will only render Milk.gif because Products -> Milk has the highest priority of all categories for the post.
* You can display icons in the sidebar. you just have to use the new function : put\_cat\_icons(). Example of use :
put\_cat\_icons(wp\_list\_categories('show\_count=1&title\_li=<h2>Categories</h2&echo=0'));
You can add any parameters to wp\_list\_categories(), but you MUST add echo=0.

The function put\_cat\_icons() accepts 2 parameters : 
	first parameter : the list to process (from the wp\_list\_categories()),
	second parameter : a string representing the same parameters as get\_cat\_icons().
* You can define the maximum number of icons to be displayed in front of the posts titles in Options.
* You can translate the plugin into your own language thanks to the .pot file provided with the plugin
If you translate it to another language than french, thank to send it to me in order to put it online and share it with everyone. :-)
* For the frenchies : I've translated the plugin into french. You just have to put the file 'category\_icons-fr\_FR.mo' in the plugins directory. (Pour avoir la traduction du plugin en français, vous devez juste déposer le fichier 'category\_icons-fr\_FR.mo' dans le répertoire des plugins).
* 

= 1.6 =
* Now compatible with WordPress 2.3+
