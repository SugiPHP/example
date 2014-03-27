<?php
/**
 * @package  sugi example
 * @category model
 */

namespace Model;

use SugiPHP\Auth\Auth as BaseAuth;
use SugiPHP\Auth\AuthInterface;
use SugiPHP\Auth\LimitInterface;
use SugiPHP\Auth\RegistrationInterface;
use SugiPHP\Auth\RememberMeInterface;
use PDO;
use SugiPHP\Sugi\Config;
use SugiPHP\Sugi\Container;

class Auth extends BaseAuth implements
	AuthInterface,
	LimitInterface,
	RegistrationInterface,
	RememberMeInterface
{
	/**
	 * Returns self
	 *
	 * @return Model\Auth
	 */
	public static function singleton()
	{
		if ( ! static::$self) {
			// BaseAuth with settings from configuration file
			static::$self = new self(Config::get("auth"));
		}
		return static::$self;
	}

	/*
	 * DB table names
	 */
	protected $tableUsers = "auth_users";
	protected $tableRememberMe = "auth_rememberme";
	protected $selectedFields = "user_id AS id, user_username AS username, user_password AS password, user_email AS email, user_state AS state, login_attempts";

	/**
	 * PDO handler
	 */
	protected $db;

	/**
	 * self
	 */
	private static $self;

	/**
	 * Here we NEED config options!
	 */
	public function __construct($config)
	{
		// Database connection
		$this->db = Container::get("db");

		parent::__construct($config);
	}

	/**
	 * @see AuthInterface::getUserByUsername()
	 */
	public function getUserByUsername($username)
	{
		// we return optional "login_attempts" as well as other necessary fields to improve performance
		// @see getLoginAttempts()
		$sql = "SELECT {$this->selectedFields} FROM {$this->tableUsers} WHERE LOWER(user_username) = LOWER(:username)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":username", $username, PDO::PARAM_STR);
		$sth->execute();
		$user = $sth->fetch(PDO::FETCH_ASSOC);

		return $user;
	}

	/**
	 * @see AuthInterface::getUserByEmail()
	 */
	public function getUserByEmail($email)
	{
		$sql = "SELECT {$this->selectedFields} FROM {$this->tableUsers} WHERE LOWER(user_email) = LOWER(:email)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":email", $email, PDO::PARAM_STR);
		$sth->execute();
		$user = $sth->fetch(PDO::FETCH_ASSOC);

		return $user;
	}

	/*******************************
	        SECURITY LIMITS
	********************************/

	/**
	 * @see LimitInterface::getLoginAttempts()
	 */
	public function getLoginAttempts($username)
	{
		$sql = "SELECT login_attempts FROM {$this->tableUsers} WHERE LOWER(user_username) = LOWER(:username)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":username", $username);
		$sth->execute();

		return $sth->fetchColumn();
	}

	/**
	 * @see LimitInterface::increaseLoginAttempts()
	 */
	public function increaseLoginAttempts($username)
	{
		$sql = "UPDATE {$this->tableUsers} SET login_attempts = login_attempts + 1 WHERE LOWER(user_username) = LOWER(:username)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":username", $username);
		$sth->execute();
	}

	/**
	 * @see LimitInterface::resetLoginAttempts()
	 */
	public function resetLoginAttempts($username)
	{
		$sql = "UPDATE {$this->tableUsers} SET login_attempts = 0 WHERE LOWER(user_username) = LOWER(:username)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":username", $username);
		$sth->execute();
	}

	/**********************************
	  USER REGISTRATION & MANIPULATION
	 **********************************/

	/**
	 * @see RegistrationInterface::addUser()
	 */
	public function addUser($username, $email, $passwordHash, $state)
	{
		$sql = "INSERT INTO {$this->tableUsers} (user_username, user_email, user_password, user_state, user_added)
				VALUES (:username, :email, :password, :state, :time)";
				// RETURNING user_id";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":username", $username, PDO::PARAM_STR);
		$sth->bindValue(":email", $email, PDO::PARAM_STR);
		$sth->bindValue(":password", $passwordHash, PDO::PARAM_STR);
		$sth->bindValue(":state", (int) $state, PDO::PARAM_INT);
		// custom fields
		$sth->bindValue(":time", date("Y-m-d H:i:s"));
		$sth->execute();

		return $this->db->lastInsertId();
	}

	/**
	 * @see RegistrationInterface::updatePassword()
	 */
	public function updatePassword($username, $passwordHash)
	{
		$sql = "UPDATE {$this->tableUsers} SET user_password = :password WHERE LOWER(user_username) = LOWER(:username)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":password", $passwordHash);
		$sth->bindValue(":username", $username);

		return $sth->execute();
	}

	/**
	 * @see RegistrationInterface::updateState()
	 */
	public function updateState($username, $state)
	{
		$sql = "UPDATE {$this->tableUsers} SET user_state = :state WHERE LOWER(user_username) = LOWER(:username)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":username", $username);
		$sth->bindValue(":state", (int) $state, PDO::PARAM_INT);

		return $sth->execute();
	}


	/*******************************
	           REMEMBER ME
	********************************/

	/**
	 * @see RememberMeInterface::getRememberMe()
	 */
	public function getRememberMe($token)
	{
		$sql = "SELECT rememberme_time AS time, user_username AS username
		        FROM {$this->tableRememberMe}
		        WHERE rememberme_token = :token";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":token", $token, PDO::PARAM_STR);
		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * @see RememberMeInterface::addRememberMe()
	 */
	public function addRememberMe($token, $time, $username)
	{
		$sql = "INSERT INTO {$this->tableRememberMe} (rememberme_token, rememberme_time, user_username)
		        VALUES (:token, :time, :username)";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":token", $token, PDO::PARAM_STR);
		$sth->bindValue(":time", (int) $time, PDO::PARAM_INT);
		$sth->bindValue(":username", $username, PDO::PARAM_STR);
		$sth->execute();

		return $sth->rowCount();
	}

	/**
	 * @see RememberMeInterface::deleteRememberMe()
	 */
	public function deleteRememberMe($token)
	{
		$sql = "DELETE FROM {$this->tableRememberMe}
		        WHERE rememberme_token = :token";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":token", $token, PDO::PARAM_STR);
		$sth->execute();
	}


	/**
	 * Not part of the RememberMeInterface
	 */
	public function deleteRememberMeForUser($username)
	{
		$sql = "DELETE FROM {$this->tableRememberMe}
		        WHERE user_username = :username";
		$sth = $this->db->prepare($sql);
		$sth->bindValue(":username", $username, PDO::PARAM_STR);
		$sth->execute();
	}


	/*
	 * Custom functions
	 */


	/**
	 * Making method public!!
	 */
	public function setUserData($key, $value = null)
	{
		parent::setUserData($key, $value);
	}
}
