<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <title>Practice : LAMBDA </title>
    <!-- Styles -->
    @include("$prefix.layout.stylesheet-quiz")
    <!-- Assets Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/variables.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets_dashboard/css/buttons.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('vue-pml/css/pml-header.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('vue-pml/css/pml-footer.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('vue-pml/css/pml-main.css') }}?v={{ time() }}">
    <!-- Scripts -->
    <script src="https://unpkg.com/mathlive"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
</head>

<body class="stretched">

    <!-- Document Wrapper -->
    <div id="wrapper" class="noice-effect">
        <div v-cloak id="lesson-app"
            data-user-id="{{ Auth::guard('child')->user()->id }}"
            data-journey-id="{{ $journeyId }}"
            data-subject-id="{{ $subjectId }}"
            data-lesson-id="{{ $lessonId ?? '' }}">
            <!-- HEADER BAR -->
            <header class="pml-header" v-if="!isSummaryPage">
                <div class="pml-header__row">
                    <button
                        type="button"
                        class="pml-header__exit-btn"
                        aria-label="Close lesson"
                        @click="exitLesson"
                    >
                        <i class="fa-solid fa-close"></i>
                    </button>
                    <div class="pml-header__progress-bar">
                        <div
                        class="pml-header__progress-fill"
                        :style="{ width: progressPercent + '%' }"
                        ></div>
                    </div>
                    <div class="pml-header__side"></div>
                </div>
            </header>

            <!-- MAIN CONTENT -->
            <main class="pml-main">
                <!-- Learning Content -->
                <section class="pml-main__container" v-if="!isSummaryPage">
                    <div class="pml-main__lesson-body">
                        <div
                            class="pml-section--digest"
                            v-if="mode === 'digest'"
                            data-type="lesson"
                            :data-id="lessonId"
                        >
                            <div
                                v-for="(page, idx) in lessonPages"
                                :key="idx"
                                class="digest-page"
                                :class="{ 'last-visible': idx === currentPage }"
                                v-show="idx <= currentPage"
                            >
                                <template v-if="page.type === 'content'">
                                    <div v-html="page.content"></div>
                                </template>
                                <template v-else-if="page.type === 'quiz'">
                                    <div v-if="page.quiz" class="digest-quiz-block" :class="{ 'is-revealed': page.quiz.revealed }">
                                        <div class="digest-quiz-question__text" v-html="page.quiz.question"></div>
                                        <div class="digest-quiz-options" role="list">
                                            <label
                                                v-for="option in page.quiz.options"
                                                :key="option.value"
                                                class="digest-quiz-choice"
                                                :class="{ 
                                                    'is-selected': page.quiz.selected && page.quiz.selected.includes(option.value),
                                                    'is-correct': page.quiz.revealed && page.quiz.correctAnswers.includes(option.value),
                                                    'is-incorrect': page.quiz.revealed && page.quiz.selected.includes(option.value) && !page.quiz.correctAnswers.includes(option.value),
                                                    'is-revealed-correct': page.quiz.revealed && page.quiz.correctAnswers.includes(option.value) && !page.quiz.selected.includes(option.value)
                                                }"
                                                @click.prevent="selectOption(page.quiz, option.value)"
                                            >
                                                <span v-if="page.quiz.revealed" class="digest-quiz-choice__icon">
                                                    <i v-if="page.quiz.correctAnswers.includes(option.value)" class="fa-solid fa-check"></i>
                                                    <i v-else-if="page.quiz.selected.includes(option.value)" class="fa-solid fa-xmark"></i>
                                                </span>
                                                <input
                                                    type="checkbox"
                                                    :value="option.value"
                                                    :checked="page.quiz.selected.includes(option.value)"
                                                    class="digest-quiz-choice__input"
                                                    :disabled="page.quiz.revealed"
                                                />
                                                <span class="digest-quiz-choice__label" v-html="option.label"></span>
                                            </label>
                                        </div>

                                        <div class="digest-explanation-toggle-row">
                                            <button
                                                v-show="page.quiz.revealed && page.quiz.explanation"
                                                class="digest-explanation-toggle-btn"
                                                @click="page.quiz.showExplanation = !page.quiz.showExplanation"
                                                :aria-expanded="page.quiz.showExplanation"
                                            >
                                                @{{ page.quiz.showExplanation ? 'Hide Explanation' : 'Show Explanation' }}
                                            </button>
                                        </div>
                                        <div
                                            v-if="page.quiz.revealed && page.quiz.showExplanation"
                                            class="digest-explanation-content"
                                            v-html="page.quiz.explanation"
                                        ></div>
                                    </div>
                                </template>
                            </div>
                            <div class="pml-lesson__spacer"></div>
                        </div>

                        <div
                            class="pml-section--application"
                            v-if="mode === 'application'"
                            data-type="application"
                            :data-id="lessonId"
                        >
                            <div
                                v-for="(page, idx) in lessonPages"
                                :key="idx"
                                class="application-page"
                                v-show="idx === currentPage"
                            >
                                <template v-if="page.type === 'quiz'">
                                    <div class="digest-quiz-block" :class="{ 'is-revealed': page.quiz.revealed }">
                                        <div class="digest-quiz-question__text" v-html="page.quiz.question"></div>

                                        <!-- MCQ block -->
                                        <div v-if="page.quiz.answerType === 'MCQ'" class="digest-quiz-options" role="list">
                                            <label
                                                v-for="option in page.quiz.options"
                                                :key="option.value"
                                                class="digest-quiz-choice"
                                                :class="{ 
                                                    'is-selected': page.quiz.selected && page.quiz.selected.includes(option.value),
                                                    'is-correct': page.quiz.revealed && page.quiz.correctAnswers.includes(option.value),
                                                    'is-incorrect': page.quiz.revealed && page.quiz.selected.includes(option.value) && !page.quiz.correctAnswers.includes(option.value),
                                                    'is-revealed-correct': page.quiz.revealed && page.quiz.correctAnswers.includes(option.value) && !page.quiz.selected.includes(option.value)
                                                }"
                                                @click.prevent="selectOption(page.quiz, option.value)"
                                            >
                                                <span v-if="page.quiz.revealed" class="digest-quiz-choice__icon">
                                                    <i v-if="page.quiz.correctAnswers.includes(option.value)" class="fa-solid fa-check"></i>
                                                    <i v-else-if="page.quiz.selected.includes(option.value)" class="fa-solid fa-xmark"></i>
                                                </span>
                                                <input
                                                    type="checkbox"
                                                    :value="option.value"
                                                    :checked="page.quiz.selected.includes(option.value)"
                                                    class="digest-quiz-choice__input"
                                                    :disabled="page.quiz.revealed"
                                                />
                                                <span class="digest-quiz-choice__label" v-html="option.label"></span>
                                            </label>
                                        </div>

                                        <!-- INP block (input) -->
                                        <div v-else-if="page.quiz.answerType === 'INP'">
                                            <div class="inp-wrapper">
                                                <span
                                                    class="mathfield-prefix"
                                                    v-if="page.quiz.prefix"
                                                    v-html="page.quiz.prefix"
                                                ></span>
                                                
                                                <div v-if="page.quiz.inputMode === 'math'" class="mathfield-box">
                                                    <math-field
                                                        ref="mathField"
                                                        v-model="page.quiz.inputValue"
                                                        class="input-mathfield"
                                                        :disabled="page.quiz.revealed"
                                                        :class="{
                                                            'mathfield-correct': page.quiz.revealed && page.quiz.isCorrect,
                                                            'mathfield-incorrect': page.quiz.revealed && !page.quiz.isCorrect
                                                        }"
                                                        @focusin="showVirtualKeyboard"
                                                        @focusout="hideVirtualKeyboard"
                                                    ></math-field>
                                                    <i
                                                        v-if="page.quiz.revealed"
                                                        :class="[
                                                            'fa-solid',
                                                            page.quiz.isCorrect ? 'fa-check mathfield-feedback-icon correct' : 'fa-xmark mathfield-feedback-icon incorrect'
                                                        ]"
                                                        aria-hidden="true"
                                                        style="margin-left: 8px;"
                                                    ></i>
                                                    <div v-if="page.quiz.revealed" class="mathfield-blocker"></div>
                                                </div>

                                                <div v-else class="mathfield-box">
                                                    <input
                                                        type="text"
                                                        v-model="page.quiz.inputValue"
                                                        class="input-textfield"
                                                        :disabled="page.quiz.revealed"
                                                        :placeholder="page.quiz.placeholder || 'Your answer'"
                                                        :class="{
                                                            'mathfield-correct': page.quiz.revealed && page.quiz.isCorrect,
                                                            'mathfield-incorrect': page.quiz.revealed && !page.quiz.isCorrect
                                                        }"
                                                    />
                                                    <i
                                                        v-if="page.quiz.revealed"
                                                        :class="[
                                                            'fa-solid',
                                                            page.quiz.isCorrect ? 'fa-check mathfield-feedback-icon correct' : 'fa-xmark mathfield-feedback-icon incorrect'
                                                        ]"
                                                        aria-hidden="true"
                                                        style="margin-left: 8px;"
                                                    ></i>
                                                    <div v-if="page.quiz.revealed" class="mathfield-blocker"></div>
                                                </div>
                                                
                                                <span
                                                    class="mathfield-suffix"
                                                    v-if="page.quiz.suffix"
                                                    v-html="page.quiz.suffix"
                                                ></span>
                                            </div>
                                        </div>

                                        <!-- DND block -->
                                        <div v-else-if="page.quiz.answerType === 'DND'" class="digest-dnd-block">
                                            <div class="drop-zone" id="dropZone">
                                                <div class="guideline top"></div>
                                                <div class="answer-container" id="answerContainer"></div>
                                                <div class="guideline bottom"></div>
                                            </div>
                                            <div class="word-slot" id="originalSlot">
                                                <div
                                                    class="answer-box"
                                                    v-for="(option, i) in page.quiz.options"
                                                    :key="option.value"
                                                    :data-answer="option.value"
                                                    v-html="option.label"
                                                ></div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </template>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Summary Section -->
                    <section class="pml-summary__container" v-if="isSummaryPage">
                        <div class="pml-summary">
                            <h2 class="pml-summary__title">Lesson Complete</h2>
                            <p class="pml-summary__subtitle">
                                Great job completing this lesson! Keep up the fantastic work!
                            </p>

                            <div class="pml-summary__stats-row">
                                <div class="pml-summary__stat-box">
                                    <div class="pml-summary__stat-title">Time Taken</div>
                                    <div class="pml-summary__stat-value">@{{ formattedElapsedTime }}</div>
                                    <div class="pml-summary__stat-img">
                                        <img src="{{ asset('assets_dashboard/img/icons/hourglass.svg') }}" alt="Hourglass" />
                                    </div>
                                </div>
                                <div class="pml-summary__stat-box">
                                    <div class="pml-summary__stat-title">Gems Earned</div>
                                    <div class="pml-summary__stat-value">+12</div>
                                    <div class="pml-summary__stat-img">
                                        <img src="{{ asset('assets_dashboard/img/icons/gem.svg') }}" alt="Hourglass" />
                                    </div>
                                </div>
                            </div>

                            <!--<button class="portal-btn3d portal-btn3d--white pml-summary__btn--continue" @click="returnToMap">
                                Continue
                            </button>-->

                            <div class="pml-summary__actions">
                                <button class="portal-btn3d portal-btn3d--grey" @click="returnToMap">
                                    Return to Map
                                </button>
                                <button
                                    v-if="nextLessonId"
                                    class="portal-btn3d portal-btn3d--white"
                                    @click="goToNextLesson"
                                >
                                    Next Lesson
                                </button>
                            </div>
                            
                        </div>
                    </section>
            </main>

                <!-- FOOTER BAR -->
                <footer
                    v-if="activePage && !isSummaryPage"
                    class="pml-footer pml-footer--fixed"
                    :class="{
                        'pml-footer--feedback-correct': activePage.quiz?.revealed && activePage.quiz?.isCorrect,
                        'pml-footer--feedback-incorrect': activePage.quiz?.revealed && !activePage.quiz?.isCorrect
                    }"
                >
                    <button
                        class="pml-footer__btn--report"
                        id="report-button"
                        aria-label="Report a problem"
                        type="button"
                    >
                        <i class="fa fa-bug"></i>
                    </button>
                    
                    <template v-if="activePage.type === 'quiz' && activePage.quiz?.revealed && !activePage.quiz?.showExplanation">
                        <div class="pml-footer__feedback-group">
                            <span class="pml-footer__feedback-message">
                                @{{ activePage.quiz?.isCorrect ? 'Great job! 🎉' : 'Give it another try next time.' }}
                            </span>
                            <div class="pml-footer__feedback-actions">
                                <template v-if="activePage.quiz?.explanation">
                                    <button
                                        v-if="mode === 'digest'"
                                        class="portal-btn3d portal-btn3d--white pml-footer__btn--duo"
                                        @click="activePage.quiz.showExplanation = true"
                                    >
                                        Explanation
                                    </button>
                                    <button
                                        v-else
                                        class="portal-btn3d portal-btn3d--white pml-footer__btn--duo"
                                        @click="openExplanationModal"
                                    >
                                        Explanation
                                    </button>
                                </template>
                                <button
                                    v-if="!isLastPage"
                                    class="portal-btn3d portal-btn3d--white pml-footer__btn--duo"
                                    @click="revealNextPage"
                                >
                                    Continue
                                </button>
                                <button
                                    v-else
                                    class="portal-btn3d portal-btn3d--white pml-footer__btn--duo"
                                    @click="finishLesson"
                                >
                                    Finish lesson
                                </button>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <template v-if="activePage.type === 'quiz' && !activePage.quiz?.revealed">
                            <button
                                class="portal-btn3d portal-btn3d--white pml-footer__btn--continue"
                                aria-label="Check Answer"
                                @click="checkAnswer"
                                :disabled="isCheckDisabled"
                                type="button"
                            >
                                Check answer
                            </button>
                        </template>
                        <template v-else>
                            <button
                                v-if="!isLastPage"
                                class="portal-btn3d portal-btn3d--white pml-footer__btn--continue"
                                aria-label="Continue"
                                @click="revealNextPage"
                                type="button"
                            >
                                Continue
                            </button>
                            <button
                                v-else
                                class="portal-btn3d portal-btn3d--white pml-footer__btn--continue"
                                aria-label="Finish Lesson"
                                @click="finishLesson"
                                type="button"
                            >
                                Finish lesson
                            </button>
                        </template>
                    </template>
                    <div class="pml-footer__side"></div>
                </footer>

                <!-- Overlay and Modal Dialog -->
                <div
                    class="portal-modal-overlay"
                    :class="{ active: showExitModal || showExplanationModal }"
                    @click="showExitModal ? closeExitModal() : showExplanationModal ? closeExplanationModal() : null"
                    tabindex="-1">
                </div>
                <div
                    id="exit-modal"
                    class="portal-modal"
                    :class="{ active: showExitModal }"
                    tabindex="-1"
                    @click.self="closeExitModal"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="portal-modal-content exit-modal-content">
                        <div class="modal-header-row">
                            <h2 class="portal-modal-title exit-modal-title">Exit Practice?</h2>
                        </div>

                        <div class="modal-body portal-modal-body">
                            <p>Your progress has been saved. Continue this practice anytime from your dashboard.</p>
                        </div>

                        <div class="portal-modal-actions exit-modal-actions">
                            <button type="button" class="button button-3d lmd-grey" @click="closeExitModal">
                                Cancel
                            </button>
                            <button class="button button-3d lmd-red" @click="exitLessonConfirmed">
                                Exit practice
                            </button>
                        </div>
                    </div>
                </div>
                <div
                    class="portal-modal"
                    id="explanation-modal"
                    :class="{ active: showExplanationModal }"
                    tabindex="-1"
                    @click.self="closeExplanationModal"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="portal-modal-content explanation-modal-content">
                        <div class="modal-header-row">
                            <h2 class="portal-modal-title explanation-modal-title">Explanation</h2>
                            <div class="exit-button-wrapper">
                                <button type="button" class="btn-close-modal" aria-label="Close modal" @click="closeExplanationModal">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                        <div class="modal-slider">
                            <div
                                v-for="(slide, idx) in explanationSlides"
                                :key="idx"
                                class="modal-slide"
                                :class="{ active: idx === explanationSlideIndex }"
                            >
                                <div v-html="slide"></div>
                            </div>
                        </div>
                        <div class="modal-navigation-row" v-if="explanationSlides.length > 1">
                            <button class="btn-prev" aria-label="Previous" :disabled="explanationSlideIndex === 0" @click="prevExplanationSlide">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <div class="modal-pagination-dots">
                                <span
                                    v-for="(dot, idx) in explanationSlides"
                                    :key="idx"
                                    class="dot"
                                    :class="{ active: idx === explanationSlideIndex }"
                                    @click="goToExplanationSlide(idx)"
                                ></span>
                            </div>
                            <button class="btn-next" aria-label="Next" :disabled="explanationSlideIndex === explanationSlides.length - 1" @click="nextExplanationSlide">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include("$prefix.dashboard-child.layout.javascript")
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script type="module" src="{{ asset('vue-pml/js/journey.js') }}?v={{ time() }}"></script>

</body>

</html>