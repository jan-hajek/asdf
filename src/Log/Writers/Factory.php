<?php
namespace Asdf\Log\Writers;

class Factory
{

	/**
	 * @var Asdf\Mail\Factory
	 */
	protected $mailFactory;

	/**
	 * vytvori tovarnu pro log writtery
	 *
	 * @param \Asdf\Mail\Factory $mailFactory preda se tovarna na maily
	 *
	 * @return void
	 */
	public function __construct (\Asdf\Mail\Factory $mailFactory)
	{
		$this->mailFactory = $mailFactory;
	}

	/**
	 * vrati writter podle jmena, pokud neexistuje, tak ho vytvori
	 *
	 * @param string $name   nazev writteru
	 * @param array  $params parametry vytvareneho writteru
	 *
	 * @return \Asdf\Log\Writers\Mail|\Asdf\Log\Writers\File
	 */
	public function getWriter ($name, array $params = array())
	{
		switch (strtolower($name)) {
			case 'mail':
				if (!isset($params['recipients'])) {
					throw new \Asdf\Log\Exception("V configu pridejte u mail writeru parametr recipients");
				}
				if (!isset($params['subject'])) {
					throw new \Asdf\Log\Exception("V configu pridejte u mail writeru parametr subject");
				}
				if (!isset($params['from'])) {
					throw new \Asdf\Log\Exception("V configu pridejte u mail writeru parametr from");
				}

				return new Mail(
					$this->mailFactory->createMail(),
					explode('|', $params['recipients']),
					$params['subject'],
					$params['from']
				);
				break;
			case 'file':
				return new File($params['path']);
				break;

			default:
				throw new \Asdf\Log\Exception("Writer s nazvem '$name' neexistuje");
				break;
		}
	}
}
