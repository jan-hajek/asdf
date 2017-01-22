<?php
namespace Asdf\Localization;

class DevNullTranslator implements ITranslator
{
    public function translate ($message, $count = NULL)
    {
        return $message;
    }
    
    public function setTranslates(array $translates)
    {
    	
    }
    
    public function onTranslate(\Closure $callback)
    {
    	
    }
}