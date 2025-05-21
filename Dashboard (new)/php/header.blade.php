<header id="header" class="border-bottom-0 sticky-header " data-mobile-sticky="true">
    <div id="header-wrap" class="p-2 pb-3 shadow">
        {{-- <div class="container"> --}}
        <div class="header-row justify-content-lg-between m-0 pt-2"> 
                <div class="col-1 col-sm-2 ps-3 display-7">
                <a class="back-to-journey"><i class="fa fa-close"></i></a>
            </div>
            <div class="col-10 col-sm-8 mx-auto center">
                <h4 class="display-6 text-uppercase">{{$subject->journey}} - {{$subject->name}}</h4>  
            </div>
            <div class="col-1 col-sm-2 pe-3 display-7" align="right">
                <strong class="me-2">0</strong> <i class="bi-coin"></i> 
                {{-- <span class="timer" style="display:inline-flex;width:82px;">00:00:00</span> --}}
            </div>
        </div>
        <div class="clear"></div>
        <div class="mx-auto center m-0 p-0" style="max-width: 710px;">
            <div class="skill-progress " data-percent="0" data-speed="1100" style="--cnvs-progress-height: 0.25rem; --cnvs-progress-trackcolor: #fff;">  
                   <div class="skill-progress-bar"> 
                       <div class="skill-progress-percent bg-color skill-animated" style="--cnvs-progress-speed: 1000ms; width: 0%"></div> 
                   </div> 
             </div>
       </div>	
        {{-- </div> --}}
    </div> 
    <div class="clear"></div>
</header>