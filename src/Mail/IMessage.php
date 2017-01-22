<?php
namespace Asdf\Mail;

interface IMessage
{

	/**
	 * Sends email.
	 * @return void
	 */
	public function send ();

	/**
	 * Adds email recipient.
	 *
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 *
	 * @return IMessage  provides a fluent interface
	 */
	public function addTo ($email, $name = NULL);

	/**
	 * Adds blind carbon copy email recipient.
	 *
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 *
	 * @return IMessage  provides a fluent interface
	 */
	public function addBcc ($email, $name = NULL);

	/**
	 * Adds carbon copy email recipient.
	 *
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 *
	 * @return IMessage  provides a fluent interface
	 */
	public function addCc ($email, $name = NULL);

	/**
	 * Sets the sender of the message.
	 *
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 *
	 * @return IMessage  provides a fluent interface
	 */
	public function setFrom ($email, $name = NULL);

	/**
	 * Sets HTML body.
	 *
	 * @param  string|Nette\Templating\ITemplate
	 * @param  mixed base-path or FALSE to disable parsing
	 *
	 * @return IMessage  provides a fluent interface
	 */
	public function setHtmlBody ($html, $basePath = NULL);

	/**
	 * Sets the subject of the message.
	 *
	 * @param  string
	 *
	 * @return IMessage  provides a fluent interface
	 */
	public function setSubject ($subject);
}
