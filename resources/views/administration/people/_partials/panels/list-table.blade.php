		<div>
			<table class="table table-striped data-table entity-person">
				<thead>
					<tr class="action-strip">
						<td colspan="7">
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
							{{ trans('administration.people_label_name') }}	
						</th>
						<th>
							{{ trans('administration.people_label_position') }}	
						</th>
						<th>
							{{ trans('administration.people_label_company') }}	
						</th>
						<th>
							{{ trans('administration.common_email_address') }}
						</th>
						<th>
							{{ trans('administration.address_label_phone_number') }}
						</th>
						<th>
							{{ trans('administration.people_label_user_account') }}
						</th>
					</tr>
			@foreach ($people as $person)
<?php
$emailAddress = $person->getPrimaryAddress('email');
if (!is_null($emailAddress)) {
	$emailAddress = $emailAddress->toString();
}
?>		
					<tr>
						<td class="checkboxcol">
							<input type="checkbox" name="id" value="{{ $person->id }}" class="record-select checkbox" />
						</td>
						<td>
							<a href="{{ action('Administration\PeopleController@show', [$person->id]) }}" data-secondary-info="{{ $emailAddress }}">{{ $person->firstName.' '.$person->lastName}}</a>
						</td>
						<td>
							{{ $person->position }}
						</td>
						<td>
							@if (!is_null($person->getAccount()))
							<a href="{{ action('Administration\AccountsController@show', [$person->getAccount()->id]) }}">{{ $person->getAccount()->name }}</a>
							@endif
						</td>
						<td>
							@if (!is_null($person->getPrimaryAddress('email')))
							<a href="mailto:{{ $person->getPrimaryAddress('email')->toString() }}">{{ $person->getPrimaryAddress('email')->toString() }}</a>
							@endif
						</td>
						<td>
							@if (!is_null($person->getPrimaryAddress('phone')))
							<a href="tel:{{ $person->getPrimaryAddress('phone')->toString() }}">{{ $person->getPrimaryAddress('phone')->toString() }}</a>
							@endif
						</td>
						<td>
							@if (!is_null($person->getUserAccount()))
							Yes
							@endif
						</td>
					</tr>
			@endforeach
					<tr>
						<td class="footer" colspan="7"></td>
					</tr>
				</tbody>
			</table>
			<div class="data-table-footer">
			
			</div>
{{ $people->links() }}
		</div>