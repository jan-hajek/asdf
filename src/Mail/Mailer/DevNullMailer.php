<?php
namespace Asdf\Mail;

class DevNullMailer extends \Nette\Object implements \Nette\Mail\IMailer
{

	public function send (\Nette\Mail\Message $mail)
	{
	}
}