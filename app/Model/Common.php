<?php
/**
 * @package  sugi example
 * @category model
 */

namespace Model;

use SugiPHP\Sugi\Container;
use SugiPHP\Sugi\Config;
use PDO;

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
