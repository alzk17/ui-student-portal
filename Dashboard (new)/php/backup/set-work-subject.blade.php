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
<div class="form-group ">
												 
   <label for="form-select">Difficulty</label> 
   
      <div id="difficulty" class="row col-mb-10 mb-0">
         @foreach ($arritem as $key => $item)  
               <div class="col-xl-2 col-12 set-work-size">
               <div class="border-box">
                     <div class="round">
                        <input type="checkbox" class="checkbox-style ml-2" id="checkbox-{{$key}}" name="level_id[]" value="{{$key}}">
                        <label for="checkbox-{{@$key}}" class="checkbox-style-1-label"></label>
                        <div>
                           {{ @$item['name'] }}
                           <div class="star">
                                 @for ($i = 1; $i <= $item['star']; $i++)<i class="fa-solid fa-star"></i>@endfor
                           </div>
                        </div>
                     </div>
               </div>
               </div>
         @endforeach
   </div>
</div>


<div class="row">
   <div class="col-md-4 col-12">
       <div class="form-group form-select-set-work">
           <label for="form-select">Year Group</label>
           <select class="form-select" id="grade_id" name="grade_id">
            <option value="">กรุณาเลือกชั้นศึกษา</option>
            @if(@$grades)
               @foreach(@$grades as $key=>$grade)
                  <option value="{{$grade->id}}" @if($key==0) selected @endif>{{$grade->name}}</option>
               @endforeach
            @endif
         </select>
       </div>
   </div>
</div>

<div class="show_topic_option"></div>

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
         }
   });

   }

   function toggleCheckbox(element) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
    }
</script>