import { initKeywordTooltips } from './keywordTooltip.js';
import { setupMathliveKeyboard } from './mathliveKeyboard.js';
import { initDndHandler } from './dnd-handler.js';
const { createApp } = Vue;

createApp({
  data() {
    return {
      isSummaryPage: false,
      lessonId: 11,
      currentPage: 0,
      mode: "application",
      digestPages: [
        {
          type: "content",
          title: "Equal Groups",
          content: `<p>Multiplication means making equal groups of things. For example, if you have \\(3\\) plates and each plate has \\(4\\) apples, that is \\(3\\) equal groups of \\(4\\).</p>
                    <p>We can write this as:</p>
                    <p style="text-align: center;">\\( \\large{ 3 \\times 4 } \\)</p>`
        },
        {
          type: "content",
          content: `<p>Here is an example of equal groups:</p>
                    <p style="text-align:center;"><img src="img/fraction-test2.svg" style="width: 350px;"></p>
                    <p>There are \\(4\\) groups and each group has \\(2\\) <span class="keyword" data-glossary="<b>Mass</b> is a measure of how much matter an object contains. It is usually measured in grams (g) or kilograms (kg).">circles</span>.</p>`
        },
        {
          type: "quiz",
          quiz: {
            question: "Which of the following numbers are even?</br>You may select more than one option.",
            img: "img/fraction-test3.svg",
            options: [
              { value: "A", label: "\\(11\\)" },
              { value: "B", label: "\\(17\\)" },
              { value: "C", label: "\\(27\\)" },
              { value: "D", label: "\\(88\\)" },
              { value: "E", label: "\\(42\\)" }
            ],
            answerType: "MCQ",
            correctAnswers: ["D", "E"],
            selected: [],
            revealed: false,
            showExplanation: false,
            explanation: `<p>This is where your explanation goes. You can include MathJax and HTML here.</p>`
          }
        },
        {
          type: "content",
          content: `<p>Understanding equal groups helps us solve multiplication problems easily. It's the first step to learning more complex operations!</p>`
        }
      ],
      applicationPages: [
        {
          type: "quiz",
          quiz: {
            answerType: "DND",
            question: "<p>Arrange the following time durations from shortest to longest.</p>",
            options: [
              { value: "1", label: "\\(49\\) seconds" },
              { value: "2", label: "\\(12\\) weeks" },
              { value: "3", label: "\\(96\\) hours" },
              { value: "4", label: "\\(4\\) days" },
              { value: "5", label: "\\(126\\) minutes" }
            ],
            correctAnswers: [
                              ["1", "2", "3", "4", "5"],
                              ["5", "4", "3", "2", "1"]
                            ],
            selected: [],
            revealed: false,
            isCorrect: false,
            explanation: "Time increases from seconds to minutes, hours, days, then weeks."
          }
        },
        {
          type: "quiz",
          quiz: {
            answerType: "INP",
            inputMode: "math", // plain text input
            question: "<p>What is the capital city of Thailand?</p>",
            inputValue: "",
            correctAnswers: ["BAngkok"],
            ignoreCase: true,
            selected: [],
            revealed: false,
            showExplanation: false,
            explanation: "Bangkok is the capital city of Thailand."
          }
        },
        {
          type: "quiz",
          quiz: {
            answerType: "MCQ",
            question: "<p>What is the value of \\(7 + 8\\)?</p>",
            options: [
              { value: "A", label: "\\(13\\)" },
              { value: "B", label: "\\(15\\)" },
              { value: "C", label: "\\(16\\)" },
              { value: "D", label: "\\(17\\)" }
            ],
            correctAnswers: ["B"],
            selected: [],
            revealed: false,
            showExplanation: false,
            explanation: "To solve \\(7 + 8\\), add the numbers to get \\(15\\)."
          }
        },
        {
          type: "quiz",
          quiz: {
            answerType: "MCQ",
            question: "<p>Which of the following are even numbers?</p>",
            options: [
              { value: "A", label: "\\(2\\)" },
              { value: "B", label: "\\(3\\)" },
              { value: "C", label: "\\(4\\)" },
              { value: "D", label: "\\(5\\)" }
            ],
            correctAnswers: ["A", "C"],
            selected: [],
            revealed: false,
            showExplanation: false,
            explanation: "Even numbers are multiples of 2: 2 and 4."
          }
        },
        {
          type: "quiz",
          quiz: {
            answerType: "INP",
            inputMode: "text", // plain text input
            question: "<p>What is the capital city of Thailand?</p>",
            inputValue: "",
            correctAnswers: ["Bangkok", "BANGKOK", "bangkok"],
            selected: [],
            revealed: false,
            showExplanation: false,
            explanation: "Bangkok is the capital city of Thailand."
          }
        }
      ],
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
      return this.lessonPages[this.currentPage];
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
    }
  },
  watch: {
    // Watch for changes in input values for INP type questions
    'activePage.quiz.inputValue': {
      handler() {
        if (this.activePage && this.activePage.type === 'quiz' && 
            this.activePage.quiz.answerType === 'INP') {
          this.checkInputAnswer();
        }
      },
      deep: true
    }
  },
  mounted() {
    this.startTime = Date.now();

    // Initialize timer for elapsed time tracking
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

      if (original && answer) {
        initDndHandler(original, answer);
      }

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