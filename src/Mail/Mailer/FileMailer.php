<?php
namespace Asdf\Mail;

class FileMailer extends \Nette\Object implements \Nette\Mail\IMailer
{
	private $dirName;

	public function __construct($dirName)
	{
		if (!file_exists($dirName) || !is_dir($dirName)) {
			throw new Exception("dir $dirName name not exists");
		}
		$this->dirName = $dirName;
	}

	public function send (\Nette\Mail\Message $mail)
	{
		$path = realpath($this->dirName) . "/" . $this->createFileName($mail);

		$f = fopen($path, 'w');
		chmod($path, 0777);

		fwrite($f, $mail->getHtmlBody());
		fclose($f);
	}

	private function createFileName(\Nette\Mail\Message $mail)
	{
		$fileName = date('Y-m-d_His') . '-' . $mail->getSubject() . '.html';
		return $this->sanitize($fileName);
	}

	private function sanitize ($string)
	{
		$string = preg_replace('/[^\w\-' . ('~_\.') . ']+/u', '-', $string);
		return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
	}
}