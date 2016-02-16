<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.address_heading_add_new_address') }}</h3>
</div>
{!! Form::open(array('url' => $formAction, 'class' => 'ajax-submit')) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.addresses._partials.forms.create')
</div>
<div class="modal-footer">
    {!! Form::hidden('parentId', $parentId) !!}
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
    <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.address_heading_add_new_address') }}</button>
</div>
{!! Form::close() !!}