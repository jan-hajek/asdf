<?php
namespace Asdf\Log\Writers;

class File implements IWriter
{
	protected $path = NULL;

	/**
	 * pri prvnim lognuti udela lajnu, aby bylo poznat kde konci jeden prubeh
	 * @var bool
	 */
	protected static $openFiles = array();

	/**
	 * konstruktor
	 *
	 * @param string $path cesta k souboru souboru
	 *
	 * @return void
	 */
	public function __construct ($path)
	{
		$this->path = $path;
	}

	
	/**
	 * funkce ktera zapise vstupni text na prislusne misto (mail, soubor, db apod.)
	 *
	 * @param string $message text
	 *
	 * @return void
	 */
	public function write ($message)
	{
		$path = $this->path;
		$dir = dirname($path);
		if ($dir) {
			if (FALSE !== strpos($path, '%date%')) {
				$path = str_replace('%date%', date("Y-m-d"), $path);
			}
			
			if (! isset(self::$openFiles[$path])) {
				self::$openFiles[$path] = 1;
				$f = fopen($path, 'a');
				if ($f) {
					chmod($path, 0777);
					fwrite($f, "\n" . str_pad('', 80, '*') . "\n\n");
					fclose($f);
				}
			}
			
			$f = fopen($path, 'a');
			fwrite($f, $message);
			fclose($f);
		}
	}

}