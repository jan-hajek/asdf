<?php
namespace Asdf\Log\Writers;

class Mail implements IWriter
{

	/**
	 * @var \Asdf\Mail\IMessage
	 */
	protected $mail;

	protected $recipients;

	protected $from;

	protected $subject;

	/**
	 * vytvoreni writteru
	 *
	 * @param IMessage $mail	   trida pro posilani mailu
	 * @param array	   $recipients prijemci
	 * @param string   $subject    subjekt mailu
	 * @param string   $from	   odesilatel emailu
	 *
	 * @return void
	 */
	public function __construct (\Asdf\Mail\IMessage $mail, array $recipients, $subject, $from)
	{
		$this->mail = $mail;
		$this->recipients = $recipients;
		$this->from = $from;
		$this->subject = $subject;
	}

	/**
	 * funkce ktera zapise vstupni text na prislusne misto (mail, soubor, db apod.)
	 *
	 * @param string $message text
	 *
	 * @return void
	 */
	public function write ($message)
	{
		foreach ($this->recipients as $recipient) {
			$mail = clone $this->mail;
			$mail->setFrom($this->from)
				->addTo($recipient)
				->setSubject($this->subject)
				->setHTMLBody($message)
				->send();
		}
	}
}