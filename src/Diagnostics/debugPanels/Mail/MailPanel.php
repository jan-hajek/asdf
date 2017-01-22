<?php

/**
 * Extension for Nette debugger bar which shows sent e-mails
 *
 * @author Jan Drábek
 * @version 2.0
 * @copyright New BSD
 */
namespace Asdf\Diagnostics\Panel;

use Nette;
use Asdf\Mail\Factory;
use Nette\Application\UI\Control;
use Tracy\IBarPanel;
use Nette\Http\SessionSection;
use Asdf\Mail\IMessage;
use Nette\Http\Request;
use Nette\Utils\Callback;

class MailPanel extends Control implements IBarPanel
{

	private $request;

	private $sessionSection;

	/**
	 * Create this panel and handle request for actions (trasmitted in requests)
	 *
	 * @param Nette\Http\Request $request
	 * @param Nette\Http\Session $session
	 */
	public function __construct (Factory $mailFactory, Request $request, SessionSection $sessionSection)
	{
		$this->request = $request;
		$this->sessionSection = $sessionSection;

		$what = $request->getQuery("mail-panel");
		switch ($what) {
			case 'delete':
				$this->handleDeleteAll();
			break;
			case 'less':
				$this->handleShowLess(self::DEFAULT_COUNT);
			break;
			case 'more':
				$this->handleShowMore(self::DEFAULT_COUNT);
			break;
			default:
			break;
		}
		if (is_numeric($what) && $what >= 0) {
			$this->handleDelete($what);
		}

		$mailFactory->onSendMail[] = Callback::closure($this, 'addMail');
	}

	const DEFAULT_COUNT = 3;

	/**
	 * Returns panel ID.
	 *
	 * @return string
	 */
	public function getId ()
	{
		return __CLASS__;
	}

	/**
	 * Renders HTML code for custom tab.
	 *
	 * @return string
	 */
	public function getTab ()
	{
		return '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAITSURBVBgZpcHLThNhGIDh9/vn7/RApwc5VCmFWBPi1mvwAlx7BW69Afeu3bozcSE7E02ILjCRhRrds8AEbKVS2gIdSjvTmf+TYqLu+zyiqszDMCf75PnnnVwhuNcLpwsXk8Q4BYeSOsWpkqrinJI6JXVK6lSRdDq9PO+19vb37XK13Hj0YLMUTVVyWY//Cf8IVwQEGEeJN47S1YdPo4npDpNmnDh5udOh1YsZRcph39EaONpnjs65oxsqvZEyTaHdj3n2psPpKDLBcuOOGUWpZDOG+q0S7751ObuYUisJGQ98T/Ct4Fuo5IX+MGZr95jKjRKLlSxXxFxOEmaaN4us1Upsf+1yGk5ZKhp8C74H5ZwwCGO2drssLZZo1ouIcs2MJikz1oPmapHlaoFXH1oMwphyTghyQj+MefG+RblcoLlaJG/5y4zGCTMikEwTctaxXq/w9kuXdm9Cuzfh9acujXqFwE8xmuBb/hCwl1GKAnGccDwIadQCfD9DZ5Dj494QA2w2qtQW84wmMZ1eyFI1QBVQwV5GiaZOpdsPaSwH5HMZULi9UmB9pYAAouBQbMHHrgQcnQwZV/KgTu1o8PMgipONu2t5KeaNiEkxgAiICDMCCFeEK5aNauAOfoXx8KR9ZOOLk8P7j7er2WBhwWY9sdbDeIJnwBjBWBBAhGsCmiZxPD4/7Z98b/0QVWUehjkZ5vQb/Un5e/DIsVsAAAAASUVORK5CYII=" />';
	}

	/**
	 * Show content of panel
	 *
	 * @return type
	 */
	public function getPanel ()
	{
		ob_start();
		// Get data from session
		$section = $this->getStorage();

		$template = new \Nette\Templating\FileTemplate(__DIR__ . "/MailPanel.latte");
		$template->registerFilter(new \Latte\Engine());
		$template->data = array();
		$template->urlPath = $this->request->getUrl()->getPath();
		if ($section->offsetExists("queue") && $section->offsetGet("queue") instanceof Nette\ArrayList) {
			$template->data = $section->queue;
			if (! $section->offsetExists("count")) {
				$section->count = self::DEFAULT_COUNT;
			}
			$template->count = $section->count;
		}
		$template->render();
		return ob_get_clean();
	}

	public function addMail (IMessage $message)
	{
		$storage = $this->getStorage();
		if ($storage === NULL) {
			throw new Nette\InvalidStateException("No session given into mailer. Cannot send this message.");
		}
		if ($storage->queue === NULL || ! $storage->queue instanceof Nette\ArrayList) {
			$storage->queue = new Nette\ArrayList();
		}
		$queue = $storage->queue;
		$message->onSend = array();
		$queue[] = $message;
	}

	private function getStorage ()
	{
		return $this->sessionSection;
	}

	private function returnWay ()
	{
		header("Location: " . $this->request->getReferer());
		exit();
	}

	private function handleDeleteAll ()
	{
		unset($this->getStorage()->queue);
		$this->returnWay();
	}

	private function handleDelete ($id)
	{
		$storage = $this->getStorage();
		foreach ($storage->queue as $key => $row) {
			if ($key == $id){
				$storage->queue->offsetUnset($key);
			}
		}
		$this->returnWay();
	}

	private function handleShowMore ($count)
	{
		if (! is_numeric($count) || $count <= 0) {
			return;
		}
		$storage = $this->getStorage();
		if (! $storage->offsetExists("count")) {
			$storage->count = self::DEFAULT_COUNT;
		}
		$storage->count = $storage->count + $count;
		$this->returnWay();
	}

	private function handleShowLess ($count)
	{
		if (! is_numeric($count) || $count <= 0) {
			return;
		}
		$storage = $this->getStorage();
		if (! $storage->offsetExists("count")) {
			$storage->count = MailPanel::DEFAULT_COUNT;
		}
		if ($storage->count - $count < 0) {
			$this->returnWay();
		}
		$storage->count = $storage->count - $count;
		$this->returnWay();
	}
}