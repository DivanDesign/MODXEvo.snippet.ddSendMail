<?php
/**
 * ddSendMail.php
 * @version 1.5.3 (2014-04-04)
 * 
 * @desc Snippet for sending e-mails.
 * 
 * @param $email {comma separated string} - Addresses to mail. @required
 * @param $subject {string} - E-mail subject. Default: 'Mail from '.$modx->config['site_url'].
 * @param $text {string} - E-mail text. @required
 * @param $from {string} - Mailer address. Default: 'info@divandesign.ru'.
 * @param $inputName {comma separated string} - “input” tags names from which accepted files are taken. Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/ddsendmail/1.5.3
 * 
 * @copyright 2014, DivanDesign
 * http://www.DivanDesign.biz
 */

if (!isset($email)){
	$modx->logEvent(1, 2, "Parameter 'email' is required. Called on document id ".$modx->documentIdentifier.'.', 'ddSendMail');
	
	return false;
}

//Тема письма
$subject = isset($subject) ? $subject : 'Mail from '.$modx->config['site_url'];
//Конвертируем тему в base64
$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
//От кого
$from = isset($from) ? $from : 'info@divandesign.ru';
//Заголовки сообщения
$headers = "From: $from\r\nMIME-Version: 1.0\r\n";

//Разделитель блоков в сообщении
$bound = 'bound'.md5(time());
$multipart = "Content-Type: multipart/mixed; boundary = \"".$bound."\"\r\n\r\n--".$bound;

//Добавлеям текст в сообщения
if(isset($text)){
	$multipart .= "\r\nContent-Type: text/html; charset=UTF-8 \r\n\r\n".$text."\r\n\r\n--".$bound;
}

if(isset($inputName)){
	$inputName = explode(',', $inputName);
	
	//Перебираем имена полей с файлами
	$attachFiles = array();
	foreach($inputName as $value){
		//Проверяем находиться ли в POST массив
		if(is_array($_FILES[$value]['name'])){
			//Если массив пустой обрываем итерацию
			if(!$_FILES[$value]['tmp_name'][0]){break;}
			
			//Перебираем пост
			foreach($_FILES[$value]['name'] as $key => $name){
				//Если нет ошибок
				if ($_FILES[$value]['error'][$key] == 0){
					//Добавляем в массив файлы
					$attachFiles[$name] = fread(fopen($_FILES[$value]['tmp_name'][$key], 'r'), filesize($_FILES[$value]['tmp_name'][$key]));
				}
			}
		}else{
			//Если массив пустой обрываем итерацию
			if(!$_FILES[$value]['tmp_name']){break;}
			//Если нет ошибок
			if ($_FILES[$value]['error'] == 0){
				//Если не массив, то добавляем один файл
				$attachFiles[$_FILES[$value]['name']] = fread(fopen($_FILES[$value]['tmp_name'], 'r'), filesize($_FILES[$value]['tmp_name']));
			}
		}
	}
	
	//Перебираем присоединяемые файлы
	if(!empty($attachFiles)){
		foreach($attachFiles as $name=>$value){
			$multipart .= "\r\n".
				'Content-Type: application/octet-stream; name = "=?UTF-8?B?'.base64_encode($name)."?=\"\r\n".
				"Content-Transfer-Encoding: base64\r\n\r\n".
				base64_encode($value)."\r\n\r\n--".$bound;
		}
	}
}

//Добавляем разделитель окончания сообщения
$headers .= $multipart."--\r\n";

$result = array();

$email = explode(',', $email);
foreach ($email as $val){
	//Если адрес валидный
	if (filter_var($val, FILTER_VALIDATE_EMAIL)){
		//Отправляем письмо 
		if(mail($val, $subject, '', $headers)){
			$result[] = 1;
		}else{
			$result[] = 0;
		}
	}
}

return count($result) > 1 ? json_encode($result) : $result[0];
?>