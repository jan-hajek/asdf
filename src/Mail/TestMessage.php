<?php
namespace Asdf\Mail;

class TestMessage extends Message
{
	private $testRecipients;

	public function __construct(array $testRecipients)
	{
		$this->testRecipients = $testRecipients;
	}

	/**
	 * odesle mail
	 *
	 *  @return void
	 */
	public function send ()
	{
		if (!$this->getHeader('To')) {
			throw new Exception("call function 'addTo' before send email");
		}
		
		if (!$this->getFrom()) {
			throw new Exception("call function 'setFrom' before send email");
		}
		
		$to = $this->getHeader('To');
		$cc = $this->getHeader('Cc');
		$bcc = $this->getHeader('Bcc');
		$replyTo = $this->getHeader('Reply-To');
		
		$this->clearHeader('To');
		$this->clearHeader('Cc');
		$this->clearHeader('Bcc');
		$this->clearHeader('Reply-To');
		
		$body = $this->getHtmlBody();
		
		$body .= "<br /><br /><hr /><br />";
		
		$body .= "Email by měl být doručen na tyto adresy:<br />";
		$body = $this->addToBodyFunction($to, $body);
		
		if ($cc) {
			$body .= "<br />Jako Cc:<br />";
			$body = $this->addToBodyFunction($cc, $body);
		}
		
		if ($bcc) {
			$body .= "<br />Jako Bcc:<br />";
			$body = $this->addToBodyFunction($bcc, $body);
		}
		
		if ($replyTo) {
			$body .= "<br />Jako Reply-To:<br />";
			$body = $this->addToBodyFunction($replyTo, $body);
		}
		
		$this->setHtmlBody($body);
		
		foreach ($this->testRecipients as $email) {
			$this->addTo($email);
		}
		
		parent::send();
	}

	protected function addToBodyFunction ($emails, $body)
	{
		foreach ($emails as $email => $name) {
			if ($name == '') {
				$body .= "$email<br />";
			} else {
				$body .= "$name<$email><br />";
			}
		}
		
		return $body;
	}
	
}
