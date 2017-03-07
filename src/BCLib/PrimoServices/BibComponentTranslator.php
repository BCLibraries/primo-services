<?php

namespace BCLib\PrimoServices;

class BibComponentTranslator
{
    private $is_multi = false;
    private $keys = [];

    /**
     * @var BibComponent[]
     */
    private $components = [];

    /**
     * @param \stdClass $sear_doc a single "sear:DOC" object
     *
     * @return BibComponent[]
     */
    public function translate(\stdClass $sear_doc)
    {
        $record = $sear_doc->PrimoNMBib->record;

        $this->is_multi = is_array($record->control->sourceid);

        if ($this->is_multi) {

            $this->keys = array_map(
                function ($value)  {
                    return $this->splitMultiField($value)->key;
                },
                $sear_doc->PrimoNMBib->record->control->sourceid
            );
        } else {
            $this->keys = [$record->control->recordid];
        }

        $this->components = [];
        foreach ($this->keys as $key) {
            $this->components[$key] = new BibComponent();
        }

        $this->assign($record->control, 'sourcerecordid', 'source_record_id');
        $this->assign($record->delivery, 'delcategory', 'delivery_category');
        $this->assign($record->control, 'sourceid', 'source');

        // AlmaIDs aren't always set accurately in the records. Fail gracefully.
        try {
            $this->assignAlmaId($record->control);
        } catch (\Exception $e) {
            foreach ($this->components as $component) {
                $component->alma_ids = [];
            }
        }

        return $this->components;
    }

    private function splitMultiField($value)
    {
        $parts = explode('$$O', $value);
        $pair = new \stdClass();
        $pair->val = substr($parts[0], 3);
        $pair->key = $parts[1];
        return $pair;
    }

    private function assign($group, $field, $property)
    {
        $values = $this->extractField($group, $field);
        foreach ($values as $value) {
            $pair = $this->splitMultiField($value);
            $this->components[$pair->key]->$property = $pair->val;
        }
    }

    private function assignAlmaId($group)
    {
        $values = $this->extractField($group, 'almaid');

        $component_keys = array_keys($this->components);

        foreach ($values as $value) {
            $pair = $this->splitMultiField($value);

            $key_parts = explode(':', $pair->key);
            $pair->key = array_pop($key_parts);

            foreach ($component_keys as $component_key) {
                if (strpos($component_key, $pair->key) !== false && strpos($pair->val, ':')) {
                    list($institution, $institution_id) = explode(':', $pair->val);
                    $this->components[$component_key]->alma_ids[$institution] = $institution_id;
                }
            }
        }
    }

    private function extractField(\stdClass $group, $field)
    {
        if (!isset($group->$field)) {
            return [];
        }
        if ($this->is_multi) {
            return $group->$field;
        }

        $fieldValues = (array) $group->$field;

        return array_map(function($fieldValue) {
            return '$$V' . $fieldValue . '$$O' . $this->keys[0];
        }, $fieldValues);
    }
}