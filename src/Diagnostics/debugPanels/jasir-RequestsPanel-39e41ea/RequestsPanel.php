<?php
/**
 * @author jasir
 * @license WTFPL (http://en.wikipedia.org/wiki/WTFPL)
 */
namespace Asdf\Diagnostics\Panel;

use Tracy\Dumper;
use Nette\Object;
use Tracy\Debugger;
use Tracy\IBarPanel;
use Nette\Utils\Html;
use Nette\Application\Responses\TextResponse;

class RequestsPanel extends Object implements IBarPanel {

	private $httpRequest;
	private $sessionSection;
	private $response;

	public function __construct(
		\Nette\Application\Application $application,
		\Nette\Http\SessionSection $sessionSection,
		\Nette\Http\Request $httpRequest
	)
	{
		$this->httpRequest = $httpRequest;
		$this->sessionSection = $sessionSection;
		$application->onResponse[] = array($this, 'onResponse');
	}

	/**
	 * Renders HTML code for custom tab.
	 * @return string
	 * @see IDebugPanel::getTab()
	 */
	public function getTab() {
		$logs = $this->sessionSection->logs;
		$s  = '<span title="Requests">';
		$s .= '<img src="data:image/gif;base64,R0lGODlhEAAQAKUkAAAAAIUlEqEtFqkvFrMxGEJdc0VheME1GklngE1shk9vit09HlR2k1d6mOZjSehvV+yKd+2SgJuyxqK3yam9zqu+zrHD0vOzpvO4rPXEusnV4MzX4c/a5PfOxtLc5dXe5vjUzfjWz9ri6dvj6v///////////////////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEKAD8ALAAAAAAQABAAAAZiwJ/wBxgaj0IAqIg8AkIRArNJ7EQih8HU2Vk8IIJAYDsEmC8RgZlsBGDSzLW5nYEnPRXGFhBxqJMcEwV7ckkbgmxlZhqIc0gAHxQWEgkNCYlEHxMTCgaYSSMTCJ9lIqRtRkEAOw%3D%3D">';
		$s .= ($cnt = count($logs)) > 1 ? Html::el('span')->class('nette-warning')->add("[$cnt]") : "[1]";
		$s .= '</span>';
		return $s;
	}

	/**
	 * Renders HTML code for custom panel.
	 * @return string
	 * @see IDebugPanel::getPanel()
	 */
	public function getPanel() {
		$session = $this->sessionSection;
		$logs = $session->logs;
		if ($this->response instanceOf TextResponse ) {
			unset($session->logs);
			ob_start();
			require dirname(__FILE__) . '/bar.requests.panel.phtml';
			return ob_get_clean();
		}
	}

	/**
	 * Returns panel ID.
	 * @return string
	 * @see IDebugPanel::getId()
	 */
	public function getId() {
		return __CLASS__;
	}

	/**
	 * @param $presenter Presenter
	 * @param $response PresenterResponse
	 * @internal
	 */
	public function onResponse(\Nette\Application\Application $application, $response) {
		$this->response = $response;

		$presenter   = $application->getPresenter();
		$request     = $presenter->getRequest();
		$httpRequest = $this->httpRequest;

		$entry = array();

		if ($signal = $presenter->getSignal()) {
			$receiver = empty($signal[0]) ? $presenter->name : $signal[0];
			$signal = $receiver . " :: " . $signal[1];
		}

		if ($response !== NULL) {
			$rInfo = get_class($response);
			if ($response->getReflection()->hasMethod('getCode')) {
				$rInfo .= ' (' . $response->code . ')';
			}
		}

		$entry['info']['presenter'] = $presenter->backlink();
		$entry['info']['response']  = $response === NULL ? 'NO RESPONSE' : $rInfo;
		$entry['info']['uri']       = $httpRequest->getUrl();
		$entry['info']['uriPath']   = $httpRequest->getUrl()->path;
		$entry['info']['request']   = $request->getMethod();
		$entry['info']['signal']    = $signal;
		$entry['info']['time']      = number_format((microtime(TRUE) - Debugger::$time) * 1000, 1, '.', ' ');


		$entry['dumps']['HttpRequest']       = Dumper::toHtml($httpRequest, array(\Tracy\Dumper::COLLAPSE => true));
		$entry['dumps']['PresenterRequest']  = Dumper::toHtml($request, array(\Tracy\Dumper::COLLAPSE => true));
		$entry['dumps']['Presenter']         = Dumper::toHtml($presenter, array(\Tracy\Dumper::COLLAPSE => true));
		$entry['dumps']['PresenterResponse'] = Dumper::toHtml($response, array(\Tracy\Dumper::COLLAPSE => true));

		$session = $this->sessionSection;

		if (!isset($session->logs)) {
			$session->logs = array();
		}
		$session->logs[] = $entry;
	}

}
