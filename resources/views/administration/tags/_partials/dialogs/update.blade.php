<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.tags_heading_update') }}</h3>
</div>
{!! Form::model($tags, array('action' => array('Administration\TagsController@store'), 'class' => 'ajax-submit')) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    
    <div class="form-group">
        {!! Form::label('tags', trans('administration.tags_label_tags')) !!}
        {!! Form::text('tags', implode(',', $tags), ['class' => 'form-control', 'data-role' => 'tagsinput', 'data-autocomplete' => action('Administration\TagsController@autocomplete'), 'autofocus']) !!}	
        {!! FormError::inline('tags', Session::get('messages')) !!}
    </div>
</div>
<div class="modal-footer">
    {!! Form::hidden('parentId', $parentId) !!}
    <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.tags_heading_update') }}</button>
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
</div>
{!! Form::close() !!}