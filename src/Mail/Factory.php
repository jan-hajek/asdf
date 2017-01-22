<?php
namespace Asdf\Mail;

class Factory extends \Nette\Object
{
    public $onCreateMail = array();
    
    public $onSendMail = array();

	private $mailer;
	
	private $params;

	public function __construct (array $params, \Nette\Mail\IMailer $mailer)
	{
		$this->params = $params;
		$this->mailer = $mailer;
	}

	public function createMail ()
	{
		if (array_key_exists('testRecipients', $this->params)) {
			$testRecipients = $this->params['testRecipients'];
			if ($testRecipients === NULL || (is_array($testRecipients) && !count($testRecipients))) {
				throw new Exception("param mail:testRecipients: exists but is empty");
			}
			$message = new TestMessage($testRecipients);
		} else {
			$message = new Message();
		}
		
		$message->setMailer($this->mailer);
	
		foreach ($this->onSendMail as $functions) {
			$message->onSend[] = $functions;
		}
		
		$this->onCreateMail($message);
		
		return $message;
	}
}