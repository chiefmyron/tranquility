<?php namespace Tranquility\Html\Form;

use \DB;
use Tranquility\Utility;

class FormBuilder extends \Collective\Html\FormBuilder {

    /**
     * Create a select box based on a set of reference data
     *
     * @param  string $name
     * @param  array  $tableOptions
     * @param  string $selected
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function selectFromReferenceData($name, $tableOptions = array(), $selected = null, $options = array()) {
        // Get table options
        $tableName = Utility::extractValue($tableOptions, 'tableName', '');
        $valueColumnName = Utility::extractValue($tableOptions, 'valueColumnName', 'code');
        $labelColumnName = Utility::extractValue($tableOptions, 'labelColumnName', 'description');
        $orderColumnName = Utility::extractValue($tableOptions, 'orderColumnName', 'ordering');
        $translateFlag   = Utility::extractValue($tableOptions, 'translateCode', false);
        $translatePrefix = Utility::extractValue($tableOptions, 'translatePrefix', '');
        
        // Retrieve values from table
        $query = DB::table($tableName)
                    ->select($valueColumnName.' AS value', $labelColumnName.' AS label')
                    ->where('effectiveFrom', '<=', 'NOW()')
                    ->where(function ($query) {
                        $query->where('effectiveUntil', '>=', 'NOW()')
                              ->orWhereNull('effectiveUntil');
                    })
                    ->orderBy($orderColumnName, 'asc');
        $codes = $query->get();
                    
        // Reformat code values
        $list = array();
        foreach ($codes as $code) {
            if ($translateFlag) {
                $list[$code->value] = trans($translatePrefix.$code->value);
            } else {
                $list[$code->value] = $code->label;    
            }
        }
                    
        return $this->select($name, $list, $selected, $options);
    }

    public function selectFromEntity($entityType, $name, $value = null, $options = []) {
        // Retrieve value for the field
        $value = $this->getValueAttribute($name, $value);

        // Retrieve entity details
        


        if (! in_array($type, $this->skipValueTypes)) {
            
        }

        // Retrieve entity details


        // Add display options        
        if (! isset($options['name'])) {
            $options['name'] = $name;
        }
        $options['id'] = $this->getIdAttribute($name, $options);

        


        // We will get the appropriate value for the given field. We will look for the
        // value in the session for the value in the old input data then we'll look
        // in the model instance if one is set. Otherwise we will just use empty.


        // Once we have the type, value, and ID we can merge them into the rest of the
        // attributes array so we can convert them into their HTML attribute format
        // when creating the HTML element. Then, we will return the entire input.
        $merge = compact('type', 'value', 'id');

        $options = array_merge($options, $merge);

        return $this->toHtmlString('<input' . $this->html->attributes($options) . '>');
    }
}
