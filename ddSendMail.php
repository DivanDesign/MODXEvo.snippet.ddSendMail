<?php
/**
 * ddSendMail.php
 * @version 1.5.4 (2014-07-13)
 * 
 * @desc Snippet for sending e-mails.
 * 
 * @uses The library MODX.ddTools 0.13.
 * 
 * @param $email {comma separated string} - Addresses to mail. @required
 * @param $subject {string} - E-mail subject. Default: 'Mail from '.$modx->config['site_url'].
 * @param $text {string} - E-mail text. @required
 * @param $from {string} - Mailer address. Default: 'info@divandesign.ru'.
 * @param $inputName {comma separated string} - “input” tags names from which accepted files are taken. Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/ddsendmail/1.5.4
 * 
 * @copyright 2014, DivanDesign
 * http://www.DivanDesign.biz
 */

//Подключаем modx.ddTools
require_once $modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php';

if (empty($email)){
	$modx->logEvent(1, 2, "Parameter 'email' is required. Called on document id ".$modx->documentIdentifier.'.', 'ddSendMail');

	return false;
}

if (!isset($text)){
	$modx->logEvent(1, 2, "Parameter 'text' is required. Called on document id ".$modx->documentIdentifier.'.', 'ddSendMail');

	return false;
}

$result = ddTools::sendMail(explode(',', $email), $text, $from, $subject, explode(',', $inputName));

return count($result) > 1 ? json_encode($result) : $result[0];
?>