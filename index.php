<?php
// This phpcode will generate:
// $sessionkey: unique identifier of the session
// $textkey: the name of the text to display (should be in firebase /text
// $baseurl: baseurl of the website excluding domainname
  $use_vulcanized = true; // will load my-app-vulcanized.html when true (instead of my-app.html)

  $basepath = "/";    // cannot be resolved by REQUEST_URI in phpstorm builtin webserver wont allow it
                        // set manually in that case (!!!)
  if (isset($_SERVER['REQUEST_URI'])) {
    $basepath = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME);
    if (strlen($basepath) == 0 || substr($basepath, -1) != "/") {
      $basepath .= "/";
    }
  }

  $ip = '0.0.0.0';

  if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
  }

  // Make random unique key: date-time-ip-randomhash
  $sessionkey = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d=H:i:s:u");

  //@todo change $ip into hash
  $hidden = str_replace(".", ":", $ip);
  $hidden .= "+".sprintf("%04d", mt_rand(0, 10000));
  $hidden = openssl_encrypt($hidden, "AES128" , "Stant1234", 0, str_pad("", 16, "1"));
  $hidden = str_replace("/", "-", $hidden); // base64 uses /, will be interpreted by key separator in firebase
  $sessionkey .= "=".$hidden;

  //@todo better algorithm or cookie?
  $parts = explode(".", $ip);
  $type = intval($parts[0]) + intval($parts[1]) + intval($parts[2]) + intval($parts[3]);
  $textkey = "versie" . chr(ord("a") + ($type % 3));
?>
<!--
@license
Copyright (c) 2016 The Polymer Project Authors. All rights reserved.
This code may only be used under the BSD style license found at http://polymer.github.io/LICENSE.txt
The complete set of authors may be found at http://polymer.github.io/AUTHORS.txt
The complete set of contributors may be found at http://polymer.github.io/CONTRIBUTORS.txt
Code distributed by Google as part of the polymer project is also
subject to an additional IP rights grant found at http://polymer.github.io/PATENTS.txt
-->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=yes">

    <title>TekstTester</title>
    <meta name="description" content="App om teksten te testen">

    <link rel="icon" href="<?php echo $basepath; ?>images/favicon.png">

    <!-- See https://goo.gl/OOhYW5 -->
    <link rel="manifest" href="<?php echo $basepath; ?>manifest.json">

    <!-- See https://goo.gl/qRE0vM -->
    <meta name="theme-color" content="#3f51b5">

    <!-- Add to homescreen for Chrome on Android. Fallback for manifest.json -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="TekstTester">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TekstTester">

    <!-- Homescreen icons -->
    <link rel="apple-touch-icon" href="<?php echo $basepath; ?>images/manifest/icon-48x48.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $basepath; ?>images/manifest/icon-72x72.png">
    <link rel="apple-touch-icon" sizes="96x96" href="<?php echo $basepath; ?>images/manifest/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $basepath; ?>images/manifest/icon-144x144.png">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo $basepath; ?>images/manifest/icon-192x192.png">

    <!-- Tile icon for Windows 8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="<?php echo $basepath; ?>images/manifest/icon-144x144.png">
    <meta name="msapplication-TileColor" content="#3f51b5">
    <meta name="msapplication-tap-highlight" content="no">

    <script>
      // Setup Polymer options
      window.Polymer = {
        dom: 'shadow',
        lazyRegister: true
      };

      // Load webcomponentsjs polyfill if browser does not support native Web Components
      (function() {
        'use strict';

        var onload = function() {
          // For native Imports, manually fire WebComponentsReady so user code
          // can use the same code path for native and polyfill'd imports.
          if (!window.HTMLImports) {
            document.dispatchEvent(
              new CustomEvent('WebComponentsReady', {bubbles: true})
            );
          }
        };

        var webComponentsSupported = (
          'registerElement' in document
          && 'import' in document.createElement('link')
          && 'content' in document.createElement('template')
        );

        if (!webComponentsSupported) {
          var script = document.createElement('script');
          script.async = true;
          script.src = '<?php echo $basepath; ?>bower_components/webcomponentsjs/webcomponents-lite.min.js';
          script.onload = onload;
          document.head.appendChild(script);
        } else {
          onload();
        }
      })();

      // Load pre-caching Service Worker
//      if ('serviceWorker' in navigator) {
//        window.addEventListener('load', function() {
//          navigator.serviceWorker.register('/service-worker.js');
//        });
//      }
    </script>

<!-- @todo: make relative againg when not using phpstorm -->
    <?php if ($use_vulcanized): ?>
    <link rel="import" href="<?php echo $basepath; ?>src/my-app-vulcanized.html">
    <?php else: ?>
    <link rel="import" href="<?php echo $basepath; ?>src/my-app.html">
    <?php endif ?>


    <style>
      body {
        margin: 0;
        font-family: 'Roboto', 'Noto', sans-serif;
        line-height: 1.5;
        min-height: 100vh;
        background-color: #eeeeee;
      }

      @media screen and (max-width: 480px) {
        body {
          background-color: white;
        }
      }

    </style>
  </head>
  <body>
    <my-app
        id="my-app" sessionkey="<?php echo $sessionkey; ?>"
        textkey="<?php echo $textkey; ?>"
        basepath="<?php echo $basepath; ?>">
    </my-app>
  </body>
</html>
