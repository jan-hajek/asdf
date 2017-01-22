<?php
namespace Asdf\Log\Decorators;

class Html implements IDecorator
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
		$text = "<table>";
		$text .= '<tr><td>Čas</td><td><strong>' . date('d/m/Y H:i', $date) . "</strong></td></tr>";
		$text .= '<tr><td>Hash</td><td><strong>' . $hash . "</strong></td></tr>";
		$text .= "<tr><td>Titulek</td><td><strong>" . $title . "</strong></td></tr>";
		$text .= '<tr><td>Id Uživatele</td><td><strong>' . $userId . "</strong></td></tr>";
		$text .= "<tr><td>Url</td><td><strong>$url</strong></td></tr>";
		$text .= "</table>";
		
		$text .= "Zpráva<br />";
		
		if ($message instanceof \Exception) {
			$text .= $message->getCode() . ': ' . $message->getMessage();
			$text .= "<br />";
			$text .= "<table>";
			$text .= "<caption>Cesta</caption>";
			$text .= "  <tr>
                            <th>Soubor</th>
                            <th>Řádek</th>
                            <th>Volaná třída</th>
                            <th>Volaná funkce</th>
                            <th>Pořadí</th>
                        </tr>";
			
			$text .= "<tr>
                        <td>" . $message->getFile() . "</td>
                        <td>" . $message->getLine() . "</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>-</td>
                      </tr>";
			
			if (count($message->getTrace())) {
				foreach ($message->getTrace() as $index => $trace) {
					$text .= "<tr>
                               <td>" . (isset($trace['file']) ? $trace['file'] : '') . "</td>
                               <td>" . (isset($trace['line']) ? $trace['line'] : '') . "</td>
                               <td>" . (isset($trace['class']) ? $trace['class'] : '') . "</td>
                               <td>" . (isset($trace['function']) ? $trace['function'] : '') . "</td>
                               <td>" . $index . "</td>
                             </tr>";
				}
			}
			$text .= "</table>";
			return $text;
		}
		
		if (! is_string($message)) {
			$message = print_r($message, 1);
		}
		
		$text .= $message;
		
		return $text;
	}
}
