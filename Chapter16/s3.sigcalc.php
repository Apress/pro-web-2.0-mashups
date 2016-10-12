<?php

# base64.encodestring
# CHECK:  The hex2b64 function was pulled from the amazon S3 PHP example library.

    function hex2b64($str) {
      $raw = '';
      for ($i=0; $i < strlen($str); $i+=2) {
        $raw .= chr(hexdec(substr($str, $i, 2)));
      }
      return base64_encode($raw);
    }

    require_once 'Crypt/HMAC.php';
    require_once 'HTTP/Request.php';

    $AWSAccessKeyId = "0PN5J17HBGZHT7JJ3X82";
    $AWSSecretAccessKey = "uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o";
    $Expires = 1175139620;
    $HTTPVerb = "GET";
    $ContentMD5 = "";
    $ContentType = "";
    $CanonicalizedAmzHeaders = "";
    $CanonicalizedResource = "/johnsmith/photos/puppy.jpg";
    $string_to_sign = $HTTPVerb . "\n" . $ContentMD5 . "\n" . $ContentType . "\n" . $Expires . "\n" . $CanonicalizedAmzHeaders . $CanonicalizedResource;
    
    $hasher =& new Crypt_HMAC($AWSSecretAccessKey, "sha1");
    $sig = hex2b64($hasher->hash($string_to_sign));
    print ($sig);
    $sig_direct = base64_encode($hasher->hash($string_to_sign));
    print ($sig_direct);
    

?>