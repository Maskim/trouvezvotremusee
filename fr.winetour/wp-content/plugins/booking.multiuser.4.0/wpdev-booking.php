<?php
/*
Plugin Name: Booking Calendar
Plugin URI: http://wpbookingcalendar.com/demo/
Description: Online reservation and availability checking service for your site.
Version: 9.MultiUser.SingleSite.4.0
Author: wpdevelop
Author URI: http://wpbookingcalendar.com/
Tested WordPress Versions: 2.8.3 - 3.3.2
*/

/*  Copyright 2009 - 2012  www.wpbookingcalendar.com  (email: info@wpbookingcalendar.com),

    www.wpdevelop.com - custom wp-plugins development & WordPress solutions.

    This file (and only this file wpdev-booking.php) is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// <editor-fold defaultstate="collapsed" desc=" T O D O : & Changelog lists ">
/*
-------------------------------------------------------
A c t u a l   T O D O   List:
-------------------------------------------------------
 * Booking   L i s t i n g     page TODO:
 ******************************************************
 * For next update: 
 * 12.!! Reupdate the CAPTCHA scripts
 * 6.!!! Authorize.net inetgration
 * 7.!!! Select more then 1 startday for a selection of days
 * 8.!!! Support timeslots dependence from the season filters.
 * 10. specify that during certain seasons, a minimum of 7 days must be booked but in other quieter seasons, minimum booking could be 3 or 5 days.
 * 9.!!! Posibility to make by visitors several reservations per date(s) in calendar, if these resrvation are not approved yet by admin and they in pending list. If some reservation is approved so then date(s) for this resrvation  become unavailbale for other visitors and all other resrvation(s) (if they exist) per these date(s) is canceled. Posibility to not show pending days as booked at the client side calendar. Its will allow for visitors to make many resrvations, untill administrator will not select and approve one resrvation. other resrvations, Administrator need to decline Or activate auto cancellation of such bookings.
 *  
 *******************************************************************************
 * New ONLINE interface for the Booking > Settings > Fields page
 * New activation/registration logic
 * 
 * Recheck correct working of selection custom form at "Add booking" menu in MultiUser version
 * qTranslate  plugin support
 * Check the fixed summ payment option, its seems that its work wrong, when using time selections or so...
 * use 2 different image background? One for the check-in date�and one for the check-out date.
 *    Combine the javascripts. Currently in hotel version, there are 9 javascript files under booking.hotel plugin loaded
 *    Include the minified version of the javascript (if done via wordpress plugin, it destroy the booking calendar functionality)
 *    Load the javascript in the footer if possible.
 *    Possibility to load / parse these javascript files of plugin at foorter for faster loading of page.
 * 
-------------------------------------------------------
M i n o r   T O D O   List:
-------------------------------------------------------
 *   Posibility to add at the booking bookmark how many days to select in range there.
 *   Different rental lengths for different types of items. For example, Room 1 is only available to rent 1 day at a time and Room 2 is available to rent for either 1, 3 or 7 days.
 *   if the user selects a Friday, Saturday or Sunday, it must be part of at least two days, but they can book single days on Monday, Tuesday, Wednesday and Thursday
 *   Dependence of maximum selection of days from start selection of week day. Its mean that user can select some first day and depends from the the day of week, which was selected, he will be able to select some specific (maximum number of days).
 *  Add labels of payment -  it would be much better if the booking manager displayed who had paid and who hadn't right now every order goes through with no way of identifying this
 * (Maybe at new CRM plugin) Google Calendar integration: it would be nice, someone books inserted into one of Google calendars of administraor, which is used to register all books. This way, it gets synchronized to desktop and iPhone as well
 * Send text messages after booking appointments, send Reminders via email and text messaging, SMS or text message option, to notify you immediately via your cell phone when you get a booking online
 *
 * Is there any way to only create a pending booking once the customer clicks the Paypal button? The problem is having two buttons to click one for the booking and then another to click through to payment.
 *
 * Field type be created that only accepts numbers (auto-validates as number only) or have REGEX defined by user.
 * Add description inside of input text fields inside of fields as grey text ???
 * set required field phone for free version ???
 * Season filtering by weeks numbers
 *
 * Add checking to the email sending according errors ( it can be when PHP Mail function is not active at the SERVER ) 
 * Set posibility to login with user permission at MultiUser version
 * Id like this [select visitors "1" "2" "3" "4"] to be dynamic. If there is 3 products free, I?d like to see [select visitors "1" "2" "3"] If there is 1 products free, I?d like to see [select visitors "1"] In the popup you can see price and product free so I imagine it?s possible to make selected visitors dynamic
 * Days cost view inside of calendar, unders the days numbers
 * Booking shopping cart. Posibility for user select booking days from diferent booking resources at the site, all these days will be added to the new booking caledar shopping cart and then user can finilize booking and make payment if its needed.
 *
 * 1) 6 possible half day users paying 'X' amount 2) There can be passengers (at a diffierent cost) but they will minus (take a position of) one of the 6 half day users/spots  3) People can also book full days but this will be at a different cost, it will then deduct from two of the 6 users/spots of the half day (the 2 bring one spot frmo the morning and one from the afternoon availability). e.g If someone books for the full day then on that day a morning and afternoon half day spots will be booked/unavailable.
 * Automated email to go out to people a couple of days prior to their reservation yet
 *  Imagine you have several hundred resources. It would be convenient to filter those resources by a simple category. An example would be restaurants and you could filter them by the food type (American, French, Chinese, etc) instead of just having a huge select menu.
 * allow the user to put the 'filters' in order of importance - so you can still have a default standard price for '7 days together' and then a further filter for '7 days in December', which is prioritised.
 * I'm using the form in two steps, also with short codes [visitorbookingediturl] and [visitorbookingcancelurl]. I note that there is no clear difference for the visitor between an action and another, because the only difference is at the end of the second step when he says "Cancel Reservation" Could be an explanation at the beginning where clearly distinguish between the two actions?
 * however it gets confusing for the hotel staff in the booking tables. Is it possible for you to make it display only one table for each group and have them ordered default by date? currently if there is a group of 2 or 5 or 7 or however many it shows each individual guest in the group as their own booking own booking table... this gets very difficult to sort through because the resort typically books larger groups
 * Restaurant tables resrvation - more easy interface
 * Check in/out text fields with popup clandars and not inline calendar
 * to have the opportunity to attach a .pdf document in the "Send payment request to customer" window. In that case we could attach the invoice directly to the email.
 * week view, day view type of claendar
 * pass the selected days on the booking search to booking form,  when you go to the form from the booking search, but not to the form directly
 * option to backup and restore the booking system
 * Fix posibility to make transfear of booking from one resource to other, if new booking resource (with cpapcity = 1 ) have already same booked dates but in different time.
 * Add button "Print" - print details from the booking and from the resource that's being booked and put them on a screen that could be printed.  Customization of print loyout. Exmaple: Thank you [firstname] [lastname] for booking of the [bookingresource].  Please make sure to arrive at least 30 minutes prior to your reservation time in order to guarentee...
 * When adding reservations through the admin panel, posibility to approve it by default instead of pending.
 * Waiting list functionality available where end user can still choose to sign up to be on a waiting list for a course, and if someone cancels, the first person on the waiting list will be notified by email. Upon notification they can choose to accept or reject. If reject then then an email will be sent to next person on the waiting list.
 * ^ Think about this feature: In combination with auto cancell posibility To deactivate the Pending option. If a customer books a particular date interval without paying, the booking request must be transfered for future payment, but must not block the calendar dates. Other visitors must be able to book these dates as long as the intial customer's payment has not come through. set pending days for selection by other visitors, untill admin will not approve them. * Allow pending bookings to be chosen with an explanation that they will be on a waiting list in the event that the original booker cancels.
 * Integeration Quantum Gateway payment system http://www.cdgcommerce.com/internet-services.php?R=2149
 * Recheck and fix working of "Valuation days" costs with "Recuerent time" active (without time shortcode).
 * display my booking form on external websites
 * the feature to select more then 1 startday for a selection of days
 * fix issue in IE7 of chnaging the booking resources in the selectbox at Booking admin page. problem in the IE7 is not recognize correctly the JavaScript event "onchange" at the selectbox,
 * fix issue in the IE7 during selecting (changing values) inside of the selectboxes or checkboxes inside of the booking form with  additional cost  setted at the advanced cost  managment section
 * include these custom fields in the search function of booking calendar
 * 1.  Show time available on the calendar sreen when hovering instead of showing time that is taken.
 * 2.  Way to schedule multiple times or days when reserving on the admin side.  It is an inconvienance to add 3 seperate bookings for us when scheduling an all day job.
 * 3.  Option for the customer to edit or cancel their booking, but only after admin approval of the edit or cancel, but not automatically like it does currently.
 * 4.  Conditional logic fields, so like when the customer clicks the range time on our site and chooses 9:00-11:30 that a time box would appear that wasn't there before and it would only show the times of 9:00, 9:15, 9:30 and so on.  Go to www.twodudeswc.com/schedule for example on our site.
 * 5.  When you go to the admin side of bookings page it shows a partially booked icon on all selected days.  Is there a way or a way in the works to make it show just like the add booking page so that I can look at it and know what is avaiable?
 * 6.  We would like the option to add a note box to our customers when approving the booking.  Like for example see you on the 26th, or we will leave your bill on the door for you.  Just like you can make a note on the admin side, but this time make it where the customer can see it when approving the booking.
 * 7.  We make Saturdays available now by appt on the phone.  The reason is we don't work every Saturday.  Have you thought about coming up with an option so that we could make certain Saturdays avaiable, but not every Saturday of the year show available like it does currently?  We don't know if we want to work a Saturday until a couple weeks before so the filter isn't a viable option for us.
 * 8.  Facebook scheduling option.  Have you thought about making it where your booking calendar could work with Facebook?
 * 9.  When clicking on a date on our scheduling page on our website it shows a time, but it goes way down or over to the left.  Is there a way or plans in a future update to resolve this and make it show up just underneath or over top the day selected.
 * At the "Cost for selected days" also with season filters, and some days are inside of different season filters, its will not work - need to fix.
 * save the IP address from people who booking
 *
 * 1. I would like to make the Reference ID a bit more 'in your face' by including it in the subject line, however the shortcode [id] does not work in the subject line. This would be 'nice' to have.
 * 2. If I want to nominate a payment system, it will ask for payment straight away when the booking is reserved by a potential visitor. However, I don't want them to pay until the reservation has been approved. It cant always be automatically approved as the property is also listed on other travel websites with their own booking systems and there could be the potential double booking (its a one resource only property). I would imagine this is a very common scenario.
 * If I uncheck the PayPal Active check-box, it doesn't send them to the payment page upon form submission. However, I'm not sure how it knows to use PayPal for the payment method, when I manually send them the payment link in the bookings page. However it does work as required. I feel this could be made clearer with a "don't send them to payment page upon from submission" check box or similar.
 * 3. I think for the added cost of the Premier version (which was obtained to get the online-payment options) it should also include the "Deposit Option", I believe a large percentage of bookings require this kind of payment option. Its another $200 option to have this feature which is a lot for a small B&B. This can be worked around by manually changing the Costs field on the list of bookings
 * 4. The Send Payment Request button - It would be nice if that could have a drop box/check box to send some pre-canned statements, instead of the need to type them out manually each time.
 * 5. API availability - It would be great to have some sort of API, so as to allow data-flow between booking system and other accountancy applications
 * It almost seems you could have another version - the features of Premium Plus but only one resource supported - Call it the B&B Version, or better yet drop the Premium Plus and roll those features into the Premium!
 * Ajax search for the customer fields, so when the admin types the name of the customer an ajax search would bring all possible customers and when one is selected all the customer information fields would be automatically filled up.
 * New booking form widget with possibility to select the resource at the top of widget
 * Fix issue of chnaging booking resource (with capcity more than 1 ) for the specific booking
 * New sheme for the downloading updates of new versions of Booking Calendar
 * Transform all to PHP5 OOP Platform.
 *
 *
 *

-------------------------------------------------------
 M a j o r   T O D O   List:
-------------------------------------------------------
 * New calendar scripts....
 
 * Popup shortcode form:
 * - Showing 2 or more calendars with one form
 * - Booking search results
 * Authorize . net support
 * Google checkout support
 * 2co payment system 
 * WordPay
 * eway  payment system support
 * eWay gateway
 * Check posibility to include this payment system : https://www.nmi.com/ (its for Panama)
 *
 * Set different times on different days, yet still have them show up on the same calendar
 *  Instead of using a select menu, i'd like to use a list of radio buttons so the user can see all available and unavailable times for that day.
 *
 * Add posibility to send link in "auto" email for payment to visitors, after visitor make booking. (Business Small and higher versions)
 * Fix issue with not correct cost at the Paypal button, when advanced cost is not set for custom field, but its setuped for normal fields.
 *
 * Add Lables with Show max visitor in name of room (Business Large, MultiUser)
 * print/email list for i.e cleaning personnel, kitchen personnel etc would be really good to have, almost every resource that can be booked needs some kind of involvement of different persons, so an option to create print/email lists of custom chosen fields from the booking form would add value and make it easier to inform the right people on what�s going on in a given time-range.
 * calendar is booked during a give period I would like to be able have an email or SMS sent out to people when I have an opening.
 * check-in and check-out days visible like in the other booking systems (mark half of day)
 * 
 * posibility to make several bookings for several time slots with capacity > 1
 *
 * Linear Calendar View:
 * -        Month                 June                                           July                                                                                      August
 * -        Ressource         01 02 03 03 05 06 07 08 09 10 11
 * -        Ressource 1       0   0    1   1   1   1    1   1    1    w w
 * -        Ressource 2       0   0    1   1   3   1    5   1    1    w w
 *
 * Make categories for resources? I have like 70 resources, and it would be great if i could split the choices on different pages or categories.
 * Show apprtment name nearly each day, if these days in diferent rooms (Business Large, MultiUser)
 * My Account & Added Extras ? We are investigating ways of people (visitors) logging in to see past orders and potentially adding items to the order, like for example booking a spa treatment, or an upgraded food package.
 * Add posibility to select booking resource from select box as a feature of booking calendar - no anymore JS tricks
 * Search results at new seperate page, diferent from search form
 * At the page you are use 2 booking calendars and on booking form. And there cabin#1 is a Main booking resource (required), so if you do not select days at this calendar, such message will show up.
 * Possible for the booking form that is emailed to me when a booking is placed to come from the sender like in contact form 7? I am having a problem with users replying to our admin email!
 * [Jingye Luo]: Enable/Disable Reseting the time of waiting for automatic cancell of booking, if visitor click at the payment link, even if he is do not made successfull payment after this. Its will give additional time for finish of payment, if visitor by some reason is not made it.
 * [Jingye Luo]: Logic for automatically send payment request "if a pending booking has been over x time"
 * [Jocelyn]: How most of these sites handle the availability calendars is to leave the check out date available by default, so each reservation only blocks the calendar up to the day before checkout.
 * [Jocelyn]:  partial booking with the clock is confusing for the user and visually confusing whether or not they can book arrive or depart that day at least for a hotel/rental application. If this functionality exists and I'm unaware or if it might something that could come in other updates I'd be very interested to know.
 * [Jocelyn]:  to have only an availability calendar and just simple date fields with pop up date selectors, some users were unclear they had to select the date from the calendar and others selected the wrong date and were unable unselect it.
 * [Jocelyn]:  When I use the reservation form and a reservation is successfully entered, a message flashes that someone will contact me, etc., but the issue is the calender shows the beginning date with a partial select clock as it should but the second day of my reservation also shows this same icon.
 * [Steve Horler]: Set start day(s) of selection only for specific week days. For exmaple visitor can start selection only from Sat, Mon and Fri
 * [Steve Horler]: Set dependence (posibility) number of selected days from start day of selection. For exmaple: visitor can select only 3 days starting at Fri and Sat, 4 days - Fir, 5 days - Monday, 7 days - Sat
 * [Ben Burnett ]  Currently we have our system set-up so that our clients have to both select the date in the calendar, and also type in the date manually.  I would like it that this text field is powered by the calendar, so that when someone clicks on a start date, that the result is that this start date is written in text form.
 * [FRED]: I have set [start times] and [duration], but when selecting 11pm and going beyond the midnight mark by selecting duration: 2:00, the price turns negative! (it looks like the system tries to book the time from 11pm to 1am the SAME DAY.  By manually selecting both days (the "start day" and the "end day"), the cost is calculated correctly, but now the 'end day' becomes completely unavailable for further booking. This is a big problem and renders the plug-in useless for our purpose. Please advise on how to fix this. Also, I have set my unavailable time as 1:00 - 14:00 with a filter applied to the availability, but it still allows me to book durations that go beyond 1am. Start time: [select starttime "14:00" "15:00" "16:00" "17:00" "18:00" "19:00" "20:00" "21:00" "22:00" "23:00" "0:00"]  Set duration: [select durationtime "1:00" "2:00" "3:00" "4:00" "5:00"]< /p >
 * ! [Steve Wheeler] More complex discount system, which are depends from season and from number of selected days togather. Posiblity to assign seson to each of already setted cost, which depends from number of selected days for reservation
 * [Stefan Rest] able to print out all the rooms, the guest names and the notes on a print out everyday so we know who is checking in and the payments they made
 * Set Approve / Cancellation links inside of email for the administrator
 * ---------------------------------------------------------------------------------------------------------------------------------------
 * Help description according translations at the: Form fields, Search form, Email templates, Thank you message, Cost description, Availability.
 * some graphical show of the activity- stat of the booking? (how many room booked in month , money receive ets? )
 * jQuery 1.6.0 and newer introduces another method: .prop() that replaces many .attr() calls. This was (partially) reverted in jQuery 1.6.1 but some uses of .attr() are not working any more. For example .attr('checked', '') doesn't uncheck checkboxes any more.  Best would be to replace all getting/setting of 'checked', 'selected' and 'disabled' from .attr() to .prop() (using .prop() is also much faster). More information on the jQuery blog: http://blog.jquery.com/2011/05/12/jquery-1-6-1-released/
 * Calculation of additional cost in advanced cost managment section - "N/day", which depend from the number of selected days in specific additionl booking calendar(if used more than one calendar in form customization) (Business Medium, Business Large).
 
 * Show search results of availbale dates in the other page then the booking search form.
 * Add the showing combination of time slots for specific days, which is depend from the season filters. Each day can have different time slots list.
 * need into the search, display a combobox with categories, now when I write [search_category]only shows a textbox,
 * Selection of time slots, which is depend from the days week.
 * If someone searched with a requirement of 6 then no rooms would show although we'd liker the search to return any number of room that could accommodate 6 . i.e. return 2 rooms in the search results.
 * Fix issue with search results showing only in English will be fixed in future updates, right now its exist because all search request is Ajax requests and there is default English language.
 * set the different time selections in the booking form, which will depend from  the selected day of week
 * 1. Merge �Filter� and �Cost and Availability� tabs in one. This would simplify tasks. Also make the main view in this new tab like the one that appears when you click on the button �availability� or �rates� for a resource.
 * 2. In this new tab place a checkmark to the left so simplify the task of BLOCKING or ASSIGNING a rate to MULTIPLE RESOURCES AT ONCE!!!!!!! The fact is that blocking dates for multiple (more than 50) rooms can take FOREVER making one by one!!! Please please please this one is not that complex to implement. Please. really please.
 * 3. a Promotional Rate 1 Free Night. This is very very common. DO NOT tell me to use coupons, I know there are plenty of workarounds but thats the point: the software should comply our needs not backwards, please!
 *  This is what I want: a 7 day booking range. But there should be possible to make exception from this on certain dates (chrismas, easter etc). I�ve managed to set the range, but how can I set exceptions on certain dates?
 * Is there a way to NOT make pending reservation dates unavailable in the calender? Want the pending dates to be available and same style as other dates.
 *          Monday � Thursday, the room is available from 7 am to 10 pm
 *          Friday and Saturday, the room is available from 7am to 1 am (1 am on Sunday)
 *          Sunday, the room is available from 9 am to 9 pm
 * Currency formatting.
 * post  calendar widget to other Wordpress sites, using the booking data from the original



-----------------------------------------------
Change Log and Features for Future Releases :
-----------------------------------------------
= 4.0 =
* Personal / Business Small / Business Medium / Business Large / MultiUser versions features:
 * New version names and functionality of Booking Calendar WordPress plugin: Free, Personal, Business Small, Business Medium, Business Large, MultiUser versions.
 *   (Personal, Business Small, Business Medium, Business Large, MultiUser)
 * New interface with reorganized menu itmes at Settings and Resource menu pages. More logical and intuitive settings. (Personal, Business Small, Business Medium, Business Large, MultiUser)
 * * New sub menu pages in Resources menu page: Cost and Rates, Advanced cost, Coupons, Availability, Season filters
 * * New cost sections at the general booking settings page.
 * * New auto cancellation pending not payed bookings sections at the general booking settings page.
 * * New tab selections for showing specific sections in Payment and Emails setttings page.
 * * Posibility to set/check active status of Payment systems and Emails templates at the top settings submenu
 * * Removed sub menu pages from Settings menu: Cost and availbaility, Season filters
 * * Removed sections: from settings Payment page - Cost of resources, Advanced cost managment, Auto cancel pending not payed bookings.
 * * New "Add resource" style section.
 * Pagination of the resource pages.
 * Ajax changing resource for specific booking, changing the cost and notes
 * Change the Payment status of booking
 * Showing the labels for the each booking, like Approved/Pending, payment status, resource...
 * Export bookings to CSV (Personal, Business Small, Business Medium, Business Large, MultiUser)
 * Print functionality (Personal, Business Small, Business Medium, Business Large, MultiUser)
 * Custom booking form selection at the Admin menu: "Add booking" (Business Medium, Business Large)
 * Integration Instant Payment Notification (IPN) is a message service that notifies you of events related to PayPal transactions (Business Small, Business Medium, Business Large, MultiUser)
 * Set costs of booking resources at the Resources page, and removed it from the Booking > Settings > Payment page. (Business Medium, Business Large, MultiUser)
 * Set costs of booking resources at the Resources page, and removed it from the Booking > Settings > Payment page. (Business Medium, Business Large, MultiUser)
 * Activation of the Auto approve functionality. (Business Small, Business Medium, Business Large)
 * Posibility to apply the 100% discount, using the coupon codes (Business Large)
 * Disbaled the submit button, when user is activated the range days selection using 2 mouse clicks, and click only once at the calendar.
 * Search of availability based on the "season filters" functionality in additional to the "booked dates" (Business Large, MultiUser)
 * Possibility to search in the custom posts and pages (Business Large, MultiUser)
 * Search form is mark the searched dates as selected at the page with booking calendar (Business Large, MultiUser)
 * Add possibility in Paypal form integration to indicates whether the transaction is payment on a final sale or an authorization for a final sale, to be captured later. (Business Small, Business Medium, Business Large, MultiUser)
 * Fix integration of Paypal Pro Hosted Solution.(Business Small, Business Medium, Business Large, MultiUser)
 * Fix issue of showing cost or availbaility tooltip in wrong place, when mouse over specific date in calendar at some themes. (Business Small, Business Medium, Business Large, MultiUser)
 * Fix. Posibility to save 0 cost for booking resource. (Business Small, Business Medium, Business Large, MultiUser)
 * Fix issue of showing no results during availability searching, when wordpress system is use some third party plugins for translation of blog. (Business Large, MultiUser)
 * Fix issue of editing exist resrvation at admin panel, when was booked several days, but after saving the edited resrvation (edited was only fields) is saved only first booking day. Right now its fixed.
 * Fix issue of cost calculation for "Valuation days" settings, when "recurent time" feature is active and do not using any time.
 * Fix issue with posibility to select the days "using range days selection with 1 mouse click", when inside of this range of days are exist some unavailbale day(s).
 * Fix issue of adding the booking to not correct booking resource at the "Add booking" admin page, when no selection of booking resource was made and first booking resource was deleted.
 * Fix showing warning message, if the "Disable reservations in different booking resources" option is activated. (Business Large)
 * Fix not showing the booking resource at the "short dates", if some last dates is belong to different booking resource. (Business Large)
 * Fix correct updating booking form, when admin chnage the booking resource for the booking.
 * Fix max. days selection, if activated the range days selection, using 2 mouse clicks and discreet number of days settings.
 * Fix coupon creation issue of not showing coupons, when some coupon not have selected "All" resources.
 * Fix Error during updating remarks in BD [Query was empty].. It happens when a '%' sign in the note.
* Features and issue fixings in All versions:
 * New clean stylish interface of Booking Listing menu page
 * New booking toolbar, with tabs: Filter, Actions, Help
 * Performance improvement at the booking admin panel for the large amount of data.
 * New filter for the showing bookings, based on number of parameters, like: Dates o booking, creation date of booking, status of bookings, keywords ( resources, cost and status of payment for the paid versions )
 * Search booking by ID
 * Actions on a single booking
 * Posibility to set Approved booking as Pending
 * Ajax approving/unapproving, deleting of bookings
 * Pagination of the booking listing.
 * Showing the date/time of booking creation
 * New tooltip system
 * German translation.
 * Fix issue of minimum days selection in range days selection, when visitor is click only once at days selection and minimum days selection is more then 1 day
 * Fix issue of not showing the "Booking Calendar" button at the edit toolbar, when toolbar in HTML mode at WOrdPress 3.3 and higher.
 * Fix issue of not posibility to save the "links" at the footer inside of the booking widget for sidebar.
 * Fix issue "... is not a legal ECMA-262 octal constant ...", which is appear at some sites, because not correct transfrom of this varable:  var wpdev_bk_today = new Array( ....
 * Removed jWPDev variable and returned to jQuery varible. The construction "jWPDev = jQuery.noConflict()" in some themes previously can generate JS errors.

*/
// </editor-fold>


    // Die if direct access to file
    if (   (! isset( $_GET['wpdev_bkpaypal_ipn'] ) ) &&    (! isset( $_GET['merchant_return_link'] ) ) && (! isset( $_GET['payed_booking'] ) ) && (!function_exists ('get_option')  )  && (! isset( $_POST['ajax_action'] ) ) ) {
        die('You do not have permission to direct access to this file !!!');
    }

    // A J A X /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if ( ( isset( $_GET['wpdev_bkpaypal_ipn'] ) )  || ( isset( $_GET['payed_booking'] ) )  || (  isset( $_GET['merchant_return_link']))  || ( isset( $_POST['ajax_action'] ) ) ) {
        require_once( dirname(__FILE__) . '/../../../wp-load.php' );
        @header('Content-Type: text/html; charset=' . get_option('blog_charset'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   D e f i n e     S T A T I C              //////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (!defined('WP_BK_DEBUG_MODE'))    define('WP_BK_DEBUG_MODE',  false );
    if (!defined('WPDEV_BK_FILE'))       define('WPDEV_BK_FILE',  __FILE__ );

    if (!defined('WP_CONTENT_DIR'))      define('WP_CONTENT_DIR', ABSPATH . 'wp-content');                   // Z:\home\test.wpdevelop.com\www/wp-content
    if (!defined('WP_CONTENT_URL'))      define('WP_CONTENT_URL', site_url() . '/wp-content');    // http://test.wpdevelop.com/wp-content
    if (!defined('WP_PLUGIN_DIR'))       define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');               // Z:\home\test.wpdevelop.com\www/wp-content/plugins
    if (!defined('WP_PLUGIN_URL'))       define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');               // http://test.wpdevelop.com/wp-content/plugins
    if (!defined('WPDEV_BK_PLUGIN_FILENAME'))  define('WPDEV_BK_PLUGIN_FILENAME',  basename( __FILE__ ) );              // menu-compouser.php
    if (!defined('WPDEV_BK_PLUGIN_DIRNAME'))   define('WPDEV_BK_PLUGIN_DIRNAME',  plugin_basename(dirname(__FILE__)) ); // menu-compouser
    if (!defined('WPDEV_BK_PLUGIN_DIR')) define('WPDEV_BK_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.WPDEV_BK_PLUGIN_DIRNAME ); // Z:\home\test.wpdevelop.com\www/wp-content/plugins/menu-compouser
    if (!defined('WPDEV_BK_PLUGIN_URL')) define('WPDEV_BK_PLUGIN_URL', WP_PLUGIN_URL.'/'.WPDEV_BK_PLUGIN_DIRNAME ); // http://test.wpdevelop.com/wp-content/plugins/menu-compouser


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   L O A D   F I L E S                      //////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-functions.php')) {     // S u p p o r t    f u n c t i o n s
        require_once(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-functions.php' ); }
    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/winetourbooking-functions.php')) {     // S u p p o r t    f u n c t i o n s
        require_once(WPDEV_BK_PLUGIN_DIR. '/lib/winetourbooking-functions.php' ); }
    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-widget.php')) {        // W i d g e t s
        require_once(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-widget.php' ); }

    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/js/captcha/captcha.php'))  {             // C A P T C H A
        require_once(WPDEV_BK_PLUGIN_DIR. '/js/captcha/captcha.php' );}

    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/inc/personal.php'))   {                  // O t h e r
        require_once(WPDEV_BK_PLUGIN_DIR. '/inc/personal.php' ); }
    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-bk-lib.php')) {                // S u p p o r t    l i b
        require_once(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-bk-lib.php' ); }

    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-class.php'))           // C L A S S    B o o k i n g
        { require_once(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-class.php' ); }

    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/panier.php'))           //Panier
        { require_once(WPDEV_BK_PLUGIN_DIR. '/lib/panier.php' ); }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // GET VERSION NUMBER
    $plugin_data = get_file_data_wpdev(  __FILE__ , array( 'Name' => 'Plugin Name', 'PluginURI' => 'Plugin URI', 'Version' => 'Version', 'Description' => 'Description', 'Author' => 'Author', 'AuthorURI' => 'Author URI', 'TextDomain' => 'Text Domain', 'DomainPath' => 'Domain Path' ) , 'plugin' );
    if (!defined('WPDEV_BK_VERSION'))    define('WPDEV_BK_VERSION',   $plugin_data['Version'] );                             // 0.1
            

    //    A J A X     R e s p o n d e r     // RUN if Ajax //
    if (file_exists(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-ajax.php'))  { require_once(WPDEV_BK_PLUGIN_DIR. '/lib/wpdev-booking-ajax.php' ); }

    // RUN //
    $wpdev_bk = new wpdev_booking(); 
?>