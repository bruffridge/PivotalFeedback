<!DOCTYPE html>
<html lang="en">
<head>
<title>Feedback Demo</title>
<link rel="stylesheet" type="text/css" href="styles/main.css" />

<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="styles/main_ie7.min.css" /><![endif]-->
    <!--[if IE 8]><link rel="stylesheet" type="text/css" href="styles/main_ie8.min.css" /><![endif]-->
    <!--[if gt IE 8]><!--><link rel="stylesheet" type="text/css" href="styles/main.min.css" /><!--<![endif]-->

</head>
<?php flush();/*Allows the browser to start getting content while the server is still loading the rest of the page. http://developer.yahoo.com/performance/rules.html#page-nav*/ ?>
<body>
  <div id="bodyContent">
    <a class="feedbackLink" href="#">Got Feedback?</a>

    <?php 
      $fieldNames['feedbackForm']['type'] = "selType";
      $fieldNames['feedbackForm']['description'] = "txtaDescription";
      $fieldNames['feedbackForm']['steps'] = "txtaSteps";
      $fieldNames['feedbackForm']['attachment'] = "filAttachment";

      require_once 'feedbackDialog.php';
    ?>
  </div>
<script type="text/javascript" src="scripts/jquery-1.7.2.js" language="javascript"></script>
<script type="text/javascript" src="scripts/bootstrap-modal.js" language="javascript"></script>
<script type="text/javascript" src="scripts/jquery.trap.min.js" language="javascript"></script>
<script type="text/javascript" src="scripts/Util.js" language="javascript"></script>
<script type="text/javascript" src="scripts/NodeManager.js" language="javascript"></script>
<script type="text/javascript" src="scripts/feedback.js" language="javascript"></script>
</body>
</html>