<?php
// config/app.php

// This calculates the base path of the application.
// It will be '/lcn' when in a subdirectory, or empty '' when in the root.
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptName, '/');

define('BASE_PATH', $basePath); 