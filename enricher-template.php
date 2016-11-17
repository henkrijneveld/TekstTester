<?php
/*
Optional php file to add extra information to the sessionkey.
Add this information to the $hidden variable.

Rename "enricher-template.php" to "enricher.php" when done

If you do not provide an enricher.php file, a 5 digit random number will be added to the key

Warning: $hidden may only contain characters which are legal to a firebase key,
especially don't use the "/" in $hidden (which is part of base64 output) as it will trigger a subkey definition on firebase

NB: enricher.php will be ignored by git (see .gitignore)
*/

$hidden = "Whatever-you-want-it-to-be";
