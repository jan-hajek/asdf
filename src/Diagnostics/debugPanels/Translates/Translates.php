<?php
namespace Asdf\Diagnostics\Panel;

use Tracy\IBarPanel;
use Tracy\Helpers;
use Asdf\Localization\ITranslator;

class Translates implements IBarPanel
{
	private $files = array();
	private $translates = array();
	private $untranslated = array();

	public function __construct(ITranslator $translator)
	{
		$self = $this;
		$translator->onTranslate(
			function($message, $count, $translated) use ($self) {
				if ($translated == null) {
					$backtrace = debug_backtrace();
					$step = 9;
					$file = isset($backtrace[$step]['file']) ? $backtrace[$step]['file'] : null;
					$line = isset($backtrace[$step]['line']) ? $backtrace[$step]['line'] : null;
					$self->addUntranslated($message, $count, $file, $line);
				}
			}
		);
	}

	public function addLangFile ($path)
	{
		$this->files[] = '<a href="' . Helpers::editorLink($path, 1) . '">' . str_replace(BASE_DIR, '', $path) . '</a>';
	}

	public function addUntranslated ($message, $count, $file, $line)
	{
		$this->untranslated[] = array(
			'id' => $message,
			'count' => $count,
			'path' => Helpers::editorLink($file, $line),
		);
	}

	public function getTab ()
	{
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


