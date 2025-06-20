import { initKeywordTooltips } from './keywordTooltip.js';
const { createApp } = Vue;

createApp({
  data() {
    return {
      isSummaryPage: false,
      lessonId: 11,
      currentPage: 0,
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
            question: "How many equal groups are there in the picture below?",
            img: "img/fraction-test3.svg",
            options: [
              { value: "A", label: "\\(2\\)" },
              { value: "B", label: "\\(3\\)" },
              { value: "C", label: "\\(4\\)" },
              { value: "D", label: "\\(5\\)" }
            ],
            correctAnswers: ["A"],
            selected: [],
            revealed: false
          }
        },
        {
          type: "content",
          content: `<p>Understanding equal groups helps us solve multiplication problems easily. It's the first step to learning more complex operations!</p>`
        }
      ]
    }
  },
  computed: {
    activePage() {
      return this.digestPages[this.currentPage];
    },
    isLastPage() {
      return this.currentPage === this.digestPages.length - 1;
    },
    progressPercent() {
      return Math.round(((this.currentPage + 1) / this.digestPages.length) * 100);
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

          // Find the DOM node of the newly revealed page
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