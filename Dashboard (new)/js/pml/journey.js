import { initKeywordTooltips } from './keywordTooltip.js';
const { createApp } = Vue;

createApp({
  data() {
    return {
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
            selected: []
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
    isLastPage() {
      return this.currentPage === this.digestPages.length - 1;
    },
    progressPercent() {
      return Math.round(((this.currentPage + 1) / this.digestPages.length) * 100);
    }
  },
  methods: {
    revealNextPage() {
      if (!this.isLastPage) {
        this.currentPage += 1;
        this.$nextTick(this.typesetMath);
      }
    },
    typesetMath() {
      if (window.MathJax && window.MathJax.typesetPromise) {
        window.MathJax.typesetPromise();
      }
    }
  },
  mounted() {
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