<?php

namespace Asdf\Diagnostics\Panel;
use Tracy\IBarPanel;

/**
 * Session Nette Debug Panel
 * @author Pavel Železný <info@pavelzelezny.cz>
 */
class SessionPanel extends \Nette\Application\UI\Control implements IBarPanel
{
	/** @var \Nette\Http\Session $session */
	private $session;

	/** @var array $hiddenSection */
	private $hiddenSections = array();

	/**
	 * Constructor
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param \Nette\Application\Application $application
	 * @param \Nette\Http\Session $session
	 * @return void
	 */
	public function __construct(\Nette\Application\Application $application, \Nette\Http\Session $session)
	{
		parent::__construct($application->getPresenter(), $this->getId());
		$this->session = $session;
	}

	/**
	 * Add section name in list of hidden
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param string $sectionName
	 * @return void
	 */
	public function hideSection($sectionName)
	{
		$this->hiddenSections[] = $sectionName;
	}

	/**
	 * Return panel ID
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return string
	 */
	public function getId()
	{
		return __CLASS__;
	}

	/**
	 * Html code for DebugerBar Tab
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return string
	 */
	public function getTab()
	{
		$template = $this->getFileTemplate(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'tab.latte');
		ob_start();
		$template->render();
		return ob_get_clean();
	}

	/**
	 * Html code for DebugerBar Panel
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return string
	 */
	public function getPanel()
	{
		$template = $this->getFileTemplate(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'panel.latte');
		$template->session = $this->session->isStarted() ? $this->session : FALSE;
		$template->sessionMetaStore = isset($_SESSION['__NF']['META']) ? $_SESSION['__NF']['META'] : array();
		$template->sessionMaxTime = ini_get('session.gc_maxlifetime');
		$template->hiddenSections = $this->hiddenSections;
		return $template;
	}

	/**
	 * Load template file path with aditional macros and variables
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param string $templateFilePath
	 * @return \Nette\Templating\FileTemplate
	 * @throws \Nette\FileNotFoundException
	 */
	private function getFileTemplate($templateFilePath)
	{
		$template = new \Nette\Templating\FileTemplate($templateFilePath);
		$template->onPrepareFilters[] = callback($this, 'templatePrepareFilters');
		$template->registerHelperLoader('\Nette\Templating\Helpers::loader');
		$template->basePath = realpath(__DIR__);
		return $template;
	}

	/**
	 * Load latte and set aditional macros
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param \Nette\Templating\Template $template
	 * @return void
	 */
	public function templatePrepareFilters($template)
	{
		$template->registerFilter($latte = new \Nette\Latte\Engine());
		$set = new \Latte\Macros\MacroSet($latte->getCompiler());
		$set->addMacro('src', NULL, NULL, 'echo \'src="\'.\Nette\Templating\Helpers::dataStream(file_get_contents(%node.word)).\'"\'');
		$set->addMacro('stylesheet', 'echo \'<style type="text/css">\'.file_get_contents(%node.word).\'</style>\'');
		$set->addMacro('clickableDump', 'echo \Tracy\Dumper::toHtml(%node.word)');
	}

}