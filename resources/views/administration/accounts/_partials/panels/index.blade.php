		<div>
			<table class="table table-striped data-table entity-account">
				<thead>
					<tr class="action-strip">
						<td colspan="5">
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
							<input type="checkbox" name="selectAll" id="selectAll" value="" class="selectAll checkbox" />
						</th>
						<th>
							{{ trans('administration.common_name') }}
						</th>
						<th>
							{{ trans('administration.address_label_country') }}
						</th>
						<th>
							{{ trans('administration.address_label_city') }}
						</th>
						<th>
							{{ trans('administration.address_label_phone_number') }}
						</th>
					</tr>
			@if (count($accounts) <= 0)
					<tr>
						<td colspan="5" class="text-center large">
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

?>		
						<tr>
							<td class="checkboxcol">
								<input type="checkbox" name="id" value="{{ $account->id }}" class="record-select checkbox" />
							</td>
							<td>
								<a href="{{ action('Administration\AccountsController@show', [$account->id]) }}" data-secondary-info="{{ $emailAddress }}">{{ $account->name }}</a>
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
			@endif		
					<tr>
						<td class="footer" colspan="6"></td>
					</tr>
				</tbody>
			</table>
			{{ $accounts->links() }}
		</div>