<?php
namespace Asdf\Log;

use Nette\Http\UrlScript;
use Nette\Security\User;

class Logger extends \Nette\Object implements ILogger
{
	protected $params;
	protected $writerFactory;
	protected $user;
	protected $decoratorsFactory;
	protected $urlScript;
	
	public function __construct (
		array $params,
		User $user,
		Writers\Factory $writerFactory,
		Decorators\Factory $decoratorsFactory,
		UrlScript $urlScript
	)
	{
		$this->user = $user;
		$this->params = $params;
		$this->writerFactory = $writerFactory;
		$this->decoratorsFactory = $decoratorsFactory;
		$this->urlScript = $urlScript;
	}

	public function log ($message, $title, $namespaceName)
	{
		$namespace = $this->getNamespaceByName($namespaceName);
		
		$hash = NULL;
		if (count($namespace)) {
			$date = time();
			
			$userId = $this->getUserId();
			$url = $this->getUrl();
			
			$hash = \Nette\Utils\Strings::random(10);
			list ($file, $line) = $this->getFileAndLine();
			
			foreach ($namespace as $info) {
				if ($info['enabled']) {
					$decorator = $this->getDecoratorByName($info['decorator']);
					$writer = $this->getWritter($info['writer']['name'], $info['writer']);
					
					$text = $decorator->decorate($hash, $userId, $date, $title, $message, $file, $line, $url);
					$writer->write($text);
				}
			}
		
		}
	}

	private function getUserId ()
	{
		return $this->user->getId();
	}

	private function getUrl ()
	{
		return $this->urlScript->getAbsoluteUrl();
	}

	private function getDecoratorByName ($name)
	{
		return $this->decoratorsFactory->getDecorator($name);
	}

	private function getWritter ($name, array $params)
	{
		return $this->writerFactory->getWriter($name, $params);
	}

	private function getFileAndLine ()
	{
		$backtrace = debug_backtrace(FALSE);
		
		$file = NULL;
		$line = NULL;
		foreach ($backtrace as $trace) {
			if (isset($trace['class'])) {
				$file = isset($trace['file']) ? $trace['file'] : NULL;
				$line = isset($trace['line']) ? $trace['line'] : NULL;
				break;
			}
		}
		return array(
					$file,
					$line
		);
	}

	private function getNamespaceByName ($name)
	{
		if (isset($this->params['namespaces'][$name])) {
			return $this->params['namespaces'][$name];
		}
		throw new Exception("Namespace s nazvem '$name' neexistuje");
	}
}
