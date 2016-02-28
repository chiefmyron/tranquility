		@foreach ($content as $person)
		<div class="media">
			<div class="media-left">
				<a href="#">
					<img class="media-object" width="64" height="64" src="/backend/images/user-avatar-default.png" alt="">
				</a>
			</div>
			<div class="media-body">
				<a href="{{ action('Administration\PeopleController@show', [$person->id]) }}"><h3 class="media-heading">{{ $person->firstName.' '.$person->lastName}}</h3></a>
				<div class="body">{{$person->position}}</div>	
			</div>
		</div>
		@endforeach