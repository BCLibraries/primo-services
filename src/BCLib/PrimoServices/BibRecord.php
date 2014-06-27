<?php

namespace BCLib\PrimoServices;

class BibRecord
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var Person
     */
    public $creator;

    /**
     * @var string[]
     */
    public $contributors = array();

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $publisher;

    /**
     * @var string
     */
    public $abstract;

    /**
     * @var string
     */
    public $type;

    /**
     * @todo refactor availability into it's own thing
     */
    //public $availability;

    /**
     * @var string[]
     */
    public $isbn = array();

    /**
     * @var string[]
     */
    public $issn = array();

    /**
     * @var string[]
     */
    public $oclcid = array();

    /**
     * @var string[]
     */
    public $subjects = array();

    /**
     * @var string
     */
    public $display_subject;

    /**
     * @var string[]
     */
    public $genres = array();

    /**
     * @var string[]
     */
    public $creator_facet = array();

    /**
     * @var string[]
     */
    public $collection_facet = array();

    /**
     * @var string[]
     */
    public $languages = array();

    /**
     * @var string
     */
    public $format;

    /**
     * @var string
     */
    public $description;

    /**
     * @var BibComponent[]
     */
    public $components = array();

    /**
     * @var string[]
     */
    public $getit = array();

    /**
     * @var string[]
     */
    public $cover_images;

    /**
     * @var string
     */
    public $link_to_source;

    /**
     * @var string
     */
    public $openurl;

    /**
     * @var string
     */
    public $openurl_fulltext;

    /**
     * @var \stdClass
     */
    public $json;

    /**
     * @var string
     */
    public $sort_title;

    /**
     * @var string
     */
    public $sort_creator;

    /**
     * @var string
     */
    public $sort_date;

    /**
     * @var string
     */
    public $fulltext;

    public function __construct(\stdClass $json_doc = null)
    {
        $this->json = $json_doc;
    }
}