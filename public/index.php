<?php
/**
 * Laravel - A clean and classy framework for PHP web development.
 *
 * @package  Laravel
 * @version  1.5.8
 * @author   Taylor Otwell
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
$application = '../application';

// --------------------------------------------------------------
// The path to the system directory.
// --------------------------------------------------------------
$system      = '../system';

// --------------------------------------------------------------
// The path to the packages directory.
// --------------------------------------------------------------
$packages    = '../packages';

// --------------------------------------------------------------
// The path to the modules directory.
// --------------------------------------------------------------
$modules     = '../modules';

// --------------------------------------------------------------
// The path to the storage directory.
// --------------------------------------------------------------
$storage     = '../storage';

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
$public      = __DIR__;

// --------------------------------------------------------------
// Launch Laravel.
// --------------------------------------------------------------
require $system.'/laravel.php';