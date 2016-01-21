@if ($toolbar)
    <div class="toolbar">
<?php
    $output = '';
    $groupStarted = false;
    
    foreach ($toolbar as $element) {
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
            $icon = '<span class="glyphicon glyphicon-'.$element['icon'].'" aria-hidden="true"></span>'."\n";
        }
        
        // Create element
        switch ($element['type']) {
            
            // Heading text for toolbar
            case 'header':
                if ($groupStarted) {
                    $output .= '        </ul>'."\n";
                }
                
                // Create header and start new group
                $output .= '        <h3 class="'.$class.'"'.$id.'>'.trans($element['text']).'</h3>'."\n";
                $output .= '        <ul class="list-group">'."\n";
                $groupStarted = true;
                break;
                
            // Hyperlinked toolbar item
            case 'link':
                $output .= '            <li class="list-group-item'.$class.'"'.$id.'>'."\n";
                
                // Add item
                $output .= '                '.$icon.'<a href="'.$element['url'].'">'.trans($element['text']).'</a>'."\n"; 
                $output .= '            </li>'."\n";
                break;
                
            // Plain text toolbar item
            case 'text':
                $output .= '            <li class="list-group-item'.$class.'"'.$id.'>'."\n";
                
                // Include icon if specified
                if ($icon != '') {
                    $ouptut .= $icon;
                }
                
                // Add item
                $output .= '                '.trans($element['text'])."\n";
                $output .= '            </li>'."\n";
                break;

            // Divider                
            case 'divider':
                // Write divider element
                $output .= '            <li class="list-group-item divider"></li>'."\n";
                break;
        }
    }   
    
    // Close off unordered list, if it is open
    if ($groupStarted) {
        $output .= '        </ul>'."\n";
    } 
    
    echo $output;
?>      
    </div>
@endif
