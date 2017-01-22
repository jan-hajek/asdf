<?php
namespace Asdf\Application\Plugin;

use Asdf\Application\UI\BasePresenter;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\IResponse;

class BasePlugin implements IPlugin
{
	/**
	 * @inheritdoc
	 */
	public function onStartup (Application $application)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function onRequest (Application $application, Request $request)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function onPresenterCreate(Application $application, BasePresenter $presenter)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function onPresenterStartup (BasePresenter $presenter)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function onPresenterCreateTemplate (BasePresenter $presenter, ITemplate $template)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function onControlCreateTemplate (Control $control, ITemplate $template)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function beforePresenterRenderAction (BasePresenter $presenter)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function afterPresenterRenderAction (BasePresenter $presenter)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function onResponse(Application $application, IResponse $response)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function onShutdown (Application $application, \Throwable $exception = null)
	{
	}
}
