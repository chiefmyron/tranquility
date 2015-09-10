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
                $output .= '            <li class="list-group-item '.$class.'"'.$id.'>'."\n";
                
                // Add item
                $output .= '                '.$icon.'<a href="'.$element['url'].'">'.trans($element['text']).'</a>'."\n"; 
                $output .= '            </li>'."\n";
                break;
                
            // Plain text toolbar item
            case 'text':
                $output .= '            <li class="list-group-item '.$class.'"'.$id.'>'."\n";
                
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
                $output .= '            <li class="list-group-item divider" />'."\n";
                break;
                
            /*case 'button':
                $output .= '<button class="btn btn-block'.$class.'" id="'.$element['id'].'" type="button">'.$element['text'].'</button>';
                break;
            case 'multibutton':
                $output .= '<div class="btn-group btn-block btn-dropdown-container">'."\n";
                $output .= '    <button type="button" class="btn btn-block'.$class.'" id="'.$element['id'].'">'.$element['text'].'</button>'."\n";
                $output .= '    <button type="button" class="btn dropdown-toggle'.$class.'" data-toggle="dropdown"><span class="caret"></span></button>'."\n";
                $output .= '    <ul class="dropdown-menu" role="menu">'."\n";
                foreach ($element['items'] as $item) {
                    if ($item['text'] == '-') {
                        $output .= '        <li class="divider"></li>'."\n";
                    } else {
                        $output .= '        <li>'."\n";
                        $output .= '            <a href="'.$item['url'].'" id="'.$item['id'].'">'.$item['text'].'</a>'."\n";
                        $output .= '        </li>'."\n";
                    }
                }
                $output .= '    </ul>'."\n";
                $output .= '</div>'."\n";
                $output .= '<div class="clearfix"></div>';
                break;
            case 'divider':
                // Write divider element
                $output .= '<li class="divider" />'."\n";;
                break;
            case 'link':
                $output .= "<li class='toolbar-16-".$element['class']."' id='".$element['id']."'>\n";
                $output .= "    <a href='".$element['url']."'>".$element['text']."</a>\n"; 
                $output .= "</li> \n";
                break;
            case 'text':
                $output .= "<div class='data-item".$class."'>\n";
                $output .= "    ".$element['text']."\n";
                $output .= "</div> \n";
                break;
            case 'textWithLabel':
                $output .= "<div class='data-item".$class."'>\n";
                $output .= "    <span id='".$element['id']."'>".$element['text']."</span> \n";
                $output .= "    <span class='sub-label'>".$element['label']."</span> \n";
                $output .= "</div> \n";
                break;*/
        }
    }    
    
    echo $output;
?>      
    </div>
@endif
