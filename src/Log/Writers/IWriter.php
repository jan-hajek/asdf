<?php
namespace Asdf\Log\Writers;

interface IWriter
{
	
	/**
	 * funkce ktera zapise vstupni text na prislusne misto (mail, soubor, db apod.)
	 *
	 * @param string $message text
	 *
	 * @return void
	 */
	public function write ($message);
}