<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.users_heading_users_record', ['name' => $user->getDisplayName()]) }}</h3>
</div>
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.users._partials.panels.user-details')
    <div class="row">
        <div class="col-md-6">
            <dl class="data-list">
                <dt>{{ trans('administration.common_actions') }}</dt>
                <dd>
                    <i class="icon-pencil"></i> <a href="{{ action('Administration\UsersController@update', ['id' => $user->id]) }}" id="dialog-submit" class="ajax" data-ajax-preload-target="modal">{{ trans('administration.users_command_update_user') }}</a><br />
                    <i class="icon-key"></i> <a href="{{ action('Administration\UsersController@changePassword', ['id' => $user->id]) }}" id="dialog-submit" class="ajax" data-ajax-preload-target="modal">{{ trans('administration.users_command_change_password') }}</a>
                </dd>
            </dl>
        </div>
        <div class="col-md-6">
            
        </div>    
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('administration.common_go_back') }}</button>
</div>