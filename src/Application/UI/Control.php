<?php
namespace Asdf\Application\UI;

use Asdf\Application\Plugin\PluginManager;
use Nette\Application\UI\ITemplate;
use Nette\ComponentModel\IContainer;

abstract class Control extends \Nette\Application\UI\Control
{
    public function __construct()
    {
        parent::__construct(null, get_class($this));
    }

    /**
     * @param  string|NULL
     * @return ITemplate
     */
    protected function createTemplate($class = null)
    {
        $template = parent::createTemplate();
        $this->getPluginManager()->onControlCreateTemplate($this, $template);

        return $template;
    }

    /**
     * @return PluginManager
     */
    private function getPluginManager()
    {
        return $this->getPresenter()->getContext()->getByType(PluginManager::class);
    }

    final public function setParent(IContainer $parent = null, $name = null)
    {
        // FIXME - jhajek
        parent::setParent($parent, $name);
        $explode = explode('\\', get_class($this));
        $name = strtolower(str_replace('Widget', '', end($explode)));
        $dir = dirname($this->getReflection()->getFileName());
        $this->template->setFile("$dir/$name.latte");
    }

    public function render()
    {
        $this->template->render();
    }
}
