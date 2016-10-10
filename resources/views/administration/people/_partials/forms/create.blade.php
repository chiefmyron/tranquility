<div class="form-group">
	{!! Form::label('title', 'Title:') !!}
	{!! Form::text('title', null, ['class' => 'form-control', 'autofocus']) !!}	
	{!! FormError::inline('title', Session::get('messages')) !!}
</div>
	
<div class="form-group">
	{!! Form::label('firstName', 'First name:') !!}
	{!! Form::text('firstName', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('firstName', Session::get('messages')) !!}
</div>	
	
<div class="form-group">
	{!! Form::label('lastName', 'Last name:') !!}
	{!! Form::text('lastName', null, ['class' => 'form-control']) !!}
	{!! FormError::inline('lastName', Session::get('messages')) !!}	
</div>	

<div class="form-group">
	{!! Form::label('position', 'Position:') !!}
	{!! Form::text('position', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('position', Session::get('messages')) !!}
</div>