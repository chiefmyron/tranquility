<div class="form-group">
	{!! Form::label('name', trans('administration.common_name')) !!}
	{!! Form::text('name', null, ['class' => 'form-control', 'autofocus']) !!}	
	{!! FormError::inline('name', Session::get('messages')) !!}
</div>