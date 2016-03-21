@if (count($emailAddresses) > 0)
<div class="form-group">	
    {!! Form::label('usernameOption', trans('administration.common_username')) !!}    
    <div class="radio">
        <label>
            {!! Form::radio('usernameOption', 'existing', true) !!}
            Use an existing email address:
        </label>
        @if (count($emailAddresses) == 1)
        <em>{{ $emailAddresses[0] }}</em>
        {!! Form::hidden('existingUsername', $emailAddresses[0]) !!}
        @else
        {!! Form::select('existingUsername', $emailAddresses, null, ['class' => 'form-control']) !!}
        @endif
    </div>
    
    <div class="radio">
        <label>
            {!! Form::radio('usernameOption', 'new') !!}
            Create a new username / email address:
        </label>
        {!! Form::email('newUsername', null, ['class' => 'form-control']) !!}
    </div>
    {!! FormError::inline('usernameOption', Session::get('username')) !!}
</div>
@else
<div class="form-group">
    {!! Form::label('newUsername', trans('administration.common_username')) !!}
    {!! Form::email('newUsername', null, ['class' => 'form-control']) !!}	
    {!! FormError::inline('newUsername', Session::get('username')) !!}
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

{{-- TODO: Security group hidden until ACL implemented --}}
{!! Form::hidden('securityGroupId', 1) !!}