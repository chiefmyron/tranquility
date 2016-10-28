		<div>
			<table class="table table-striped data-table entity-account">
				<thead>
					<tr class="action-strip">
						<td colspan="6">
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
								</ul>
							</div>
							<div class="actions-container">
								<div class="actions">
									<p><span id="item-selected-counter">0</span>{{ trans('administration.common_selection_num_items')}}: </p>
									<a href="{{ action('Administration\AccountsController@confirmDelete') }}" id="toolbar-delete-multiple-accounts" class="btn btn-primary ajax table-action disabled multi-select" data-ajax-preload-target="modal" role="button">{{ trans('administration.common_delete') }}</a>
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
							{{ trans('administration.common_name') }}
						</th>
						<th>
							{{ trans('administration.address_label_work_address') }}
						</th>
						<th>
							{{ trans('administration.address_label_email_address') }}
						</th>
						<th>
							{{ trans('administration.address_label_phone_number') }}
						</th>
						<th>
							{{ trans('administration.accounts_label_primary_contact') }}
						</th>
					</tr>
			@if (count($accounts) <= 0)
					<tr>
						<td colspan="6" class="text-center large">
								{{ trans('administration.accounts_message_no_records') }}<br />
								<a href="{{ action('Administration\AccountsController@create') }}"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.accounts_heading_create') }}</a>
						</td>
					</tr>
			@else
				@foreach ($accounts as $account)
<?php
// Get primary contact details for account
$emailAddress = $account->getPrimaryAddress('email');
if (!is_null($emailAddress)) {
	$emailAddress = $emailAddress->toString();
}

// Get business address for the account
$businessAddress = $account->getAddresses('physical', 'work');
if (!is_null($businessAddress) && count($businessAddress) > 0) {
	$businessAddress = $businessAddress[0]->getSingleLineAddress();
} else {
	$businessAddress = '';
}
?>		
						<tr>
							<td class="checkboxcol">
								<input type="checkbox" name="id" value="{{ $account->id }}" class="record-select checkbox" />
							</td>
							<td>
								<a href="{{ action('Administration\AccountsController@show', [$account->id]) }}" data-secondary-info="{{ $businessAddress }}">{{ $account->name }}</a>
							</td>
							<td>
								{{ $businessAddress }}
							</td>
							<td>
								@if (!is_null($account->getPrimaryAddress('email')))
								<a href="mailto:{{ $account->getPrimaryAddress('email')->toString() }}">{{ $account->getPrimaryAddress('email')->toString() }}</a>
								@endif
							</td>
							<td>
								@if (!is_null($account->getPrimaryAddress('phone')))
								<a href="tel:{{ $account->getPrimaryAddress('phone')->toString() }}">{{ $account->getPrimaryAddress('phone')->toString() }}</a>
								@endif
							</td>
							<td>
								@if (!is_null($account->getPrimaryContact()))
								<a href="{{ action('Administration\PeopleController@show', [$account->getPrimaryContact()->getPerson()->id]) }}">{{ $account->getPrimaryContact()->getPerson()->getFullName() }}</a>
								@endif
							</td>
						</tr>
				@endforeach
			@endif		
					<tr>
						<td class="footer" colspan="6"></td>
					</tr>
				</tbody>
			</table>
			{{ $accounts->links() }}
		</div>