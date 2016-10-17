		<div>
			<table class="table table-striped data-table entity-person">
				<thead>
					<tr class="action-strip">
						<td colspan="5">
							<div class="filter">
								<h4>Filter results</h4>
								<ul class="filter-items">
									<li>
										Show:
										<select>
											<option>All people</option>
											<option>Only people with user accounts</option>
										</select>
									</li>
									<li>
										<input type="checkbox" class="checkbox" />Show names as <em>Last name, First name</em>
									</li>
								</ul>
							</div>
							<div class="actions-container">
								<div class="actions">
									<p><span id="item-selected-counter">4</span>{{ trans('administration.common_selection_num_items')}}: </p>
									<a href="{{ action('Administration\PeopleController@confirmAction', ['action' => 'delete']) }}" id="toolbar-delete-multiple-people" class="btn btn-primary ajax table-action disabled multi-select" data-ajax-preload-target="modal" role="button">Delete</a>
								</div>
							</div>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="checkboxcol">
							<input type="checkbox" name="selectAll" value="" class="record-select-all checkbox" />
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
			@foreach ($people as $person)
<?php
// Get primary contact details for person
$emailAddress = $person->getPrimaryAddress('email');
if (!is_null($emailAddress)) {
	$emailAddress = $emailAddress->toString();
}
$userAccount = $person->getUserAccount();

?>		
					<tr>
						<td class="checkboxcol">
							<input type="checkbox" name="id" value="{{ $person->id }}" class="record-select checkbox" />
						</td>
						<td>
							<a href="{{ action('Administration\PeopleController@show', [$person->id]) }}" data-secondary-info="{{ $emailAddress }}">{{ $person->firstName.' '.$person->lastName}}</a>
						</td>
						<td>
							{{ $emailAddress or 'No email' }}
						</td>
						<td>
							
						</td>
						<td>
							
						</td>
					</tr>
			@endforeach
					<tr>
						<td class="footer" colspan="6"></td>
					</tr>
				</tbody>
			</table>
			<div class="data-table-footer">
			
			</div>
{{ $people->links() }}
		</div>