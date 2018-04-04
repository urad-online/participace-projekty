=== Participace na projektech ===
Contributors: improvemycity
Tags: improvemycity, imc, improve, city
Requires at least: 4.4
Tested up to: 4.9
Stable tag: trunk
License: AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.en.html

Direct citizen-government communication & collaboration.

== Description ==

Participace na projektech for WordPress is a platform for managing local issues; from reporting, to administration and analysis.

It is an integrated solution aimed to local governments seeking collaboration with their citizens, freely available as open source.

= Report =
Collecting issues via web or mobile.

* Reported via web or mobile

    *By allowing citizens to report issues from their home using the web version, or while on the street using the mobile app (iOS & Android).*

* Easily composed but descriptive

    *By asking citizens to provide only the information necessary to locate and resolve the issue, such as title, description, location and category.*

* Accurately positioned

    *By offering a map to facilitate citizens in determining the exact location of their issue.*

* Picture enabled

    *By allowing to attach an image on the spot for describing the issue.*

* Categorized based on their nature

    *By urging citizens to select one of the pre-specified categories reflecting the municipality departments.*

* Commented and voted

    *By offering the mechanisms to post comments or vote for issues that have been submitted by other citizens.*


= Administer =
Easy to use administration platform.

* Browse effectively

    *Issues are presented on the city map, as an ordered list but also in a single-issue page displaying the full set of submitted details.*

* Distribute responsibilities

    *Assign one or more officers per category and split the administration effort across the municipality departments.*

* Track pending issues

    *Issues are automatically routed not only to the appropriate department but also to the inbox of the responsible officer.*

* Monitor progress and update citizens

    *Resolve issues and inform citizens by email or through a progress indication bar (Open -> Acknowledged -> Closed).*

* Provide direct feedback

    *Provide written feedback to the citizens giving non-standard explanations for each specific case.*

* Customize easily

    *Fully customize the system in terms of user rights, number and nature of categories, notification rules and localization settings.*


== Installation ==

1. Install the plugin through the WordPress plugins screen directly or upload the plugin files to the `/wp-content/plugins/plugin-name` directory.

2. Activate the plugin through the 'Plugins' screen in WordPress.

3. Use the Settings->IMC Settings screen to configure the plugin.

You're done!

== Frequently Asked Questions ==

= Is Participace na projektech free? =

Yes! All the functionality offered by the plugin is free, as long as you have a working WordPress installation.

= Can I translate Participace na projektech in my language? =

Yes you can! As of version 1.3.1 the community translation system has been activated.

Go to https://translate.wordpress.org/projects/wp-plugins/improve-my-city

Select your language and start translating!

= How can I customize the permalink structure slug? =

It can be customized from the IMC Settings.

= How to insert Boundaries? =

You can now search for a city or a municipality from settings. Choose one from the results and preview the boundaries on the map before saving changes.
Alternatively you can set your own boundaries usign the following format:
[[{"lng":22.9014986, "lat":40.6532172},{"lng":22.9016877,"lat":40.6535071},...{"lng":22.9016877,"lat":40.6535071}]]

= Is there a way to enable everyone to submit an issue, without having to sign in? =

Unfortunately no, since this would be against the rationale of 'Participace na projektech'. Every citizen must have his own account, in order to vote, comment and follow the progress of the submitted issues. This also ensures the transparency between citizens and municipality.

= How can I make the social registration work? =

Step 1. Create a Facebook or Google app and note the provided information.

Step 2. Access the config.php file that is located in plugin_root_folder/hybridauth/ and change the base_url, and the provider information to yours.


== Screenshots ==

1. Issues Overview (Grid)

2. Issues Overview (List)

3. Detailed Issue information

4. Report a New Issue

== Changelog ==


= 1.4.1 =
*Release Date - 24/01/2018*

Now fully compatible with PHP 7.2.0 and WordPress 4.9.2

Removed deprecated function screen_icon()

Fixed warning about Status Ordering parameter (backend) on PHP 7.2.0

Fixed bug when Taxonomy Category is empty at meta box (backend)

Fixed bug about $tag parameter variable type error (mail & color at backend)

Fixed bug at color picker warning (specified default value)

Fixed warning at issue columns (backend)

= 1.4.0 =
*Release Date - 28/11/2017*

Fully compatible with WordPress 4.9

Upped PHP version requirement: 5.2.4+ -> 5.4.0+

Tested and fully compatible with PHP 7.0.

Change in how various scripts are loaded, to avoid rare bugs and align with WordPress best practices.

Updated HybridAuth to version 2.9.6 (Facebook login error persists. It's going to be fixed in an upcoming version).

Theme fix: In the issues list view, odd & even list items have now different background color to differentiate between successive elements.

Bugfix: Disabling submit button after an issue has been submitted or edited to avoid submitting multiple times.

Bugfix: Fixed an issue where the language or encoding of an issue image filename, was interfering with the upload process.

Improvement: Addressed all php warnings incited by the plugin.

Theme fixes / improved web components abstraction for better theme integration.

Many bug fixes and theme & code improvements.


= 1.3.2 =
*Release Date - 16/03/2017*

Bugfix: Fixed bug on the backend when editing a category or a step and the edit category icon and color respectively were broken.

Bugfix: Detailed issue view: Added the correct issue id variable. The variable was implied, in rare cases breaking functionality.

Various bug fixes and code improvements.


= 1.3.1 =
*Release Date - 15/03/2017*

Changed text domain for the translations to be accessible to community.

= 1.3.0 =
*Release Date - 15/03/2017*

Fully compatible with WordPress 4.7

The code about adding our IMC page templates, has been updated to work on WordPress 4.7+

Major bugfix: In WP Installations 4.7+ the issue status was not saved correctly and as a result was breaking functionality.
It has now been fixed. If you have been affected by this issue, just change the status of all the affected issues to make them work again as intended.

In single issue page, the edit button was shown inconsistently. It has been fixed.

Fixed a styling bug on the backend, where in screens under 782 width, the title in the admin view for issues was obstructed.

Added a PHP version check message.


= 1.2.0 =
*Release Date - 07/11/2016*

Fully compatible with WordPress 4.6.

Implemented a new boundaries check on the backend. You can now search for a city or a municipality, choose one from the results and preview the boundaries on the map before saving changes.
Alternatively you can set your own boundaries usign the following format:
[[{"lng":22.9014986, "lat":40.6532172},{"lng":22.9016877,"lat":40.6535071},...{"lng":22.9016877,"lat":40.6535071}]]

Settings option added, about setting the default issues layout (grid or list view).

Some more options on settings have been added.

Critical bug about email notifications on comments resolved.

Mail title bug resolved.

Many layout & styling fixes on the frontend & backend.

Cleaned up some JS scripts & unneeded assets.

New translation strings & fixes (please send us the new ones to upload in next incremental update).

Added arabic translations.

Updated social login authentication plugin to latest version.


= 1.1.2.2 =
*Release Date - 13/09/2016*

When editing an issue from the backend, You couldn't change the date. It has been fixed.

Added some missing translations too.
Please update your po/mo files.


= 1.1.1.5 =
*Release Date - 26/08/2016*

Fixed some issues with translations.
Please update your po/mo files.

= 1.1.1 =
*Release Date - 24/08/2016*

Translations are now working properly. Just update your language through poedit.
	-> Greek translation has been updated.
    -> English .mo template file has been updated.
    (Please update if you have been working on translating the plugin to another language)

Squashed a lot of bugs!


= 1.1.0 =
*Release Date - 08/07/2016*

* NOTICE: Major changes in this release. Please read below before updating!

This update to the Participace na projektech plugin adds new functionality, changes the initial structure of the plugin and paves the way for the upcoming release of the Participace na projektech WordPress API.

It is advised to delete your current plugin and do a clean installation of the new one, as some functionality will not be working with the update.

Make sure you delete all the Participace na projektech posts, categories and statuses.

If you have been already using the plugin and you have a sufficient userbase / issue load, please feel free to contact us so as to help you migrate your installation to the new one.


* New Features:

Social Media login (using Facebook or Google).

Custom slug for issues page.

Api section in settings, regarding the upcoming Participace na projektech WordPress API functionality.

Many fixes and tweaks.


= 1.0.1 =
*Release Date - 13/05/2016*

* Bugfix: Fixed an issue with the comments form, when using intrusive themes.


= 1.0.0 =
*Release Date - 07/04/2016*

* First stable release.


== Documentation ==

= [1] Set IMC Main Page  =
*The Participace na projektech Main Page (IMC-Participace na projektech Main Page) is located on Pages > IMC Participace na projektech Main Page.*

After activating the plugin, a page called "IMC - Participace na projektech Main Page" is automatically created. This is the core page of the plugin, where all the issues will be presented. You can add it on a menu, or assign it as you front page.  If you ever delete this page or any page that is created by the Participace na projektech plugin you can bring them back by disabling and enabling the plugin again.

* **[1.1] Add Participace na projektech Main Page to your main menu**

1. Visit Appearance > Menus
2. Choose an existing menu or create a new one.
3. Search and Add to Menu the "IMC - Participace na projektech Main page" from the Pages section.
4. Choose a location for your menu at Theme locations and save it.

* **[1.2] Set Participace na projektech Main Page as your site's Front page**

If you want to use the Participace na projektech Main Page as your front page, go to Settings > Reading and set "Front page displays" to "A static page". From the dropdown menu, select the "IMC - Participace na projektech Main Page".

= [2] Issues & Categories  =

* **[2.1] Before creating your first Issue**

Each issue that is submitted to the "Participace na projektech" platform is assigned a Status and a Category. Statuses denote the progress of an issue. You can have as many statuses as you want, although it is best to limit the number of statuses to 6. You can have as many Categories as you like.

Before using the plugin to report issues, there has to be at least one Status and one Category set, so the plugin can function as intended.

In the following sections, it is described how to manage the Statuses & Categories.

* **[2.2] Manage Statuses**

*To access the Status manager navigate to Participace na projektech menu > Issue Statuses.*

On the left hand side of this screen you can add a new status by providing the necessary information:

1. **Name**: The Status name that will be shown everywhere in the application.
2. **Slug**: The unique identifier of the Status. It usually is the status name in lowercase without special characters and spaces.
3. **Description**: A short description about the status that is usually used internally to convey information between administrators.
4. **Color**: A unique color that best represents the status. Use the color picker to select from a predefined color or set your own.

Then click on the Add new Status button to save it.

You can also edit an existing Status.

On the right hand side of this screen resides the Status table that lists all of your created statuses. The chronological order that they are created, define the order of each step (as seen in the order field).

For example the first status that you create, is also an issue's first (initial) status (the one with the smallest order number). The second one is the status after the initial, and so on.

There is a soon to be implemented ordering mechanism to enable setting the ordering manually.

* **[2.3] Manage Categories**

*To access the Category manager navigate to Participace na projektech menu > Issue Categories*

On the left hand side of this screen you can add a new category by providing the necessary information:

1. **Name**: The Category name that will be shown everywhere in the application.
2. **Slug**: The unique identifier of the Category. It usually is the category name in lowercase without special characters and spaces.
3. **Parent**: Use this dropdown if you want to make this Category a sub-Category; you have to select the sub-category's parent category. "Participace na projektech" can only support depth of one level.
4. **Description**: A short description about the category that is usually used internally to convey information between administrators.
5. **E-mail Notifications**: The e-mail address that will receive all notifications about any changes. You can use more than one if you like, by separating them with a comma. By leaving it blank, it means that all the notifications for this category will be sent to the website's admin mail (WordPress admin).
6. **Category Image**: Select an icon that will represent this category. Icons should have at least 100x100 pixels size and background transparency.
Then click on the Add new Category button to save it.

You can also edit an existing Category.

On the right hand side of this screen resides the Categories table that lists all of your created categories.

= [3] Issue Reporting  =
*In the following section, the default roadmap on how to report an issue is described, both from the frontend and from the administrator panel.*

* **[3.1] Report an issue (Citizens)**

To report a new issue as a citizen:
1. Visit Participace na projektech Main Page on your website.
2. Click on Report an issue link at the top right of your screen
3. There are five pieces of information associated with each new Issue that you need to fill in.

**Title**: Add a short title for the issue

**Category**: Select an appropriate category from the dropdown option.

**Description**: Write a thorough description of the issue.

**Address**: Add the specific address of issue's location. By dragging the pin icon on the map, the Address field is populated automatically. You can also start typing and the auto-complete feature will suggest an address based on your location. The Locate button is used to make sure that the address entered is correct.

**Photo**: (optional) Attach a photo of the issue. Photo size must be less than 2MB.

* **[3.2] Report an issue (Administrators)**

To report an issue as administrator:
1. Visit Participace na projektech > Add New at your dashboard
2. There are six pieces of information associated with each new Issue that you need to fill in.

**Title**: Add a short title for the issue [4.2.1]

**Description**: Write a thorough description of the issue [4.2.2]

**Address**: Add the specific address of issue's location. By dragging the pin icon on the map, the Address field is populated automatically. You can also start typing and the auto-complete feature will suggest an address based on your location. The Locate address button is used to make sure that the address entered is correct.  [4.2.3]

**Category**: Select an appropriate category from the dropdown option. [4.2.4]

**Status**: Select an appropriate status from the dropdown option. [4.2.5]

**Photo**: (optional) Attach a photo using the Featured Image field. [4.2.6]

You can also enable/disable comments for this issue by checking/unchecking the "Allow Comments" checkbox.

= [4] Participace na projektech Settings  =
*The Participace na projektech Settings are located on Settings > IMC Settings, spanning 3 tabs.*

* **[4.1] Settings regarding Google Map**

In this tab resides all the configuration options about the Map functionality of the plugin.

1. Google Maps API KEY: Add your API Key to authenticate requests about your Google Maps. For more information on getting an API Key, see the Google Developers website.[5.1.1]
2. Initial Address: Add specific address that every map in IMC will have as default. By dragging the pin icon on the map, the Initial Address field is populated automatically.[5.1.2]
3. Initial Latitude: Add a specific latitude value that every map in IMC will have as default.[5.1.3]
4. Initial Longitude: Add a specific longitude value that every map in IMC will have as default.[5.1.4]
Note: The 5.1.3 and 5.1.4 values are populated automatically when you set an Initial Address on 5.1.2.
5. Initial Map Zoom: Set the default zoom of the map. Available values range between 0 and 20.[5.1.5]
6. Map Language: Change the in-map language as well as the directions output language. See supported list of languages.[5.1.6]
7. Map Region: Change map region to apply bias for IMC behavior towards your Region.  See Unicode region subtag identifiers.[5.1.7]
8. Allow zooming with mouse scroll wheel: Choose if you want to enable zooming on the map, using the mouse scroll wheel.[5.1.8]
9. Clustering markers: The map clustering functionality will be available and thoroughly documented at a later time.[5.1.9]
10. Boundaries: Insert a GeoJSON object, extracted from the OpenStreetMaps page to display your city's boundaries and allow reporting only inside these limits.[5.1.10]

* **[4.2] Settings regarding Notifications**

In this tab the administrator can enable or disable the dispatch of notification e-mails for a range of the plugin actions.

1. On new issue to user: Send an e-mail notification to Citizen, immediately after reporting an issue.[5.2.1]
2. On new issue to admins: Send an e-mail notification to Admins, immediately after an issue reported on their Category (See [3.2]).[5.2.2]
3. On change category to user: Send an e-mail notification to Citizen when his issue has changed category.[5.2.3]
4. On change category to admins: Send an e-mail notification to Admins, immediately after an issue has moved to their category (See [3.2]).[5.2.4]
5. On change status to user: Send an e-mail notification to Citizen when his issue has changed status.[5.2.5]
6. On change status to admins:  Send an e-mail notification to Admins (See [3.2]), immediately after an issue has changed status.[5.2.6]
7. On new comment to admins: Send an e-mail notification to Admin immediately after a comment is published.[5.2.7]

* **[4.3] General Settings**

In this section the administrator can set whether new issues will be published immediately, or will be moderated at first.

1. Moderate new Issues: Set if you want to moderate new issues. This means that when a citizen reports an issue, it is saved as a draft, so an administrator can check the issues information first and then publish it. [5.3.1]

= [5] Roles and Capabilities  =

* **[5.1] Roles of Participace na projektech**

A Role defines a set of tasks that an assigned user can perform. Participace na projektech plugin uses three different roles for giving the ability to control what users can and cannot do within the platform.

1. **Administrator**: (WordPress default role) A user that has access to everything in your site and the entirety of the Participace na projektech plugin.
2. **Department Admin**: (Custom role) A user that has access to the entirety of the Participace na projektech plugin but not on the rest of the website.
3. **Subscriber**: (WordPress default role) This role is the default for citizens. It allows reporting, editing and browsing issues on the front end, while it denies access to the administration platform.

* **[5.2] Add New User as Department Admin**

You can create a new Department Admin user by visiting Users > Add New and selecting "Department Admin" as a Role option.

= [6] Important Notes  =

* **[6.1] Allow citizens to report issues**

In order to allow citizens to report issues, you need to enable registration to your website.
1. Visit Settings > General
2. Check Anyone can register at Membership option
3. Select Subscriber at New User Default Role option
4. Provide Login & Register Links at your site