@if (count($emailAddresses) > 0)
<div class="form-group">	
    {!! Form::label('usernameOption', trans('administration.common_username')) !!}    
    <div class="radio">
        <label>
            {!! Form::radio('usernameOption', 'existing', true) !!}
            {{ trans('administration.users_label_use_existing_email_address') }}:
        </label>
        @if (count($emailAddresses) == 1)
        <em>{{ reset($emailAddresses) }}</em>
        {!! Form::hidden('existingUsername', reset($emailAddresses)) !!}
        @else
        {!! Form::select('existingUsername', $emailAddresses, null, ['class' => 'form-control', 'autofocus']) !!}
        @endif
    </div>
    
    <div class="radio">
        <label>
            {!! Form::radio('usernameOption', 'new') !!}
            {{ trans('administration.users_label_create_new_username') }}:
        </label>
        {!! Form::email('addressText', null, ['class' => 'form-control']) !!}
        {!! FormError::inline('addressText', Session::get('messsages')) !!}
    </div>
    {!! FormError::inline('usernameOption', Session::get('username')) !!}
</div>
@else
<div class="form-group">
    {!! Form::label('addressText', trans('administration.common_email_address')) !!}
    {!! Form::email('addressText', null, ['class' => 'form-control', 'autofocus']) !!}	
    {!! FormError::inline('addressText', Session::get('messsages')) !!}
</div>
@endif

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

<div class="form-group">
	{!! Form::label('localeCode', trans('administration.users_label_locale')) !!}
	{!! Form::selectFromReferenceData('localeCode', ['tableName' => 'cd_locales', 'translateCode' => true, 'translatePrefix' => 'locales.'], null, ['class' => 'form-control']) !!}
	{!! FormError::inline('localeCode', Session::get('messages')) !!}
</div>	
	
<div class="form-group">
	{!! Form::label('timezoneCode', trans('administration.users_label_timezone')) !!}
    {!! Form::selectFromReferenceData('timezoneCode', ['tableName' => 'cd_timezones', 'translateCode' => true, 'translatePrefix' => 'timezones.'], null, ['class' => 'form-control']) !!}
	{!! FormError::inline('timezoneCode', Session::get('messages')) !!}	
</div>	

<div class="form-group">	
    {!! Form::label('active', trans('administration.users_label_account_status')) !!}    
    <div class="radio">
        <label>
            {!! Form::radio('active', 1, true) !!}
            {{ trans('administration.users_status_active') }}
        </label>
    </div>
    <div class="radio">
        <label>
            {!! Form::radio('active', 0) !!}
            {{ trans('administration.users_status_suspended') }}
        </label>
    </div>
    {!! FormError::inline('active', Session::get('messages')) !!}
</div>

{!! Form::hidden('parentId', $person->id) !!}
{{-- TODO: Security group hidden until ACL implemented --}}
{!! Form::hidden('securityGroupId', 1) !!}
