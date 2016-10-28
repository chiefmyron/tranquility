<div class="alert alert-inline alert-{{ $level }}" data-form-validation-element="#{{ $fieldId }}">
	@foreach ($messages as $message)
	{{ trans('messages.'.$message) }}<br />
	@endforeach
</div>