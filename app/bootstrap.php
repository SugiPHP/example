<?php
/**
 * Bootstrap
 *
 * @package 𝓢𝓾𝓰𝓲𝓟𝓗𝓟 𝓮𝔁𝓪𝓶𝓹𝓵𝓮
 */

use SugiPHP\Sugi\Config;
use SugiPHP\Sugi\Container;
use SugiPHP\Sugi\Router;
use SugiPHP\Sugi\Event;
use SugiPHP\Sugi\Logger;

/**
 * Shortcut for directory separator.
 *
 * @var string
 */
defined("DS") or define("DS", DIRECTORY_SEPARATOR);

/**
 * Application root path.
 *
 * @var string
 */
defined("BASEPATH") or define("BASEPATH", dirname(__DIR__) . DS);

/**
 * Where application lives. This is current file's path.
 *
 * @var string
 */
defined("APPPATH") or define("APPPATH", __DIR__.DS);

/**
 * Document Root ($_SERVER["DOCUMENT_ROOT"])
 *
 * @var string
 */
defined("WWWPATH") or define("WWWPATH", BASEPATH."www".DS);

/**
 * Temp path.
 *
 * @var string
 */
defined("TMPPATH") or define("TMPPATH", BASEPATH."tmp".DS);

/**
 * Are we on development or on production server
 * To set development environment add the following code in your apache configuration file
 * <code>
 * 	SetEnv APPLICATION_ENV development
 * </code>
 *
 * When PHP runs from CLI (Linux bash) you can set it with
 * export APPLICATION_ENC=development
 * this can be also added in your ~/.bashrc file
 *
 * @var string
 */
defined("APPLICATION_ENV") or define("APPLICATION_ENV", in_array(getenv("APPLICATION_ENV"), ["development", "testing", "production"]) ? getenv("APPLICATION_ENV") : "production");

/**
 * Define DEBUG flag
 * Debug depends of is it on development or production,
 * or it can be manually set to true or false in config file or with
 * <code>
 * 	define("DEBUG", true);
 * 	define("DEBUG", false);
 * </code>
 *
 * @var boolean
 */
defined("DEBUG") or define("DEBUG", (APPLICATION_ENV == "development"));

/*
 * Set error reporting level
 * Error reporting depends of DEBUG
 */
error_reporting((DEBUG) ? E_ALL | E_STRICT : E_ALL ^ E_NOTICE ^ E_USER_NOTICE ^ E_WARNING ^ E_DEPRECATED);

/*
 * The errors are welcome even in production if we use error and exception handlers to display
 * custom error page, rather than a blank page
 * Display errors can be set to false, or to DEBUG which will show errors on development and
 * hide them from production
 */
ini_set("display_errors", DEBUG);

/*
 * Since we have no error_handler at this time, it's a good idea to see them in HTML format
 * can be set to true or false
 * or ini_get("display_errors") which will make them appear in HTML format on the screen, or in text format when errors does not appear on screen
 */
ini_set("html_errors", ini_get("display_errors"));

// Set the default time zone
date_default_timezone_set("Europe/Sofia");

// Composer
include BASEPATH."vendor/autoload.php";

// Setting path for auto config loader
Config::$path = APPPATH."config";

// PDO DB Handler
Container::set("db", function () {
	$config = Config::get("db");
	$db = new PDO($config["dsn"], $config["user"], $config["pass"]);
	// Set error handling to Exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Fetch return results as associative array
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	if (Config::get("db.dsn") == "sqlite::memory:") {
		$sql = file_get_contents(APPPATH."Model/db.sql");
		$db->exec($sql);
	}

	return $db;
});

// return if we are coming form CLI (crontab jobs for example)
if (php_sapi_name() == "cli") {
	return ;
}

// Registering event listener for error 404, or when a route is not found
Event::listen(array("404", "sugi.router.nomatch"), function () {
	Logger::debug("Page " . $_SERVER["REQUEST_URI"] . " Not Found");
	header("HTTP/1.0 404 Not Found");
	include APPPATH . "View/errors/404.html";
	exit;
});

// Registering event listener for error 500
Event::listen("500", function () {
	Logger::debug("Page " . $_SERVER["REQUEST_URI"] . " Internal Server Error");
	header("HTTP/1.0 500 Internal Server Error");
	include APPPATH . "/View/errors/500.html";
	exit;
});

// Registering event listener for error 503
Event::listen("503", function () {
	Logger::debug("Page " . $_SERVER["REQUEST_URI"] . " Service Unavailable");
	header("HTTP/1.0 503 Service Unavailable");
	include APPPATH . "View/errors/503.html";
	exit;
});

// Registering event listener when a route is found
Event::listen("sugi.router.match", function ($event) {
	$method = $event->getParam("action")."Action";
	try {
		// Load the controller using reflection
		$class = new \ReflectionClass("\\Controller\\".ucfirst($event->getParam("controller")));
		// Check we can create an instance of the class
		// Check the class has needed method and it is callable
		if ($class->isInstantiable() and $class->hasMethod($method) and $class->getMethod($method)->isPublic()) {
			$controller = $class->newInstance();
			$controller->$method($event->getParam("param"));
			return ;
		}
	} catch (\ReflectionException $e) {
		// Reflection will throw exceptions for missing classes or actions
	}
	// class or the method not found -> fire 404
	Event::fire("404");
	// OR
	// Router::matchNext();
});

session_start();

// Go! Start an app!
Router::match();

/**
 * TODO: Translation function.
 * @param  string $msgid
 * @return string
 */
function __($msgid)
{
	return $msgid;
}
