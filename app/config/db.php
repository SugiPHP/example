<?php
/**
 * Database configuration (PDO style)
 *
 * @package  sugi example
 * @category config
 */

if (APPLICATION_ENV == "development") {
	return array(
		"dsn"  => "sqlite::memory:",
		"user" => "",
		"pass" => "",
	);
}

// Production environment settings
return array(
	"dsn"  => "pgsql:dbname=example;host=example.com;port=9876",
	"user" => "",
	"pass" => "",
);
