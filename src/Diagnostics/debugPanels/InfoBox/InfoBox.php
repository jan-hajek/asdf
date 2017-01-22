<?php
namespace Asdf\Diagnostics\Panel;

use Tracy\Debugger;
use Tracy\IBarPanel;

class InfoBox implements IBarPanel
{
	private $messages = array();
	private $mailFactory;

	public function __construct (\Asdf\Mail\Factory $mailFactory)
	{
		$this->mailFactory = $mailFactory;
	}

	private function getWarningsCount ()
	{
		$count = 0;
		foreach ($this->messages as $message) {
			if ($message['type'] == 'warning') {
				$count ++;
			}
		}
		return $count;
	}

	private function setMessages ()
	{
		$this->checkMail();
		$this->checkDb();
	}

	private function checkMail ()
	{
		try {
			$mailer = $this->getPrivatePropertyValue($this->mailFactory, 'mailer');
			$mailerDump = \Nette\Diagnostics\Dumper::toHtml($mailer);
			$mail = $this->mailFactory->createMail();
			$testRecipients = $this->getPrivatePropertyValue($mail, 'testRecipients');

			$this->addMessage('mail', $mailerDump);
			if ($testRecipients) {
				$testRecipients = implode('<br />', $testRecipients);
				$this->addMessage('mail - test recipients', $testRecipients);
			}
		} catch (\Exception $e) {
			$this->addMessage('mail', $e->getMessage(), 'warning');
		}
	}

	private function checkDb ()
	{
	}

	private function addMessage ($title, $message, $type = 'notice')
	{
		$this->messages[] = array(
				'title' => $title,
				'message' => is_array($message) ? Debugger::dump($message, TRUE) : $message,
				'type' => $type
		);
	}

	private function getPrivatePropertyValue ($object, $name)
	{
		$prop = \Nette\Reflection\ClassType::from($object)->getProperty($name);
		$prop->setAccessible(TRUE);
		return $prop->getValue($object);
	}


	public function getTab ()
	{
		$this->setMessages();
		ob_start();
		require __DIR__ . '/templates/tab.phtml';
		return ob_get_clean();
	}

	public function getId ()
	{
		return __CLASS__;
	}

	public function getPanel ()
	{
		ob_start();
		require __DIR__ . '/templates/panel.phtml';
		return ob_get_clean();
	}
}

