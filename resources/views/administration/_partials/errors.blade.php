<div id="process-message-container">
@if(isset($messages))
	@foreach($messages as $message)
		@if(!isset($message['fieldId']))
			<div class="alert alert-{{ $message['level'] }}">
				{!! trans('messages.'.$message['text']) !!}
			</div>
		@endif
	@endforeach
@endif
</div>