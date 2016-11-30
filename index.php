<?php
// This phpcode will generate:
// $sessionkey: unique identifier of the session
// $textkey: the name of the text to display (should be in firebase /text
// $baseurl: baseurl of the website excluding domainname
  $basepath = "/";    // cannot be resolved by REQUEST_URI in phpstorm builtin webserver wont allow it
                        // set manually in that case (!!!)

  if (isset($_SERVER['REQUEST_URI'])) {
    $basepath = $_SERVER['REQUEST_URI'];
    if (strlen($basepath) == 0 || substr($basepath, -1) != "/") {
      $basepath = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME);
      if (strlen($basepath) == 0 || substr($basepath, -1) != "/") {
        $basepath .= "/";
      }
    }
  }

  $use_vulcanized = ($basepath == "/tekst/"); // so will be used on nutesten.nl/tekst
//  $use_vulcanized = true; // will load my-app-vulcanized.html when true (instead of my-app.html)


  $ip = '0.0.0.0';

  if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
  }

  // Make random unique key: date-time-ip-randomhash
  $sessionkey = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d=H:i:s:u");

  // make it unique
  $hidden = sprintf("%05d", mt_rand(0, 100000));
  define("TEKSTTESTER", "OK");
  include "enricher.php";
  $sessionkey .= "=".$hidden;

  //@todo better algorithm or cookie?
  $parts = explode(".", $ip);
  $type = intval($parts[0]) + intval($parts[1]) + intval($parts[2]) + intval($parts[3]);
  $textkey = "versie" . chr(ord("a") + ($type % 2));

  // cache control of vulcanized
  $time_vulcanized_changed = filemtime(__DIR__."/src/my-app-vulcanized.html");

//  $textkey = "versieb";
//  $use_vulcanized = false;
/*
echo "Basepath: ".$basepath;
print_r($_SERVER);
echo phpinfo();
die;
*/
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=yes">

    <title>TekstTester</title>
    <meta name="description" content="Verhalentester">

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

    <!-- facebook opengraphs -->
    <meta property="og:title" content="Lees een verhaal van Ger" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https://nutesten.nl/tekst" />
    <meta property="og:site_name" content="TekstTester" />
    <meta property="og:description" content="Carmen loopt in de duinen. Haar verleden haalt haar in." />
    <meta property="og:image" content="https://nutesten.nl/tekst/images/ger.jpg" />



    <script>
      console.log("TekstTester is GO!");
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
          console.log("Loading webcomponents.js");
          var script = document.createElement('script');
          script.async = true;
          script.src = '<?php echo $basepath; ?>bower_components/webcomponentsjs/webcomponents-lite.min.js';
          script.onload = onload;
          document.head.appendChild(script);
        } else {
          console.log("*NOT* Loading webcomponents.js");
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

    <?php if ($use_vulcanized): ?>
    <link rel="import" href="<?php echo $basepath; ?>src/my-app-vulcanized.html<?php echo '?t='.strval($time_vulcanized_changed); ?>">
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

      @keyframes textcolor {
        0% {
          color: blue;
        }
        25% {
          color: green;
        }
        50% {
          color: darkorange;
        }
        75% {
          color: red;
        }
        100% {
          color: black;
        }
      }

      #waitmessage {
        animation-duration: 2s;
        animation-name: textcolor;
        animation-iteration-count: infinite;
      }

    </style>
  </head>
  <body>

    <!--[if lt IE 11]>
  <h1>Uw browser wordt niet ondersteund</h1>
  <p>U gebruikt een verouderde webbrowser. Deze wordt niet meer ondersteund door de leverancier sinds januari 2016.</p>
  <p>Hierdoor komen geen veiligheidsupdates meer beschikbaar, en bent u bijzonder kwetsbaar op het internet!</p>
  <p>Upgrade uw browser, of gebruik er één van een andere leverancier.</p>
  <br/><br/>
  <h1>Your web browser is not supported</h1>
  <p>Your webbrowser is no longer maintained by the manufacturer since januar 2016.</p>
  <p>Security patches will not be available since that date.</p>
  <p>Please upgrade or use a different webbrowser.</p>
  <![endif]-->

  <!--[if gte IE 11]><!-->
    <p id="waitmessage">De teksttester wordt geladen ...</p>

    <?php
    require "firebasecreds.php";
    ?>

    <my-app
        id="my-app" sessionkey="<?php echo $sessionkey; ?>"
        textkey="<?php echo $textkey; ?>"
        basepath="<?php echo $basepath; ?>">
    </my-app>
  <!--<![endif]-->
  </body>
</html>
