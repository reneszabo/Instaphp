<?php 
/**
 * The MIT License (MIT)
 * Copyright © 2013 Randy Sesser <randy@instaphp.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the “Software”), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Randy Sesser <randy@instaphp.com>
 * @filesource
 */
namespace Instaphp\Http\Events;

use \GuzzleHttp\Event\BeforeEvent;
use \GuzzleHttp\Event\RequestEvents;
/**
* Instagram Signed Authentication Event Subscriber
*/
class InstagramSignedAuthEvent implements \GuzzleHttp\Event\SubscriberInterface
{
	private $client_secret;
	private $ip_address;

	function __construct($ip_address, $client_secret)
	{
		$this->client_secret = $client_secret;
		$this->ip_address = $ip_address;
	}

	public function getEvents()
	{
		return ['before' => ['sign', RequestEvents::SIGN_REQUEST]];
	}

	public function sign(BeforeEvent $e)
	{
		$method = $e->getRequest()->getMethod();

		if (preg_match('/post|put|delete/i', $method)) {
			$e->getRequest()->setHeader('X-Insta-Forwarded-For', join('|', array($this->ip_address, hash_hmac('SHA256', $this->ip_address, $this->client_secret))));
		}
	}
}