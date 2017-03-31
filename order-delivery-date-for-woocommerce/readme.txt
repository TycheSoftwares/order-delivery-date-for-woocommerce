=== Order Delivery Date for WooCommerce ===
Contributors: ashokrane, MoxaJogani, bhavik.kiri, komal-maru
Tags: delivery date, checkout, order delivery, calendar, checkout calendar, woocommerce delivery date, delivery scheduling, delivery time, delivery calendar
Requires at least: 1.4
Tested up to: 4.7.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.me/TycheSoftwares

Allow the customers to choose an order delivery date on the checkout page for WooCommerce store owners.

== Description ==

This plugin will allow the customer to choose an order delivery date on the checkout page. The customer can choose any delivery date that is after the current date. The plugin uses the inbuilt datepicker that comes with WordPress.

The plugin allows the site administrator to select delivery weekdays, specify minimum delivery time and display number of dates on calendar. The delivery date also shows in a column on WooCommerce -> Orders page.

The 'Mandatory field?' setting will allow the Delivery Date field to be set as mandatory on the checkout page.

The delivery date chosen by the customer will be visible to the site administrator while viewing the order under the "Custom Fields" section.

This plugin allows you to improve your customer service by delivering the order on the customer's specified date.

**Pro Version:**

**[Order Delivery Date Pro 6.5.1](https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21 "Order Delivery Date Pro")** - The Pro version allows the customer to choose a delivery date & time on the checkout page. Date Settings, Time Settings, Appearance & Black-out dates allow the site owner to decide which dates should be made available for delivery. Following features are available in PRO version:

<ol>
<li>Ability to allow the customer to select <strong>Delivery Time along with Delivery Date</strong></li>
<li><strong>Same-day & Next-day delivery</strong> with cut-off time</li>
<li>Two-way deliveries sync with Google Calendar</li>
<li>Create Delivery Settings by Shipping Zones & Shipping Classes</li>
<li>Set Shipping Days & Delivery Days separately</li>
<li>Disable Delivery Date for select Categories</li>
<li>Customer & Admin can edit Delivery Date & Time for orders</li>
<li>Capture Delivery Time by Time Slot or Specific Time of Delivery</li>
<li>Separate Delivery Charges, Cut-off, etc. for Weekdays</li>
<li>View upcoming Deliveries in Delivery Calendar</li>
<li>Create Delivery Settings by Product Categories</li>
<li>Set Maximum Deliveries per day by Orders or Product Quantity</li>
<li>Filter your WooCommerce Orders by Delivery Dates</li>
<li>Specify minimum delivery interval between the order and first available delivery</li>
<li>Set Specific Delivery Dates for delivery instead of Weekdays</li>
<li>Block a time slot for a date or weekday</li>
<li>Auto-updates delivery dates for renewal orders from WooCommerce Subscriptions Plugin by Prospress Inc</li>
<li>Create Delivery Settings from USPS Shipping Method Plugin by WooCommerce</li>
<li>Create Delivery Settings from Table Rate Shipping Plugin by WooCommerce</li>
<li>Different Delivery dates when using WooCommerce Shipping Multiple addresses</li>
 </ol>

**[View Demo](https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21 "View Demo")**

**Some of our other free plugins:**

1. **[Abandoned Cart Lite for WooCommerce](https://wordpress.org/plugins/woocommerce-abandoned-cart/ "Abandoned Cart Lite for WooCommerce")**

2. **[Product Delivery Date for WooCommerce - Lite](https://wordpress.org/plugins/product-delivery-date-for-woocommerce-lite/ "Product Delivery Date for WooCommerce")**


**Some of our Pro plugins:**

1. **[Booking & Appointment Plugin for WooCommerce 3.5.1](https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/ "Booking & Appointment Plugin for WooCommerce")**

2. **[Order Delivery Date Pro for WooCommerce 6.4.3](https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/ "Order Delivery Date Pro for WooCommerce")**

3. **[Product Delivery Date Pro for WooCommerce 2.0.1](https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/ "Product Delivery Date Pro for WooCommerce")**

**[Github Repository](https://github.com/TycheSoftwares/woocommerce-delivery-date "Github Repository")**

== Installation ==

1. Ensure you have latest version of WooCommerce plugin installed
2. Unzip and upload contents of the plugin to your /wp-content/plugins/ directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Order Delivery Date calendar will appear on the checkout page of your store.


== Frequently Asked Questions ==

= Can the customer enter the preferred order delivery time? =

Currently there is no provision for entering the delivery time in the free version. This is possible in the Pro version. [View Demo](https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21 "View Demo")

= Is the order delivery date field mandatory on checkout page? =

The field can be configured as Mandatory or optional using the 'Mandatory field?' setting.

== Screenshots ==

1. The Delivery date field will be visible on checkout page, according to the settings.

2. The selected delivery date will be shown in "Custom Fields" in Sales Log.

3. Delivery Date will be displayed on the Orders page in a new column titled "Delivery Date".

== Changelog ==

= 2.9 =

* Fix - Warnings were displayed on the Order Received page with WooCommerce version 3.0.0. This is fixed now.

= 2.8 =

* Feature - From now you can set holidays on the dates you do not deliver products under Holidays tab. Selected holidays will be disabled for delivery in the delivery calendar on the checkout page.
* Feature - A new checkbox named "Auto-populate first available Delivery date" is added in Date Settings tab. If this checkbox is enabled then the first available Delivery Date will be Auto-populated in the Delivery Date field on the Checkout Page.
* Feature - A new checkbox named "Apply Minimum Delivery Time for non-working weekdays" is added under Date Settings tab. If this checkbox is checked then the Minimum Delivery Time (in hours) will be calculated on the non-working days which are unchecked under Delivery Days. If the checkbox is unchecked then the minimum delivery time will be calculated on the working days. The default value of the checkbox is checked when the plugin is updated.

= 2.7 =

* The delivery date selected on the checkout page can now be cleared without refreshing the checkout page.
* The calendar was not working on the checkout page when Field note text has line breaks. There was an error in the console as "Unexpected token". This is fixed now.
* If the first available delivery date is disabled for delivery then number of days to choose is not working properly in the calendar. This is fixed now.

= 2.6 =

* From now on the Delivery Date can be enabled or disabled for capturing the delivery date. A checkbox named as 'Enable Delivery Date' is added in the Date Settings tab.

The default value of this setting is disabled, which when enabled will allow clients to capture the Delivery Date for an order on the checkout page.
* The Delivery Date field can also be disabled for Virtual and Featured products on the checkout page.

A setting named 'Disable the Delivery Date Field for' is added in the Appearance tab. The default value for both the checkboxes is disabled. When a checkbox named 'Virtual Products' is enabled then the delivery date field will be disabled for only virtual products.

When a checkbox 'Featured Products' is enabled then the delivery date field will be disabled for only featured products.
* A notice will be displayed when the Order Delivery Date for WooCommerce plugin is activated but the WooCommerce plugin is deactivated or it is not installed. The plugin will be deactivated automatically.
* The width of the Delivery Date field will be the same as that of the other fields on the checkout page.
* Calendar was not working on the checkout page when Impreza theme was active.
* The cursor for the Delivery Date field will be text instead of a pointer.

= 2.5 =

* The Delivery dates will now be freed up when an order is cancelled, refunded, failed or trashed.
* Calendar was not showing up on iPhone 6+ when the Delivery Date field was selected. Instead, it would only show when the delivery date field label was clicked. This is fixed now.

= 2.4 =
* A new language "Hebrew" is added for the calendar. Now you can set your Delivery Calendar in the Hebrew language on the Checkout Page.
* 'Delivery date in the Shipping Section' setting in the Appearance tab is now renamed to 'Field placement on the Checkout page'. It will allow the Delivery Date field to be displayed in Billing Section, Shipping Section, before Order notes or after Order notes on the checkout page.
* The plugin is now using the jquery libraries available in WordPress core instead of the googleapis.com.
* The notice "Minimum Delivery time (in days) will now be calculated in hours which is from current WordPress time. To keep the functionality of our plugin intact at your site, we have added +24 hours to the 'Minimum Delivery time (in hours)' setting." is made dismissible now.
* A warning was displayed in the admin and on the front-end pages when the both Order Delivery Date Pro and Order Delivery Date Lite is activated together.
* The update notice 'Order Delivery Date for WooCommerce Plugin needs to update your database' appears even when the plugin is re-installed. The notice should not come once the database is already updated. This is fixed now.

= 2.3 =
* Calendar next and previous arrows were not displayed correctly with the Galleria theme on the checkout page. This is fixed now.
* Delivery Date was not displayed on the invoice and packing list due to a hook being deprecated from WooCommerce Print Invoices/Packing Lists plugin. This issue is fixed now.
* A deprecated hook was used to add the Delivery Date value in customer notification email. This is fixed now.
* Calendar weekdays were overlapping in the calendar on the checkout page when the "Number of Months" was set to 2. This is fixed now.
* Delivery Dates were not translated on the Order Received page, My Account page and WooCommerce -> Orders page when date language is set to Dutch-Belgian. This is fixed now.

= 2.2 =
* The Delivery Date field label, field placeholder text and the field note text in Order Delivery Date -> Appearance can now be translated with WPML plugin.
* Until now, only admin user was able to access the Order Delivery Date settings page. From this update, users with the role 'Shop manager' will be able to access and edit the Order Delivery Date settings.
* The calendar was not appearing on the checkout page when clicked on the Delivery Date field on the checkout page. This issue is specific to the 'Stockholm' theme. This is fixed now.

= 2.1 =
* The jQuery Calendar on the checkout page will now appear with a Flat Design. The CSS file for the Flat Design is used from the WP Datepicker Styling plugin on Github: https://github.com/stuttter/wp-datepicker-styling
* The time sliders were not displayed on the admin product page for auction product type from the "WooCommerce Simple Auctions" plugin. This is fixed now.

= 2.0 =
* A checkbox is added on the Date settings tab to prevent the default sorting of the orders (in descending order) on the WooCommerce ->Orders page when the Delivery Date field is visible. 

= 1.9 =
* A new 'Appearance' tab is added on the settings page where admin will be able to
	- Change the label, placeholder text for the Delivery Date field on the checkout page.
	- To choose different language in the delivery calendar from 62 available languages. 
	- Date format for the Delivery Date can be changed.
	- To set the 1st day of the week on the delivery date calendar.
	- Field note text can be changed.
	- Number of months to be shown on the calendar can be selected to maximum 2.
	- The delivery date field can be set to shown in the Shipping section instead of always showing in the Billing section on the checkout page.
	- Choose different theme for the delivery calendar to match with the theme of the website.
* From this update, sorting of orders will be done based on Delivery Date on the WooCommerce-> Order page.
You will need to update the database for sorting the previous orders from the notice displayed after updating the plugin.

Note: Please take a back up before updating this version.

= 1.8 =
* The Minimum Delivery time (in days) feature will now be replaced with Minimum Delivery time (in hours) Feature. When the plugin will be updated, the existing value will be multiplied with 24 hours and more 24 hours will be added to it, which will keep your setting intact.
* Plugin will have some default settings when it is installed for the first time.
* Order Delivery Date for WooCommerce (Lite version) is now compatible with Order Delivery Date Pro for WooCommerce plugin. 

= 1.7.1 =
* The order was placed without selecting delivery date on the checkout page even if the Delivery Date field is mandatory. This is fixed now.

= 1.7 =

* A new setting is being added named as 'Lockout date after X orders' which allows to block the dates for further deliveries after X number of orders.
* The plugin is now compatible with 3rd party plugins like:
	- WooCommerce Print Invoice & Delivery Note
	- WooCommerce PDF Invoices & Packing Slips
	- WooCommerce Customer/Order CSV Export
	- WooCommerce Print Orders
	- WooCommerce Print Invoice/Packing list
* Delivery Date field on the checkout page has been made readonly preventing manual editing.

= 1.6 =
* The jQuery UI version has been updated to 1.10.4. The old version was throwing a Javascript error in some pages in the WordPress Admin.

= 1.5 =
* The plugin fields in admin have been restructured. We are now using the WordPress Settings API for all the plugin fields in admin.
* We have included .po, .pot and .mo files in the plugin. The plugin strings can now be translated to any language using these files.

= 1.4 =
* We have added a new setting 'Mandatory field?' in the admin dashboard, which will allow the Delivery Date field to be set as mandatory on the checkout page.

= 1.3 =
* The delivery date will be displayed on the My Account page's View Order page.
* The delivery date settings were getting reset for some customers, this has been fixed.
* The delivery date will be added to the email notification received by the customer on placing the order.
* The delivery date is attached to the customer invoice too.

= 1.2 =
* On deactivating the plugin, all the settings were getting reset. This has been fixed. Now on deactivating the plugin, the settings will stay intact.

= 1.1 =
* You can set which weekdays you want the delivery service to be available.
* You can set the Minimum delivery time (in Days). Enter the minimum number of days it takes for you to deliver an order.
* You can set the number of dates to be available for the customers to choose the delivery date.
* A column on the Orders page will be created where the delivery date will be displayed.

= 1.0 =
* Initial release.
