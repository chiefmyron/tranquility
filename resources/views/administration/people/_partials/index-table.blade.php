    	<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th class="checkboxcol">
						<input type="checkbox" name="selectAll" id="selectAll" value="" class="selectAll checkbox" />
					</th>
					<th>
						{{ trans('administration.people_name') }}	
					</th>
					<th>
						{{ trans('administration.common_email_address') }}
					</th>
					<th>
						User account
					</th>
					<th>
						Logged in
					</th>
				</tr>
		@foreach ($content as $person)
				<tr>
					<td class="checkboxcol">
						<input type="checkbox" name="id" value="{{ $person->id }}" class="record-select checkbox" />
					</td>
					<td>
						<a href="{{ action('Administration\PeopleController@show', [$person->id]) }}">{{ $person->firstName.' '.$person->lastName}}</a>
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
				</tr>
		@endforeach
				<tr>
					<td class="tablefooter" colspan="6"></td>
				</tr>
			</table>
		</div>