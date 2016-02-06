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
}
