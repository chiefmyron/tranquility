<div class="row">
    <div class="col-xs-12">
        {!! Form::open(['action' => 'Administration\SearchController@index', 'method' => 'GET', 'class' => 'search-form']) !!}
            <input class="form-control large" type="text" name="q" placeholder="{{ trans('administration.search_label_placeholder') }}" value="{{ $query['searchTerm'] }}">
            <button type="submit" class="btn btn-primary submit-btn">
                <span class="glyphicon glyphicon-search"></span>
            </button>
        {!! Form::close() !!}
    </div>
</div>