@if(Session::has('messages'))
	@foreach(Session::get('messages') as $message)
		@if(!isset($message['fieldId']))
			<div class="alert alert-{{ $message['level'] }}">
				{!! trans('messages.'.$message['text']) !!}
			</div>
		@endif
	@endforeach
@endif