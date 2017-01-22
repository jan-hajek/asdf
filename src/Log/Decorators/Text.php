<?php
namespace Asdf\Log\Decorators;

class Text implements IDecorator
{

	/**
	 * upravi parametry do jedineho textoveho retezce, ktery pak muze byt zapsan na vystup
	 *
	 * @param string $hash    unikatni kod logu
	 * @param int    $userId  id uzivatele
	 * @param string $date    datum
	 * @param string $title   titulek zpravy
	 * @param string $message zprava
	 * @param string $file    cesta k souboru ve kterem se logovalo
	 * @param int    $line    radek v souboru ve kterem se logovalo
	 * @param string $url     aktualni url
	 *
	 * @return string
	 */
	public function decorate ($hash, $userId, $date, $title, $message, $file, $line, $url)
	{
		if ($message instanceof \Exception) {
			$exception = $message;
			$message = '';
			while ($exception) {
				$line = $exception->getLine();
				$file = $exception->getFile();
				$trace = $exception->getTraceAsString();
				$exceptionMessage = $exception->getMessage();
				$message .= "\nException \n-------------\n"
				. "Message: $exceptionMessage \n"
				. "File: $file \n"
				. "Line: $line \n"
				. "Trace:\n $trace \n";
				
				$exception = $exception->getPrevious();
			}
		}
		
		if (! is_string($message)) {
			$message = print_r($message, 1);
		}
		
		$text = str_pad('', 60, '-') . "\n";
		$text .= "Hash: $hash \n";
		$text .= "Time: " . date("d.m.Y H:i:s", $date) . "\n";
		$text .= "Title: $title \n";
		$text .= "Message:\n";
		$text .= "$message \n";
		$text .= "----------\n";
		$text .= "User: $userId \n";
		$text .= "IP: {$_SERVER["REMOTE_ADDR"]} \n";
		$text .= "URL: $url \n";
		$text .= "Dump:\n";
		$text .= "GET:\n";
		$text .= print_r($_GET, 1);
		$text .= "\n";
		$text .= "POST:\n";
		$text .= print_r($_POST, 1);
		$text .= "\n";
		$text .= "SESSION:\n";
		$text .= print_r($_SESSION, 1);
		$text .= "\n";
		return $text;
	}
}
