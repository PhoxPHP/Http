<?php
/**
* @author 	Peter Taiwo
* @version 	1.0.0
*
* MIT License
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:

* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.

* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

namespace Kit\Http;

use Kit\Http\Request\RequestManager;

class Response
{
	
	/**
	* @var 		$request
	* @access 	private
	*/
	private 	$request;

	/**
	* @var 		$body
	* @access 	private
	*/
	private 	$body;

	/**
	* @var 		$headers
	* @access 	private
	*/
	private 	$headers = [];

	/**
	* Constructor
	*
	* @param 	$request \Http\Request\Interfaces\RequestInterface
	* @param 	$body <String>	Response body | Response body of request
	* @param 	$headers <Mixed> Response headers | Headers fetched from request
	* @access 	public
	* @return 	void
	*/
	public function __construct(RequestManager $request, $body='', $headers=null)
	{
		$this->request = (Object) $request->getReponse($this);
		$this->body = $body;
		$this->headers = $headers;

		if (gettype($headers) == 'string') {

			$this->headers = explode("\n", $headers);
		
		}
	}

	/**
	* Sets a response header.
	*
	* @param 	$key <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	String
	*/
	public function setHeader($key='', $value='')
	{
		$this->headers[$key] = $value;
	}

	/**
	* Return the body of a response.
	*
	* @param 	$setDecode <Boolean>
	* @access 	public
	* @return 	String
	*/
	public function body($setDecode=false)
	{
		$body = $this->body;
		return ($setDecode == true) ? html_entity_decode($body) : $body;
	}

	/**
	* Returns status code returned from request.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function statusCode()
	{
		if (!isset($this->request->http_code)) {
			
			$this->request->http_code = '';
			$httpCodeSegment = $this->headers[0];

			$preg = preg_match("/[0-9][0-9][0-9]/", $httpCodeSegment, $match);

			if ($preg) {

				$this->request->http_code = $match[0];

			}
		}

		return $this->request->http_code;
	}

	/**
	* Sets or returns the response content type.
	*
	* @param 	$contentType <String>
	* @access 	public
	* @return 	String
	*/
	public function contentType($contentType='')
	{
		if ('' !== $contentType) {

			$this->request->content_type = $contentType;
		
		}
		
		return $this->request->content_type;
	}

	/**
	* Returns the response content length.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function contentLength()
	{
		return $this->request->download_content_length;
	}

	/**
	* Returns the response redirect url.
	*
	* @access 	public
	* @return 	String
	*/
	public function redirectUrl() {
		return $this->request->redirect_url;
	}

	/**
	* Returns ip address fetched from response header.
	*
	* @access 	public
	* @return 	Double
	*/
	public function ip()
	{
		return $this->request->primary_ip;
	}

	/**
	* Returns the client ip address.
	*
	* @access 	public
	* @return 	Double
	*/
	public function clientIp()
	{
		return $this->request->local_ip;
	}

	/**
	* Returns the primary port.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function primaryPort()
	{
		return $this->request->primary_port;
	}


	/**
	* Returns the local port.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function localPort()
	{
		return $this->request->local_port;
	}	

	/**
	* Returns json string of the response body.
	*
	* @access 	public
	* @return 	Object
	*/
	public function json()
	{
		return json_decode($this->body());
	}

	/**
	* Returns an array of header.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getAllHeaders()
	{
		return $this->headers;
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function send()
	{
		if (sizeof($this->headers) > 1) {
		
			return null;
		
		}

		foreach(array_keys($this->headers) as $key) {

			header("$key: $this->headers[$key]");
		
		}
	}

	/**
	* Redirects to given url and also sends the given status code.
	*
	* @param 	$url <String>
	* @param 	$code <Integer>
	* @access 	public
	* @return 	void
	*/
	public function goto(String $url=null, Int $code=302)
	{
		$httpCodes = [
			100 => "HTTP/1.1 100 Continue",
			101 => "HTTP/1.1 101 Switching Protocols",
			200 => "HTTP/1.1 200 OK",
			201 => "HTTP/1.1 201 Created",
			202 => "HTTP/1.1 202 Accepted",
			203 => "HTTP/1.1 203 Non-Authoritative Information",
			204 => "HTTP/1.1 204 No Content",
			205 => "HTTP/1.1 205 Reset Content",
			206 => "HTTP/1.1 206 Partial Content",
			300 => "HTTP/1.1 300 Multiple Choices",
			301 => "HTTP/1.1 301 Moved Permanently",
			302 => "HTTP/1.1 302 Found",
			303 => "HTTP/1.1 303 See Other",
			304 => "HTTP/1.1 304 Not Modified",
			305 => "HTTP/1.1 305 Use Proxy",
			307 => "HTTP/1.1 307 Temporary Redirect",
			400 => "HTTP/1.1 400 Bad Request",
			401 => "HTTP/1.1 401 Unauthorized",
			402 => "HTTP/1.1 402 Payment Required",
			403 => "HTTP/1.1 403 Forbidden",
			404 => "HTTP/1.1 404 Not Found",
			405 => "HTTP/1.1 405 Method Not Allowed",
			406 => "HTTP/1.1 406 Not Acceptable",
			407 => "HTTP/1.1 407 Proxy Authentication Required",
			408 => "HTTP/1.1 408 Request Time-out",
			409 => "HTTP/1.1 409 Conflict",
			410 => "HTTP/1.1 410 Gone",
			411 => "HTTP/1.1 411 Length Required",
			412 => "HTTP/1.1 412 Precondition Failed",
			413 => "HTTP/1.1 413 Request Entity Too Large",
			414 => "HTTP/1.1 414 Request-URI Too Large",
			415 => "HTTP/1.1 415 Unsupported Media Type",
			416 => "HTTP/1.1 416 Requested range not satisfiable",
			417 => "HTTP/1.1 417 Expectation Failed",
			500 => "HTTP/1.1 500 Internal Server Error",
			501 => "HTTP/1.1 501 Not Implemented",
			502 => "HTTP/1.1 502 Bad Gateway",
			503 => "HTTP/1.1 503 Service Unavailable",
			504 => "HTTP/1.1 504 Gateway Time-out"
		];

		$status = (isset($httpCodes[$code])) ? $httpCodes[$code] : $httpCodes[200];
		header($status);
		header("Location: $url", true, $code);
		exit;
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	String
	*/
	public function getHeader($key='')
	{
		(Boolean) $responseheader = null;
		(Array) $headers = $this->headers;
		$header = array_map([$this, 'resolveHeaderName'], $headers);

		foreach($header as $value) {

			if (isset($value[$key])) {
			
				(String) $responseheader = $value[$key];
			
			}

		}

		return $responseheader;
	}

	/**
	* Sets client response code.
	*
	* @param 	$code <Integer>
	* @access 	public
	* @return 	void
	*/
	public function setResponseCode($code=404)
	{
		http_response_code($code);
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Array
	*/
	private function resolveHeaderName($string='')
	{
		(Array) $resolvedHeader = [];

		if (preg_match("/.*[a-zA-Z0-9]: (.*?)/", $string, $match)) {

			$key = $match[0];
			$value = str_replace($key, "", $string);
			
			$key = str_replace(': ', '', $key);
			$resolvedHeader[$key] = $value;

		}

		return $resolvedHeader;
	}

}