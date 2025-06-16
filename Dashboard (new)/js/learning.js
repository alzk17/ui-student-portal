var fullUrl = window.location.href;
var pathname = window.location.pathname.replace("/", "");
var journeyId = pathname.split("/")[2];
var subjectId = pathname.split("/")[3];
var userId = document.querySelector('input[name="userId"]').value;
var latest = document.querySelector('[name="latest"]').value;
var latest_type = document.querySelector('[name="latest_type"]').value;
var finished = document.querySelector('[name="finished"]').value;
var controls = document.getElementById("footer");
var targetTime = 15 * 60;
let currentTimerStamps;
let timesStamps = localStorage.getItem("timesStamps") ? JSON.parse(localStorage.getItem("timesStamps")) : [];

const currentTimesStamps = () => {
  let current = { user: userId, journey: journeyId, subject: subjectId, timer: 0 };
  if (timesStamps != "undefined") {
    timesStamps?.map((v) => {
      if (v.user == userId && v.journey == journeyId && v.subject == subjectId) {
        current = v;
        return false;
      }
    });
  }
  return current;
};

let currentTimer = currentTimesStamps();
const timerDisplay = document.querySelector(".timer");
if (document.querySelector(".back-to-journey"))
  document.querySelector(".back-to-journey").href = window.location.href.replace("/learning", "");
const card = document.createElement("div");
card.setAttribute("data-delay", 200);
card.innerHTML = `<div class="card-body"><div class="lesson-content"></div></div>`;
const tabs = document.createElement("div");
tabs.classList.add("tab-content");
tabsid = "canvas-TabContent";
card.querySelector(".lesson-content").append(tabs);

let lessons;
const getLessons = async () => {
  const request = await fetch(`${fullUrl.replace("/learning", "")}/get/lessons`);
  if (!request.ok) console.log(request.statusText);
  const response = await request.json();
  fetchLessonsData(response);
  // setTimeout(()=> { progressBar(); },500);
};
// function updateTimerDisplay() {
// 	const hours = Math.floor(currentTimer.timer / 3600);
//     const minutes = Math.floor((currentTimer.timer % 3600) / 60);
//     const secs = currentTimer.timer % 60;
// 	const formattedTime = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
//     timerDisplay.innerHTML = `${formattedTime}`;
// }
function updateTimerById(data) {
  let current;
  for (var i in timesStamps) {
    if (timesStamps[i].user == userId && timesStamps[i].journey == journeyId && timesStamps[i].subject == subjectId) {
      current = timesStamps[i];
      break;
    }
  }
  if (current == undefined) timesStamps.push(data);
  localStorage.setItem("timesStamps", JSON.stringify(timesStamps));
}
// function startCountUp() {

//     if (!currentTimerStamps) {
//         currentTimerStamps = setInterval(() => {
//             currentTimer.timer++;
//             updateTimerDisplay();
// 			updateTimerById(currentTimer);
//         }, 1000);
//     }
// }
const stopCountUp = () => clearInterval(currentTimerStamps);

function resetCountUp() {
  stopCountUp();
  let p;
  timesStamps.find((v) => {
    if (v.user === data.id) {
      p = k;
      return false;
    }
  });
  timesStamps.remove(p);
}
getLessons();
const fetchLessonsData = (data) => {
  console.log("1. Fetch Lessons Data >>>", data);
  data.lessons.map((v, k) => {
    let tabPane = document.createElement("div");
    tabPane.classList.add("tab-pane");
    if (k < 1) tabPane.dataset.first = true;
    if (latest == v.id && latest_type == "lesson") {
      tabPane.classList.add("active");
      if (data.learning.finished != 1) {
        tabPane.dataset.latest = true;
      }
    }
    if (!latest && latest_type == null && k == 0) tabPane.classList.add("active");

    tabPane.id = `canvas-tab-${k}`;
    tabPane.dataset.type = "lesson";
    tabPane.dataset.id = v.id;
    tabPane.innerHTML = `<div id="page${k}0" class="page first-page min-vh-85">${v.content}`;
    let run = 0;
    if (v.pages.length > 0) {
      let continuBtn = document.createElement("button");
      continuBtn.type = "button";
      continuBtn.setAttribute("class", "button button-border rounded tab-btn-control px-4 m-0 me-2 mt-4");
      continuBtn.dataset.control = `continue`;
      // continuBtn.dataset.scrollto = `page${k}${k+1}`;
      continuBtn.innerHTML = 'Continue <i class="uil uil-arrow-circle-down ms-1"></i>';
      tabPane.querySelector(".first-page").append(continuBtn);
      let lastPage = v.pages.length;

      v.pages.map((vpa, kpa) => {
        let pageNo = `page${run + k}${kpa + (run + 1)}`;
        let page = document.createElement("div");
        let continueHTM = `<button type="button" class="button button-border rounded tab-btn-control px-4 m-0 me-2 mt-4" data-control="continue" data-scrollto="${pageNo}" >Continue <i class="uil uil-arrow-circle-down ms-1"></i></button>`;
        page.id = `${pageNo}`;
        page.classList.add("page", "mt-4", "min-vh-85", "d-none");
        page.innerHTML = `<div>${vpa.detail}</div>${continueHTM}`;
        page.dataset.show = false;

        if (lastPage == kpa + 1) {
          page.dataset.lastPage = true;
          page.querySelector('[data-control="continue"]').remove();
        }
        tabPane.append(page);
      });
      run++;
    }
    console.log(`Append lesson tab ${k + 1} โก๏ธ`, tabPane);
    card.querySelector(".tab-content").append(tabPane);

    v.practices.map((vp, kp) => {
      let tabPractice = document.createElement("div");
      tabPractice.classList.add("tab-pane");
      if (latest == vp.id && latest_type == "practice" && data.learning.finished != 1) {
        tabPractice.classList.add("active");
        tabPractice.dataset.latest = true;
      }
      tabPractice.id = `canvas-tab-practice-${k}${kp}`;
      tabPractice.dataset.type = "practice";
      tabPractice.dataset.typeQuestion = vp.type_question;
      tabPractice.dataset.id = vp.id;
      tabPractice.innerHTML = `<h2>Practice</h2>${vp.question}`;
      let typeQuestion = vp.type_question;
      let typeMath = vp.type_math;

      setHint(vp);
      if (vp.answers.length > 0) {
        let answersEl = document.createElement("div");
        answersEl.setAttribute("class", "answers-content");
        let ddLabel = document.createElement("div");
        ddLabel.classList.add("instructions", "mt-5");
        if (typeQuestion == "drag-drop") {
          let dropEl = document.createElement("div");
          dropEl.classList.add("d-flex", "justify-content-center");
          vp.answers.map((v, k) => {
            let dropItem = document.createElement("div");
            dropItem.classList.add("code-block");
            dropItem.innerHTML = `<div id="drop-answer-${kp}${k + 1}" class="drop-zone" data-default-text="${
              k + 1
            }" ondrop="drop(event)" ondragover="allowDrop(event)"><p>${k + 1}</p></div>`;
            dropEl.append(dropItem);
          });
          answersEl.append(dropEl);

          ddLabel.innerHTML = '<p clnass="text-center">Click or drag and drop to fill the blank.</p>';
          answersEl.append(ddLabel);
        }
        let dragEl = document.createElement("div");
        dragEl.classList.add("d-flex", "justify-content-center", "mt-3", "mb-1", "answers");
        answersEl.append(dragEl);
        let dragDropEl = document.createElement("div");
        dragDropEl.classList.add("answer-options", "answer-options", "justify-content-around");
        dragDropEl.setAttribute("ondrop", "drop(event)");
        dragDropEl.setAttribute("ondragover", "allowDrop(event)");
        dragDropEl.setAttribute("style", "min-height:64px;min-width:20rem;border:2px solid #ddd;border-radius:10px;");
        let html;
        vp.answers.map((va, ka) => {
          let label = document.createElement("div");
          label.classList.add("mx-1", "answer-text");
          if (typeQuestion == "file") {
            label.style.display = "grid";
            html = `
							<div><input name="answer[]" id="answer${k}${kp}${ka}" class="btn-check" type="checkbox" value="${va.id}">
							<label for="answer${k}${kp}${ka}" class="btn btn-outline-primary m-1"><img src="${va.answer_image}" width="100%"/></label></div>
						`;
          }
          if (typeQuestion == "input-user" && typeMath == "N") {
            html = `<label>Answer ${ka + 1}</label><input type="text" name="answer[]" class="form-control" placeholder="">`;
          }
          if (typeQuestion == "input-user" && typeMath == "Y") {
            label.classList.add("w-75");
            // <input type="text" name="answer[]" class="form-control d-none"></input>
            html = `<label>Answer </label><input type="text" name="math-field" disabled class="form-control d-none"></input><math-field id="math-input-${kp}" name="answer[]" virtual-keyboard-mode="auto" class="form-control mx-auto"></math-field>`;
          }
          if (typeQuestion == "drag-drop") {
            let dragItem = document.createElement("div");
            dragItem.id = `answer-item-${kp}${ka}`;
            dragItem.classList.add("draggable");
            dragItem.draggable = true;
            dragItem.setAttribute("ondragstart", "drag(event)");
            dragItem.dataset.answer = va.id;
            dragItem.innerText = va.answer_text;
            dragDropEl.append(dragItem);
          }
          if (typeQuestion == "text") {
            html = `<div><input name="answer[]" id="answer${k}${kp}${ka}" class="btn-check" type="checkbox" value="${
              va.id
            }" text="${va.answer_text}">
						<label for="answer${k}${kp}${ka}" class="btn btn-outline-primary m-1"><kbd>${ka + 1}</kbd> ${
              va.answer_text
            }</label></div>`;
          }
          if (typeQuestion != "drag-drop") {
            label.innerHTML = html;
            answersEl.querySelector(".answers").append(label);
          }
        });
        if (typeQuestion == "drag-drop") {
          answersEl.querySelector(".answers").append(dragDropEl);
        }
        if (vp?.myAnswer) {
          const cor = `<i class="bi-check-circle-fill"></i><strong> Great job !</strong> You got it right! +1 <i class="bi-coin color-btc"></i>`;
          const inc = `<i class="bi-x-circle-fill"></i><strong> That's incorrect. </strong> Don't worry ! Learning takes practice.`;
          let myAnswer = JSON.parse(vp.myAnswer.testId);
          // let myAnswerText = JSON.parse(vp.myAnswer.answer_text);
          const correctIcon = document.createElement("i");
          correctIcon.classList.add("bi-check-circle", "correct");
          vp.answers.map((ans, i) => {
            answersEl.querySelectorAll('[name="answer[]"]').forEach((e) => {
              e.disabled = true;
              if (ans.correct_status == "1" && ans.id == e.value) {
                let thisLabel = e.closest(".answer-text").querySelector(".btn");
                thisLabel.classList.add("btn-success");
                thisLabel.append(correctIcon);
              }
            });
          });
          answersEl.querySelectorAll('[name="answer[]"]').forEach((e, k) => {
            if (myAnswer != null) {
              if (myAnswer.indexOf(e.value) >= 0) {
                e.checked = true;
              }
            }
          });
          let answerLabel = document.createElement("div");
          answerLabel.classList.add("alert", "bg-transparent", "fadeInUp", "animated", "mt-4");
          answerLabel.dataset.animate = "fadeInUp";
          answerLabel.dataset.delay = 200;
          let yourAnswer = answersEl.querySelector('[name="answer[]"]:checked');

          if (vp.myAnswer.answer_type == "checkbox") {
            if (yourAnswer.nextElementSibling.classList.contains("btn-success")) {
              answerLabel.classList.add("border-success", "text-success");
              answerLabel.innerHTML = cor;
            } else {
              answerLabel.classList.add("border-danger", "text-danger");
              answerLabel.innerHTML = inc;
            }
          }
          if (vp.myAnswer.answer_type == "drag-drop") {
            let dragAnswer = [];
            myAnswer.map((a) => {
              dragAnswer.push(answersEl.querySelector(`[data-answer="${a}"]`));
            });
            answersEl.querySelectorAll(".code-block").forEach(function (el, k) {
              el.querySelector(".drop-zone > p").replaceWith(dragAnswer[k]);
              el.querySelector(".draggable").draggable = false;
            });
            answersEl.querySelector(".instructions").classList.add("d-none");
            answersEl.querySelector(".answers").classList.add("d-none");
            vp.answers.sort((a, b) => a.list_answer.localeCompare(b.list_answer));
            let anwserCheck = [];
            vp.answers.map((v, k) => {
              anwserCheck[k] = v.id == answersEl.querySelectorAll(".draggable")[k].dataset.answer ? true : false;
            });
            // console.log(anwserCheck)
            // console.log(anwserCheck.find((e) => e == true));
            if (anwserCheck.find((e) => e == false) == false) {
              answerLabel.classList.add("border-danger", "text-danger");
              answerLabel.innerHTML = inc;
            } else {
              answerLabel.classList.add("border-success", "text-success");
              answerLabel.innerHTML = cor;
            }
          }
          if (vp.myAnswer.answer_type == "input" || vp.myAnswer.answer_type == "input-user") {
            if (data.learning.finished == 1) {
              if (vp.type_math == "Y") {
                answersEl.querySelector("math-field")?.classList.add("d-none");
                let input = answersEl.querySelector('[name="math-field"]');
                input.value = vp.myAnswer.answer_text.replaceAll('"', "");
                input.classList.remove("d-none");

                if (vp.myAnswer.answer_text.replaceAll('"', "") == vp.correct_answer) {
                  answerLabel.classList.add("border-success", "text-success");
                  answerLabel.innerHTML = cor;
                } else {
                  answerLabel.classList.add("border-danger", "text-danger");
                  answerLabel.innerHTML = inc;
                }
              } else {
                answersEl.querySelector('[type="text"]')?.classList.remove("d-none");
                answersEl.querySelector('[type="text"]').value = JSON.parse(vp.myAnswer.answer_text);
                if (answersEl.querySelector('[type="text"]')?.value == vp.correct_answer) {
                  answerLabel.classList.add("border-success", "text-success");
                  answerLabel.innerHTML = cor;
                } else {
                  answerLabel.classList.add("border-danger", "text-danger");
                  answerLabel.innerHTML = inc;
                }
              }
            }
          }
          answersEl.append(answerLabel);
        }
        tabPractice.append(answersEl);
        card.querySelector(".btn-hint")?.classList.remove("d-none");
      }
      card.querySelector(".tab-content").append(tabPractice);
      console.log(`Append practice(${kp + 1}) to tab ${k + 1} โก๏ธ`, tabPractice);
    });
  });
  let lastEl = document.createElement("div");
  lastEl.classList.add("tab-pane");
  lastEl.id = "summary";
  lastEl.dataset.type = "summary";
  lastEl.dataset.next = data.next?.id;
  lastEl.innerHTML = `
		<div class="alert center m-3">
			<img src="images/finish.png" width="500">
			<h2 class="center font-normal"> Lesson Complete </h2>
			<h3 class="font-normal text-secondary">Total Earned</h3>
			<div class="d-flex justify-content-center"><h1 class="mr-4 earned">1</h1><i class="bi-coin"></i></div>
			<button type="button" class="button button-black button-rounded tab-btn-control tab-btn-continue m-0" data-control="continue">Continue</button>
		</div>
	`;
  card.querySelector(".tab-content").append(lastEl);

  let last = card.querySelector(".tab-content").querySelectorAll(".tab-pane");
  last[last.length - 1].dataset.last = true;
  let x = 0;
  card.querySelectorAll(".tab-pane").forEach((el, k) => {
    if (el.classList.contains(".active")) {
      x = k;
    }
    if (k > x) {
      el.setAttribute("data-learning", false);
    }
  });

  if (card.querySelector(".active") == null) card.querySelector('[data-first="true"]').classList.add("active");
  setTimeout(() => {
    document.querySelector(".lesson-body").append(card);
  }, 500);
};

const getExaminationResults = async() => {
  try{
    const request = await fetch(`${fullUrl}/dashboard-child/journey/${journeyId}/${subjectId}/examination/results`); 
    if (!request.ok) {
      throw new Error(`Response status: ${request.statusText}`);
    }
    const response = request.json();
  }catch(error){
    console.log(error)
  }
}
const setHint = (hint) => {
  if (hint) {
    // console.log(document.querySelector('.tab-content'))
    let hintBtn = document.querySelector(".hint-btn");
    hintBtn.href = "#hintModal";
    let Modal = document.getElementById("hintModal");
    let tab = document.createElement("div");
    tab.dataset.hint = hint.id;
    tab.classList.add("d-none");
    tab.innerHTML = hint.hint;
    Modal.querySelector(".hint-content").append(tab);
  }
};

const sendAnswer = async (data) => {
  if (document.querySelector('[name="finished"]').value != "1") {
    try {
      const request = await fetch(`${fullUrl}/answer`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
      });
      if (!request.ok) {
        throw new Error(`Response status: ${request.statusText}`);
      }
      const response = await request.json();
      console.log("Send answer response โก๏ธ", response);
      return response;
    } catch (error) {
      console.log(error);
    }
  }
};

const adjustLearning = async (lessonId) => {
  try {
    const request = await fetch(`${fullUrl}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: {
        userId: userId,
        current: lessonId,
      },
    });
    if (!request.ok) {
      throw new Error(`Response status: ${request.statusText}`);
    }
    const response = await request.json();
    return response;
  } catch (error) {
    console.log(error.message);
  }
};
const progressBar = () => {
  const progressBar = document.querySelector(".skill-progress");
  const lessonContent = document.querySelector(".lesson-content");
  const allLesson = lessonContent?.querySelectorAll(".tab-pane");
  const currentTab = lessonContent.querySelector(".active");
  let now = 0;
  allLesson.forEach((v, k) => {
    if (v == currentTab) {
      now = Math.round(((k + 1) * 100) / allLesson.length);
      return false;
    }
  });
  // console.log(allLesson.length)
  progressBar.dataset.percent = now;
  progressBar.querySelector(".skill-progress-percent").style.width = `${now}%`;
};
const setLatest = async (data) => {
  try {
    const request = await fetch(`${fullUrl.replace("/learning", "")}/set-latest`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(data),
    });
    if (!request.ok) {
      throw new Error(`Response status: ${request.statusText}`);
    }
    const response = await request.json();
    if (response.statusCode == 200 || response.statusCode == 200) {
      document.querySelector('[name="latest_type"]').value = data.latest_type;
      document.querySelector('[name="latest"]').value = data.latest;
      document.querySelectorAll(".tab-pane").forEach((el) => {
        if (el.dataset.type == data.latest_type && el.dataset.id == data.latest) {
          el.removeAttribute("data-learning");
          return false;
        }
      });
    }

    return response;
  } catch (error) {
    console.log(error.message);
  }
};
const Finished = async () => {
  try {
    const request = await fetch(`${fullUrl}/finished`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ timer: currentTimer.timer }),
    });
    if (!request.ok) {
      throw new Error(`Response status: ${request.statusText}`);
    }
    const response = await request.json();
    return response;
  } catch (error) {
    console.log(error.message);
  }
};

const answerRequired = (tab) => {
  let response = tab.querySelector('input[name^="answer"]:checked') == undefined ? false : true;
  return response;
};

const NextTab = (lessonContent, allTabs, current, control) => {
  const backBtn = document.querySelector('[data-control="back"]');
  const nextBtn = document.querySelector('[data-control="next"]');
  const continueBtn = document.querySelector('[data-control="continue"]');
  const summaryTab = document.querySelector('[data-type="summary"]');

  allTabs.forEach((v, k) => {
    if (v == current) {
      let action = control == "next" ? k + 1 : k - 1;
      if (action == allTabs.length - 1) nextBtn.classList.add("invisible");
      else nextBtn.classList.remove("invisible");
      v.classList.remove("active");
      v.removeAttribute("data-latest");
      v.querySelector(".current-page")?.classList.remove("current-page");
      allTabs[action]?.classList.add("active");
      setTimeout(() => {
        let newCurrentTab = document.querySelector(".tab-content > .active");
        if (document.querySelector('[name="finished"]').value != "1") newCurrentTab.dataset.latest = true;
        if (newCurrentTab.querySelectorAll(".page").length > 0) {
          document.querySelector("#footer").classList.add("d-none");
          backBtn.classList.remove("d-none");
          continueBtn.classList.remove("d-none");
          continueBtn.dataset.scrollto = newCurrentTab.querySelector(".page").id;
        } else {
          document.querySelector("#footer").classList.remove("d-none");
        }
        if (newCurrentTab.dataset.lastPage == "true") {
          document.querySelector("#footer").classList.remove("d-none");
        }
        if (control == "next") {
          if (summaryTab == newCurrentTab) {
            getExaminationResults();
            stopCountUp();
            Finished();
            // if (currentTimer.timer <= targetTime) {
            // 	document.querySelector('.extra-coin').classList.remove('d-none');
            // }
            FinalControlButton();

          }
          let type = newCurrentTab?.dataset.type;
          let id = newCurrentTab?.dataset.id;

          if (type == "practice") document.querySelector(".hint-btn").classList.remove("d-none");
          else document.querySelector(".hint-btn").classList.add("d-none");

          if (newCurrentTab.dataset.latest == "true" && finished != 1) setLatest({ latest_type: type, latest: id });
        }
      }, 100);
      return false;
    }
  });
};

function allowDrop(event) {
  event.preventDefault();
}
function drag(event) {
  event.dataTransfer.setData("text", event.target.id);
}

function drop(event) {
  event.preventDefault();
  var data = event.dataTransfer.getData("text");
  var draggedElement = document.getElementById(data);
  var dropZone = event.target;
  if (dropZone.classList.contains("drop-zone")) {
    if (dropZone.classList.contains("occupied")) {
      var existingElement = dropZone.querySelector(".draggable");

      if (existingElement) {
        returnToOptions(existingElement);
      }
    }
    dropZone.innerHTML = "";
    dropZone.appendChild(draggedElement);
    dropZone.classList.add("occupied");
    dropZone.setAttribute("data-answer", draggedElement.getAttribute("data-answer"));
  } else if (dropZone.classList.contains("answer-options")) {
    returnToOptions(draggedElement);
  }
}
function fillFirstAvailableSpace(answerId) {
  var answer = document.getElementById(answerId);
  var dropZones = document.getElementsByClassName("drop-zone");
  for (var i = 0; i < dropZones.length; i++) {
    if (!dropZones[i].classList.contains("occupied")) {
      dropZones[i].innerHTML = "";
      dropZones[i].appendChild(answer);
      dropZones[i].classList.add("occupied");
      dropZones[i].setAttribute("data-answer", answer.getAttribute("data-answer"));
      break;
    }
  }
}
function returnToOptions(draggedElement) {
  // Check if the draggedElement is still in the DOM before accessing its parentNode
  if (draggedElement && draggedElement.parentNode) {
    var parentZone = draggedElement.parentNode;
    if (parentZone.classList.contains("drop-zone")) {
      var defaultText = parentZone.getAttribute("data-default-text");
      parentZone.innerHTML = "<p>" + defaultText + "</p>";
      parentZone.classList.remove("occupied");
      parentZone.setAttribute("data-answer", "");
    }
  }
  document.querySelector(".answer-options").appendChild(draggedElement);
}
document.querySelectorAll(".draggable").forEach((item) => {
  item.addEventListener("click", function () {
    if (this.parentNode.classList.contains("drop-zone")) {
      returnToOptions(this);
    } else {
      fillFirstAvailableSpace(this.id);
    }
  });
});
document.addEventListener("click", function (e) {
  const currentTab = document.querySelector(".tab-content > .active");
  const lessonControl = e.target.closest(".tab-btn-control");
  const backBtn = e.target.closest('[data-control="back"]');
  const nextBtn = e.target.closest('[data-control="next"]');
  if (backBtn) {
    const currentPage = document.querySelector(".current-page");
    if (currentPage) {
      document.querySelector('[data-control="continue"]').dataset.scrollto = currentPage.getAttribute("id");
      currentPage.classList.remove("current-page");
    }
    if (currentPage?.previousElementSibling) {
      document.querySelector('[data-control="back"]').dataset.scrollto =
        currentPage.previousElementSibling.getAttribute("id");

      if (backBtn.dataset.scrollto) {
        currentPage.classList.remove("current-page");
        document.getElementById(backBtn.dataset.scrollto).classList.add("current-page");
        window.scrollTo({
          top: document.getElementById(backBtn.dataset.scrollto).offsetTop,
          behavior: "smooth",
        });
      }
    } else {
      currentPage?.classList.remove("current-page");
      currentTab.classList.remove("active");
      const prevEl = currentTab.previousElementSibling;
      prevEl.classList.add("active");
      if (currentTab.previousElementSibling.querySelectorAll(".page").length > 0) {
        const continueBtn = document.querySelector('[data-control="continue"]');
        continueBtn.dataset.scrollto = prevEl.querySelector(".page").id;
        continueBtn.classList.remove("d-none");
        // document.querySelector('[data-control="next"]').classList.add('d-none');
      }
      if (document.querySelector(".tab-content > .active")?.dataset.first == "true") {
        backBtn.classList.add("d-none");
        document.querySelector('[data-control="next"]').classList.remove("d-none");
      }
    }
    setTimeout(() => {
      let newCurrentPage = document.querySelector(".current-page");
      const footer = document.getElementById("footer");
      if (newCurrentPage.dataset.lastPage != "true") footer.classList.remove("d-none");
    }, 500);
    // progressBar();
  }

  if (nextBtn) {
    e.preventDefault();
    const lessonContent = document.querySelector(".lesson-content");
    const allTabs = lessonContent.querySelectorAll(".tab-pane");
    const current = lessonContent.querySelector(".active");
    const control = nextBtn.dataset.control;
    const finished = document.querySelector('[name="finished"]').value;

    let required = false;
    let answer = [];
    let answer_text = [];
    let practiceId = current.dataset.id;

    if (current.dataset.type == "practice") {
      required = answerRequired(current);
    }
    if (current.dataset.type == "practice" && required) {
      current.querySelectorAll('[name^="answer"]:checked').forEach((el) => {
        answer.push(el.value);
        answer_text.push(el.getAttribute("text"));
      });
      sendAnswer({
        practiceId: practiceId,
        testId: answer,
        answer_type: "checkbox",
        answer_text: answer_text,
      });
    }
    if (current.dataset.type == "lesson") {
      required = true;
    }
    if (current.querySelector("math-field")) {
      const send = sendAnswer({
        practiceId: practiceId,
        answer_type: "input-user",
        answer_text: document.getElementById(current.querySelector("math-field").id).value.replaceAll('"', ""),
      });
      send.then((res) => {
        console.log("Send Answer >>>", res);
        if (res.statusCode != 500) {
          NextTab(lessonContent, allTabs, current, control);
        } else {
          alert(`${res.status}, ${res.statusText}`);
        }
      });
    }
    // if(finished == '1') return false;
    if (required) {
      console.log("Next lesson โก๏ธ");
      let currentActive = document.querySelector(".active");
      if (currentActive.dataset.type == "lesson" && currentActive.querySelectorAll('[data-show="false"]') > 0) {
        // console.log(nextBtn)
        // currentActive.querySelector('[data-show="false"]').classList.remove('d-none')
      } else {
        current.querySelectorAll('[name^="answer"]')?.forEach((el) => {
          el.nextElementSibling.classList.remove("border-danger", "text-danger");
        });
        current.querySelector(".invalid-feedback")?.remove();
        if (nextBtn) {
          currentActive.classList.remove("active");
          currentActive.removeAttribute("data-latest");
          if (finished != "") currentActive.nextElementSibling.dataset.latest = true;
        }
        NextTab(lessonContent, allTabs, current, control);
      }
      // practice condition
    } else {
      console.log("Next practice โก๏ธ");
      let typeQuestion = current.dataset.typeQuestion;
      if (typeQuestion == "drag-drop") {
        console.log("Drag & drop practice โก๏ธ");
        answer_type = "drag-drop";
        current.querySelectorAll(".drop-zone").forEach((el, k) => {
          if (el.dataset?.answer) {
            answer_text.push(el.innerText);
            answer.push(el.dataset.answer);
          }
        });
        if (finished != "1" && answer_text.length == current.querySelectorAll(".drop-zone").length) {
          const send = sendAnswer({
            testId: answer,
            practiceId: practiceId,
            answer_type: typeQuestion,
            answer_text: answer_text,
          });
          send.then((res) => {
            if (res.statusCode != 500) {
              NextTab(lessonContent, allTabs, current, control);
            } else {
              alert(`${res.status}, ${res.statusText}`);
            }
          });
        } else {
          NextTab(lessonContent, allTabs, current, control);
        }
      } else {
        if (finished != "1") {
          current.querySelectorAll('[name^="answer"]').forEach((el, k) => {
            if (el.type == "checkbox") {
              const invalidEl = document.createElement("span");
              invalidEl.classList.add("invalid-feedback", "text-center", "d-block", "m-0");
              invalidEl.innerHTML = "Please select your answer";
              el.nextElementSibling?.classList.add("text-danger", "border-danger");
            }
            if (el.type == "text") {
              let invalidEl = document.createElement("span");
              invalidEl.classList.add("invalid-feedback", "d-block", "m-0");
              invalidEl.innerHTML = "Please fill in the answer";
              if (el.value) {
                answer_text.push(el.value);
                el.closest(".answer-text").querySelector(".invalid-feedback")?.remove();
              } else {
                if (el.closest(".answer-text").querySelector(".invalid-feedback") == null)
                  el.closest(".answer-text")?.append(invalidEl);
              }
            }
          });
          if ((answer_text.length == current.querySelectorAll('[name^="answer"]').length) & (finished != "1")) {
            console.log("Send answer โก๏ธ", {
              practiceId: practiceId,
              answer_type: "input-user",
              answer_text: answer_text,
            });
            const send = sendAnswer({
              practiceId: practiceId,
              answer_type: "input-user",
              answer_text: answer_text,
            });
            send.then((res) => {
              console.log("Send Answer Reponse โก๏ธ", res);
              if (res.statusCode != 500) {
                NextTab(lessonContent, allTabs, current, control);
              } else {
                alert(`${res.status}, ${res.statusText}`);
              }
            });
          }
        } else {
          console.log("Next tab โก๏ธ");
          NextTab(lessonContent, allTabs, current, control);
        }
      }
      if (Boolean(current.dataset.last)) Finished();
    }
    // progressBar()
  }
  const continueBtn = e.target.closest('[data-control="continue"]');
  if (continueBtn) {
    let nextEl = continueBtn.closest(".page").nextElementSibling;
    let previouEl = document.getElementById(nextEl.getAttribute("id"))?.previousElementSibling;
    const backBtn = document.querySelector('[data-control="back"]');
    let currentPage = document.querySelector(".current-page");
    if (previouEl) backBtn.dataset.scrollto = previouEl.id;
    currentPage?.classList.remove("current-page");
    scrollToElement = document.getElementById(nextEl.getAttribute("id"));

    if (scrollToElement) {
      scrollToElement?.classList.add("current-page");
      scrollToElement?.classList.remove("d-none");
      scrollToElement.dataset.show = true;
      scrollToElement.scrollIntoView({ behavior: "smooth" });
    }
    setTimeout(() => {
      let thisPage = document.querySelector(".current-page");
      // console.log(thisPage)
      if (thisPage.dataset.lastPage == "true") {
        controls.classList.remove("d-none");
        controls.querySelector('[data-control="next"]').classList.remove("d-none");
      }
    }, 100);
  }
  const endTestBtn = e.target.closest(".end-test");
  if (endTestBtn) {
    // console.log(fullUrl.replace(`/${journeyId}/${subjectId}/learning`,''))
    window.location.replace(fullUrl.replace(`/${journeyId}/${subjectId}/learning`, ""));
  }
  const hintBtn = e.target.closest('[data-control="hint"]');

  if (hintBtn && currentTab.dataset.type == "practice") {
    hintBtn.classList.remove("d-none");
    const lessonTab = document.querySelector(".tab-content");
    const practice = lessonTab?.querySelector(".active");
    const modal = document.getElementById("hintModal");
    lessonTab.querySelectorAll('[data-hint^=""]').forEach((el) => el.classList.add("d-none"));
    modal.querySelector(`[data-hint="${practice.dataset.id}"]`)?.classList.remove("d-none");
  }
});

const FinalControlButton = () => {
  // const controls = document.getElementById('footer');
  controls.querySelector('[data-control="back"]').classList.add("d-none");
  controls.querySelector('[data-control="next"]').classList.add("d-none");
  controls.querySelector('[data-control="end"]').classList.remove("d-none");
};
document.addEventListener("change", function (e) {
  const answer = e.target.closest(".btn-check");
  if (answer?.checked && answer.type == "checkbox") {
    document.querySelectorAll('[name^="answer"]').forEach((el) => {
      if (el.nextElementSibling) el.nextElementSibling.classList.remove("border-danger", "text-danger");
    });
    document.querySelector(".invalid-feedback")?.remove();
  }
});
setTimeout(() => {
  const currentTab = document.querySelector(".active");
  const backBtn = document.querySelector('[data-control="back"]');
  if (currentTab?.dataset.first != "true") backBtn.classList.remove("d-none");
  if (currentTab && currentTab.dataset.type == "practice") {
    document.querySelector(".hint-btn").classList.remove("d-none");
  }

  if (currentTab && currentTab.querySelectorAll(".page").length > 0) {
    const continueBtn = controls.querySelector('[data-control="continue"]');
    // controls.querySelector('[data-control="back"]').classList.add('d-none');
    // controls.querySelector('[data-control="next"]').classList.add('d-none');
    // continueBtn.classList.remove('d-none');
    // continueBtn.dataset.scrollto =  currentTab.querySelector('[data-show="false"]').id;
  } else {
    document.querySelector('[data-control="next"]').classList.remove("d-none");
  }
  // startCountUp();
  document.querySelector("math-field")?.addEventListener("focus", () => {
    mathVirtualKeyboard.layouts = ["minimalist"];
    mathVirtualKeyboard.visible = true;
  });
  document.querySelector("math-field")?.addEventListener("change", (e) => {
    console.log(document.getElementById(e.target.id).value);
  });
}, 1000);