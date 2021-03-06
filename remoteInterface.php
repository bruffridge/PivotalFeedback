<?php
  include 'includes/classes/Exception.php';
  include 'includes/classes/UIException.php';
  include 'includes/classes/Validate.php';
  $rootdir = '/PivotalFeedback/';//todo: set this to the subfolder your site is under or / if your site is at the root URL.
  try {

    ini_set('display_errors',0);
    header('Content-type: text/javascript');
    

    if($_REQUEST['method'] == 'feedback') {
      $formVals = $_POST['args'][0];
      $fieldNames = $_POST['args'][1];
      $valErrors = array();
      
      //validate fields
      switch($formVals[$fieldNames['type']]) {
        case 'bug':
          if(isset($formVals[$fieldNames['steps']]) && strlen($formVals[$fieldNames['steps']]) > 2000) {
            $valErrors['steps'] = '2000 characters max.';
          }
        case 'feature':
        case 'chore':
          if(empty($formVals[$fieldNames['description']])) {
            $valErrors['description'] = 'Description is required.';
          }
          else if(strlen($formVals[$fieldNames['description']]) > 2000) {
            $valErrors['description'] = '2000 characters max.';
          }
          break;
        default:
          throw new feedback\Exception('Invalid feedback type.', feedback\Exception::AJAX);
      }
      
      // Process Uploaded Attachment
      if($_FILES[$fieldNames['attachment']]["tmp_name"] != "") {
        $newfilename = preg_replace('/(\.pdf|\.jpg|\.jpeg|\.png|\.gif)$/', '', $_FILES[$fieldNames['attachment']]["name"]);
        $newfilename = preg_replace('/\W/', '', $newfilename);
        $userfile_tmp = $_FILES[$fieldNames['attachment']]["tmp_name"];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $userfile_tmp);

        switch($mime) {
          case 'image/jpeg':
            $fileExt = '.jpg';
            break;
          case 'image/gif':
            $fileExt = '.gif';
            break;
          case 'image/png':
            $fileExt = '.png';
            break;
          case 'application/pdf':
            $fileExt = '.pdf';
            break;
          default:
            //wrong file type.
            $valErrors['attachment'] = 'Only .jpg, .gif, .png, and .pdf files allowed.';
        }
      }
      
      if(!empty($valErrors)) {
        try {
          throw new feedback\UIException('One or more errors occurred', $valErrors);
        }
        catch(feedback\UIException $e) {
          //validation failed. show errors.
          exit(prefixJSON(1,json_encode($e->getValErrors())));
        }
      }
      
      $name = '';
      $desc = '';
      
      switch($formVals[$fieldNames['type']]) {
        case 'bug':
          if(strpos($_SERVER['HTTP_REFERER'], $rootdir) !== false) {
            $name = substr($_SERVER['HTTP_REFERER'], strpos($_SERVER['HTTP_REFERER'], $rootdir) + strlen($rootdir));
          }
          $desc = 
'*What happened?*
'.$formVals[$fieldNames['description']].'

*Steps to reproduce*
'.$formVals[$fieldNames['steps']];
          break;
        case 'feature':
        case 'chore':
          $name = 'users_name';//todo: replace users_name with name of logged in user.
          $desc = $formVals[$fieldNames['description']];
          break;
      }
      
      $page = '';
      $subBy = 'users_name';//todo: replace users_name with name of logged in user.
      $userAgent = '';
      
      if(isset($_SERVER['HTTP_REFERER'])) {
        $page = $_SERVER['HTTP_REFERER'];
      }
      if(isset($_SERVER['HTTP_USER_AGENT'])) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
      }
      
      $desc .= '

*Details*
_Page:_               '.$page.'
_Date:_                '.date('d-M-Y H:i:s e', time()).'
_Submitted by:_ '.$subBy.'
_User-Agent:_    '.$userAgent;
      
      //todo: replace tracker_token with yours from your profile: https://www.pivotaltracker.com/profile
      $custom_headers = array('X-TrackerToken' => 'tracker_token');

      $xmldata = '<story><story_type>' . $formVals[$fieldNames['type']] . '</story_type><name>' . date('[n/j/Y g:i:sa]', time()) . ' ' . htmlspecialchars($name) . '</name><description>' . htmlspecialchars($desc) . '</description></story>';
      
      //todo: replace project_id with yours.
      $httpResponse = feedback\Validate::http_request('POST', 'www.pivotaltracker.com', 443, '/services/v3/projects/project_id/stories', array(), array(), $xmldata, array(), array(), $custom_headers, 1, false, false);
      
      //check the response for a single <story> node. This indicates success.
      $xmlParser = xml_parser_create();
      xml_parse_into_struct($xmlParser, $httpResponse, $xmlResp);
      if($xmlResp[0]['tag'] == 'STORY') {
        if($xmlResp[1]['tag'] != 'ID' || !preg_match('/^[0-9]+$/', $xmlResp[1]['value'])){
          throw new feedback\Exception('An error occurred while submitting feedback.', feedback\Exception::AJAX);
        }
        
        // story successfully submitted, now upload the attachment if there is one.
        if(isset($newfilename)) {
          $storyId = $xmlResp[1]['value'];
          $formdata = array();
          $formdata[] = 'Content-Disposition: form-data; name="Filedata"; filename="'.$newfilename.$fileExt.'"';
          $formdata[] = 'Content-Type: '.$_FILES[$fieldNames['attachment']]["type"];
          $formdata['formVal'] = file_get_contents($_FILES[$fieldNames['attachment']]["tmp_name"]);
          //todo: replace project_id with yours.
          $httpResponse = feedback\Validate::http_request('POST', 'www.pivotaltracker.com', 443, '/services/v3/projects/project_id/stories/'.$xmlResp[1]['value'].'/attachments', array(), array(), NULL, $formdata, array(), $custom_headers, 1, false, false);

          $xmlParser = xml_parser_create();
          xml_parse_into_struct($xmlParser, $httpResponse, $xmlResp);

          $isError = true;

          foreach($xmlResp as $value) {
            if($value['tag'] == 'STATUS' && strtolower($value['value']) == 'pending') {
              $isError = false;
              break;
            }
          }

          if($xmlResp[0]['tag'] != 'ATTACHMENT' || $isError) {
            //if the attachment upload is successful than send back a success msg. If it's not send back a failure message, and delete the story.
            //delete story.
            //todo: replace project_id with yours.
            $httpResponse = feedback\Validate::http_request('DELETE', 'www.pivotaltracker.com', 443, '/services/v3/projects/project_id/stories/'.$storyId, array(), array(), NULL, array(), array(), $custom_headers, 1, false, false);
            throw new feedback\Exception('An error occurred while submitting feedback attachment.', feedback\Exception::AJAX);
          }
        }
        exit(prefixJSON(2, json_encode('Feedback successfully submitted.')));
      }

      throw new feedback\Exception('An error occurred while submitting feedback.', feedback\Exception::AJAX);
    }

   
  }
  catch (Exception $e) {
    //prefix json, pass a generic message.
    $errorMsg = 'server error';
    
    exit(prefixJSON(1, json_encode($errorMsg)));
  }
  
  function prefixJSON($status, $jsonStr) {
    switch($status) {
      case 1:
        $statusTxt = 'error';
        break;
      case 2:
        $statusTxt = 'success';
        break;
    }
    
    return 'while(1);{"result":{"status":"' . $statusTxt . '", "value":' . $jsonStr . '}}';
  }
?>