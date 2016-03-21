<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.users_heading_create_user') }}</h3>
</div>
{!! Form::open(array('action' => array('Administration\PeopleController@storeUser', $person->id), 'class' => 'ajax-submit')) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.users._partials.forms.create')
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
    <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.users_heading_create_user') }}</button>
</div>
{!! Form::close() !!}