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
		const fetchLessonsData = (data) => 
		{	
			console.log('All data >>> ',data)
			lessons = data.lessons.length;
			let latest = data.learning.latest? data.learning.latest : null;
			let all_latest = data.learning.all_latest? JSON.parse(data.learning.all_latest) : null;
			console.log('all latest >>> ',all_latest);
			let latest_type = data.learning.latest_type? data.learning.latest_type: 'lesson';
			let active = {type:null,id:null}; 
			
			const posts = document.getElementById('posts');
			const contElement = document.querySelector('.count');
			contElement.querySelector('.lesson').innerHTML = data.length;
			
			data.lessons.map((v,k)=>{
				
				let entryEl = document.createElement('div');
				entryEl.setAttribute('class','entry');
				entryEl.dataset.id = v.id
				entryEl.dataset.type = 'lesson';
				entryEl.innerHTML = `<div class="entry-timeline cursor-pointer" data-href="${fullUrl}/learning"> 
					<i class="fa-solid fa-play" style="font-size: 16px !important;"></i>
					<div class="timeline-divider"></div>
				</div>
				<div class="entry-title title-sm p-3">
					<a data-href="${fullUrl}/learning" class="learn-this"><p class="fw-light">${v.name}</p>
				</div>`;
				if (latest == v.id || latest == null) {
					let a = entryEl.querySelector('a');
					a.classList.add('learn-this');
					// a.href = a.dataset.href;
					// entryEl.querySelector('.cursor-pointer').onclick = (e) => {
					// 	location.href = e.target.closest('.cursor-pointer').dataset.href;
					// }
				}
				
				if(v.id == latest && latest_type=='lesson') {
					active.type = 'lesson';
					active.id = v.id;
					// entryEl.querySelector('.entry-timeline').classList.add('active');
					let a = entryEl.querySelector('a');
					a.href = a.dataset.href;
					entryEl.querySelector('.cursor-pointer').onclick = (e) => {
						location.href = e.target.closest('.cursor-pointer').dataset.href;
					}
				}
				posts.append(entryEl);
				v.practices.map((prt,i) =>
				{
					practiceEl = document.createElement('div');
					practiceEl.setAttribute('class','entry');
					practiceEl.dataset.id = prt.id
					practiceEl.dataset.type = 'practice';
					practiceEl.innerHTML = `<div class="entry-timeline cursor-pointer" data-href="${fullUrl}/learning"> 
						<i class="fa-solid fa-edit" style="font-size: 16px !important;"></i>
						<div class="timeline-divider"></div>
					</div>
					<div class="entry-title title-sm p-3">
						<a data-href="${fullUrl}/learning" class="learn-this"><p class="fw-light">Practices: ${prt.name}</p>
					</div>`;
					if(prt.id == latest && latest_type=='practice') {
						active.type = 'practice';
						active.id = prt.id;
						practiceEl.querySelector('.entry-timeline').classList.add('active');
						let a = practiceEl.querySelector('a');
						a.href = a.dataset.href;
						practiceEl.querySelector('.cursor-pointer').onclick = (e) => {
							location.href = e.target.closest('.cursor-pointer').dataset.href;
						}
					}
					posts.append(practiceEl);
				})
				practices += v.practices.length;
			});
			if(latest == null) {
				const a = posts.querySelector('.learn-this');
				a.href = a.dataset.href;
				a.closest('.entry').querySelector('.cursor-pointer').onclick = (e) => {
					location.href = e.target.closest('.cursor-pointer').dataset.href;
				}
			}
			
			if(data.learning.finished == 1) {
				const entries = document.querySelectorAll('.entry');
				entries.forEach((el,k)=>{
					el.querySelector('.entry-timeline')?.classList.add('complete');
					el.querySelector('.active')?.classList.add('complete');
					el.querySelector('.active')?.classList.remove('active');
					// el.querySelector('a')?.removeAttribute('href');
					el.querySelector('i')?.classList.add('fa-check');
					el.querySelector('i')?.classList.remove('fa-edit');
				})
			}
			document.getElementById('jumpto').href = `${fullUrl}/learning`;
			
			let last = 0
			posts.querySelectorAll('.entry').forEach((v,k)=>{
				if(Number(v.dataset.id) == active.id && v.dataset.type == active.type){
					last = k;
				}
			});
			posts.querySelectorAll('.entry').forEach((v,k)=>{
				if (k<last) {
					v.querySelector('.entry-timeline').classList.add('complete');
					v.querySelector('.fa-play')?.classList.add('fa-check');
					v.querySelector('.fa-edit')?.classList.add('fa-check');
					v.querySelector('a').href = fullUrl+'/learning';
					v.querySelector('.cursor-pointer').onclick = (e) => {
						location.href = e.target.closest('.cursor-pointer').dataset.href;
					}
				}
			})
			if(all_latest && last==0){
				posts.querySelectorAll('.entry').forEach((el,k)=>
				{	
					if(Number(el.dataset.id) == all_latest[k]){
						el.querySelector('.entry-timeline').classList.add('complete');
						el.querySelector('.fa-play')?.classList.add('fa-check');
						el.querySelector('.fa-edit')?.classList.add('fa-check');
						el.querySelector('a').href = fullUrl+'/learning';
						el.querySelector('.cursor-pointer').onclick = (e) => {
							location.href = e.target.closest('.cursor-pointer').dataset.href;
						}
					}
				})
				let setLast = posts.querySelectorAll('.complete').length;
				const lastEl = posts.querySelectorAll('.entry')[setLast];
				lastEl.querySelector('.entry-timeline').classList.add('active')
				lastEl.querySelector('.fa-edit')?.classList.add('fa-play');
				lastEl.querySelector('.fa-edit')?.classList.remove('fa-edit');
				lastEl.querySelector('a').href = fullUrl+'/learning';
				// lastEl.querySelector('.cursor-pointer').onclick = (e) => {
				// 	location.href = e.target.closest('.cursor-pointer').dataset.href;
				// }
				
			}
			contElement.querySelector('.practice').innerHTML = practices;
			contElement.querySelector('.lesson').innerHTML = data.lessons.length;

			let complete = document.querySelectorAll('.complete').length;
			let percent = Math.round(complete * 100 / (lessons+practices));
			progressBar.dataset.percent = percent;
			progressBar.querySelector('.skill-progress-percent').style.width = `${percent}%`;
		}
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