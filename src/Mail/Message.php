<?php
namespace Asdf\Mail;

class Message extends \Nette\Mail\Message implements IMessage
{
	/**
	 * @var array
	 */
	public $onSend = array();

	public function send ()
	{
		if (!$this->getHeader('To')) {
			throw new Exception("call function 'addTo' before send email");
		}
		
		if (!$this->getFrom()) {
			throw new Exception("call function 'setFrom' before send email");
		}
		
		$this->onSend($this);
		
		parent::send();
	}
}
