<div class="box-headerpage">
    <div class="box-group-title">
        <div class="box-image">
            <img src="{{asset(@$child->image)}}" class="img-fluid" alt="">
        </div>

        <div class="box-item-content">
            {{-- <h1 class="title-page">{{@$subject->name}}</h1>
            <h6 class="sub-title-page">{{@$subject->journey}}</h6> --}}

            <h1 class="title-page">{{@$navbar_name}}</h1>
            <h6 class="sub-title-page">{!!@$navbar_detail!!}</h6>
        </div>
    </div>

    <div class="box-point">
        <div class="box-coin">
            <div class="number-coins">{{@$child->streak_point}} </div>
            <div> <img src="{{asset("assets_dashboard/img/icon/icon-thunder.svg")}}" class="img-fluid" alt=""> </div>
        </div>

        <div class="box-coin">
            <div class="number-coins">{{@$child->wallet_point}} </div>
            <div> <img src="{{asset("assets_dashboard/img/icon-menu/9.png")}}" class="img-fluid" alt=""> </div>
        </div>
    </div>
</div>