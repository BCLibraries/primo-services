<?php

namespace BCLib\PrimoServices;

/**
 * @property Facet[]       facets
 * @property BibRecord[]   results
 */
class BriefSearchResult implements \JsonSerializable
{
    use Accessor, EncodeJson;

    private $_facets = array();
    private $_results = array();
}