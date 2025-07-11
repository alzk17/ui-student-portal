<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lambda</title>
    @include("$prefix.dashboard-child.layout.stylesheet")
</head>

<body>
      <div class="wrapper">
        @include("$prefix.dashboard-child.layout.sidebar")
         <div class="main">
            <div class="border-header-page">
                <div class="container-custom">
                    @include("$prefix.dashboard-child.layout.navbar")
                </div>
              </div>

              <div class="container-custom">
                <div class="box-set-work-main">
                    
                  <div class="box-set-work1">
                    <h2 class="heading-setwork">Choose type of practice</h2>
                    <ul class="row nav nav-pills mb-3 " id="pills-tab-setwork" role="tablist">
                      <li class="col-md-6 col-12 nav-item" role="presentation">
                        <button class="nav-link active" id="custom-practice-tab" data-bs-toggle="pill" data-bs-target="#custom-practice" type="button" role="tab" aria-controls="custom-practice" aria-selected="true">
                          <h5> Custom Practice</h5>
                          <p>Set some practice question to improve in a topic</p>
                        </button>
                      </li>
                      <li class="col-md-6 col-12 nav-item" role="presentation">
                        <button class="nav-link" id="mock-test-tab" data-bs-toggle="pill" data-bs-target="#mock-test" type="button" role="tab" aria-controls="mock-test" aria-selected="false">
                          <h5> Mock Test</h5>
                          <p>Set an extra test to build exam skills and confidence.</p>
                        </button>
                      </li>
                    </ul>
        
                    <!-- Tab -->
                    <div class="tab-content" id="pills-tabContent-setwork">
                      <!-- Tab1 -->
                      <div class="tab-pane fade show active" id="custom-practice" role="tabpanel" aria-labelledby="custom-practice-tab" tabindex="0">
                        <form id="form_submit" method="POST" enctype="multipart/form-data" class="">
                        @csrf
                        <!-- 01. Choose Subject -->
                            <div class="box-set-work2">
                                <div class="question">
                                    <h4 class="heading-setwork">01. Choose Subject</h4>
                                    <div class="show_subject"></div>
                                </div>
                            </div>
                            <!--02. Choose Topics  -->
                            <div class="box-set-work3">
                                <div class="question">
                                    <h4 class="heading-setwork">02. Choose Topics</h4>
                                    <div class="show_data"></div>
                                </div>
                            </div>

                            
            
                            
                            <!--  Detail -->
                            <div class="box-set-work4">
                                <div class="question">
                                    <h4 class="heading-setwork">03. Add Details</h4>
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                            <div class="mb-3">
                                                <label for="input-name" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="practice_name" name="practice_name" placeholder="Enter a name">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-12"></div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                            <div class="mb-3">
                                                <label for="range-time" class="form-label">Time Limit</label>
                                                <input type="range" class="form-range" id="range-time" name="time_limit" min="0" max="200" step="5" value="0">
                                                <p id="value-range-time">No time limit</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                            <div class="mb-3">
                                                <label for="range-question" class="form-label">Number of question</label>
                                                    <input type="range" class="form-range" id="range-question" name="number_question" min="5" max="100" step="5" value="0">
                                                    <p id="value-range-question">0 Question</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
            
                            <div class="row">
                            <div class=" col-12">
                                <div class="box-btn">
                                <button type="button" onclick="submit_pratice();" class="btn btn-outline-primary btn-settest">Set practice</button>
                                </div>
                            </div>
                            </div>
                            </ul>
                        </form>
                      </div>
        
                      <!-- Tab2 -->
                      <div class="tab-pane fade" id="mock-test" role="tabpanel" aria-labelledby="mock-test-tab" tabindex="0">
        
                          <div class="row">
                            <div class="col-12">
                              <div class="box-item-filter">
                                <div class="box-input">
                                  <div class="search">
                                    <input type="text" id="searchmocktest" name="searchmocktest" oninput="searchMockTest(this.value)" class="form-control" placeholder="คำค้นหา">
                                    <i class="fa fa-search"></i>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div id="show_search_mocktest" class="show_search_mocktest mb-5" > </div>
        
                        <!-- Level 2 -->
                        

                        <ul id="show_topic_01" class="nav nav-tabs nav-level2" id="tab-setwork"role="tablist">
                            @if (@$mocktests)
                                @foreach (@$mocktests as $key => $item)
                                    <li class="nav-item" role="presentation">
                                        <button onclick="top_nav({{ $item->id }});" class="nav-link link-under @if (@$key == 0) active @endif" id="mock-tab-{{ @$item->id }}" data-bs-toggle="tab" data-bs-target="#mock-tab-pane-{{ @$item->id }}" type="button" role="tab" aria-controls="mock-tab-pane-{{ @$item->id }}" aria-selected="true">
                                            {{ @$item->name }}
                                        </button>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                        <input type="hidden" id="mocktests_topic_id" name="mocktests_topic_id" value="{{@$mocktests[0]->id}}">
                        <div id="show_item_topic" class="show_item_topic" > </div>

                        
        
                    </div>
                  </div>
                </div>
              </div>


         </div>



      </div>
    @include("$prefix.dashboard-child.layout.javascript")
    <script src="{{asset("assets_dashboard/js/bs-switches.js")}}"></script>
    <script>
        const events = ['mousemove', 'touchmove']
        $( document ).ready(function() {
          top_nav({{@$mocktests[0]->id}});
        });
        function top_nav(mocktest_topic_id){
         var mocktests_topic_id = $('#mocktests_topic_id').val(mocktest_topic_id);
         $.ajax({
            type: 'GET',
            url: "dashboard-child/mocktest-subject-get",
            data: {
               "_token": "{{ csrf_token() }}",
               mocktest_topic_id:mocktest_topic_id,
            },
            dataType: 'html',
            success: function(data) {
               $('#searchmocktest').val(null);
               // $('#show_topic_01').attr("hidden",false);
               // $('#show_topic_02').attr("hidden",true);
               $('.show_item_topic').html(data);
            }
         });
      }
        $.each(events, function(k, v) {
            $('#range-time').on(v, function() {
                const value = $('#range-time').val()
                $('#value-range-time').text(`Time limit ${value} mins`)
            });

            $('#range-question').on(v, function() {
                const value = $('#range-question').val()
                $('#value-range-question').text(`${value} Question `)
            });
        })
        jQuery(".bt-switch").bootstrapSwitch();
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


        // Script 

            function submit_pratice() {
            var formData = new FormData($("#form_submit")[0]);
            var level_id = $('#level_id').val();
            var practice_name = $('#practice_name').val();
            if(practice_name == "" || level_id == ""){
                toastr.error("Sorry, please complete the information.");
                return false;
            }
            Swal.fire({
                icon: 'warning',
                title: 'Please press confirm to complete the transaction.',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: `Cancel`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "dashboard-child/create-pratice",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(data) {
                        if (data.status == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: data.message,
                                    text: data.desc,
                                    showCancelButton: false,
                                    confirmButtonText: 'Close',
                                }).then((result) => {
                                    location.href = "{{url("dashboard-child")}}";
                                });
                            } 
                            else if (data.status == 500) {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.message,
                                    text: data.desc,
                                    showCancelButton: false,
                                    confirmButtonText: 'Close',
                                });
                            }
                            else if (data.status == 501) {
                                Swal.close();
                                $('#show_duplicate').attr('hidden',false);
                            }
                        }
                    })
                }else{
                    return false;
                }
            });
    
            return false;
        }

        $(document).ready(function() {
            selectChild();
            $(document).on('click', '.subject-radio', function () {
               var selectedSubjectId = $(this).data('subject-id');
               $.ajax({
                     type: 'GET',
                     url: "get/subject/practice",
                     data: {
                        subject_id: selectedSubjectId,
                     },
                     dataType: 'html',
                     success: function(data) {
                        $('.show_data').html(data);
                     }
               });
            });
        });
        function selectChild(){
           var practice_child_id = {{Auth::guard('child')->id()}};
           $.ajax({
              type: 'GET',
              url: "dashboard-child/check-subject",
              data: {
                 practice_child_id:practice_child_id,
              },
              dataType: 'json',
              success: function(data) {
                  $('.show_subject').html(data.data);
                  $('input.subject-radio:checked').each(function () {
                     select_radio.call(this);
                  });
              }
           });
        }
        function select_radio(){
            var selectedSubjectId = $(this).data('subject-id');
                $.ajax({type: 'GET', url: "get/subject/practice",
                  data: {
                     subject_id: selectedSubjectId,
                  },
                  dataType: 'html',
                  success: function(data) {
                     $('.show_data').html(data);
                  }
            });
         }

         function searchMockTest(query) {
         var mocktests_topic_id = $('#mocktests_topic_id').val();
         if(query == ""){
            top_nav(mocktests_topic_id);
            // $('#show_topic_01').attr("hidden",false);
            // $('#show_topic_02').attr("hidden",true);
         }else{
            $.ajax({
               type: 'GET',
               url: "dashboard-child/searchKeywordMocktest",
               data: {
                  'keyword' : query,
                  'mocktests_topic_id' : mocktests_topic_id,
               },
               dataType: 'json',
               success: function(data) {
                  if (data.status === 'empty') {
                     $('.show_item_topic').html(null);
                  } else if (data.status === 'success') {
                     // $('#show_topic_01').attr("hidden",true);
                     // $('#show_topic_02').attr("hidden",false);
                     $('.show_item_topic').html(data.view);
                  }
               }
            });
         }
        
      }

      function start_test(key){
         var formData = new FormData($("#form_submit_start_test_"+key)[0]);
         $.ajax({
            type: 'POST',
            url: "dashboard-child/startmocktest",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
               if (data.status == 200) {
                     Swal.fire({
                        icon: 'success',
                        title: data.message,
                        text: data.desc,
                        showCancelButton: false,
                        confirmButtonText: 'Close',
                     }).then((result) => {
                        location.reload();
                     });
               } 
               else if (data.status == 500) {
                     Swal.fire({
                        icon: 'error',
                        title: data.message,
                        text: data.desc,
                        showCancelButton: false,
                        confirmButtonText: 'Close',
                     });
               }
               else if (data.status == 501) {
                  $('#show_duplicate').attr('hidden',false);
               }
            }
         });
      }
      
    </script>
</body>

</html>
