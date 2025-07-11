
<div class="row">
    <div class="col-md-12">
        <div class="form-check-box-set-work topic-options">
        <label for="multi-select" class="form-label">Topic Options</label>

         <div class="tree">
            <ul>

                @if(@$mainQuestions)
                    @foreach(@$mainQuestions as $mainQuestion)
                    @php
                    $sub = $mainQuestion['question_group']->group;
                    @endphp
                    <li>
                        <span class="toggle-btn" style="font-size:20px;">+</span>
                        <input type="checkbox" id="parent1_{{$sub->id}}" value="{{@$sub->id}}" name="group[]">
                        <label for="parent1_{{$sub->id}}">{{ $sub->name }}</label>
                        <ul style="display: none;">
                            @if(!empty($mainQuestion['subGroups']))
                                @foreach($mainQuestion['subGroups'] as $sub1Group)
                                @php
                                $sub1 = $sub1Group['question_group']->group;
                                @endphp
                                <li>
                                    <span class="toggle-btn" style="font-size:20px;">+</span>
                                    <input type="checkbox" id="parent{{@$sub->id}}_child_{{$sub1->id}}" value="{{@$sub1->id}}" name="group[]">
                                    <label for="parent{{@$sub->id}}_child_{{$sub1->id}}">{{ $sub1->name }}</label>

                                    @if(!empty($sub1Group['subSubGroups']))
                                    <ul>
                                        @foreach($sub1Group['subSubGroups'] as $sub2Group)
                                        @php 
                                        $sub2 = $sub2Group->group;
                                        @endphp
                                        <li style="margin-left:50px;">
                                            <input type="checkbox" id="{{$sub2->id}}" value="{{@$sub2->id}}" name="group[]">
                                            <label for="{{$sub2->id}}">{{@$sub2->name}}</label>
                                        </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </li>
                                @endforeach
                           @endif
                        </ul>
                    </li>
                    @endforeach
                @endif
                
            </ul>
        </div>

      </div>
   </div>
</div>

<script>
    $(document).ready(function() {
      $('.toggle-btn').on('click', function() {
         var $this = $(this);
         $this.text($this.text() == '+' ? '-' : '+');
         $this.siblings('ul').toggle();
      });
      $('.tree input[type="checkbox"]').on('change', function() {
         $(this).siblings('ul').find('input[type="checkbox"]').prop('checked', this.checked);
      });
   });
</script>