<?php
namespace Asdf\Log\Decorators;

class Line implements IDecorator
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
			$message = $message->getMessage();
		}
		if (! is_string($message)) {
			$message = print_r($message, 1);
		}
		$date = str_pad(date('Y-m-d H:i:s', $date) . ';', 15, ' ');

		$message = "$hash;\t$url\t$userId;\t$date\t$title;\t$message\n";
		return $message;
	}
}
