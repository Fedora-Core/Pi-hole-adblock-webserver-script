<?php
/* ---------------------------------------------------------------------------------------------------------------------- */
/* code by Fedora Core       autumn/fall 2015                                                    version : prototype 0.3  */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*      - REQUIRES  :  (1)  webserver  config :  module        : "rewrite"                                                */
/*      - REQUIRES  :  (2)  webserver  config :  rewrite-rule  : ".*"        "/myPihole_RewriteRule.php"                  */
/*      - REQUIRES  :  (3)  webserver  in document-root        :              /myPihole_RewriteRule.php                   */
/*      - OPTIONAL  :  (o)  webserver  in document-root        :              /myPihole_AntiAntiAdblock.php               */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Triggered by :                                                                                                         */
/*                 http://jacobsalmela.com                                                                                */
/*                     Archives                                                                                           */
/*                         august                                                                                         */
/*                             2015-08-26                                                                                 */
/*                                 a-new-easy-insyallation-method-for-the-ad-blocking-pi-hole                             */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Proof-Of-Concept :                                                                                                     */
/*    Prototyped on :                                                                                                     */
/*                       Hardware Platform = RaspberryPi 2 B                                                              */
/*                        Operating System = Diet-Pi - Based on Raspbian (Wheezy) - Derived from Debian 7 (Wheezy)        */
/*                              Web Server = Apache2                                                                      */
/*                            Virtual-Host = on static IPv4 IPaddress#2 : port 80                                         */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* ONLY FOR DEBUGGING :                                                                                                   */
/*                         The PHP statement "error_log()" writes the message to the webserver error.log                  */
/*                         While in development : Un-comment the "$myBool = error_log()" statements.                      */
/*                         While in production  :    Comment the "$myBool = error_log()" statements.                      */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* PHASE (A)                                                                                                              */
/*           - Prepare PHP work variables with information from the environment of the webserver :                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (A)-1                                                                                                             */
/*           - Fetch the webserver variable that identifies the original domain-name :                                    */
/*           - The original domain-name is passed to this PHP from de HTTP request-header 'Host:' :                       */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myHost = '';
    $myHost = $_SERVER["HTTP_HOST"];
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (A)-2                                                                                                             */
/*           - Sanitize the original domain-name :                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myHost = preg_replace('/:\d+$/' , '' , $myHost);
    $myHost = trim($myHost);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (A)-3                                                                                                             */
/*           - The original URI is passed to this PHP in the webserver variable $SERVER["REQUEST_URI"] :                  */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myRequest = '';
    $myRequest = $_SERVER["REQUEST_URI"];
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* PHASE (B)                                                                                                              */
/*           - Prepare PHP work variables that will help with the prototyping :                                           */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (B)-1                                                                                                             */
/*           - Search for the ? (if present) that separates the URI path from the URI parameter string(s) :               */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myPos = false;
    $myPos = strpos($myRequest , "?");
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (B)-2                                                                                                             */
/*           - If no ? is present, then take all; else take only the part before the ? :                                  */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myUri = '';
    if ($myPos === false) {
        $myUri = $myRequest;
    } else {
        $myUri = substr($myRequest , 0 , $myPos);
    }
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (B)-3                                                                                                             */
/*           - Sanitize the original URI :                                                                                */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myUri = trim($myUri);
    $myUri = strtolower($myUri);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (B)-4                                                                                                             */
/*           - Determine the original URI length, and prepare the extraction of the extention :                           */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myUriLen = 0;
    $myUriLen = strlen($myUri);
    if ($myUriLen > 0) {
        $myExt = $myUri;
    } else {
        $myExt = '';
    }
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (B)-5                                                                                                             */
/*           - Prepare PHP work variables for the identification of the the original extention :                          */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myExt3 = substr("___"   . $myExt  , -3);
    $myExt4 = substr("____"  . $myExt  , -4);
    $myExt5 = substr("_____" . $myExt  , -5);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* PHASE (C)                                                                                                              */
/*           - Allow for personal customization.                                                                          */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Some sites detect that the advertisement has been blocked.                                                             */
/* When that happens, the content of that site is not available,                                                          */
/*    and the first reaction would be to either cut the adblock service, or to whitelist the advertising server...        */
/* Note: There exist already "anti-adblock" companies that sell this kind of anti-adblock methods.                        */
/* Note: The sites that use the blog software 'WordPress' are already using the method documented at sitepoint.com...     */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Analyzing the anti-adblock method used, and responding with what the anti-adblock method expects to find,              */
/*    is a personal task, which can not be generalized, and that is what the Anti-Anti-Adblock PHP might accomplish.      */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (C)-1                                                                                                             */
/*           - If the personal customization Anti-Anti-Adblock PHP file exists, include its source code.                  */
/* ---------------------------------------------------------------------------------------------------------------------- */
    $myAntiAntiAdblock = 'myPihole_AntiAntiAdblock.php' ;
    if (is_file($myAntiAntiAdblock)) {
        include $myAntiAntiAdblock;
    }
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Note :                                                                                                                 */
/*        Inspiration can be found in :                                                                                   */
/*            Existing anti-adblock UserScripts for Greasemonkey, or Tampermonkey, or similar.                            */
/*        "google is your friend". (sometimes)                                                                            */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* PHASE (D)                                                                                                              */
/*           - Depending on the original extention, respond with a nice ad-block :                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*          This PHP will control the HTTP headers.                                                                       */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*          The first HTTP header contains the response status code, as describe by RequestForComment 7231.               */
/*             RFC7231 document text : maintained at ietf.org (Internet Engineering Task Force)                           */
/*             RFC7231 status codes  : maintained at iana.org (Internet Assigned Numbers Authority)                       */
/*          The range 1xx indicates "Informational".                                                                      */
/*          The range 2xx indicates "Success".                                                                            */
/*          The range 3xx indicates "Redirection".                                                                        */
/*          The range 4xx indicates "Client Error".                                                                       */
/*          The range 5xx indicates "Server Error".                                                                       */
/*             QUOTE : 'A 200 response always has a payload, though the origin server MAY generate a body of zero length. */
/*                      If no payload is desired, an origin server ought to send 204 instead.'                            */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*          This PHP will respond with a response status code 200 or a response status code 204.                          */
/*          The code 200 indicates = all is OK, and there is some HTTP content that will follow the HTTP headers.         */
/*          The code 204 indicates = all is OK, but there is -no- HTTP content that will follow the HTTP headers.         */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*          NOTE : When a PHP does not control the HTTP headers, the PHP interpreter will generate the HTTP header 200.   */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------------------------------------------ */
    /* WARNING : When testing this in the address-bar of the browser :                                                    */
    /*       The 204 does NOT modify the content in the browser.                                                          */
    /*       Content that resulted from a previous test request with the browser remains visible in the browser.          */
    /*       That is by design of the HTTP protocol. And it is no webserver error. And it is no PHP error !               */
    /* ------------------------------------------------------------------------------------------------------------------ */
    /* WARNING : When testing this in the address-bar of the browser :                                                    */
    /*       When running this Pi-hole adblock PHP script for the first time :                                            */
    /*       First clear the Cache(s) of your Browser(s) completely :                                                     */
    /*       The 204 response is cacheable. (RFC7231)                                                                     */
    /*       The 204 does NOT modify the content that resulted from a previous request and that may reside in the Cache.  */
    /*       That is by design of the HTTP protocol. And it is no webserver error. And it is no PHP error !               */
    /* ------------------------------------------------------------------------------------------------------------------ */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*  Known extentions, that will be recognized by this PHP, are :                                                          */
/*                                                                                                                        */
/*       .js      JavaScript                                                                                              */
/*       .htm     Hypertext                                                                                               */
/*       .html    Hypertext                                                                                               */
/*       .php     Hypertext (mostly - can be JavaScript also - can be Image also - can be XML also - can be json also)    */
/*       .gif     Image                                                                                                   */
/*       .png     Image                                                                                                   */
/*       .jpg     Image                                                                                                   */
/*       .jpeg    Image                                                                                                   */
/*       .bmp     Image                                                                                                   */
/*       .ico     Image                                                                                                   */
/*       .swf     Shockwave-Flash                                                                                         */
/*                                                                                                                        */
/*       .test   (only for Pi-hole debugging purposes)                                                                    */
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (D)-1                                                                                                             */
/*           - If the original request was for a javascript file, then respond with a javascript comment :                */
/* ---------------------------------------------------------------------------------------------------------------------- */
    if ($myExt3 === '.js') {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK : Send the HTTP header with code 200 :                               */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 200 Ok ");
        header('Content-Type: text/javascript');
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP CONTENT :                                                                                                     */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        print('/* JavaScript has been blocked by Pi-hole. */');
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked a JavaScript       for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (D)-2                                                                                                             */
/*           - If the original request was for an html file, then respond with an html page :                             */
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt4 === '.htm' OR $myExt5 === '.html' OR $myExt4 === '.php') {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK : Send the HTTP header with code 200 :                               */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 200 Ok ");
        header('Content-Type: text/html');
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP CONTENT :                                                                                                     */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        print('<!DOCTYPE HTML><html><h3>Blocked by Pi-hole.</h3></html>');
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked an html page       for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (D)-3                                                                                                             */
/*           - If the original request was for some image, then respond with nothing.                                     */
/*           - Prototype 0.1 responded with a Pihole image, but : the expected size of the requested image is unknown.    */
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt4 === '.gif') {
 /* ===================================================================================================================== */ 
 /* NOTE : deprecated:                                                                                                    */ 
 /* ===================================================================================================================== */ 
 // header('Content-Type: image/gif');
 // die("\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x90\x00\x00\xff\x00\x00\x00\x00\x00\x21\xf9\x04\x05\x10\x00\x00\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x04\x01\x00\x3b");
 /* ===================================================================================================================== */ 
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK, but that there is no content : Send the HTTP header with code 204 : */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked a gif image        for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt4 === '.png') {
 /* ===================================================================================================================== */ 
 /* NOTE : deprecated:                                                                                                    */ 
 /* ===================================================================================================================== */ 
 // header('Content-Type: image/png');
 // die("\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52\x00\x00\x00\x01\x00\x00\x00\x01\x01\x03\x00\x00\x00\x25\xdb\x56\xca\x00\x00\x00\x03\x50\x4c\x54\x45\x00\x00\x00\xa7\x7a\x3d\xda\x00\x00\x00\x01\x74\x52\x4e\x53\x00\x40\xe6\xd8\x66\x00\x00\x00\x0a\x49\x44\x41\x54\x08\xd7\x63\x60\x00\x00\x00\x02\x00\x01\xe2\x21\xbc\x33\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82");
 /* ===================================================================================================================== */ 
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK, but that there is no content : Send the HTTP header with code 204 : */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked a png image        for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt4 === '.jpg' OR $myExt5 === '.jpeg') {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK, but that there is no content : Send the HTTP header with code 204 : */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked a jpeg image       for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt4 === '.bmp') {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK, but that there is no content : Send the HTTP header with code 204 : */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked a bmp image        for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt4 === '.ico') {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK, but that there is no content : Send the HTTP header with code 204 : */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked an ico image       for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (D)-4                                                                                                             */
/*           - If the original request was for a ShockWave-Flash file, then respond with nothing :                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt4 === '.swf') {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK, but that there is no content : Send the HTTP header with code 204 : */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked a shockwave-flash  for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (D)-5                                                                                                             */
/*           - If the original request was for our dummy .TEST request, then respond with a debugging html page :         */
/* ---------------------------------------------------------------------------------------------------------------------- */
    } elseif ($myExt5 === '.test') {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Say that the HTTP request was OK : Send the HTTP header with code 200 :                               */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 200 Ok ");
        header('Content-Type: text/html');
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP CONTENT :                                                                                                     */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        print('<!DOCTYPE HTML>');
        print('<html>');
        print('<h3>Pi-hole adblock for port 80</h3>');
        print('<pre style="border:solid 1px black;padding:4px;margin:4px;">');
        print "SERVER_PROTOCOL " . $_SERVER["SERVER_PROTOCOL"] . "\n" ;
        print "      HTTP_HOST " . $_SERVER["HTTP_HOST"]       . "\n" ;
        print "    REQUEST_URI " . $_SERVER["REQUEST_URI"]     . "\n" ;
        print('</pre>');
        print('</html>');
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        $myBool = error_log("NOTE: Pi-hole TEST success:                  for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* Step (D)-6                                                                                                             */
/*           - For all other requests, we will respond with nothing.                                                      */
/* ---------------------------------------------------------------------------------------------------------------------- */
    } else {
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    /* HTTP HEADERS :                                                                                                     */
    /*              Make the browser very happy :                                                                         */
    /*              Say that the HTTP request was OK, but that there is no content : Send the HTTP header with code 204 : */
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
      // DEBUG: $myBool = error_log("NOTE: Pi-hole has blocked                    for $myHost$myUri" , 4);
    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
        exit(0);
    }
/* ---------------------------------------------------------------------------------------------------------------------- */
/*                                                                                                                        */
/*                                                                                                                        */
/* ---------------------------------------------------------------------------------------------------------------------- */
/* END-OF-JOB                                                                                                             */
/* ---------------------------------------------------------------------------------------------------------------------- */
    exit(0);
?>
