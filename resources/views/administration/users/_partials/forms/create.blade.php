<div class="form-group">
    {!! Form::label('username', trans('administration.common_username')) !!}
    {!! Form::hidden('username') !!}
    <p>{{ $user->username }}</p> 
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
            {!! Form::radio('active', 1) !!}
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
{!! Form::hidden('securityGroupId') !!}