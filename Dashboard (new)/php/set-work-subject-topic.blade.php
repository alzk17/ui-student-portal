<div class="topic-tree-wrapper">
  <label class="setwork-label">Topic options</label>
  <div class="topic-tree">
    <div class="tree">
      <ul>
        @if(@$mainQuestions)
          @foreach($mainQuestions as $mainQuestion)
            @php
              $sub = $mainQuestion['question_group']->group;
            @endphp
            <li>
              <div class="tree-row">
                <span class="tree-toggle-btn"><i class="fa-solid fa-plus"></i></span>
                <input type="checkbox" id="parent1_{{ $sub->id }}" name="group[]" value="{{ $sub->id }}">
                <label for="parent1_{{ $sub->id }}">{{ $sub->name }}</label>
              </div>

              @if(!empty($mainQuestion['subGroups']))
                <ul>
                  @foreach($mainQuestion['subGroups'] as $sub1Group)
                    @php
                      $sub1 = $sub1Group['question_group']->group;
                    @endphp
                    <li>
                      <div class="tree-row">
                        <span class="tree-toggle-btn"><i class="fa-solid fa-plus"></i></span>
                        <input type="checkbox" id="parent{{ $sub->id }}_child_{{ $sub1->id }}" name="group[]" value="{{ $sub1->id }}">
                        <label for="parent{{ $sub->id }}_child_{{ $sub1->id }}">{{ $sub1->name }}</label>
                      </div>

                      @if(!empty($sub1Group['subSubGroups']))
                        <ul>
                          @foreach($sub1Group['subSubGroups'] as $sub2Group)
                            @php
                              $sub2 = $sub2Group->group;
                            @endphp
                            <li>
                              <div class="tree-row">
                                <input type="checkbox" id="{{ $sub2->id }}" name="group[]" value="{{ $sub2->id }}">
                                <label for="{{ $sub2->id }}">{{ $sub2->name }}</label>
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
  </div>
</div>