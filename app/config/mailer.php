<?php
/**
 * Configuration for Swift Mailer used via SugiPHP\Sugi\Mail
 *
 * @package  sugi example
 * @category config
 */

return array(
	// what transport to use
	"transport" => "mail",
	// nothing to set
	"mail"      => array(),
	// settings for sendmail transport
	"sendmail"  => array(
		"path"      => "/usr/sbin/sendmail -bs",
	),
	// settings for SMTP transport
	"smtp"      => array(
		"host"      => "smtp.example.com",
		"port"      => 25, // optional defaults to 25 (465 for SSL)
		"username"  => "", // optional
		"password"  => "", // optional
		"security"	=> NULL, // NULL - default, "ssl", "tls"
	),
	// some other default settings
	"from"      => array("foo@example.com" => "SugiPHP"),
	"returnTo"  => "noreply@example.com",
);
