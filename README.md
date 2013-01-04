PivotalFeedback
===============

A plugin that allows users to submit feedback including bugs and features to Pivotal Tracker.

### Tools used

It uses basic HTML, CSS that borrows from Twitter's Bootstrap and compiles with LESS, and javascript powered by jQuery. The backend is powered by vanilla PHP 5.3.

For those not familiar with LESS I left unminified versions of the css in the styles folder. The version of jQuery is 1.7.2 which is a little old so feel free to update it.

### Setup

Add the class ```feedbackLink``` to any link to cause the dialog to show when the link is clicked.

The only modifications you need to make are in remoteInterface.php and Util.js. Search for the comments marked "todo:". You need to set the ```rootdir``` variable in remoteInterface.php and Util.js to the site subfolder of your site. So if your site is at 'mysite.com/pivotalfeedback' then set ```rootdir``` to '/pivotalfeedback/'. You also need to insert your Pivotal API token from your profile, and your Pivotal project id. You can also add the logged in user's name to see who submitted the feedback.

It will communicate with Pivotal over SSL but if you want to speed up performance change the parameter "443" in the ```http_request``` function to 80. If you have SSL only set in Pivotal though 80 won't work.

### Coming soon...

Add attachments to feedback.
