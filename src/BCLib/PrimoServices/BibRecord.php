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
    public $contributors = [];

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
     * @var string[]
     */
    public $isbn = [];

    /**
     * @var string[]
     */
    public $issn = [];

    /**
     * @var string[]
     */
    public $oclcid = [];

    /**
     * @var string[]
     */
    public $subjects = [];

    /**
     * @var string
     */
    public $display_subject;

    /**
     * @var string[]
     */
    public $genres = [];

    /**
     * @var string[]
     */
    public $creator_facet = [];

    /**
     * @var string[]
     */
    public $collection_facet = [];

    /**
     * @var string[]
     */
    public $resourcetype_facet = [];

    /**
     * @var string[]
     */
    public $languages = [];

    /**
     * @var string
     */
    public $format;

    /**
     * @var string[]
     */
    public $description;

    /**
     * @var BibComponent[]
     */
    public $components = [];

    /**
     * @var GetIt[]
     */
    public $getit = [];

    /**
     * @var string
     */
    public $frbr_group_id;

    /**
     * @var string[]
     */
    public $cover_images;

    /**
     * @var string
     */
    public $link_to_source;

    /**
     * @var string[]
     */
    public $openurl;

    /**
     * @var string[]
     */
    public $openurl_fulltext;

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

    private $fields = [];

    public function __construct(\stdClass $json_doc = null)
    {
        $this->json = $json_doc;
    }

    /**
     * @param string $field_name The qualified PNX field with group, e.g. 'display/lds01'
     *
     * @return mixed|null the value of the field or null if not available
     */
    public function field($field_name)
    {
        list($group, $field) = explode('/', $field_name);
        if (!isset($this->fields[$group], $this->fields[$group][$field])) {
            return null;
        }
        return $this->fields[$group][$field];
    }

    public function addField($group_name, $field_name, $field_value)
    {
        if (!isset ($this->fields[$group_name])) {
            $this->fields[$group_name] = [];
        }
        $this->fields[$group_name][$field_name] = $field_value;
    }
}