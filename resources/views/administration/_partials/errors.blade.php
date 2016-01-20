<div id="process-message-container">
@if(isset($messages))
	@foreach($messages as $message)
		@if(!isset($message['fieldId']))
			<div class="alert alert-{{ $message['level'] }}">
                @if(!isset($message['params']))
				{!! trans('messages.'.$message['text']) !!}
                @else
                {!! trans('messages.'.$message['text'], $message['params']) !!}
                @endif
			</div>
		@endif
	@endforeach
@endif
</div>