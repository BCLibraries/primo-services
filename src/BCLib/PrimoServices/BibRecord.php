<?php

namespace BCLib\PrimoServices;

/**
 * Class BibRecord
 * @package BCLib\PrimoServices
 *
 * @property string               $title
 * @property Person               $creator
 * @property Person[]             $contributors
 * @property string               $date
 * @property string               $publisher
 * @property string               $abstract
 * @property string               $frbr_group_id
 * @property string               $type
 * @property string               $url
 * @property string               $availability
 * @property object               $cover_images
 * @property string               $isbn
 * @property string               $issn
 * @property string               $oclcid
 * @property string               $reserves_info
 * @property string[]             $subjects
 * @property string               $display_subject
 * @property string[]             $genres
 * @property string[]             $languages
 * @property string               $table_of_contents
 * @property string               $format
 * @property string               $description
 * @property string               $permalink
 * @property Holding[]            $holdings
 * @property string               $find_it_url
 * @property string               $available_online_url
 * @property BibRecordComponent[] $components
 */
class BibRecord implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_title;
    private $_creator;
    private $_contributors = array();
    private $_components = array();
    private $_date;
    private $_publisher;
    private $_abstract;
    private $_frbr_group_id;
    private $_type;
    private $_url;
    private $_availability;
    private $_cover_images;
    private $_isbn;
    private $_issn;
    private $_oclcid;
    private $_reserves_info;
    private $_subjects = array();
    private $_display_subject;
    private $_genres = array();
    private $_languages;
    private $_table_of_contents;
    private $_format;
    private $_description;
    private $_permalink;
    private $_holdings = array();
    private $_find_it_url;
    private $_available_online_url;

    public function addContributor(Person $contributor)
    {
        $this->_contributors[] = $contributor;
    }

    public function addSubject($subject)
    {
        $this->_subjects[] = $subject;
    }

    public function addGenre($genre)
    {
        $this->_genres[] = $genre;
    }

    public function addLanguages($language)
    {
        $this->_languages[] = $language;
    }

    public function addCoverImage($image_url, $size = 'small')
    {
        $sizes = array('small', 'medium', 'large');
        if (!in_array($size, $sizes))
        {
            throw new \Exception($size . ' is not a valid image size');
        }

        $this->_cover_images = new \stdClass();
        $this->_cover_images->$size = $image_url;
    }

    public function addHoldings(Holding $holding)
    {
        $this->_holdings[] = $holding;
    }

    public function addComponent(BibRecordComponent $component)
    {
        $this->_components[] = $component;
    }

    private function _set_creator(Person $creator)
    {
        $this->_creator = $creator;
    }

}