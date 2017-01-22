<?php
namespace Asdf\Log;

interface ILogger
{
	/**
	 * @param string $message
	 * @param string $title
	 * @param string $namespaceName
	 * @return void
	 */
	public function log ($message, $title, $namespaceName);
	
}