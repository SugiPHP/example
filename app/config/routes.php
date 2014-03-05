<?php
/**
 * Routes for SugiPHP\Sugi\Router
 *
 * @package  sugi example
 * @category config
 */

return array(
	"home" => array(
		"path"       => "/",
		"defaults"   => array("controller" => "home", "action" => "index", "param" => "")
	),
	"mvc"  => array(
		"path"       => "{controller}/{action}/{param}",
		"defaults"   => array("action" => "index", "param" => ""),
		"requisites" => array("param" => ".*"),
	),
);
