<?php
namespace Asdf\Application\Plugin;

use Asdf\Application\UI\BasePresenter;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\IResponse;

interface IPlugin
{
	/**
	 * first function called in \Nette\Application\Application::run()
	 *
	 * @param Application $application
	 * @return void
	 */
	public function onStartup (Application $application);

	/**
	 * called after route match
	 *
	 * @param Application $application
	 * @param Request $request
	 * @return void
	 */
	public function onRequest (Application $application, Request $request);

	/**
	 * called after presenter created
	 *
	 * @param Application $application
	 * @param BasePresenter $presenter
	 */
	public function onPresenterCreate(Application $application, BasePresenter $presenter);

	/**
	 * called after presenter run
	 *
	 * @param BasePresenter $presenter
	 * @return void
	 */
	public function onPresenterStartup (BasePresenter $presenter);

	/**
	 * called when template created in presenter
	 *
	 * @param BasePresenter $presenter
	 * @param ITemplate $template
	 * @return void
	 */
	public function onPresenterCreateTemplate (BasePresenter $presenter, ITemplate $template);

	/**
	 * called when template created in control
	 *
	 * @param Control $control
	 * @param ITemplate $template
	 * @return void
	 */
	public function onControlCreateTemplate (Control $control, ITemplate $template);

	/**
	 * called before controller render action
	 *
	 * @param BasePresenter $presenter
	 * @return void
	 */
	public function beforePresenterRenderAction (BasePresenter $presenter);

	/**
	 * called after controller render action
	 *
	 * @param BasePresenter $presenter
	 * @return void
	 */
	public function afterPresenterRenderAction (BasePresenter $presenter);

	/**
	 * called when a new response is ready for dispatch
	 *
	 * @param Application $application
	 * @param IResponse $response
	 * @return void
	 */
	public function onResponse(Application $application, IResponse $response);

	/**
	 * last function called in \Nette\Application\Application::run()
	 * could by called multiple times in case of exception
	 *
	 * @param Application $application
	 * @param \Throwable $exception
	 * @return void
	 */
	public function onShutdown (Application $application, \Throwable $exception = null);

}
