<?php
namespace Asdf\Diagnostics\Panel;

use Asdf\Application\Plugin\PluginManager;
use Asdf\Application\Plugin\IPlugin;
use Asdf\Application\UI\Control;
use Asdf\Application\UI\BasePresenter;
use Tracy\IBarPanel;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\IResponse;
use Nette\Bridges\ApplicationLatte\Template;

class Plugins implements IBarPanel, IPlugin
{
	private $pluginManager;
	private $actions = array();

	public function __construct (PluginManager $pluginManager)
	{
		$this->pluginManager = $pluginManager;
		$pluginManager->addPlugin($this);
		$this->actions[] = array(
				'method' => '__construct',
				'params' => array(),
		);
	}


	////// odchyceni akci

	public function onStartup (Application $application)
	{
		$this->actions[] = array(
				'method' => 'onStartup',
				'params' => array($application)
		);
	}

	public function onRequest (Application $application, Request $request)
	{
		$this->actions[] = array(
				'method' => 'onRequest',
				'params' => array($application, $request)
		);
	}

	public function onPresenterCreate (Application $application, BasePresenter $presenter)
	{
		$this->actions[] = array(
				'method' => 'onPresenterCreate',
				'params' => array($application, $presenter)
		);
	}

	public function onPresenterStartup (BasePresenter $presenter)
	{
		$this->actions[] = array(
				'method' => 'onPresenterStartup',
				'params' => array($presenter)
		);
	}

	public function onPresenterCreateTemplate (BasePresenter $presenter, Template $template)
	{
		$this->actions[] = array(
				'method' => 'onPresenterCreateTemplate',
				'params' => array($presenter, $template)
		);
	}

	public function onControlCreateTemplate (Control $control, Template $template)
	{
		$this->actions[] = array(
				'method' => 'onControlCreateTemplate',
				'params' => array($control, $template)
		);
	}

	public function beforePresenterRenderAction (BasePresenter $presenter)
	{
		$this->actions[] = array(
				'method' => 'beforePresenterRenderAction',
				'params' => array($presenter)
		);
	}

	public function afterPresenterRenderAction (BasePresenter $presenter)
	{
		$this->actions[] = array(
				'method' => 'afterPresenterRenderAction',
				'params' => array($presenter)
		);
	}

	public function onResponse(Application $application, IResponse $response)
	{
		$this->actions[] = array(
				'method' => 'onResponse',
				'params' => array($application, $response)
		);
	}

	public function onShutdown (Application $application, \Throwable $exception = null)
	{
		$this->actions[] = array(
				'method' => 'onShutdown',
				'params' => array($application, $exception)
		);
	}


	public function process ()
	{
		$pluginsByMethods = array();
		foreach ($this->getPlugins() as $plugin) {
			if ($plugin == $this) {
				continue;
			}
			foreach ($this->getPluginMethods($plugin) as $methodName) {
				if (!isset($pluginsByMethods[$methodName])) {
					$pluginsByMethods[$methodName] = array();
				}
				$pluginsByMethods[$methodName][] = $this->getPluginInfo($plugin);
			}
		}

		foreach ($this->actions as &$action) {
			$methodName = $action['method'];
			$action['plugins'] = isset($pluginsByMethods[$methodName]) ? $pluginsByMethods[$methodName]: array();
		}
	}

	private function getPlugins()
	{
		$property = $this->pluginManager->getReflection()->getProperty('plugins');
		$property->setAccessible(true);
		return $property->getValue($this->pluginManager);
	}

	private function getPluginInfo(IPlugin $plugin)
	{
		$reflection = $plugin->getReflection();
		return array(
				'name' => $reflection->getName(),
				'path' => $reflection->getFileName(),
		);
	}

	private function getPluginMethods(IPlugin $plugin)
	{
		return $this->getObjectOwnMethods($plugin->getReflection());
	}

	private function getObjectOwnMethods(\ReflectionClass $class)
	{
		$className = $class->getName();
		$methods = $class->getMethods();
		$return = array();
		foreach ($methods as $method) {
			if ($method->class == $className) {
				$return[] = $method->name;
			}
		}
		return $return;
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
		$this->process();
		ob_start();
		require __DIR__ . '/templates/panel.phtml';
		return ob_get_clean();
	}
}

