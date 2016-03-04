<div class="breadcrumb-container">
@if ($breadcrumbs)
    <?php $counter = 0; ?>
	<ol class="breadcrumb">
		@foreach ($breadcrumbs as $breadcrumb)
			@if ($breadcrumb->url && ($counter == (count($breadcrumbs) - 2)))
                <li class="previous"><a href="{{{ $breadcrumb->url }}}"><span class="glyphicon glyphicon-chevron-left hidden-lg hidden-md" aria-hidden="true"></span> {{{ $breadcrumb->title }}}</a></li>
            @elseif ($breadcrumb->url && !$breadcrumb->last)
				<li><a href="{{{ $breadcrumb->url }}}">{{{ $breadcrumb->title }}}</a></li>
            @else
				<li class="active">{{{ $breadcrumb->title }}}</li>
			@endif
            <?php $counter++; ?>
		@endforeach
	</ol>
@endif
    
    <a class="btn hidden-lg hidden-md" id="toggle-toolbar" href="#toolbar-container">{{ trans('administration.common_actions') }}...</a>
</div>

<hr />