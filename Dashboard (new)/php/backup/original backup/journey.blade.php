<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lambda</title>
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
                <div class="journeys-box">
                    <div class="row">
                        <div class="col-lg-8 col-md-12 col-sm-12">
                            <div class="box-filter-journey">
                                <h2>Browse over 70 + journeys</h2>
                            </div>

                            <?php
                            $data_choose_tab = [
                              1 => 'English Program',
                              2 => 'Thai Program',
                            ];
                            ?>
                            <ul class="nav nav-tabs level1-tab" id="tab-main-choose-content" role="tablist">
                                <?php foreach ($data_choose_tab as $key => $item) { ?>
                                  <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $key == 1 ? 'active' : ''; ?>" id="choosetab-main-<?php echo $key; ?>" data-bs-toggle="tab" data-bs-target="#choose-main<?php echo $key; ?>" type="button" role="tab" aria-controls="choose-main<?php echo $key; ?>" aria-selected="<?php echo $key == 1 ? 'true' : 'false'; ?>" onclick="handleButtonClickChangeSubTab()">
                                      <?php echo $item; ?>
                                    </button>
                                  </li>
                                <?php } ?>
                            </ul>
                            
                            <div class="tab-content" id="tab-main-choose-content">
                                <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="choosetab-main-<?php echo $key; ?>" tabindex="0">
                                    
                                    @if(@$rows)
                                        @foreach($rows as $key => $val)  
                                        <div class="box-primary">
                                            <div class="row">
                                                <div class="box-item-header">
                                                    <div class="box-title">
                                                        <h2 class="title-box"></h2>
                                                        <p class="text-choose">Choose a subject</p>
                                                    </div>

                                                    <div class="box-status">
                                                        {{@$val->name}}
                                                    </div>
                                                </div>

                                                @if(count($val->subjects)>0)
                                                    @foreach($val->subjects as $k => $v)
                                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                                        <div class="box-item-primary">
                                                            <a href="javascript:{{$val->id}}/{{$v->id}}" class="subject-link">
                                                                <div class="box-img">
                                                                    <img src="{{asset("$v->image")}}" class="img-fluid" alt="">
                                                                    @if(@$key==0)
                                                                    <div class="progress">
                                                                        <div class="progress-bar progress-bar-custom"
                                                                            role="progressbar" style="width: 55%;"
                                                                            aria-valuenow="25" aria-valuemin="0"
                                                                            aria-valuemax="100"></div>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                                <div class="box-content">
                                                                    <h6 class="title-content">{{@$v->name}}</h6>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="col-lg-12">
                                            Coming soon
                                        </div>
                                    @endif

                                </div>
                            </div>


                            

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("$prefix.dashboard-child.layout.javascript")
    <script>
        var fullUrl = window.location.origin + window.location.pathname;
        document.querySelectorAll('.subject-link').forEach((v)=>{
            let url = `${fullUrl}/${v.href.replace('javascript:','')}`;
            v.href = url;
        })
    </script>
</body>

</html>
