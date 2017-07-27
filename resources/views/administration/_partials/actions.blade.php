<?php

if (is_array($primaryAction)) {
    // Get details of primary action
    $url = '';
    if (isset($primaryAction['url'])) {
        $url = $primaryAction['url'];
    }

    $id = '';
    if (isset($primaryAction['id'])) {
        $id = ' id="'.$primaryAction['id'].'"';
    }

    $class = "btn btn-primary";
    if (isset($primaryAction['class'])) {
        $class .= ' '.$primaryAction['class'];
    }
    if (isset($primaryAction['enabled']) && $primaryAction['enabled'] == false) {
        $class .= ' disabled';
    }

    $icon = '';
    if (isset($primaryAction['icon'])) {
        $icon = '<span class="glyphicon glyphicon-'.$primaryAction['icon'].'" aria-hidden="true"></span>';
    }

    $attributes = '';
    if (isset($primaryAction['attributes']) && is_array($primaryAction['attributes'])) {
        foreach ($primaryAction['attributes'] as $key => $value) {
            $attributes .= ' '.$key.'="'.$value.'"';
        }
    }

    // Start output for action button
    $output = '    <div class="btn-group">';

    // Generate link for primary action
    $output .= '        <a href="'.$url.'"'.$id.' class="'.$class.'"'.$attributes.'>';
    if ($icon != '') {
        $output .= $icon;
    }
    $output .= ' '.trans('administration.'.$primaryAction['text']);
    $output .= '        </a>';

    // Check if there are dropdown menu items defined
    if (is_array($dropdownMenuItems) && count($dropdownMenuItems) > 0) {
        $output .= '        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $output .= '            <span class="caret"></span>';
        $output .= '            <span class="sr-only">'.trans('administration.common_toggle_dropdown').'</span>';
        $output .= '        </button>';
        $output .= '        <ul class="dropdown-menu dropdown-menu-right">';

        // Add items to menu
        foreach ($dropdownMenuItems as $element) {
            // Add additional class attributes if necessary
            $class = '';
            if (isset($element['class']) && $element['class'] != '') {
                $class .= ' '.$element['class'];
            }

            // If item is disabled, add additional class
            if (isset($element['enabled']) && $element['enabled'] == false) {
                $class .= ' disabled';
            }

            // Generate id attribute if necessary
            $id = '';
            if (isset($element['id']) && $element['id'] != '') {
                $id = ' id="'.$element['id'].'"';
            }

            // Generate span for icon (if specified)
            $icon = '';
            if (isset($element['icon']) && $element['icon'] != '') {
                $icon = '<span class="glyphicon glyphicon-'.$element['icon'].'" aria-hidden="true"></span>';
            }

            // Generate additional attributes (if specified)
            $attributes = '';
            if (isset($element['attributes']) && is_array($element['attributes'])) {
                foreach ($element['attributes'] as $key => $value) {
                    $attributes .= ' '.$key.'="'.$value.'"';
                }
            }

            // Create element
            switch ($element['type']) {
                // Hyperlinked item
                case 'link':
                    $output .= '        <li>';
                    $output .= '            <a href="'.$element['url'].'"'.$id.' class="'.$class.'"'.$attributes.'>'.$icon." ".trans('administration.'.$element['text'])."</a>";
                    $output .= '        </li>';
                    break;
                // Plain text item
                case 'text':
                    $output .= '        <li>';
                    $output .= '            <span '.$id.' class="'.$class.'"'.$attributes.'>'.$icon." ".trans('administration.'.$element['text'])."</span>";
                    $output .= '        </li>';
                    break;
                // Divider                
                case 'divider':
                    // Write divider element
                    $output .= '        <li role="separator" class="divider"></li>';
                    break;
            }
        }
        $output .= '        </ul>';
    }

    $output .= '    </div>';
    echo $output;
}

