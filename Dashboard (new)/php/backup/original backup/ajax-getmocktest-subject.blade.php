@if(@$data)
    @foreach(@$data as $key=>$item)
    <div class="box-content-style2">
        <div class="row">
            <div class="col-xl-1 col-lg-2 col-md-2 col-12">
                <img src="{{\Helper::getImage($item->image)}}" class="img-fluid" alt="">
            </div>
            <div class="col-xl-8 col-lg-7 col-md-7 col-12">
                <h5 class="title-subject">{{@$item->name}}</h5>
                <span class="detail-subject"><i class="fa-regular fa-question-circle"></i> {{@$item->amount_question}} Questions </span>
                <span class="detail-subject"><i class="fa-regular fa-clock" style="margin-left:5px;"></i> {{@$item->time_question}} mins</span>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-12">
                <div class="box-btn">
                    <button type="button" class="btn btn-outline-primary btn-settest" data-bs-toggle="modal" data-bs-target="#modalSetTest-{{@$key}}">
                        + Add to do list
                     </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-settest" id="modalSetTest-{{@$key}}" tabindex="-1" aria-labelledby="modalSetTest-{{@$key}}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
           <div class="modal-content content-set-test">
              <div class="modal-body">
              <form id="form_submit_start_test_{{$key}}" method="POST" enctype="multipart/form-data" class="">
                @csrf
                    <input type="hidden" id="question_topic_id" name="question_topic_id" value="{{$item->id}}">
                    <img src="{{\Helper::getImage($item->image)}}" class="img-fluid" alt="">
                    <h5 class="title-subject">{{@$item->name}}</h5>
                    <span class="detail-subject">{{@$item->amount_question}} Questions {{@$item->time_question}} mins  </span>
                    <button onclick="start_test({{$key}});" type="button" class="btn btn-blue btn-starttest">
                        Start Test
                    </button>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" class="btn btn-exit" data-bs-dismiss="modal">
                    <i class="fa-regular fa-circle-xmark"></i> </button>
              </div>
           </div>
        </div>
     </div>
    @endforeach
@endif


