<?php
namespace Asdf\Log\Decorators;

class Factory
{

	public function getDecorator ($name)
	{
		switch (strtolower($name)) {
			case 'text':
				return new Text();
				break;

			case 'line':
				return new Line();
				break;

			case 'html':
				return new Html();
				break;

			default:
				throw new \Asdf\Log\Exception("Dekorator s nazvem '$name' neexistuje");
				break;
		}
	}
}
