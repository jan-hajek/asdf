<?php
namespace Asdf\Application\UI;

use Asdf\Application\Plugin\PluginManager;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
	/**
	 * @inheritdoc
	 */
	protected function createTemplate ()
	{
		$template = parent::createTemplate();
		$this->getPluginManager()->onPresenterCreateTemplate($this, $template);
		return $template;
	}
	/**
	 * @inheritdoc
	 */
	protected function startup ()
	{
		parent::startup();
		$this->getPluginManager()->onPresenterStartup($this);
	}
	/**
	 * @inheritdoc
	 */
	protected function beforeRender ()
	{
		parent::beforeRender();
		$this->getPluginManager()->beforePresenterRenderAction($this);
	}
	/**
	 * @inheritdoc
	 */
	protected function afterRender ()
	{
		parent::afterRender();
		$this->getPluginManager()->afterPresenterRenderAction($this);
	}

	/**
	 * @return PluginManager
	 */
	private function getPluginManager()
	{
		return $this->context->getByType(PluginManager::class);
	}
}
