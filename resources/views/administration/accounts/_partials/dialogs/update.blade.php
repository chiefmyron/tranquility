<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.accounts_heading_update', ['name' => $account->name]) }}</h3>
</div>
{!! Form::model($account, ['action' => 'Administration\AccountsController@store', 'class' => 'ajax-submit']) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.accounts._partials.forms.create')
    {!! Form::hidden('id', $account->id) !!}
</div>
<div class="modal-footer">
    {!! Form::submit(trans('administration.accounts_command_update'), ['class' => 'btn btn-primary pull-right', 'id' => 'dialog-submit']) !!}
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
</div>
{!! Form::close() !!}