<?php
  namespace feedback;
  
  class Validate
  {
    
    static public function http_request( 
        $verb = 'GET',             /* HTTP Request Method (GET, POST, and DELETE supported) */ 
        $ip,                       /* Target IP/Hostname */ 
        $port = 80,                /* Target TCP port */ 
        $uri = '/',                /* Target URI */ 
        $getdata = array(),        /* HTTP GET Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
        $postdata = array(),       /* HTTP POST Data ie. array('var1' => 'val1', 'var2' => 'val2') */
        $xmldata = NULL,
        $formdata = array(),
        $cookie = array(),         /* HTTP Cookie Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
        $custom_headers = array(), /* Custom HTTP headers ie. array('Referer: http://localhost/ */ 
        $timeout = 1,           /* Socket timeout in seconds */ 
        $req_hdr = false,          /* Include HTTP request headers */ 
        $res_hdr = false           /* Include HTTP response headers */ 
        ) 
    {
      $ret = '';
      $verb = strtoupper($verb); 
      $cookie_str = ''; 
      $getdata_str = count($getdata) ? '?' : ''; 
      $postdata_str = '';
      $boundary = "AaB03x";

      foreach ($getdata as $k => $v) 
                  $getdata_str .= urlencode($k) .'='. urlencode($v) . '&'; 

      foreach ($postdata as $k => $v) 
          $postdata_str .= urlencode($k) .'='. urlencode($v) .'&'; 

      foreach ($cookie as $k => $v) 
          $cookie_str .= urlencode($k) .'='. urlencode($v) .'; '; 

      $crlf = "\r\n";
      $req = $verb .' '. $uri . $getdata_str .' HTTP/1.1' . $crlf;
      $req .= 'Host: '. $ip . $crlf; 
      $req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12' . $crlf; 
      $req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf; 
      $req .= 'Accept-Language: en-us,en;q=0.5' . $crlf; 
      $req .= 'Accept-Encoding: gzip,deflate' . $crlf; 
      $req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . $crlf;
      $req .= 'Connection: close' . $crlf;

      foreach ($custom_headers as $k => $v)
          $req .= $k .': '. $v . $crlf; 

      if (!empty($cookie_str)) 
          $req .= 'Cookie: '. substr($cookie_str, 0, -2) . $crlf; 
      
      if ($verb == 'POST' && !empty($postdata_str)) 
      { 
          $postdata_str = substr($postdata_str, 0, -1); 
          $req .= 'Content-Type: application/x-www-form-urlencoded' . $crlf; 
          $req .= 'Content-Length: '. strlen($postdata_str) . $crlf . $crlf; 
          $req .= $postdata_str; 
      }
      else if ($verb == 'POST' && !empty($xmldata))
      {
          $req .= 'Content-type: application/xml' . $crlf;
          $req .= 'Content-Length: '. strlen($xmldata) . $crlf . $crlf; 
          $req .= $xmldata;
      }
      else if ($verb == 'POST' && !empty($formdata))
      {
        $req .= 'Content-Type: multipart/form-data; boundary=' . $boundary . $crlf;
        $reqTmp = '--'.$boundary.$crlf;
        foreach($formdata as $key => $value) {
          if($key === 'formVal') {
            $reqTmp .= $crlf;
          }
          $reqTmp .= $value.$crlf;
        }
        $reqTmp .= '--'.$boundary.'--';
        $req .= 'Content-Length: '. strlen($reqTmp) . $crlf . $crlf;
        $req .= $reqTmp;
      }
      else $req .= $crlf; 

      if ($req_hdr) 
          $ret .= $req; 
      
      $ssl = '';
      if($port == 443) {
        $ssl = 'tls://';
      }
      
      if (($fp = @fsockopen($ssl . $ip, $port, $errno, $errstr)) == false) 
          return "Error $errno: $errstr\n"; 

      stream_set_timeout($fp, $timeout); 

      fputs($fp, $req);
      while ($line = fgets($fp)) {
        $ret .= $line;
      }
      fclose($fp);

      //check the header to see if the response is gzipped then use gzdecode() to decode it.
      $httpHeader = substr($ret, 0, strpos($ret, "\r\n\r\n"));
      $httpHeader = Validate::parse_http_header($httpHeader);
      $httpHeader = array_change_key_case($httpHeader);
      
      if(array_key_exists(strtolower('Transfer-Encoding'), $httpHeader) && strtolower($httpHeader['transfer-encoding']) == 'chunked') {
        $chunked = true;
      }
      
      if(array_key_exists(strtolower('Content-Encoding'), $httpHeader) && strtolower($httpHeader['content-encoding']) == 'gzip') {
        $gzipped = true;
      }
      
      if (!$res_hdr)
          $ret = substr($ret, strpos($ret, "\r\n\r\n") + 4);
      
      if(isset($chunked)) {
        $ret = Validate::unchunk_string($ret);
      }
      
      if(isset($gzipped)) {
        $ret = gzinflate(substr($ret,10,-8));
      }
      
      return $ret;
    }
    
    private function parse_http_header($str) {
      $lines = explode("\r\n", $str);
      $head  = array(array_shift($lines));
      foreach ($lines as $line) {
        list($key, $val) = explode(':', $line, 2);
        if ($key == 'Set-Cookie') {
          $head['Set-Cookie'][] = trim($val);
        } else {
          $head[$key] = trim($val);
        }
      }
      return $head;
    }
    
    private function unchunk_string ($str) {

      // A string to hold the result
      $result = '';

      // Split input by CRLF
      $parts = explode("\r\n", $str);

      // These vars track the current chunk
      $chunkLen = 0;
      $thisChunk = '';

      // Loop the data
      while (($part = array_shift($parts)) !== NULL) {
        if ($chunkLen) {
          // Add the data to the string
          // Don't forget, the data might contain a literal CRLF
          $thisChunk .= $part."\r\n";
          if (strlen($thisChunk) == $chunkLen) {
            // Chunk is complete
            $result .= $thisChunk;
            $chunkLen = 0;
            $thisChunk = '';
          } else if (strlen($thisChunk) == $chunkLen + 2) {
            // Chunk is complete, remove trailing CRLF
            $result .= substr($thisChunk, 0, -2);
            $chunkLen = 0;
            $thisChunk = '';
          } else if (strlen($thisChunk) > $chunkLen) {
            // Data is malformed
            return FALSE;
          }
        } else {
          // If we are not in a chunk, get length of the new one
          if ($part === '') continue;
          if (!$chunkLen = hexdec($part)) break;
        }
      }

      // Return the decoded data of FALSE if it is incomplete
      return ($chunkLen) ? FALSE : $result;

    }
  }
?>