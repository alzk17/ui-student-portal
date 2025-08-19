<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>hello brother</title>
  @include("$prefix.dashboard-child.layout.stylesheet")

  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/components.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/header.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/layout.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/sidebar.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/modal.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/tooltip-widgets.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets_dashboard/css/lesson-map.css') }}?v={{ time() }}">
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
                <div class="learning-journey">
                    <input type="hidden" name="userId" value="{{ Auth::guard('child')->user()->id }}">
                    <div class="row">
                        @if (@$navs)
                            <div class="my-4 text-smaller text-info">
                                @for ($i = 0; $i < count($navs); $i++)
                                    @if ($i > 0)
                                        &gt; {{ $navs[$i]->name }}
                                    @else
                                        {{ $navs[$i]->name }}
                                    @endif
                                @endfor
                            </div>
                        @endif
                    </div>


                    <div class="lesson-body row justify-content-between mt-5">
				
                        <div class="card backInLeft animated my-3 position-relative" data-delay="200" style="max-width: 500px; background-color: rgba(255,255,255,0.45)"> 
                            <div class="card-body float-start ">
                                @if($subject->image !='')
                                    <div><img src="{{$subject->image}}" width="200"></div>
                                @else
                                    <div><img src="images/learning-journey.png" width="200"></div>
                                @endif
                                <h5 class="mt-3"><span>{{$subject->journey}}</span></h5>
                                <h3 class="fw-bold text-black fs-4 ">{{$subject->name}} </h3>
                            
                                <div class="font-secondary fw-normal fs-6 mb-3 ">
                                    Start your algebra Journey here with an introduction to variable and equations. 
                                </div>
                                <div class="d-flex count">
                                    <div> <i class="uil-book-open"></i> <span class="lesson">0</span> Lessons  </div> 
                                    <div><i class="uil-file-edit-alt ms-2"></i> <span class="practice">0</span> Practices </div> 
                                </div>
								<div class="my-3 d-flex justify-content-between">
                                    <a href="javascript:" id="back" class="btn button-border btn-light"><i class="bi-chevron-left"></i> Back </a> 
                                    <div>
                                        <a href="javascript:" id="jumpto" class="btn button-border btn-light"> <i class="uil-plus"></i> Jump to ... </a> 
                                        <a href="javascript:" id="reset" class="btn button-border btn-primary ms-2"> Reset </a> 
                                    </div>
                                </div>
                                
								<div class="skill-progress mt-2" data-percent="40" data-speed="500">
									<div class="progress" style="height: 10px">
										<div class="progress-bar progress-bar-custom skill-progress-percent" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
										</div>
									</div>
								</div>
                            
                            </div> 
                        </div>

                        
						<div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 overflow-x-scroll max-vh-100 animate__animated animate__fadeInUp" data-delay="300">
                            <div class="d-flex justify-content-start">
                                <div class="my-3">
                                    <h2 class="fw-bold title-start"> Start <span>Journey</span> </h2>
                                </div>
                            </div>
                            <main class="postcontent order-lg-last ms-3">		
                                <!-- Posts ========================== -->
                                <div id="posts" class="post-timeline">
                                </div>
                            </main>
                        </div>
                    </div>		



                </div>
            </div>
        </div>
    </div>
    @include("$prefix.dashboard-child.layout.javascript")
    <script>
		var fullUrl = window.location.href;
		var pathname = window.location.pathname.replace('/','');
		var journeyId = pathname.split('/')[3];
		var subjectId = pathname.split('/')[4];
		var userId = document.querySelector('input[name="userId"]').value;
		var progressBar = document.querySelector('.skill-progress');
		const completeIcon = document.createElement('i');
		completeIcon.setAttribute('class','fa-solid fa-check');
		const playIcon = document.createElement('i');
		playIcon.setAttribute('class','fa-solid fa-play');
		const practiceIcon = document.createElement('i');
		practiceIcon.setAttribute('class','fa-solid fa-edit');
		let practices = 0;
		let lessons = 0;
		
		const tabs = document.createElement('div');
		tabs.id = "canvas-TabContent";
		tabs.classList.add('tab-content');
		
		const getLessons = async () => {
			const request = await fetch(`${fullUrl}/get/lessons`);
			if(!request.ok) console.log(request.statusText);
			const response = await request.json();
			fetchLessonsData(response);
		}
		getLessons();
        const fetchLessonsData = (data) => {
            console.log('All data >>> ', data);

            const posts = document.getElementById('posts');
            const contElement = document.querySelector('.count');

            // This will hold the count of each type
            let lessonCount = 0;
            let practiceCount = 0;

            // Loop through all lessons provided by the API
            data.lessons.map((v, k) => {
                // Create a new entry element for the map
                let entryEl = document.createElement('div');
                entryEl.setAttribute('class', 'entry');
                entryEl.dataset.id = v.id;
                entryEl.dataset.type = v.type; // Get the type ('digest' or 'application')

                // Construct the correct URL with the lesson ID as a query parameter
                const lessonUrl = `${fullUrl}/learning?lesson=${v.id}`;

                // Use the correct icon based on the lesson type
                const iconClass = v.type === 'application' ? 'fa-solid fa-edit' : 'fa-solid fa-play';

                // Set the HTML for the lesson node
                entryEl.innerHTML = `
                    <div class="entry-timeline cursor-pointer">
                        <i class="${iconClass}" style="font-size: 16px !important;"></i>
                        <div class="timeline-divider"></div>
                    </div>
                    <div class="entry-title title-sm p-3">
                        <a href="${lessonUrl}" class="learn-this">
                            <p class="fw-light">${v.name}</p>
                        </a>
                    </div>`;

                // Add the new node to the map
                posts.append(entryEl);

                // Increment the count for the lesson/practice display
                if (v.type === 'application') {
                    practiceCount++;
                } else {
                    lessonCount++;
                }
            });

            // Update the lesson and practice counts on the page
            contElement.querySelector('.lesson').innerHTML = lessonCount;
            contElement.querySelector('.practice').innerHTML = practiceCount;

            // We can re-implement the logic for showing which nodes are 'active' or 'complete' later.
            // For now, this focuses on building the map correctly.
        };
		// const progress = () => {
		// }

		document.addEventListener('click',function(e){
			const backBtn = e.target.closest('#back');
			if(backBtn){
				window.location.replace('/dashboard-child/journey');
			}
			const resetBtn = e.target.closest('#reset');
			if(resetBtn) {
				if(confirm('Confirm learning reset') == true){ 
					let req = learningReset();
					req.then(res => {
						if(res.status != 200){
							alert(`${res.status} ${res.statusText}`)
						}else{
							location.reload();
						}
					})
				}
			}
		})
		const  learningReset = async() => {
			const request = await fetch(`${window.location.href}/reset`);
			if(!request.ok){
				return request;
			}
			const response = await request.json();
			return response;
		}
	</script>
</body>

</html>