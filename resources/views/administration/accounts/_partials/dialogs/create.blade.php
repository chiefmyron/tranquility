<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.accounts_heading_create') }}</h3>
</div>
{!! Form::open(array('action' => array('Administration\AccountsController@store'), 'class' => 'ajax-submit')) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.accounts._partials.forms.create')
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.accounts_command_create') }}</button>
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
</div>
{!! Form::close() !!}