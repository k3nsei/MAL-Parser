<?php

namespace MLNG\MAL\People;

use DOMDocument;
use DOMXPath;
use Exception;

class Parser
{

	private $dom;
	private $xpath;

	function __construct($html)
	{
		if (!empty($html)) {
			$this->dom = new DOMDocument();
			if (@$this->dom->loadHTML($html)) {
				$this->xpath = new DOMXPath($this->dom);
				if (!empty($this->xpath)) {
					//$this->setAll();
				}
			} else {
				throw new Exception('PeopleParser Error 1: Received data isn\'t HTML Document or Class can\'t parse it.');
			}
		} else {
			throw new Exception('PeopleParser Error 0: Class have not received the html document.');
		}
	}
}
