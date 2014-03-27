<?php
/**
 * @package  sugi example
 * @category controller
 */

namespace Controller;

use Model\Auth as ModelAuth;
use SugiPHP\Auth\Exception as AuthException;
use SugiPHP\Auth\RememberMeInterface;
use SugiPHP\Sugi\Logger;
use SugiPHP\Sugi\Request;
use SugiPHP\Sugi\Mail;
use SugiPHP\Sugi\Config;
use SugiPHP\Sugi\File;

/**
 * Auth - user login, logout, activation
 */
class Auth
{
	protected $modelAuth;
	protected $lang;

	/**
	 * Whether to show password fields in registration form,
	 * or this function to be after user confirms his/her account
	 * @param bool TRUE shows passwords in registration form, FALSE - in activation form
	 */
	const PASSWORDS_IN_REGISTRATION = true;

	public function __construct()
	{
		$this->modelAuth = ModelAuth::singleton();
		// TODO: translations and localization
		$this->lang = "en";
	}

	/**
	 * Login page
	 */
	public function loginAction()
	{
		// Display checkbox for remember me functionality
		$showRememberMe = $this->modelAuth instanceof RememberMeInterface;

		$error = false;
		$username = filter_input(INPUT_POST, "username");
		$password = filter_input(INPUT_POST, "password");
		$remember = $showRememberMe && (bool) filter_input(INPUT_POST, "rememberme");

		// if form has been submitted
		if (Request::getMethod() == "POST") {
			try {
				$user = $this->modelAuth->login($username, $password, $remember);
				// Redirect - login successful
				$redirect = filter_input(INPUT_POST, "redirect");
				$redirect = ($redirect) ? $redirect : "/";
				header("Location: " . $redirect);
				exit;
			} catch (AuthException $e) {
				Logger::notice($e->getLogMessage());
				$error = __($e->getMessage());
			}
		}

		$lang = $this->lang;
		include APPPATH."View/auth/login.php";
	}

	/**
	 * Logout
	 */
	public function logoutAction()
	{
		$this->modelAuth->logout();

		// redirect to main page
		header("Location: /");
		exit;
	}

	/**
	 * User registration page
	 */
	public function registerAction()
	{
		$showPasswords = self::PASSWORDS_IN_REGISTRATION;
		$lang = $this->lang;

		$error = false;
		$success = false;

		$username = filter_input(INPUT_POST, "username");
		$email = filter_input(INPUT_POST, "email");
		if ($showPasswords) {
			$password = filter_input(INPUT_POST, "password");
			$password2 = filter_input(INPUT_POST, "password2");
		}
		// if form has been submitted
		if (Request::getMethod() == "POST") {
			try {
				if ($showPasswords) {
					$result = $this->modelAuth->register($username, $email, $password, $password2);
				} else {
					$result = $this->modelAuth->register($username, $email);
				}
				// Send an email
				$res = $this->sendActivationMail($result["email"], $result["username"], $result["token"], $lang);
				if (DEBUG) {
					$this->registrationdoneAction();
					echo "<textarea cols='160' rows='16'>".htmlspecialchars($res)."</textarea>";
					exit;
				}
				// Redirect - registration is successful
				header("Location: /auth/registrationdone");
				exit;
			} catch (AuthException $e) {
				Logger::notice($e->getLogMessage());
				$error = __($e->getMessage());
			}
		}

		include APPPATH."View/auth/register.php";
	}

	/**
	 * Registration successful page
	 */
	public function registrationdoneAction()
	{
		$lang = $this->lang;
		$title = __("Sign Up");
		$message = __("An email with activation code has been sent. Please check your mail and confirm your registration.");
		$redirect = "/";

		include APPPATH."View/auth/message.php";
	}

	/**
	 * User Activation page
	 * Page after clicking on a link from the email for account activation.
	 * Changes user password and sets user state to active
	 */
	public function activateAction()
	{
		$error = false;
		$showPasswords = !self::PASSWORDS_IN_REGISTRATION;
		$lang = $this->lang;
		$username = filter_input(INPUT_GET, "user");
		$token = filter_input(INPUT_GET, "token");

		// This block can be omitted, but it's more user friendly.
		try {
			$this->modelAuth->checkToken($username, $token);
		} catch (AuthException $e) {
			$error = __("The link is either wrong or outdated. Please check you have correctly copied the link and try again.");
			$title = __("Activate Account");
			include APPPATH."View/auth/message.php";
			exit;
		}

		if (!$showPasswords) {
			try {
				$this->modelAuth->activate($username, $token, null, null);
				// TODO: do we need a message that says that we have successfully activated account?
				header("Location: /auth/login");
				exit;
			} catch (AuthException $e) {
				Logger::notice($e->getLogMessage()." User: $username, Token: $token");
				$error = __($e->getMessage());
				$title = __("Activate Account");
				$redirect = "/auth/login";
				include APPPATH."View/auth/message.php";
				exit;
			}
		}

		//
		// Password is needed!
		//
		if (Request::getMethod() == "POST") {
			$token = filter_input(INPUT_POST, "token");
			$username = filter_input(INPUT_POST, "username");
			$password = filter_input(INPUT_POST, "password");
			$password2 = filter_input(INPUT_POST, "password2");
			try {
				$this->modelAuth->activate($username, $token, $password, $password2);
				header("Location: /auth/login");
				exit;
			} catch (AuthException $e) {
				Logger::notice($e->getLogMessage()." User: $username, Token: $token");
				$error = __($e->getMessage());
			}
		}

		include APPPATH."View/auth/activate.php";
	}

	/**
	 * Forgot password (step 1).
	 * Page for forgot password.
	 */
	public function forgotpasswordAction()
	{
		$data = array();
		$lang = $this->lang;
		$email = filter_input(INPUT_POST, "email");
		$showUserInput = false;

		if (Request::getMethod() == "POST") {
			try {
				$res = $this->modelAuth->forgotPassword($email);
				$token = $res["token"];
				$username = $res["username"];
				$state = $res["state"];
				$email = $res["email"];

				// send forgot password mail
				$res = $this->sendForgotPasswordMail($email, $username, $token, $lang);
				if (DEBUG) {
					$this->registrationdoneAction();
					echo "<textarea cols='160' rows='16'>".htmlspecialchars($res)."</textarea>";
					exit;
				}
				header("Location: /auth/passwordsent");
				exit;
			} catch (AuthException $e) {
				// TODO: if the user does not exists ($e->getCode() === AuthException::USER_NOT_FOUND) it's better to obfuscate
				// the fact and send an email or just print we've sent it.
				Logger::notice($e->getLogMessage()." User: $username, Token: $token");
				$error = __($e->getMessage());
			}
		}

		include APPPATH."View/auth/forgotpassword.php";
	}

	/**
	 * Forgot password (step 2).
	 * Page after successful forgot password request, confirming that an email has been sent
	 */
	public function passwordsentAction()
	{
		$lang = $this->lang;
		$message = __("Password reset instructions has been emailed to you. Follow the link in the email to reset your password.");
		$title = __("Forgot Password");
		$redirect = "/auth/login";

		include APPPATH."View/auth/message.php";
	}

	/**
	 * Forgot password (step 3).
	 * Page after clicking on a link from the email for forgot password.
	 * Changes user password.
	 */
	public function resetAction()
	{
		$lang = $this->lang;
		$error = false;

		if (Request::getMethod() == "POST") {
			$username = filter_input(INPUT_POST, "user");
			$token = filter_input(INPUT_POST, "token");
			$password = filter_input(INPUT_POST, "password");
			$password2 = filter_input(INPUT_POST, "password2");
			try {
				$this->modelAuth->resetPassword($username, $token, $password, $password2);
				Header("Location: /auth/resetdone");
				exit;
			} catch (AuthException $e) {
				Logger::notice($e->getLogMessage()." User: $username, Token: $token");
				$error = __($e->getMessage());
			}
		} else {
			$username = filter_input(INPUT_GET, "user");
			$token = filter_input(INPUT_GET, "token");
			try {
				$this->modelAuth->checkToken($username, $token);
			} catch (AuthException $e) {
				Logger::notice($e->getLogMessage()." User: $username, Token: $token");
				$error = __("The link is either wrong or outdated. Please check you have correctly copied the link or request a new password");
				$title = __("Forgot Password");
				$redirect = "/auth/forgotpassword";
				include APPPATH."View/auth/message.php";
				exit;
			}
		}

		include APPPATH."View/auth/reset.php";
	}

	/**
	 * Forgot password (step 4).
	 * Confirmation page after successful password reset.
	 */
	public function resetdoneAction()
	{
		$lang = $this->lang;
		$message = __("You can now log in with your new password.");
		$title = __("Forgot Password");
		$redirect = "/auth/login";

		include APPPATH."View/auth/message.php";
	}

	/**
	 * Change user password page
	 */
	public function changepassAction()
	{
		$lang = $this->lang;
		$error = false;
		$oldpass = filter_input(INPUT_POST, "oldpassword");
		$password = filter_input(INPUT_POST, "password");
		$password2 = filter_input(INPUT_POST, "password2");

		if (Request::getMethod() == "POST") {
			try {
				$this->modelAuth->changePassword($oldpass, $password, $password2);
				header("Location: /");
				exit;
			} catch (AuthException $e) {
				Logger::notice($e->getLogMessage());
				$error = __($e->getMessage());
			}
		}

		include APPPATH."View/auth/changepass.php";
	}

	/**
	 * Sending email for account activation.
	 *
	 * @param string $email
	 * @param string $username
	 * @param string $token Activation token
	 * @param string $lang
	 */
	public function sendActivationMail($email, $username, $token, $lang)
	{
		$url = Request::getScheme()."://".Request::getHost()."/auth/activate?user=$username&token=$token";
		if (!$body = File::get(APPPATH."mails/activation.{$lang}.txt")) {
			$body = File::get(APPPATH."mails/activation.en.txt", null);
		}
		$body = str_replace(array("{email}", "{username}", "{token}", "{lang}", "{url}"), array($email, $username, $token, $lang, $url), $body);
		if (!$html = File::get(APPPATH."mails/activation.{$lang}.html")) {
			$html =  File::get(APPPATH."mails/activation.en.html", null);
		}
		$html = str_replace(array("{email}", "{username}", "{token}", "{lang}", "{url}"), array($email, $username, $token, $lang, $url), $html);

		Logger::debug($body);

		if (!DEBUG) {
			return Mail::send($email, "Account activation", $body, $html, Config::get("mailer.from"));
		}

		return $body;
	}

	/**
	 * Sending email for forgot password.
	 *
	 * @param string $email
	 * @param string $username
	 * @param string $token Forgot password token
	 * @param string $lang  Language for the mail
	 */
	public function sendForgotPasswordMail($email, $username, $token, $lang)
	{
		$url = Request::getScheme()."://".Request::getHost()."/auth/reset?user=$username&token=$token";
		if (!$body = File::get(APPPATH."mails/forgot.{$lang}.txt")) {
			$body = File::get(APPPATH."mails/forgot.en.txt", null);
		}
		$body = str_replace(array("{email}", "{username}", "{token}", "{lang}", "{url}"), array($email, $username, $token, $lang, $url), $body);
		if (!$html = File::get(APPPATH."mails/forgot.{$lang}.html")) {
			$html =  File::get(APPPATH."mails/forgot.en.html", null);
		}
		$html = str_replace(array("{email}", "{username}", "{token}", "{lang}", "{url}"), array($email, $username, $token, $lang, $url), $html);


		Logger::debug($body);

		if (!DEBUG) {
			return Mail::send($email, "Forgot password request", $body, $html, Config::get("mailer.from"));
		}

		return $body;
	}
}
