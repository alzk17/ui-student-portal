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
               
                <div class="row">
                    <div class="col-xxl-6 col-xl-6 col-md-6 col-sm-6">

                        <div class="accordion-task">

                            <div class="accordion" id="accordion_main">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Task
                                        </button>
                                    </h2>
    
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordion_main">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="box-task">
                                                        <div class="row">
    
                                                            @if(@$pratices)
                                                            @foreach(@$pratices as $key=>$pratice)
                                                                @php 
                                                                $sku = "";
                                                                $answer_practices = \App\Models\Backend\Child_practice_question_answerModel::where(["child_id"=>$child->id, "child_practice_main_id"=>$pratice->id])->where('correct','W')->first();
                                                                if($answer_practices)
                                                                {
                                                                    $question = \App\Models\Backend\Child_practice_questionModel::where('id',$answer_practices->child_practice_question_id)->first();
                                                                    if($question){
                                                                        $sku = $question->sku;
                                                                    }
                                                                }

                                                                $image = @$pratice->subject->image;
                                                                if($pratice->isType != "pratice"){
                                                                    $image = $pratice->image;
                                                                }
                                                                @endphp
                                                                <div class="col-lg-12 col-md-12 col-12">
                                                                    <div class="box-item-task">
                                                                        <div class="row">
                                                                            <div
                                                                                class="col-xxl-9 col-xl-8 col-lg-9 col-md-10 col-9">
                                                                                <div class="box-content">
                                                                                    <div class="row">
                                                                                        <div
                                                                                            class="col-xxl-2 col-xl-6 col-lg-3 col-md-3 col-3">
                                                                                            <img src="{{ \Helper::getImage(@$image) }}" class="img-fluid img-task" alt="">
                                                                                        </div>
                                                                                        <div class="col-xxl-10 col-xl-6 col-lg-7 col-md-9 col-12">
                                                                                            <h6 class="title-box">
                                                                                                {{@$pratice->title}}
                                                                                                @if(\Carbon\Carbon::parse($pratice->created_at)->isToday())
                                                                                                    <div class="box-item-calendar"><i class="icofont-calendar"></i> Today</div>
                                                                                                @endif
                                                                                            </h6>
                                                                                            <p class="sub-title-box">{{@$pratice->number_question}} Questions | {{@$pratice->time_limit}} Min. </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div
                                                                                class="col-xxl-3 col-xl-4 col-lg-3 col-md-2 col-3">
                                                                                <div class="box-button">
                                                                                    <a href="{{url("$folder/practice/$pratice->uuid?sku=$sku")}}" class="btn-circle">
                                                                                        <i class="fa-solid fa-chevron-right"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                              @endif
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
    
                                </div>
                            </div>
    
                        </div>
                        

                    </div>
                    <div class="col-1"></div>
                    <div class="col-xxl-5 col-xl-5 col-md-5 col-sm-5">
                        <?php
                        $today = date('D'); 
                        $weekDays = [
                            'Sun' => 'Su',
                            'Mon' => 'M',
                            'Tue' => 'T',
                            'Wed' => 'W',
                            'Thu' => 'Th',
                            'Fri' => 'F',
                            'Sat' => 'S'
                        ];

                        $days = [];
                        $currentDate = strtotime('last Sunday');

                        foreach ($weekDays as $fullName => $shortName) {
                            $formattedDate = date('Y-m-d', $currentDate); 
                            $icon = ($fullName == $today) ? './assets_dashboard/img/icon/icon-thunder.svg' : './assets_dashboard/img/icon/icon-thunder-nocolor.svg';
                            $days[] = [
                                'short' => $shortName,
                                'date' => $formattedDate,
                                'icon' => $icon
                            ];
                            $currentDate = strtotime('+1 day', $currentDate);
                        }
                        ?>

                        <div class="box-streaks">
                            <h2 class="title-streaks">Streaks</h2>
                            <h1 class="total-streaks">{{@$child->streak_point}} <img src="{{asset("assets_dashboard/img/icon/icon-thunder.svg")}}" class="img-fluid" /></h1>
                            <p class="description-streaks">Complete a task to earn streak!</p>

                            <div class="box-days">
                                @foreach(@$days as $day)
                                    @php  
                                    $icon = "assets_dashboard/img/icon/icon-thunder-nocolor.svg";
                                    $practice_main = \App\Models\Backend\Child_pratice_mainModel::whereDate('complete_date', $day['date'])->count();
                                    if($practice_main>0){
                                        $icon = "assets_dashboard/img/icon/icon-thunder.svg";
                                    }
                                    @endphp
                                    <div class="item-day">
                                        <div class="box-icon">
                                            <img src="{{asset($icon)}}" class="img-fluid" alt="">
                                        </div>
                                        <p class="text-item-day">{{@$day['short']}}</p>
                                        {{-- <p class="date-item-day"><?php echo $day['date']; ?></p> --}}
                                    </div>
                                @endforeach
                            </div>

                            <a href="{{url('dashboard-child/set-work')}}" class="btn btn-set-task">Set Task</a>
                        </div>

                        <!-- End Box Streaks -->

                        <!-- Box Next Reward -->
                        <div class="box-next-reward">
                            <h2>Next Streak Reward</h2>
                            <div class="box-content-next">
                                <div class="box-item-content">
                                    <p>Unlock Pepe the price portrait.</p>
                                    <div class="progress" style="height: 20px;">
                                        @php 
                                        $percent = 0;
                                        $percent =($child->streak_point / 50) * 100;
                                        @endphp
                                        <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{@$percent}}%;" aria-valuenow="{{@$child->streak_point}}" aria-valuemin="0" aria-valuemax="100">{{@$child->streak_point}}/50</div>
                                    </div>
                                </div>
                                <div class="box-item-image">
                                    <img src="{{asset("assets_dashboard/img/mascot2.png")}}" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <!-- End Box Next Reward -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    @include("$prefix.dashboard-child.layout.javascript")
</body>

</html>
