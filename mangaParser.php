<?php

namespace MLNG\MAL\Manga;

use DOMDocument;
use DOMXPath;
use Exception;

/*!
 * MyAnimeList Manga Parser Class
 *
 * Version: 1.0.0
 * Usage:
 *        $pareser = new MLNG\MAL\Manga\Parser($html);
 *        $data = $parser->getAll();
 *
 * Copyright (c) 2014 k3nsei.pl@gmail.com
 *
 */
class Parser
{

	private $dom;
	private $xpath;

	private $id = NULL;
	private $title = NULL;
	private $other_titles = array(
		'english' => '',
		'japanese' => '',
		'synonyms' => array()
	);
	private $type = NULL;
	private $authors = array();
	private $genres = array();
	private $status = NULL;
	private $start_date = NULL;
	private $end_date = NULL;
	private $synopsis = NULL;
	private $volumes = 0;
	private $chapters = 0;
	private $rank = 0;
	private $popularity_rank = 0;
	private $score = 0;
	private $image_url = NULL;

	function __construct($html)
	{
		if (!empty($html)) {
			$this->dom = new DOMDocument();
			if (@$this->dom->loadHTML($html)) {
				$this->xpath = new DOMXPath($this->dom);
				if (!empty($this->xpath)) {
					$this->setAll();
				}
			} else {
				throw new Exception('MangaParser Error 1: Received data isn\'t HTML Document or Class can\'t parse it.');
			}
		} else {
			throw new Exception('MangaParser Error 0: Class have not received the html document.');
		}
	}

	/**
	 * Set $id
	 */
	private function setId()
	{

		$x = $this->xpath->query('//*[@id="horiznav_nav"]/ul/li/a/@href');
		if ($x->length > 0) {
			if (filter_var($x->item(0)->nodeValue, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
				$match = FALSE;
				if (preg_match('/myanimelist\.net\/manga\/(\d+)/', $x->item(0)->nodeValue, $match)) {
					$this->id = (int)$match[1];
				}
			}
		}
	}

	/**
	 * @return $id
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Set $title
	 */
	private function setTitle()
	{
		$x = $this->xpath->query('//*[@id="contentWrapper"]/h1/text()');
		if ($x->length > 0) {
			$this->title = trim($x->item(0)->nodeValue);
		}
	}

	/**
	 * @return $title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set $authors
	 */
	private function setAuthors()
	{
		$x = $this->xpath->query('//span[text()="Authors:"]');
		if ($x->length > 0) {
			$aStack = array();
			foreach ($x->item(0)->parentNode->childNodes as $aNode) {
				$aId = NULL;
				$aLink = NULL;
				if ($aNode->nodeName === "a") {
					if ($aNode->attributes->item(0)->nodeName === 'href') {
						$match = FALSE;
						if (preg_match('/myanimelist\.net\/people\/(\d+)/', $aNode->attributes->item(0)->value, $match)) {
							$aId = (int)$match[1];
						}
						if (filter_var($aNode->attributes->item(0)->value, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
							$aLink = $aNode->attributes->item(0)->value;
						}
					}
					$aStack[] = array(
						'author_id' => $aId,
						'name' => trim(
							str_replace(
								',',
								'',
								$aNode->nodeValue
							)
						),
						'job' => trim(
							str_replace(
								array('(', ')', ','),
								array('', '', ''),
								$aNode->nextSibling->nodeValue
							)
						),
						'link' => $aLink
					);
				}
				$this->authors = $aStack;
			}
			$this->authors = $aStack;
		}
	}

	/**
	 * @return $authors
	 */
	public function getAuthors()
	{
		return $this->authors;
	}

	/**
	 * Set $other_titles
	 */
	private function setOtherTitles()
	{
		$e = $this->xpath->query('//span[text()="English:"]');
		$j = $this->xpath->query('//span[text()="Japanese:"]');
		$s = $this->xpath->query('//span[text()="Synonyms:"]');

		if ($e->length > 0) {
			$this->other_titles['english'] = trim($e->item(0)->nextSibling->nodeValue);
		}
		if ($j->length > 0) {
			$this->other_titles['japanese'] = trim($j->item(0)->nextSibling->nodeValue);
		}
		if ($s->length > 0) {
			$sStack = array();
			foreach (explode(',', $s->item(0)->nextSibling->nodeValue) as $sItem) {
				$sStack[] = trim($sItem);
			}
			$this->other_titles['synonyms'] = $sStack;
		}
	}

	/**
	 * @return $other_titles
	 */
	public function getOtherTitles()
	{
		return $this->other_titles;
	}

	/**
	 * Set $type
	 */
	private function setType()
	{
		$x = $this->xpath->query('//span[text()="Type:"]');
		if ($x->length > 0) {
			$this->type = trim($x->item(0)->nextSibling->nodeValue);
		}
	}

	/**
	 * @return $type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set $genres
	 */
	private function setGenres()
	{
		$x = $this->xpath->query('//span[text()="Genres:"]');
		if ($x->length > 0) {
			if (!empty($x->item(0)->parentNode) and mb_strlen($x->item(0)->parentNode->nodeValue) > 10) {
				$gStack = array();
				foreach (explode(',', trim(str_replace('Genres:', '', $x->item(0)->parentNode->nodeValue))) as $gItem) {
					$gStack[] = trim($gItem);
				}
				$this->genres = $gStack;
			}
		}
	}

	/**
	 * @return $genres
	 */
	public function getGenres()
	{
		return $this->genres;
	}

	/**
	 * Set $status
	 */
	private function setStatus()
	{
		$x = $this->xpath->query('//span[text()="Status:"]');
		if ($x->length > 0) {
			$this->status = trim($x->item(0)->nextSibling->nodeValue);
		}
	}

	/**
	 * @return $status
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set $start_data
	 */
	private function setStartDate()
	{
		$x = $this->xpath->query('//span[text()="Published:"]');
		if ($x->length > 0) {
			$match = FALSE;
			if (preg_match('/(.*?) to/', trim($x->item(0)->nextSibling->nodeValue), $match)) {
				$this->start_date = strtotime($match[1]);
			}
		}
	}

	/**
	 * @return $start_data
	 */
	public function getStartDate()
	{
		return $this->start_date;
	}

	/**
	 *  Set $end_date
	 */
	private function setEndDate()
	{
		$x = $this->xpath->query('//span[text()="Published:"]');
		if ($x->length > 0) {
			$match = FALSE;
			if (preg_match('/ to (.*)/', trim($x->item(0)->nextSibling->nodeValue), $match)) {
				if ($match[1] !== '?') {
					$this->end_date = strtotime($match[1]);
				}
			}
		}
	}

	/**
	 * @return $end_date
	 */
	public function getEndDate()
	{
		return $this->end_date;
	}

	/**
	 * Set $synopsis
	 */
	private function setSynopsis()
	{
		$x = $this->xpath->query('//h2[text()="Synopsis"]');
		if ($x->length > 0) {
			$this->synopsis = trim($x->item(0)->nextSibling->nodeValue);
		}
	}

	/**
	 * @return $synopsis
	 */
	public function getSynopsis()
	{
		return $this->synopsis;
	}

	/**
	 * Set $volumes
	 */
	private function setVolumes()
	{
		$x = $this->xpath->query('//span[text()="Volumes:"]');
		if ($x->length > 0) {
			if (is_numeric(trim($x->item(0)->nextSibling->nodeValue))) {
				$this->volumes = trim($x->item(0)->nextSibling->nodeValue);
			}
		}
	}

	/**
	 * @return $volumes
	 */
	public function getVolumes()
	{
		return $this->volumes;
	}

	/**
	 * Set $chapters
	 */
	private function setChapters()
	{
		$x = $this->xpath->query('//span[text()="Chapters:"]');
		if ($x->length > 0) {
			if (is_numeric(trim($x->item(0)->nextSibling->nodeValue))) {
				$this->chapters = trim($x->item(0)->nextSibling->nodeValue);
			}
		}
	}

	/**
	 * @return $chapters
	 */
	public function getChapters()
	{
		return $this->chapters;
	}

	/**
	 * Set $rank
	 */
	private function setRank()
	{
		$x = $this->xpath->query('//span[text()="Ranked:"]');
		if ($x->length > 0) {
			$this->rank = (int) filter_var(
				str_replace(
					'#',
					'',
					trim($x->item(0)->nextSibling->nodeValue)
				),
				FILTER_VALIDATE_INT
			);
		}
	}

	/**
	 * @return $rank
	 */
	public function getRank()
	{
		return $this->rank;
	}

	/**
	 * Set $popularity_rank
	 */
	private function setPopularityRank()
	{
		$x = $this->xpath->query('//span[text()="Popularity:"]');
		if ($x->length > 0) {
			$this->popularity_rank = (int) filter_var(
				str_replace(
					'#',
					'',
					trim($x->item(0)->nextSibling->nodeValue)
				),
				FILTER_VALIDATE_INT
			);
		}
	}

	/**
	 * @return $popularity_rank
	 */
	public function getPopularityRank()
	{
		return $this->popularity_rank;
	}

	/**
	 * Set $score
	 */
	private function setScore()
	{
		$x = $this->xpath->query('//span[text()="Score:"]');
		if ($x->length > 0) {
			$this->score = number_format(trim($x->item(0)->nextSibling->nodeValue), 2);
		}
	}

	/**
	 * @return $score
	 */
	public function getScore()
	{
		return $this->score;
	}

	/**
	 * Set $image_url
	 */
	private function setImageUrl()
	{
		$x = $this->xpath->query('//*[@id="content"]/table/tbody/tr/td/div/a/img/@src');
		if ($x->length > 0) {
			if (filter_var($x->item(0)->nodeValue, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
				$this->image_url = $x->item(0)->nodeValue;
			}
		}
	}

	/**
	 * @return $image_url
	 */
	public function getImageUrl()
	{
		return $this->image_url;
	}

	private function setAll()
	{
		$this->setId();
		$this->setTitle();
		$this->setOtherTitles();
		$this->setType();
		$this->setAuthors();
		$this->setGenres();
		$this->setStatus();
		$this->setStartDate();
		$this->setEndDate();
		$this->setSynopsis();
		$this->setVolumes();
		$this->setChapters();
		$this->setRank();
		$this->setPopularityRank();
		$this->setScore();
		$this->setImageUrl();
	}

	public function getAll()
	{
		return array(
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'other_titles' => $this->getOtherTitles(),
			'type' => $this->getType(),
			'authors' => $this->getAuthors(),
			'genres' => $this->getGenres(),
			'status' => $this->getStatus(),
			'start_date' => $this->getStartDate(),
			'end_date' => $this->getEndDate(),
			'synopsis' => $this->getSynopsis(),
			'volumes' => $this->getVolumes(),
			'chapters' => $this->getChapters(),
			'rank' => $this->getRank(),
			'popularity_rank' => $this->getPopularityRank(),
			'score' => $this->getScore(),
			'image_url' => $this->getImageUrl(),
			'related_manga' => array(),
			'alternative_versions' => array()
		);
	}

}
