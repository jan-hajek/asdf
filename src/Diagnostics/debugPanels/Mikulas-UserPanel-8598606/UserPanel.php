<?php
namespace Asdf\Diagnostics\Panel;

use Tracy\IBarPanel;
use Nette\Object;


class User extends \Nette\Application\UI\Control implements IBarPanel
{

	/** @var \Nette\Http\User */
	private $user;

	/** @var array username => password */
	private $credentials = array();

	/** @var string */
	private $userColumn = 'login';



	/**
	 * @throws \LogicException
	 */
	public function __construct(
		\Nette\Security\User $user,
		\Nette\Http\Request $request,
		\Nette\Http\Response $response,
		array $credentials)
	{
		$this->user = $user;
		$this->credentials = $credentials;

		if (isset($_POST['action'])) {
			if ($_POST['action'] == 'userPanelSignIn') {
				$data = $credentials[$_POST['index']];
				$identity = new \Nette\Security\Identity($data[0], $data[1], $data[2]);
				$user->storage->setIdentity($identity);
				$user->storage->setAuthenticated(TRUE);
				$response->redirect($request->getUrl());
				exit();
			} elseif ($_POST['action'] == 'userPanelSignOut') {
				$this->user->logout();
				$response->redirect($request->getUrl());
				exit();
			}
		}
	}



	/**
	 * Renders HTML code for custom tab
	 * IDebugPanel
	 * @return void
	 */
	public function getTab()
	{
		return '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAnpJREFUeNqEU19IU1EY/927e52bWbaMQLbJwmgP0zIpffDFUClsyF56WJBQkv1RyJeo2IMPEghRQeAIoscegpBqTy6y3CDwrdzDwjCVkdqmzT+7u//O1jm3knkV/MF3z3e+8zu/7zv3O4crFotgaHC7jfHrwgKuBYPtVqt1BBx3SlNV5HK5KSmXu/N6fPxTKY+BMwvUNzY22cvFz6TIi0TXoWkaFEWBrkra+rrUtJLJTJcKCDCBZrqvyBaRCTMBnRCwKhRZFlVFuUspl0r5OwRUKXu+opxgsP8qfE4Bmk7wZV7Bg5FRqIR0m/m8OfA7K9n6bt1GvbeWlq2CKxCcPnEM1wf6sZknFXsKDF+c+dHgVKBmf4JoqmHMb/Va8OTK4vSeAhThpW9vwdsPociJ1ATD/zU7bqyZyVtdKMWHIXH0SJ3/RrWn05hn5t5jeeZN+OyQdtPMFbA77i1/f9dE7cy/+RS10G7EbRX4fL42OvQGAoFgT6uM2uPnjHhq9iNeTABjY2Mv6fR5IpGY2Cbg9XqPUr/PZrMNOJ1Oq65pfCQSwcPwK1TtE9F7OYCurgsQRbGQSqWUfD7/lPKfJZPJWc7j8ZzkeX7S5XLZHA6HIEkSqBCam5uxYqnDwf02WDeTiMVikGUZdrsdq6urOhWSCSGdFhoIud3ulrKyMiGbzRrXVqX9j8fj8Pu7UXO4EiPDIZYdNDN7F6DvhKf7+HQ6bRGoaju970bm/2CZmCXn0nAcyBn+xsbG1joTooJsbxv71LDNhUJh299lpPnFNaxt/hVjlZWCPTIar+YEQXhEzzxobk9HRyeWrC2oqhRRnplENBrd0UKa5PEfAQYAH6s95RSa3ooAAAAASUVORK5CYII=">' .
			($this->user->isLoggedIn() ? 'Logged as <span style="font-style: italic; margin: 0; padding: 0;">' . $this->getUsername() . '</span>' : 'Guest');
	}



	/**
	 * Renders HTML code for custom panel
	 * IDebugPanel
	 */
	public function getPanel()
	{
		$template = new \Nette\Templating\FileTemplate(__DIR__ . '/bar.user.panel.latte');

		$template->hashedPassword = '';
		if (isset($_POST['action']) && $_POST['action'] == 'hashPassword') {
			$template->hashedPassword = $this->user->getAuthenticator()->calculateHash($_POST['pass']);
		}

		$template->registerFilter(new \Latte\Engine());
		$template->user = $this->user;
		$template->userName = $this->getUsername();
		$template->credentials = $this->credentials;

		ob_start();
		$template->render();
		return ob_get_clean();
	}



	/**
	 * IDebugPanel
	 * @return string
	 */
	public function getId()
	{
		return __CLASS__;
	}

	/**
	 * Username from user->identity->data from column set via setNameColumn()
	 * @return string|NULL
	 */
	public function getUsername()
	{
		$data = $this->user->getIdentity() ? $this->user->getIdentity()->getData() : null;
		return isset($data[$this->userColumn]) ? $data[$this->userColumn] : NULL;
	}

}
