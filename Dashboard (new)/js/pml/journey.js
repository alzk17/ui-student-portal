const { createApp } = Vue;

createApp({
  data() {
    return {
      lessonId: 11,
      currentPage: 0,
      digestPages: [
        {
          title: "What Are Equal Groups?",
          content: `
            <p>Equal groups are groups that have the same number of items in each group. Understanding equal groups helps us learn about multiplication and division!</p>
            <img src="img/equal-groups-1.svg" alt="Equal Groups Example" style="width:300px; display:block; margin:1.5rem auto;">
          `
        },
        {
          title: "How Can We Show Equal Groups?",
          content: `
            <ul>
              <li>Arrange objects in rows.</li>
              <li>Make sure each row has the same number of objects.</li>
              <li>For example: 3 rows of 4 apples each.</li>
            </ul>
            <img src="img/equal-groups-2.svg" alt="Rows of Apples" style="width:350px; display:block; margin:1.5rem auto;">
          `
        },
        {
          title: "Why Are Equal Groups Important?",
          content: `
            <p>Equal groups help us solve problems in sharing, grouping, and multiplication. Whenever you split objects evenly, you're using the idea of equal groups!</p>
            <img src="img/equal-groups-3.svg" alt="Equal Sharing Example" style="width:350px; display:block; margin:1.5rem auto;">
          `
        }
      ]
    }
  },
  computed: {
    isLastPage() {
        return this.currentPage === this.digestPages.length - 1;
    },
    progressPercent() {
        // +1 so the first page isn't zero percent
        return Math.round(((this.currentPage + 1) / this.digestPages.length) * 100);
    }
  },
  methods: {
    revealNextPage() {
      if (!this.isLastPage) {
        this.currentPage += 1;
      } else {
        // Optionally trigger transition to practice/summary section here!
      }
    }
  }
}).mount('#lesson-app')