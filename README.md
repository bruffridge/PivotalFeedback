Feedback for Pivotal Tracker
===============

A plugin that allows users to submit feedback including bugs and features to Pivotal Tracker.

[**See it LIVE!**](http://bruffridge.github.com/PivotalFeedback)<br />
This demo is a view of the `gh-pages` branch of the repository. It is automatically updated as commits are made to the branch.
Actually submitting the form will produce an error since it requires submitting an AJAX request to remoteInterface.php on a php server. But it will give you a look at what the UI looks like.

### Tools used

It uses basic HTML, CSS that borrows from Twitter's Bootstrap and compiles with LESS, and javascript powered by jQuery. The backend is powered by vanilla PHP 5.3.

For those not familiar with LESS I left unminified versions of the css in the styles folder. The version of jQuery is 1.7.2 which is a little old so feel free to update it.

### Setup

Add the class ```feedbackLink``` to any link to cause the dialog to show when the link is clicked.

The only modifications you need to make are in remoteInterface.php and Util.js. Search for the comments marked "todo:". You need to set the ```rootdir``` variable in remoteInterface.php and Util.js to the site subfolder of your site. So if your site is at 'mysite.com/pivotalfeedback' then set ```rootdir``` to '/pivotalfeedback/'. You also need to insert your Pivotal API token from your profile, and your Pivotal project id. You can also add the logged in user's name to see who submitted the feedback.

It will communicate with Pivotal over SSL but if you want to speed up performance change the parameter "443" in the ```http_request``` function to 80. If you have SSL only set in Pivotal though 80 won't work.

**Update 1/15/2013:** You can now add an attachment to your pivotal story through the plugin. It uses [XMLHttpRequest 2](http://caniuse.com/#feat=xhr2)'s [FormData](https://developer.mozilla.org/en-US/docs/DOM/XMLHttpRequest/FormData) object to upload the file using AJAX which is only supported by newer browers.
