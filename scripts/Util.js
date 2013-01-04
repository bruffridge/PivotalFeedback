
  var Util = {};
  
  Util.setRootdir = function() {
    var root = '/PivotalFeedback/';//todo: set this to the subfolder your site is under or / if your site is at the root URL.
    var sPath = window.location.pathname;
    return sPath.substring(0, sPath.indexOf(root) + root.length);
  }
  
  //for root relative urls in html.
  Util.rootdir = Util.setRootdir();
  
  Util.parseJSON = function(prefixedJSON, dataType) {
    //if prefixedJSON is not valid json is null, undefined, or empty then throw an error that will make the ajax error function get called.
    var json = prefixedJSON.replace(/^while\(1\);/,"");
    var jsonParsed = $.parseJSON(json);//throws an error if it is not valid json.
    if(typeof json === 'undefined' || json == null || json == "") {
      throw "returned json object was null, undefined, or an empty string.";
    }
    //if the result returned was "server error", throw an exception.
    if(jsonParsed.result.status == "error" && jsonParsed.result.value == "server error") {
      throw "A server error occurred.";
    }
    return json;
  }