=== Contact Form By Mega Forms - Drag and Drop Form Builder ===
Contributors: alikhallad
Donate link: https://alikhallad.com/donations/donation-form/
Tags: drag and drop form builder, ajax forms, multi step ajax form, file upload forms, custom form
Requires at least: 5.6
Tested up to: 6.6
Stable tag: 1.5.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Contact form builder that allows you to create forms for any purpose. Drag & drop form fields to build modern, professional contact forms in minutes.

== Description ==

Mega Forms is highly advanced contact form builder for WordPress, it comes with all the contact form features you will ever need, including AJAX submission, multi-page contact forms, secure file uploads, conditional logic, save and continue, user creation, front end posting, and tons more. You can use Mega Forms to save time, grow customer interaction, and build better contact forms for any purpose.

[Mega Forms](https://wpmegaforms.com/?utm_source=wprepo&utm_medium=link&utm_campaign=wp-repo) gives you a modern interface, easy customization, and the ability to build modern & professional forms thanks to our intuitive drag & drop visual editor.

Now you can create better forms, embed them anywhere on your WordPress website, get email notification for each submission, perform custom tasks, and collect & manage data without being a coding ninja.

Mega Forms contact forms are also highly optimized for web and server performance. We know how important speed is when it comes to SEO and user experience, that's why we have built every piece of Mega Forms with performance and usability in mind. Mega Forms will load the least possible amount of CSS & JS assets, and only store necessary data to the database to keep your website fast and provide your users with better experience.

= No Coding Skills Required =

No technical skill? No problem. You can easily design simple and complex forms with our highly advanced visual builder. Mega Forms offers a flexible row/column layout system that requires very minimal effort to build forms that blends nicely with your website design.

= Developer Friendly =

Mega Forms has been built with developers in mind. This means it's flexible, easily extendable, and full of action and filter hooks, making it easy to customize to your own needs.


= Top Features =

Mega Forms comes with a visual editor and ton of other features:

* Intuitive user interface
* Drag & drop form builder
* Optimized for speed & performance
* Tons of free field types ( text, select, radio, checkboxes and more )
* Regular updates & dedicated support
* Fully responsive & mobile friendly
* Unlimited forms & form submission
* Merge tags support
* Multi-steps support
* Conditional logic support ( for fields, form notifications and more )
* Save And Continue Later support
* Front end posting & User creation
* Export and import forms
* Export entries
* Customizable templates
* Full control ( styles, email templates, field templates and more )
* Developer friendly
* Highly effective Anti-spam system ( invisible to users )
* reCaptcha support

= Available Extensions ( third-party ) =

The following extensions above are provided by third-party developers, we do not manage or support these extensions.

* [Local captcha by MobiCMS](https://github.com/lichtmetzger/mega-forms-local-captcha): Integrates a local captcha by MobiCMS into Mega Forms.


== Screenshots ==

1. Mega Forms Drag & Drop forms builder
2. Creating a form
3. Editing a field
4. Adding a container
5. Columns
6. Form settings
7. Email settings

== Frequently Asked Questions ==
= Does Mega Forms Have A Getting Started Guide =

We're currently still working on building Mega Forms site, once completed, it will have a complete list of resources at https://wpmegaforms.com/ 


= Is There A Documentation For Developers =

We're currently still working on building Mega Forms site, once completed, it will have a complete list of resources at https://wpmegaforms.com/ 


= Does It Work On All Devices =

Yes, Mega Forms is completely responsive and will display forms properly on all types of devices.

= Does It Include Spam Protection =

Yes, we are using a combination of Honeypot & Timetrap to make great spambot-proof forms. We have implemented these thwarting techniques in a way that makes them very effective in detecting and blocking spam submissions without annoying the real users. On top of this, we've added an integration with Google reCaptcha in addition to internal spam filters for more security.

= Can I Export & Import Forms =

Yes, you can easily export and import forms via the dedicated export/import tool in the admin area.

= Can I Export Entries/Submissions =

Yes, you can easily export form entries using the dedicated export/import tool in the admin area.

= What Field Types Does Mega Forms Offer =

Mega Forms comes with all the fields you need:

- Text Field
- Paragraph Text (Textarea)
- Dropdown Field
- Radio Buttons
- Checkboxes
- Number
- Name
- Email Address
- Address
- Phone
- Password
- Date
- Website
- Hidden
- Section
- HTML ( HTML code )
- Divider
- Question
- File Upload
- Consent

Here is a list of the field types in progress:

- Calculated Field
- Star Rating
- Rangle Slider
- Toggle
- Signature
- Pricing ( field group )


Here is a list of the field containers in progress:

- Repeatable Group
- Tabs
- Accordions 

= What Actions Does Mega Forms Offer =

Mega Forms comes with the necessary actions by default:

- Email Notification
- WordPress Hook
- User Registration
- Post Submission ( Front End Posting )

Here is a list of actions in progress:

- Google Spreadsheet
- Integrations ( MailChimp, ActiveCampaign, ConvertKit, AWeber, Campaign Monitor...etc )
- Webhooks

= Is it Translation Ready =

Yes, Mega Forms has full translation and localization support via the 'megaforms' textdomain.


= Can the Plugin Save Progress on Multi-Step Forms  =

Yes, there is a feature on multi-step forms that will save the user progress even if they stop or do not complete the submission. To enable this feature, add the following line to your child theme's functions.php file:

`add_filter('mf_save_paginated_form_pages', '__return_true');`

Once enabled, the plugin will create an entry as soon as the user clicks the "Next" button on multi page forms, and it will update the entry each time the user proceeds to the next page. 

= What Features are Coming Next =

We are in the process of building more features, here is a list:

- Form Chaining
- Tons Of Integrations
- Payments
- PDF Copy
- Advanced Stats

We don't promise all of this will be available soon, but we promise we'll do our best to ship these features as soon as we can.

== Changelog ==

= 1.0.0 =
* First release

= 1.0.1 =
* Fixed bug ( error when saving forms without adding actions )

= 1.0.2 =
* Fixed bugs ( Form editior not taking full screen size, admin area styling fixes..etc  )
* Added "Section" field
* Added "Question" field ( mainly added to help with Spam prevention )
* Added the possibility to make "Divider" field transparent ( maily to be used as a "spacer" when needed )
* Corrected some spelling mistakes

= 1.0.3 =
- Fixed bugs
- Added option to disable saving entries on form submission ( usefull for updating user info..etc )
- Implemented Anti-spam technique ( Honeypot trap )
- Added shortcode processing feature to the HTML field

= 1.0.4 =
- Fixed bugs ( honeypot issues, php errors on empty form submission, choices not saving in order after moving them around...etc )
- Added Anti-spam technique ( timetrap )

= 1.0.5 =
- Fixed bugs
- Added pre-made forms feature ( you can now create form based on pre-made templates )

= 1.0.6 =
- Fixed bugs ( Styling issues, repeated name attributes when using multiple forms on same page )
- Improvements to the overall user experience
- Improvements to the form editor design to allow more clarity while building and managing forms
- Added more flexibility for developers to create fields, field options..etc
- Added AJAX submission support 
- Added conditional logic 

= 1.0.7 =
- Improved: order and display of options in the `edit field` tab ( Form editor )
- Fixed: duplicate field setting tabs not responding ( Form editor )
- Fixed: AJAX form doesn't respond after first submission 
- Fixed: "Maximum call stack size exceeded" JS error when deep conditional logic is implemented 
- Added: multi-step feature ( paged forms ) 
- Added: file upload field 

= 1.0.8 =
- Styling fixes
- form editor preview fixes ( wrong column width + wrong description position when typed the first time )
- Fixed: form shortcode was returning as an empty string when called via AJAX
- Fixed: AJAX validation was adding duplicate notices for choice fields (radios, checkboxes)
- Fixed: bug in `mfget_option` function that mixes between false and non-existing setting values causing some of the true/false settings to have unexpected behavior
- Added: Save and continue feature 
- Added: Consent field 

= 1.0.9 =
- Extended "One line text" and "Paragraph text" fields so that the user can view number of characters typed if the maximum length is set for these fields.
- Added the ability to control field label visibility (show/hide)
- Fixed bug: When duplicating a field in the form editor, the field values were not copied over to the new field
- Fixed bug: When adding field tag in the form editor, the field preview didn't update with the new field value because the change event is not triggered for that field
= 1.1.0 =
- Fixed: emails were not being sent when there are multiple recipients
= 1.1.1 =
- Performance optimization ( combined and minifed CSS & JS resources )
- Improved conditional logic 
= 1.1.2 =
- Fix: a JS bug that prevents items from displaying in the single entry page
- Fix: email header issues
- Fix: conditional logic not working on compound fields
- Improvement: Make phone & number fields display a keypad on mobile view instead of the default keyboard.
= 1.1.3 =
- Update: include all PRO features in free version and drop the paid version ( Mega Forms with all PRO features is now free ).
- Fix: a bug in the `referrer` column in entries table that prevents the creation of entries if the referrer is longer than 100 chars.
= 1.1.4 =
- added filter hook "mf_bypass_session_error" to allow bypassing session validation error ( usefull when cookies aren't allowed/saved ).
= 1.1.5 =
- Fix: form email action issues ( subject was empty by default, email not sending when the "from" field is not formatted correctly )
- Fix: entries list display ( the list was showing all columns/fields by default which can cause unpleasant view for large forms)
- Fix: fields order was based on ID order on different areas ( emails, entries, entry lists..etc ), this is corrected to show fields in the same order as they appear on the form.
- Improved the "Change columns visibility" option design/display on the entries list page
- Ensure that once an entry is deleted, all associated files will be deleted as well.
- Added a link to download/view uploaded files in email notifications
= 1.1.6 =
- Fix: bug in file field description ( removing file field description was not possible )
- Added WordPress shortcodes support to the "default value" field
- Fix: syntax error during activation
= 1.1.7 =
- Added cookies compatibility with wpengine ( to prevent caching issues )
- Fixed a bug with WP admin notices ( hiding notices outside the plugin's pages )
- Fixed a bug with the plugin's admin snackbar ( not being loaded on some pages )
= 1.1.8 =
- Bug fixes in the drag and drop uploads field ( inaccurate file count ... )
= 1.1.9 =
- Fix: a bug in "more options" selection in email action that only allows a single value to be saved
- Fix: a bug making the filetype case-sensitive in upload field
= 1.2.0 =
- Fix: a bug in checkbox option that is causing only the last selected value to be saved from a checkbox list in AJAX submissions
- Fix: a bug in "form save" process that is causing only a single "form action" to be saved.
= 1.2.1 =
- Fix: maintain the selected tab selected after page refresh ( admin settings )
- Added: ability to export form entries
= 1.2.2 =
- Changed Mega Forms session cookie prefix from "mf" to "wordpress_mf" to ensure compatibility with caching plugins and hosting providers caching.
- Fixed: CSS clearfix incompatibility with some theme which causing the layout of some fields to break ( eg; address field when error are displayed )
- Fixed: Bug that prevented the "CSS Classes" value from being saved for form fields.
= 1.2.3 =
- Styling fixes
- Reduced resources ( CSS, JS ) size.
= 1.2.4 =
- Add support to multisite.
= 1.2.5 =
- CSS Fixes
- Fixed an XSS issue. Credit: ptsfence.
= 1.2.6 =
- Fix compatibility issues with PHP version 8.
= 1.2.7 =
- Replace inconsistent prefixes for filter/action hooks using "mf_" as the new standard prefix.
- Additional security against spam.
= 1.2.8 =
- Include field type in the submission values array during entry creation ( Help identify field types when extending the plugin )
- Allow HTML in the email message field.
= 1.2.9 =
- Added "Google reCaptcha" feature for spam prevention.
- Fixes to the spam feature on entries list
- More spam filters for the "Paragraph" and "Name" fields
= 1.3.0 =
- Bug fixes ( styling, object cache..etc )
- Added German/Deutsch translation ( credit: Danny Schmarsel )
- Added a "User Registration" action to allow creating users via forms.
- Added additional background filters to detect spam entries
= 1.3.1 =
- Bug fixes
- Conditional logic for form actions ( send email, register users, or trigger hooks conditionally based on a field value or multiple field values )
= 1.3.2 =
- Update PHP dependencies
- Fixed PHP 8.1 compatbility issue: an error appers when trying to save a form the uses a "page container" ( multi-step form ).
- Added the ability to save each page values to an entry in multi-page forms before the user submits the form. This feature can be enabled by returning true on the filter `mf_save_paginated_form_pages`; eg: `add_filter('mf_save_paginated_form_pages', '__return_true');`.
- Added additional filters to detect spam entries.
= 1.3.3 =
- Added "post submission" action. This will allow users to submit posts/pages or custom post types from the front end.
- Bug fixes.
- Styling fixes.
= 1.3.4 =
- Styling fixes.
- Add a "row" container when a page is created for better user experience. 
= 1.3.5 =
- Styling improvement
- Update German/Deutsch translation files.
= 1.3.6 =
- Styling fixes
- Added more spam filters for textarea field.
= 1.3.7 =
- Added more spam filters.
- Bug fixes ( fix incompatibility issues with sessions and full-page caching )
= 1.3.8 =
- Fix conditional logic for fields
- Update translations
= 1.3.9 =
- Beautify `{mf:fields all_fields}` tag output on emails + maintain fields order
= 1.4.0 =
- Bug fixes
= 1.4.1 =
- Added "auto advance" feature for multi-step forms
- Added the ability to enable/disable reCaptcha for each form.
- Combined CSS files and JS files for better performance.
= 1.4.2 =
- Bug fixes
- Allow some HTML tags ( br, span, a, img, strong, em, p ) in choice fields ( radios and checkboxes )
- Added the ability to set "hidden" field as non-submitting field to allow for background processing without actually saving the value.
= 1.4.3 =
- Bug fixes ( date range field validation error + others )
= 1.4.4 =
- Bug fixes with the "email" action
= 1.4.5 =
- Added `mf_mail_data` filter to allow changing email data before it's sent (eg; subject, headers...etc)
= 1.4.6 =
- Disable session check on form submission due to cache issues on some hosting providers, to restore session check use `add_filter( 'mf_bypass_session_error', '__return_false' ); `
= 1.4.7 =
- Bug fix: an entry with a modified form may produce an error for some fields.
- Added the ability to set a placeholder for select fields.
- Restored session check that was disabled in version `1.4.6` as disabling it increased spam submissions.
- Bug fix: Fixed false-positives for the session check caused by heavy caching.
= 1.4.8 =
- Added: reCAPTCHA Enterprise v2 and v3 integrations for enhanced spam protection.
- Added: filter 'megaforms_recaptcha_score_threshold' to allow customization of reCAPTCHA score threshold.
- Added: a background task to automatically attempt to clear cache when the plugin is updated or when the settings are changed. This was done to prevent inconsistencies between the backend and the frontend that are usually caused by page caching. Clearing cache manually might still be needed when you make changes, especially on individual forms. This is not a problem with the plugin itself, but it's how caching mechanism works in general, if a static copy of the page is being served to the users, changes to the backend will not reflect on the frontend until you clear cache.
= 1.4.9 =
- Fix: a bug in the "non-ajax" submission flow that prevent the form from submitting when AJAX is disabled.
= 1.5.0 =
- Fix: a bug that causes an error on plugin update.
- Fix: a bug with AJAX submissions on iOS devices.
- General fixes to ensure reCaptcha works as expected with AJAX and non-ajax form submissions.