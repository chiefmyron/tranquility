<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.users_command_change_password') }}</h3>
</div>
{!! Form::open(array('action' => array('Administration\UsersController@saveNewPassword', $id), 'class' => 'ajax-submit')) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    <div class="form-group">
        {!! Form::label('password', trans('administration.users_label_new_password')) !!}
        {!! Form::password('password', ['class' => 'form-control']) !!}	
        {!! FormError::inline('password', Session::get('messages')) !!}
    </div>
    
    <div class="form-group">
        {!! Form::label('passwordConfirm', trans('administration.users_label_new_password_confirm')) !!}
        {!! Form::password('passwordConfirm', ['class' => 'form-control']) !!}	
        {!! FormError::inline('passwordConfirm', Session::get('messages')) !!}
    </div>
</div>
<div class="modal-footer">
    {!! Form::hidden('id', $id) !!}
    <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.users_command_change_password') }}</button>
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
</div>
{!! Form::close() !!}