import { initKeywordTooltips } from './keywordTooltip.js';
import { setupMathliveKeyboard } from './mathliveKeyboard.js';
import { initDndHandler } from './dnd-handler.js';
const { createApp } = Vue;

axios.defaults.baseURL = 'https://lambda.in.th/dashboard-child';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const lessonApp = createApp({
  data() {
    return {
      userId: Number(document.getElementById('lesson-app').dataset.userId),
      journeyId: Number(document.getElementById('lesson-app').dataset.journeyId),
      subjectId: Number(document.getElementById('lesson-app').dataset.subjectId),
      lessonId: Number(document.getElementById('lesson-app').dataset.lessonId),
      lessons: [],
      learningProgress: null,
      currentLesson: null,
      currentLessonIndex: 0,
      currentPractices: [],
      isSummaryPage: false,
      currentPage: 0,
      mode: "digest",
      digestPages: [],
      applicationPages: [],
      nextLessonId: null, // Add this line
      showExitModal: false,
      showExplanationModal: false,
      explanationSlides: [],
      explanationSlideIndex: 0,
      startTime: null,
      endTime: null,
      elapsedTime: 0,
      _timer: null
    }
  },
  computed: {
    lessonPages() {
      return this.mode === "digest" ? this.digestPages : this.applicationPages;
    },
    activePage() {
      // Return the current lesson page if available, else return an empty object to avoid undefined
      if (this.lessonPages && this.lessonPages.length > 0 && this.currentPage >= 0 && this.currentPage < this.lessonPages.length) {
        return this.lessonPages[this.currentPage];
      }
      return {}; // fallback empty object to prevent errors in template
    },
    isLastPage() {
      return this.currentPage === this.lessonPages.length - 1;
    },
    progressPercent() {
      return Math.round(((this.currentPage + 1) / this.lessonPages.length) * 100);
    },
    formattedElapsedTime() {
      let ms = this.elapsedTime || 0;
      let totalSeconds = Math.floor(ms / 1000);
      let minutes = Math.floor(totalSeconds / 60);
      let seconds = totalSeconds % 60;
      return `${minutes} min ${seconds} sec`;
    },
    isCheckDisabled() {
      const quiz = this.activePage?.quiz;
      if (!quiz) return true;

      switch (quiz.answerType) {
        case 'MCQ':
          return quiz.selected.length === 0;
        case 'INP':
          return !quiz.inputValue.trim();
        case 'DND':
          return quiz.selected.length === 0;
        default:
          return true;
      }
    },
  },
  methods: {
    fetchLearningProgress(journeyId, subjectId) {
      console.log('Fetching learning progress for journey:', journeyId, 'subject:', subjectId);
      axios.get(`/journey/${journeyId}/${subjectId}/learning/get`)
        .then(response => {
          this.learningProgress = response.data;
          console.log('Learning progress loaded:', response.data);
        })
        .catch(error => {
          console.error('Error fetching learning progress:', error);
        });
    },
    fetchLessons(journeyId, subjectId) {
        // CORRECTED: This URL now matches the route defined in web.php
        const url = `/journey/${journeyId}/${subjectId}/learning/lessons`;

        axios.get(url)
            .then(response => {
                this.lessons = response.data || [];
                console.log('Lessons loaded:', this.lessons);
                this.lessons.forEach(lesson => console.log('Lesson', lesson.id, 'type:', lesson.type));

                // If an initial lessonId is set from the URL, load it.
                // Otherwise, load the very first lesson in the list.
                const initialLessonId = this.lessonId || (this.lessons.length > 0 ? this.lessons[0].id : null);

                if (initialLessonId) {
                    this.loadLessonNode(initialLessonId);
                }
            })
            .catch(error => {
                console.error('Error fetching lessons:', error);
            });
    },
    fetchDigestContent(journeyId, subjectId, lessonId) {
      console.log('Fetching digest content for lessonId:', this.lessonId);
      axios.get(`/journey/${journeyId}/${subjectId}/learning/lesson/${lessonId}/digest-content`)
        .then(response => {
          const data = response.data;
          this.currentLesson = data.lesson;
          this.digestPages = data.pages.map(page => ({ ...page }));
          this.currentPage = 0;
          this.$nextTick(() => {
            this.typesetMath();
          });
        })
        .catch(error => {
          console.error('Failed to fetch digest content:', error);
        });
    },
    updateLatestLesson(journeyId, subjectId, lessonId) {
      axios.post(`/journey/${journeyId}/${subjectId}/set-latest`, {
          lessonId: lessonId
      })
      .then(response => {
        console.log('Progress updated:', response.data);
      })
      .catch(error => {
        console.error('Error updating latest lesson:', error);
      });
    },
    fetchApplicationContent(lessonId) {
        console.log('Fetching application content for lessonId:', lessonId);
        const url = `/journey/${this.journeyId}/${this.subjectId}/learning/lesson/${lessonId}/application-content`;

        axios.get(url)
            .then(response => {
                this.applicationPages = response.data;
                this.mode = 'application'; // Switch to application mode
                this.isSummaryPage = false; // Ensure summary page is hidden
                this.currentPage = 0; // Reset to the first question
            })
            .catch(error => {
                console.error('Error fetching application content:', error);
            });
    },
    loadLessonNode(lessonId) {
        // Find the full lesson object from our list
        const lesson = this.lessons.find(l => l.id === lessonId);

        if (!lesson) {
            console.error('Could not find lesson with ID:', lessonId);
            return;
        }

        // Update the main lessonId and start the timer
        this.lessonId = lesson.id;
        this.startTime = Date.now();

        // Check the type and call the appropriate content function
        if (lesson.type === 'application') {
            this.fetchApplicationContent(lesson.id);
        } else {
            // Default to digest mode
            this.fetchDigestContent(this.journeyId, this.subjectId, lesson.id);
        }
    },
    returnToMap() {
      window.location.href = `/dashboard-child/journey/${this.journeyId}/${this.subjectId}`;
    },
    goToNextLesson() {
      if (this.currentLessonIndex + 1 < this.lessons.length) {
        this.currentLessonIndex++;
        const nextLesson = this.lessons[this.currentLessonIndex];
        console.log('Going to lesson', nextLesson.id, 'type:', nextLesson.type);
        this.lessonId = nextLesson.id;
        this.isSummaryPage = false;
        this.currentPage = 0;
        this.startTime = Date.now();
        this.elapsedTime = 0;

        if (nextLesson.type === 'application') {
          this.fetchApplicationContent(nextLesson.id);
          this.mode = 'application';
        } else {
          this.fetchDigestContent(this.journeyId, this.subjectId, nextLesson.id);
          this.mode = 'digest';
        }
      } else {
        this.nextLessonId = null;
        // Show end screen or return to map, etc.
      }
    },
    // Optional: Central error handler
    handleError(error) {
      console.error('API error:', error);
      // You can also add user notifications here if desired
    },
    checkAnswer() {
      if (this.activePage.type === 'quiz') {
        const quiz = this.activePage.quiz;
        quiz.revealed = true;

        if (quiz.answerType === 'MCQ') {
          // Check MCQ answers (unordered)
          const selected = quiz.selected.sort().join(',');
          const correct = quiz.correctAnswers.sort().join(',');
          quiz.isCorrect = (selected === correct);

        } else if (quiz.answerType === 'INP') {
          // Check input answers
          const userAnswer = quiz.inputValue.trim();
          if (quiz.ignoreCase) {
            quiz.isCorrect = quiz.correctAnswers.some(ans =>
              userAnswer.toLowerCase() === ans.toLowerCase()
            );
          } else {
            quiz.isCorrect = quiz.correctAnswers.includes(userAnswer);
          }

        } else if (quiz.answerType === 'DND') {
            const selected = quiz.selected || [];
            const correctOrders = quiz.correctAnswers || [];
            
            if (!correctOrders.length) {
              // No correct answers specified: never allow correct
              quiz.isCorrect = false;
              console.warn('No correct answers specified for this DND question!');
            } else if (Array.isArray(correctOrders[0])) {
              quiz.isCorrect = correctOrders.some(
                order => JSON.stringify(selected) === JSON.stringify(order)
              );
            } else {
              quiz.isCorrect = JSON.stringify(selected) === JSON.stringify(correctOrders);
            }
            this.$nextTick(() => {
              this.updateDndFeedbackClasses();
            });
          }
      }
    },
    checkInputAnswer() {
      if (this.activePage.type === 'quiz' && this.activePage.quiz.answerType === 'INP') {
        const quiz = this.activePage.quiz;
        const userAnswer = quiz.inputValue.trim();

        if (userAnswer) {
          if (quiz.ignoreCase) {
            quiz.isCorrect = quiz.correctAnswers.some(ans =>
              userAnswer.toLowerCase() === ans.toLowerCase()
            );
          } else {
            quiz.isCorrect = quiz.correctAnswers.includes(userAnswer);
          }
        }
      }
    },
    revealNextPage() {
      if (!this.isLastPage) {
        this.currentPage += 1;
        this.$nextTick(() => {
          this.typesetMath();

          if (this.mode === 'digest') {
            // For digest mode, scroll to the newly revealed page
            const pageSelector = `.digest-page:nth-child(${this.currentPage + 1})`;
            const pageEl = document.querySelector(pageSelector);
            
            if (pageEl) {
              pageEl.scrollIntoView({
                behavior: "smooth",
                block: "start"
              });
            }
          }
        });
      } else {
        this.finishLesson();
      }
    },
    selectOption(quiz, optionValue) {
      if (quiz.revealed) {
        return;
      }
      
      // Handle single-select vs multi-select
      if (quiz.correctAnswers.length === 1) {
        // Single select
        if (quiz.selected[0] === optionValue) {
          quiz.selected = [];
        } else {
          quiz.selected = [optionValue];
        }
      } else {
        // Multi-select
        const selections = new Set(quiz.selected);
        
        if (selections.has(optionValue)) {
          selections.delete(optionValue);
        } else {
          // Allow selection up to the number of correct answers
          if (selections.size < quiz.correctAnswers.length) {
            selections.add(optionValue);
          }
        }
        quiz.selected = Array.from(selections);
      }
    },
    dndFeedbackClass(quiz, idx, value) {
      if (!quiz.revealed) return '';
      
      let classes = ['answer-active', 'locked'];
      if (quiz.isCorrect) {
        classes.push('answer-correct');
      } else {
        classes.push('answer-incorrect');
      }
      return classes.join(' ');
    },
    updateDndFeedbackClasses() {
      if (
        this.activePage &&
        this.activePage.type === 'quiz' &&
        this.activePage.quiz.answerType === 'DND' &&
        this.activePage.quiz.revealed
      ) {
        const quiz = this.activePage.quiz;
        const answerContainer = document.getElementById('answerContainer');
        const originalSlot = document.getElementById('originalSlot');

        // Update answer boxes in the drop zone (answerContainer)
        if (answerContainer) {
          const answerBoxes = answerContainer.querySelectorAll('.answer-box');
          answerBoxes.forEach((box, idx) => {
            // All-or-nothing feedback: all correct or all incorrect
            let classes = ['answer-active', 'locked'];
            if (quiz.isCorrect) {
              classes.push('answer-correct');
            } else {
              classes.push('answer-incorrect');
            }
            box.className = `answer-box ${classes.join(' ')}`;
          });
        }

        // Lock answer boxes in the original slot (originalSlot)
        if (originalSlot) {
          const originalBoxes = originalSlot.querySelectorAll('.answer-box');
          originalBoxes.forEach((box) => {
            // Add "locked" if not already present
            if (!box.classList.contains('locked')) {
              box.classList.add('locked');
            }
          });
        }
      }
    },
    showDigestExplanation(page) {
      if (page.quiz) {
        page.quiz.showExplanation = true;
      }
    },
    finishLesson() {
      this.endTime = Date.now();
      this.elapsedTime = this.endTime - this.startTime;
      if (this._timer) {
        clearInterval(this._timer);
      }
      this.isSummaryPage = true;

      const currentLesson = this.lessons[this.currentLessonIndex];
      if (!currentLesson) {
        alert("No current lesson found.");
        return;
      }

      // The URL for the API call
      const url = `/journey/${this.journeyId}/${this.subjectId}/learning/finished`;

      // The data to send. This now uses the correct lesson ID from the array
      const data = {
        lessonId: this.lessonId, // This is correct
        timer: this.elapsedTime
      };

      axios.post(url, data)
        .then(response => {
          if (response.data.status) {
            this.nextLessonId = response.data.nextLessonId;
            console.log('Lesson marked as complete. Next lesson ID:', this.nextLessonId);
            this.updateLatestLesson(this.journeyId, this.subjectId, currentLesson.id);
          }
        })
        .catch(error => {
          console.error('Error marking lesson as complete:', error);
        });
    },
    typesetMath() {
      if (window.MathJax && window.MathJax.typesetPromise) {
        window.MathJax.typesetPromise().catch(err => {
          console.warn('MathJax typeset error:', err);
        });
      }
    },
    exitLesson() {
      this.showExitModal = true;
    },
    exitLessonConfirmed() {
      window.location.href = "/dashboard-child";
    },
    closeExitModal() {
      this.showExitModal = false;
    },
    // Modal methods
    openExplanationModal() {
      let explanation = this.activePage.quiz.explanation;
      if (typeof explanation === 'string') {
        this.explanationSlides = explanation.split('[new-pagination]');
      } else if (Array.isArray(explanation)) {
        this.explanationSlides = explanation;
      } else {
        this.explanationSlides = [explanation || 'No explanation available'];
      }
      this.explanationSlideIndex = 0;
      this.showExplanationModal = true;
      
      // Typeset math in modal after it opens
      this.$nextTick(() => {
        this.typesetMath();
      });
    },
    closeExplanationModal() {
      this.showExplanationModal = false;
    },
    nextExplanationSlide() {
      if (this.explanationSlideIndex < this.explanationSlides.length - 1) {
        this.explanationSlideIndex++;
        this.$nextTick(() => {
          this.typesetMath();
        });
      }
    },
    prevExplanationSlide() {
      if (this.explanationSlideIndex > 0) {
        this.explanationSlideIndex--;
        this.$nextTick(() => {
          this.typesetMath();
        });
      }
    },
    goToExplanationSlide(idx) {
      if (idx >= 0 && idx < this.explanationSlides.length) {
        this.explanationSlideIndex = idx;
        this.$nextTick(() => {
          this.typesetMath();
        });
      }
    },
    attachMathFieldListeners() {
      this.$nextTick(() => {
        const mathFields = document.querySelectorAll('math-field');
        mathFields.forEach(mathField => {
          // More robust check
          if (!mathField._keyboardSetup) {
            setupMathliveKeyboard(mathField);
          }
        });
      });
    },
    initDndOnPage() {
      this.$nextTick(() => {
        // This now safely checks the full path
        if (
          this.activePage?.type === 'quiz' &&
          this.activePage?.quiz?.answerType === 'DND'
        ) {
          const original = document.getElementById('originalSlot');
          const answer = document.getElementById('answerContainer');
          if (original && answer) {
            initDndHandler(original, answer);
          }
        }
      });
    }
  },
  watch: {
    // Your watcher for currentPage is fine and can stay
    currentPage(newVal, oldVal) {
      this.$nextTick(() => {
        if (typeof this.initDndOnPage === 'function') {
          this.initDndOnPage();
        }
      });
    },
    
    // REPLACE your activePage watcher with this one
    activePage: {
      handler(newPage) {
        // This safely checks if the new page is an INP quiz before doing anything.
        if (newPage?.quiz?.answerType === 'INP') {
          this.checkInputAnswer();
        }
      },
      deep: true
    }
  },
  mounted() {
    console.log('User ID:', this.userId);
    console.log('Journey ID:', this.journeyId);
    console.log('Subject ID:', this.subjectId);
    console.log('Initial Lesson ID:', this.lessonId);

    // Fetches the user's overall progress record
    this.fetchLearningProgress(this.journeyId, this.subjectId);
    
    // This single function now handles fetching the lesson list AND loading the first node
    this.fetchLessons(this.journeyId, this.subjectId);
    
    // The global timer is still managed here
    this._timer = setInterval(() => {
      if (this.startTime) {
        this.elapsedTime = Date.now() - this.startTime;
      }
    }, 1000);

    this.$nextTick(() => {
      // Initialize components
      const container = document.querySelector('.pml-main');
      const original = document.getElementById('originalSlot');
      const answer = document.getElementById('answerContainer');
      if (container) {
        initKeywordTooltips(container);
      }
      this.typesetMath();

      // Setup MathLive keyboard for initial math fields
      this.attachMathFieldListeners();
      // Removed initDndHandler(original, answer); and this.initDndOnPage();
    });

    document.addEventListener('dnd-updated', (e) => {
      if (this.activePage && this.activePage.type === 'quiz' && 
          this.activePage.quiz.answerType === 'DND') {
        this.activePage.quiz.selected = e.detail.selected;
        console.log('DND updated:', e.detail.selected); // temporary debug log
      }
    });
  },
  updated() {
    this.$nextTick(() => {
      const container = document.querySelector('.pml-main');
      if (container) {
        initKeywordTooltips(container);
      }
      this.typesetMath();
      this.attachMathFieldListeners(); // This ensures new math fields get keyboard setup
    });
  },
  beforeUnmount() {
    // Clean up timer
    if (this._timer) {
      clearInterval(this._timer);
    }

    document.removeEventListener('dnd-updated', this.handleDndUpdate);
  }
}).mount('#lesson-app');
window.lessonApp = lessonApp;