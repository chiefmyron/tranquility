{{-- Tags section --}}
<li>
    <div class="icon">
        <i class="icon-tag"></i>
    </div>
    <div class="data-item">
        <span class="heading">{{ trans('administration.common_tags') }}</span>
            <div id="tag-container">
                <p class="tag-list">
                    @include('administration.tags._partials.panels.entity-tag-list', ['entity' => $entity, 'tags' => $entity->getTags(), 'editable' => true])
                </p>
            </div>
        <span class="action"><a href="{{ action('Administration\TagsController@update', ['parentId' => $entity->id]) }}" class="ajax" data-ajax-preload-target="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.tags_command_add_tag') }}</a></span>
    </div>
</li>