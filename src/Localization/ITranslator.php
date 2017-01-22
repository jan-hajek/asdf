<?php
namespace Asdf\Localization;

interface ITranslator extends \Nette\Localization\ITranslator
{
	public function setTranslates(array $translates);
	
	public function onTranslate(\Closure $callback);
}