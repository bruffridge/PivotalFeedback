<?php
  namespace feedback;
  
  class Exception extends \Exception
  {
    /* Codes */
    const AJAX = 4; // An exception was caught in remoteInterface.php while handling an AJAX request.
    
    private $displayOutput;

    public function __construct($message = NULL, $code = 0, \Exception $previous = NULL, $displayOutput = true)
    {
      $this->displayOutput = ($displayOutput === false ? false : true);
      parent::__construct($message, $code, $previous);
    }
    
    public function getDisplayOutput() {
      return $this->displayOutput;
    }
    public function displayOutputOn() {
      $this->displayOutput = true;
    }
    public function displayOutputOff() {
      $this->displayOutput = false;
    }
  }
?>