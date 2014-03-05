<?php
/**
 * Configuration for SugiPHP\Sugi\Logger
 *
 * @package  sugi example
 * @category config
 */

return array(
	array(
		"type"     => "file",
		"filename" => BASEPATH."log/custom.".date("Y-m-d").".log",
		"filter"   => APPLICATION_ENV == "production" ? "all -debug" : "all",
		"filemode" => 0666,
	)
);
