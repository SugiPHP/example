<?php
/**
 * Configuration for SugiPHP\Sugi\Cache
 *
 * @package  sugi example
 * @category config
 */

return array(
	"prefix"    => "example",
	"store"     => "apc", // select one of the following: apc, memcache(d), file, null
	// settings for memcached server
	"memcached" => array(
		"host"      => "127.0.0.1",
		"port"      => 11211
	),
	// file store settings
	"file"      => array(
		"path"      => TMPPATH,
	),
);
