<div class="form-group">
	{!! Form::label('username', 'Email address:') !!}
	{!! Form::text('username', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('username', Session::get('messages')) !!}
</div>
	
<div class="form-group">
	{!! Form::label('password', 'Password:') !!}
	{!! Form::password('password', ['class' => 'form-control']) !!}	
	{!! FormError::inline('password', Session::get('messages')) !!}
</div>	

<div class="form-group">
	{!! Form::label('passwordConfirm', 'Confirm password:') !!}
	{!! Form::password('passwordConfirm', ['class' => 'form-control']) !!}	
	{!! FormError::inline('passwordConfirm', Session::get('messages')) !!}
</div>	
	
<div class="form-group">
	{!! Form::label('timezoneCode', 'Timezone:') !!}
	{!! Form::text('timezoneCode', null, ['class' => 'form-control']) !!}
	{!! FormError::inline('timezoneCode', Session::get('messages')) !!}	
</div>	
	
<div class="form-group">
	{!! Form::label('localeCode', 'Locale:') !!}
	{!! Form::text('localeCode', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('localeCode', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::label('active', 'Active:') !!}
	{!! Form::text('active', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('active', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::label('securityGroupId', 'Account type:') !!}
	{!! Form::text('securityGroupId', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('securityGroupId', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
</div>