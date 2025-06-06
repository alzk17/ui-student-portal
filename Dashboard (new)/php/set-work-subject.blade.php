@php
    $arritem = [
        '1' => [
            'name' => 'Beginner',
            'star' => 1,
        ],
        '2' => [
            'name' => 'Novice',
            'star' => 2,
        ],
        '3' => [
            'name' => 'Balance',
            'star' => 3,
        ],
        '4' => [
            'name' => 'Challenge',
            'star' => 4,
        ],
        '5' => [
            'name' => 'God Mode',
            'star' => 5,
        ],
    ];
@endphp

<div class="grade-filter">
    <label class="setwork-label">Year group</label>
    <select name="grade_id" id="grade_id" class="lmd-select">
        <option value="">Select a year group</option>
        @if(@$grades)
            @foreach(@$grades as $key => $grade)
                <option value="{{$grade->id}}" @if($key==0) selected @endif>{{$grade->name}}</option>
            @endforeach
        @endif
    </select>
</div>

<div class="topic-tree-wrapper">
    <label class="setwork-label">Topic options</label>
    <div class="topic-tree">
        <div class="show_topic_option"></div>
    </div>
</div>

<div class="difficulty-filter">
    <label class="setwork-label">Difficulty level</label>
    <div class="difficulty-options">
        @foreach ($arritem as $key => $item)
            <div class="difficulty-card">
                <label for="checkbox-{{$key}}">
                     <span class="star">
                        @for ($i = 1; $i <= $item['star']; $i++)
                            <i class="fa-solid fa-star"></i>
                        @endfor
                    </span>
                    <input type="checkbox" class="checkbox-style" id="checkbox-{{$key}}" name="level_id[]" value="{{$key}}">
                </label>
            </div>
        @endforeach
    </div>
</div>

<script>

   $(document).ready(function() {
      checkAndSendData();
      $('input[name="level_id[]"], #grade_id').on('change', function() { checkAndSendData();});
   });

   function checkAndSendData() {
      var selectedLevels = $('input[name="level_id[]"]:checked').map(function() {
         return $(this).val();
      }).get();

      var selectedGradeId = $('#grade_id').val();
      $.ajax({
         type: 'GET',
         url: 'get/subject/practice/topic_option',
         data: {
            subject_id: {{$subject_id}},
            level_ids: selectedLevels,
            grade_id: selectedGradeId
         },
         dataType: 'html',
         success: function(response) {
            $('.show_topic_option').html(response);
            initTreeState();
         }
   });

   }

   function toggleCheckbox(element) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
    }
</script>