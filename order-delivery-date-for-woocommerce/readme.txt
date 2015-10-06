=== Order Delivery Date for Woocommerce ===
Contributors: ashokrane, MoxaJogani, bhavik.kiri, mansishah, komal-maru, dharakothari
Tags: delivery date, checkout, order delivery, calendar, checkout calendar, woocommerce delivery date
Requires at least: 1.4
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: http://www.tychesoftwares.com/

Allow the customers to choose an order delivery date on the checkout page for Woocommerce store owners.

== Description ==

This plugin will allow the customer to choose an order delivery date on the checkout page. The customer can choose any delivery date that is after the current date. The plugin uses the inbuilt datepicker that comes with WordPress.

The plugin allows the site administrator to select delivery weekdays, specify minimum delivery time and display number of dates on calendar. The delivery date also shows in a column on Woocommerce > Orders page.

The 'Mandatory field?' setting will allow the Delivery Date field to be set as mandatory on the checkout page.

The delivery date chosen by the customer will be visible to the site administrator while viewing the order under the "Custom Fields" section.

This plugin allows you to improve your customer service by delivering the order on the customer's specified date.

**Pro Version:**

**[Order Delivery Date Pro 2.8.5](https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21 "Order Delivery Date Pro")** - The Pro version allows the customer to choose a delivery date & time on the checkout page. Date Settings, Time Settings, Appearance & Black-out dates allow the site owner to decide which dates should be made available for delivery. Following features are available in PRO version:

<ol>
<li>Ability to allow the customer to select <strong>Delivery Time along with Delivery Date</strong></li>
<li><strong>Same-day & Next-day delivery</strong> with cut-off time</li>
<li>Choose from <strong>24 different themes for the calendar</strong></li>
<li>Specify the time range available for delivery / pick up</li>
<li><strong>Add holidays or black-out dates</strong> to the calendar</li>
<li>Option to <strong>show Delivery Date in Customer Notification Email</strong></li>
<li>Show 2 months in calendar</li>
<li>Choose the convenient date format</li>
<li><strong>Customize field label, field note</strong> text</li>
<li>Capture only delivery date or only delivery time or both</li>
 </ol>

**[View Demo](https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21 "View Demo")**

== Installation ==

1. Ensure you have latest version of Woocommerce plugin installed
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

= 1.7 =

* A new setting is being added named as 'Lockout date after X orders' which allows to block the dates for further deliveries after X number of orders.
* The plugin is now compatible with 3rd party plugins like:
	- WooCommerce Zapier Integration.
	- WooCommerce Print Invoice & Delivery Note
	- WooCommerce PDF Invoices & Packing Slips
	- WooCommerce Customer/Order CSV Export
	- WooCommerce Subscriptions
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