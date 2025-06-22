import { initKeywordTooltips } from './keywordTooltip.js';
const { createApp } = Vue;

createApp({
  data() {
    return {
      isSummaryPage: false,
      lessonId: 11,
      currentPage: 0,
      mode: "digest",
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
            correctAnswers: ["D", "E"],
            selected: [],
            revealed: false
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
            question: "<p>What is the value of \\(7 + 8\\)?</p>",
            options: [
              { value: "A", label: "\\(13\\)" },
              { value: "B", label: "\\(15\\)" },
              { value: "C", label: "\\(16\\)" },
              { value: "D", label: "\\(17\\)" }
            ],
            correctAnswers: ["B"],
            selected: [],
            revealed: false
          }
        },
        {
          type: "quiz",
          quiz: {
            question: "<p>Which of the following are even numbers?</p>",
            options: [
              { value: "A", label: "\\(2\\)" },
              { value: "B", label: "\\(3\\)" },
              { value: "C", label: "\\(4\\)" },
              { value: "D", label: "\\(5\\)" }
            ],
            correctAnswers: ["A", "C"],
            selected: [],
            revealed: false
          }
        },
        {
          type: "quiz",
          quiz: {
            question: `<p><span style="font-size:18px;">Find all the numbers that meets all the conditions below.</span></p>

<ul style="margin-left: 40px;">
	<li><span style="font-size:18px;">The number is less than \(40\) hundreds.</span></li>
	<li><span style="font-size:18px;">All the digits are not the same.</span></li>
	<li><span style="font-size:18px;">The tens digit is \(7\).</span></li>
	<li><span style="font-size:18px;">The number is not an even number.</span></li>
	<li><span style="font-size:18px;">The hundreds digit is twice the ones digit.</span></li>
</ul>
`,
            options: [
              { value: "A", label: "\\(2\\)" },
              { value: "B", label: "\\(3\\)" },
              { value: "C", label: "\\(4\\)" },
              { value: "D", label: "\\(5\\)" }
            ],
            correctAnswers: ["A", "C"],
            selected: [],
            revealed: false
          }
        }
      ],
      showExitModal: false,
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
    }
  },
  methods: {
    checkAnswer() {
      if (this.activePage.type === 'quiz') {
        this.activePage.quiz.revealed = true;
      }
    },
    revealNextPage() {
      if (!this.isLastPage) {
        this.currentPage += 1;
        this.$nextTick(() => {
          this.typesetMath();

          // Find the DOM node of the newly revealed page... digest-page can be a problem
          const pageSelector = `.digest-page:nth-child(${this.currentPage + 1})`;
          const pageEl = document.querySelector(pageSelector);

          // Only scroll if the page element is found
          if (pageEl) {
            pageEl.scrollIntoView({
              behavior: "smooth",
              block: "start"
            });
          }
        });
      } else {
        this.isSummaryPage = true;
      }
    },
    selectOption(quiz, optionValue) {
      if (quiz.revealed) {
        return;
      }
      if (quiz.correctAnswers.length === 1) {
        if (quiz.selected[0] === optionValue) {
          quiz.selected = [];
        } else {
          quiz.selected = [optionValue];
        }
        return;
      }
      const selections = new Set(quiz.selected);

      if (selections.has(optionValue)) {
        selections.delete(optionValue);
      } else {
        if (selections.size < quiz.correctAnswers.length) {
          selections.add(optionValue);
        }
      }
      quiz.selected = Array.from(selections);
    },
    finishLesson() {
      this.isSummaryPage = true;
    },
    typesetMath() {
      if (window.MathJax && window.MathJax.typesetPromise) {
        window.MathJax.typesetPromise();
      }
    },
    exitLesson() {
      this.showExitModal = true;
    },
    exitLessonConfirmed() {
      // Example: Go back to dashboard
      window.location.href = "/dashboard-child";
    },
    closeExitModal() {
      this.showExitModal = false;
    }
  },
  mounted() {
    console.log("Initial Quiz Data:", this.digestPages);
    this.$nextTick(() => {
      initKeywordTooltips(document.querySelector('.pml-main'));
      this.typesetMath();
    });
  },
  updated() {
    this.$nextTick(() => {
      const container = document.querySelector('.pml-main');
      if (container) {
        initKeywordTooltips(container);
      }
      this.typesetMath();
    });
  }
}).mount('#lesson-app');