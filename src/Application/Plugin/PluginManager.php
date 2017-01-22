<?php
namespace Asdf\Application\Plugin;

use Asdf\Application\UI\BasePresenter;
use Asdf\Application\UI\Control;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\UI\ITemplate;
use Nette\Application\IResponse;

class PluginManager
{
	/**
	 * @var IPlugin[]
	 */
	protected $plugins;

	/**
	 * @param IPlugin[] $plugins
	 */
	public function __construct (array $plugins)
	{
		$this->plugins = $plugins;
	}

	/**
	 * @param IPlugin $plugin
	 */
	public function addPlugin(IPlugin $plugin)
	{
		$this->plugins[] = $plugin;
	}

	/**
	 * @param Application $sender
	 */
	public function onStartup (Application $sender)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onStartup($sender);
		}
	}

	/**
	 * @param Application $sender
	 * @param Request $request
	 */
	public function onRequest (Application $sender, Request $request)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onRequest($sender, $request);
		}
	}

	/**
	 * @param Application $application
	 * @param BasePresenter $presenter
	 */
	public function onPresenterCreate(Application $application, BasePresenter $presenter)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onPresenterCreate($application, $presenter);
		}
	}

	/**
	 * @param BasePresenter $presenter
	 */
	public function onPresenterStartup (BasePresenter $presenter)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onPresenterStartup($presenter);
		}
	}

	/**
	 * @param BasePresenter $presenter
	 * @param ITemplate $template
	 */
	public function onPresenterCreateTemplate (BasePresenter $presenter, ITemplate $template)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onPresenterCreateTemplate($presenter, $template);
		}
	}

	/**
	 * @param Control $control
	 * @param ITemplate $template
	 */
	public function onControlCreateTemplate (Control $control, ITemplate $template)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onControlCreateTemplate($control, $template);
		}
	}

	/**
	 * @param BasePresenter $presenter
	 */
	public function beforePresenterRenderAction (BasePresenter $presenter)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->beforePresenterRenderAction($presenter);
		}
	}

	/**
	 * @param BasePresenter $presenter
	 */
	public function afterPresenterRenderAction (BasePresenter $presenter)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->afterPresenterRenderAction($presenter);
		}
	}

	/**
	 * @param Application $application
	 * @param IResponse $response
	 */
	public function onResponse(Application $application, IResponse $response)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onResponse($application, $response);
		}
	}

	/**
	 * @param Application $application
	 * @param \Throwable $exception
	 */
	public function onShutdown (Application $application, \Throwable $exception = null)
	{
		foreach ($this->plugins as $plugin) {
			$plugin->onShutdown($application, $exception);
		}
	}
}
