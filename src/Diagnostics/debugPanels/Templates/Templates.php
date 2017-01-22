<?php
namespace Asdf\Diagnostics\Panel;

use Tracy\IBarPanel;
use Asdf\Application\Plugin\PluginManager;
use Asdf\Application\Plugin\BasePlugin;
use Asdf\Application\UI\BasePresenter;
use Asdf\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;

class Templates extends BasePlugin implements IBarPanel
{
	public $templates = array();

	public function __construct (PluginManager $pluginManager)
	{
		$pluginManager->addPlugin($this);
	}

	public function onControlCreateTemplate (Control $control, Template $template)
	{
		$this->templates[] = $template;
	}

	public function onPresenterCreateTemplate (BasePresenter $control, Template $template)
	{
		$this->templates[] = $template;
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

