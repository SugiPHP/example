<?php
/**
 * Single entrance point.
 *
 * @package sugi example
 */

/**
 * Document Root path.
 * Usually same as $_SERVER["DOCUMENT_ROOT"]
 */
define("WWWPATH", __DIR__ . DIRECTORY_SEPARATOR);

// Go go the project
include __DIR__."/../app/bootstrap.php";
