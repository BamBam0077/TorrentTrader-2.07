<?php
//
//  TorrentTrader v2.x
//  Mail functions
//  Author: TorrentialStorm
//
//    $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//
//    http://www.torrenttrader.org
//
//

$GLOBALS["TTMail"] = new TTMail;

class TTMail {
	var $type;

	var $smtp_host;
	var $smtp_port = 25;
	var $smtp_ssl = false;
	var $smtp_auth = false;

	var $smtp_user;
	var $smtp_pass;

	function TTMail () {
		GLOBAL $site_config;

		switch (strtolower($site_config["mail_type"])) {
			case "pear":
				$this->smtp_ssl = $site_config["mail_smtp_ssl"];

				if ($this->smtp_ssl) {
					$this->smtp_host = "ssl://".$site_config["mail_smtp_host"];
				} else {
					$this->smtp_host = $site_config["mail_smtp_host"];
				}
				$this->smtp_port = $site_config["mail_smtp_port"];
				$this->smtp_auth = $site_config["mail_smtp_auth"];
				$this->smtp_user = $site_config["mail_smtp_user"];
				$this->smtp_pass = $site_config["mail_smtp_pass"];

				if (!@include_once("Mail.php")) {
					trigger_error("Config is set to use PEAR Mail but it is not installed (or include_path is wrong).", E_USER_WARNING);
					$this->type = "php";
				}
			break;
			case "php":
			default:
				$this->type = "php";
		}
	}

	function Send ($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
		GLOBAL $site_config;

		if (preg_match("!^From:(.*)!m", $additional_headers, $matches)) {
			$from = trim($matches[1]);
		} else {
			$from = "$site_config[SITENAME] <$site_config[SITEEMAIL]>";
		}

		$additional_headers = preg_replace("!^From:(.*)!m", "", $additional_headers);
		$additional_headers .= "\nFrom: $from\nReturn-Path: $from";

		switch ($this->type) {
			case "pear":
				$headers = array("From" => $from, "Return-Path" => $from, "To" => $to, "Subject" => $subject);
				$params = array("host" => $host, "port" => $this->smtp_port, "auth" => $this->smtp_auth, "username" => $this->smtp_user, "password" => $this->smtp_pass);
				$smtp = Mail::Factory("smtp", $params);

				$mail = $smtp->send($to, $headers, $message);
			break;
			case "php":
				@mail($to, $subject, $message, $additional_headers, $additional_parameters);
			break;
		}
	}
}

function sendmail ($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
	$GLOBALS["TTMail"]->Send($to, $subject, $message, $additional_headers, $additional_parameters);
}
?>