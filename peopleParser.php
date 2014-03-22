<?php

namespace MLNG\MAL\People;

use DOMDocument;
use DOMXPath;
use Exception;

/*!
 * MyAnimeList People Parser Class
 *
 * Version: 1.0.5
 * Usage:
 *        $pareser = new MLNG\MAL\People\Parser($html);
 *        $data = $parser->getAll();
 *
 * Copyright (c) 2014 k3nsei.pl@gmail.com
 *
 */

class Parser
{

    private $dom;
    private $xpath;

    private $id;
    private $slug;
    private $name;
    private $surname;
    private $bday;
    private $website;
    private $foto;

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
                throw new Exception('PeopleParser Error 1: Received data isn\'t HTML Document or Class can\'t parse it.');
            }
        } else {
            throw new Exception('PeopleParser Error 0: Class have not received the html document.');
        }
    }

    /**
     * Set $id
     */
    private function setId()
    {
        $x = $this->xpath->query('//*[@id="horiznav_nav"]/ul/li[1]/a/@href');
        if ($x->length > 0) {
            if (filter_var($x->item(0)->nodeValue, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                $match = false;
                if (preg_match('/myanimelist\.net\/people\/(\d+)/', $x->item(0)->nodeValue, $match)) {
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
     * Set $slug
     */
    private function setSlug()
    {
        $x = $this->xpath->query('//*[@id="horiznav_nav"]/ul/li[1]/a/@href');
        if ($x->length > 0) {
            if (filter_var($x->item(0)->nodeValue, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                $match = false;
                if (preg_match('/myanimelist\.net\/people\/(\d+)\/(.*)/', $x->item(0)->nodeValue, $match)) {
                    $this->slug = str_replace(
                        '/',
                        '',
                        $match[2]
                    );
                }
            }
        }
    }

    /**
     * @return $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set $name
     */
    private function setName()
    {
        $x = $this->xpath->query('//span[text()="Given name:"]');
        if ($x->length > 0) {
            $this->name = trim($x->item(0)->nextSibling->nodeValue);
        }
    }

    /**
     * @return $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set $surname
     */
    private function setSurname()
    {
        $x = $this->xpath->query('//span[text()="Family name:"]');
        if ($x->length > 0) {
            $this->surname = trim($x->item(0)->nextSibling->nodeValue);
        }
    }

    /**
     * @return $surname
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set $bday
     */
    private function setBday()
    {
        $x = $this->xpath->query('//span[text()="Birthday:"]');
        if ($x->length > 0) {
            $this->bday = strtotime(trim($x->item(0)->nextSibling->nodeValue));
        }
    }

    /**
     * @return $bday
     */
    public function getBday()
    {
        return $this->bday;
    }

    /**
     * Set $website
     */
    private function setWebsite()
    {
        $x = $this->xpath->query('//span[text()="Website:"]');
        if ($x->length > 0) {
            $this->website = trim($x->item(0)->nextSibling->nodeValue);
        }
    }

    /**
     * @return $website
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set $foto
     */
    private function setFoto()
    {
        $x = $this->xpath->query('//*[@id="content"]/table/tbody/tr/td/div/a/img/@src');
        if ($x->length > 0) {
            if (filter_var($x->item(0)->nodeValue, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                $this->foto = $x->item(0)->nodeValue;
            }
        }
    }

    /**
     * @return $foto
     */
    public function getFoto()
    {
        return $this->foto;
    }


    private function setAll()
    {
        $this->setId();
        if ($this->getId() !== null) {
            $this->setSlug();
            $this->setName();
            $this->setSurname();
            $this->setBday();
            //$this->setWebsite();
            $this->setFoto();
        } else {
            throw new Exception('No person found.');

            return;
        }
    }

    public function getAll()
    {
        return array(
            'id' => $this->getId(),
            'slug' => $this->getSlug(),
            'name' => $this->getName(),
            'surname' => $this->getSurname(),
            'bday' => $this->getBday(),
            //'website' => $this->getWebsite(),
            'foto' => $this->getFoto()
        );
    }

}
