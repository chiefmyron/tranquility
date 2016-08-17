<div class="pagination-container">
    <span class="item-summary">
        {{ trans_choice('administration.common_pagination_summary', $totalPages, ['currentPage' => $currentPage, 'totalPages' => $totalPages]) }}
    </span>

    @if ($links)
    <ul class="pagination">
<?php
// Loop through links and render
$output = "";
    //var_dump($links);
foreach ($links as $link) {
    $label = "";
    $rel = "";
    $class = "";

    switch($link['type']) {
        case 'previous':
            $label = trans('administration.common_pagination_previous'); 
            $rel = ' rel="prev"';
            break;
        case 'next':
            $label = trans('administration.common_pagination_next'); 
            $rel = ' rel="next"';
            break;
        case 'page':
            $label = $link['pageNumber'];
            break;
        case 'dots':
            $label = "...";
            break;
    }

    // Render item depending on its state
    if ($link['pageNumber'] == $currentPage) {
        // Current page is highlighed, but not hyperlinked
        $output .= '<li class="active"><span>'.$label.'</span></li>';
    } elseif ($link['active']) {
        // Active page number is hyperlinked
        $output .= '<li><a href="'.htmlentities($link['url']).'"'.$rel.'>'.$label.'</a></li>';
    } else {
        // Inactive page number is not hyperlinked
        $output .= '<li class="disabled"><span>'.$label.'</span></li>';
    }
}
echo $output;

?>
    </ul>
    @endif
</div>
