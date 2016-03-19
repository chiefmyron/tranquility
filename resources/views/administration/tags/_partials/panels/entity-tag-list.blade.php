                    <div id="tag-container">
                        <p class="tag-list">
                            {{-- Start with entity specific tag (not removable) --}}
                            @if ($entity->getEntityType() == 'person')
                            <span class="tag label-entity-{{ $entity->getEntityType() }}"><a href="{{ action('Administration\PeopleController@index') }}">{{ trans('administration.common_entity_type_'.$entity->getEntityType()) }}</a></span>
                                @if (!is_null($entity->getUserAccount()))
                            <span class="tag label-entity-user"><a href="#">{{ trans('administration.common_entity_type_user') }}</a></span>    
                                @endif
                            @elseif ($entity->getEntityType() == 'account')
                            
                            @endif

                            @foreach ($tags as $tag)
                            <span class="tag"><a href="#">{{ $tag->text }}</a> | <a href="{{ action('Administration\TagsController@remove', ['parentId' => $entity->id, 'id' => $tag->id]) }}" class="ajax">&times;</a></span>
                    		@endforeach
                        </p>
                        <a href="{{ action('Administration\TagsController@update', ['parentId' => $entity->id]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.tags_command_add_tag') }}</a>
                    </div>