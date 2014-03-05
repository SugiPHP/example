<?php
/**
 * @package  sugi example
 * @category model
 */

namespace Model;

use SugiPHP\Sugi\Container;
use SugiPHP\Sugi\Config;
use PDO;

// PDO DB Handler
Container::set("db", function() {
	$config = Config::get("db");
	$db = new PDO($config["dsn"], $config["user"], $config["pass"]);
	// Set error handling to Exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Fetch return results as associative array
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	if (Config::get("db.dsn") == "sqlite::memory:") {
		$sql = "CREATE TABLE links (id INTEGER NOT NULL, title VARCHAR(100), uri VARCHAR(255)); INSERT INTO links VALUES (1, 'home', '/');";
		$db->exec($sql);
	}

	return $db;
});

/**
 * Connecting to the database
 */
abstract class Common
{
	protected $db;

	public function __construct()
	{
		$this->db = Container::get("db");
	}
}
