<div class="tree">
    <ul>
        @if(isset($mainQuestions) && count($mainQuestions))
            @foreach($mainQuestions as $main)
                @php $parent = $main['question_group']->group; @endphp
                <li>
                    <div class="tree-row">
                        @if(!empty($main['subGroups']))
                            <span class="tree-toggle-btn"><i class="fa-solid fa-plus"></i></span>
                        @endif
                        <input type="checkbox" id="parent1_{{ $parent->id }}" name="group[]" value="{{ $parent->id }}">
                        <label for="parent1_{{ $parent->id }}">{{ $parent->name }}</label>
                    </div>
                    @if(!empty($main['subGroups']))
                        <ul>
                            @foreach($main['subGroups'] as $child)
                                @php $childGroup = $child['question_group']->group; @endphp
                                <li>
                                    <div class="tree-row">
                                        @if(!empty($child['subSubGroups']))
                                            <span class="tree-toggle-btn"><i class="fa-solid fa-plus"></i></span>
                                        @endif
                                        <input type="checkbox" id="parent{{ $parent->id }}_child_{{ $childGroup->id }}" name="group[]" value="{{ $childGroup->id }}">
                                        <label for="parent{{ $parent->id }}_child_{{ $childGroup->id }}">{{ $childGroup->name }}</label>
                                    </div>
                                    @if(!empty($child['subSubGroups']))
                                        <ul>
                                            @foreach($child['subSubGroups'] as $grandchild)
                                                @php $grand = $grandchild->group; @endphp
                                                <li>
                                                    <div class="tree-row">
                                                        <input type="checkbox" id="{{ $grand->id }}" name="group[]" value="{{ $grand->id }}">
                                                        <label for="{{ $grand->id }}">{{ $grand->name }}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        @endif
    </ul>
</div>