<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.address_heading_update_address') }}</h3>
</div>
{!! Form::model($address, array('action' => array('Administration\AddressController@store'), 'class' => 'ajax-submit')) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.addresses._partials.forms.physical')
</div>
<div class="modal-footer">
    {!! Form::hidden('id', $address->id) !!}
    {!! Form::hidden('parentId', $address->getParentEntity()->id) !!}
    {!! Form::hidden('category', 'physical') !!}
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
    <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.address_heading_update_address') }}</button>
</div>
{!! Form::close() !!}