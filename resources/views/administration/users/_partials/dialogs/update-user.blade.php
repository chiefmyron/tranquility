<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.users_command_update_user') }}</h3>
</div>
{!! Form::model($user, ['action' => 'Administration\UsersController@store', 'class' => 'ajax-submit']) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.users._partials.forms.update')
    {!! Form::hidden('id', $user->id) !!}
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.users_command_update_user') }}</button>
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
</div>
{!! Form::close() !!}