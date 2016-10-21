<div class="form-group">
	{!! Form::label('title', trans('administration.people_label_title')) !!}
	{!! Form::text('title', null, ['class' => 'form-control', 'autofocus']) !!}	
	{!! FormError::inline('title', Session::get('messages')) !!}
</div>
	
<div class="form-group">
	{!! Form::label('firstName', trans('administration.people_label_first_name')) !!}
	{!! Form::text('firstName', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('firstName', Session::get('messages')) !!}
</div>	
	
<div class="form-group">
	{!! Form::label('lastName', trans('administration.people_label_last_name')) !!}
	{!! Form::text('lastName', null, ['class' => 'form-control']) !!}
	{!! FormError::inline('lastName', Session::get('messages')) !!}	
</div>	

<div class="form-group">
	{!! Form::label('position', trans('administration.people_label_position')) !!}
	{!! Form::text('position', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('position', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::label('accountId', trans('administration.people_label_company')) !!}
	{!! Form::text('accountId', null, ['class' => 'form-control', 'data-custom-control' => 'entity-select-single', 'data-custom-control-entity-type' => 'account', 'data-custom-control-datasource' => action('Administration\SearchController@autocomplete')]) !!}	
	{!! FormError::inline('accountId', Session::get('messages')) !!}
</div>