<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta content="{{ csrf_token() }}" name="csrf-token">
    @include("$prefix.layout.stylesheet-quiz")


    <!-- Document Title =================== -->
    <title>Practice : LAMBDA </title>
    <script src="https://unpkg.com/mathlive"></script>
    <style>
        @media not (pointer: coarse) {
            math-field::part(virtual-keyboard-toggle) {
                display: none;
            }
        }

        .drop-zone {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .drop-zone p {
            margin: 0;
            pointer-events: none;
        }
    </style>
    @if (@$question->type_question == 'input' && @$question->type_math == 'Y')
        <style>
            body {
                --keycap-height: 50px;
                --keycap-max-width: 55px;
                --keycap-font-size: 22px;
            }

            math-field {
                font-size: 1.5rem;
                line-height: 1.5;
                width: 300px;
                padding: 1px 10px;
                border: 2px solid #a6a6a6;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                background-color: #fff;
            }

            math-field:focus {
                border: 2px solid #007bff;
                box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
                outline: none;
                transition: border 0.2s, box-shadow 0.2s;
            }


            /* Hide the virtual keyboard toggle and menu toggle */
            math-field::part(virtual-keyboard-toggle) {
                display: none;
            }

            math-field::part(menu-toggle) {
                display: none;
            }

            div.minimalist-container {
                background: var(--keyboard-background);
                padding: 20px 20px 0px 20px;
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
                border: 1px solid var(--keyboard-border);
                box-shadow: 0 0 32px rgb(0 0 0 / 30%);
            }
        </style>
    @endif

</head>

<body class="stretched side-panel-left">
    @include("$prefix.layout.body")

    <div id="particles-nasa"></div>
    <!-- Document Wrapper ==================== -->
    <div id="wrapper" class="noice-effect overflow-hidden">

        <!-- Header ============================ -->
        <header id="header" class="border-bottom-0 sticky-header " data-mobile-sticky="true">
            <div id="header-wrap" class="p-2 pb-3 shadow">
                <div class="header-row justify-content-lg-between m-0 pt-2">
                    <div class="col-1 col-sm-2 ps-3 display-7">
                        <a class="back-to-journey"><i class="fa fa-close"></i></a>
                    </div>
                    <div class="col-10 col-sm-8 mx-auto center">
                        <h4 class="display-6 text-uppercase">{{ $subject->journey }} - {{ $subject->name }}</h4>
                    </div>
                    <div class="col-1 col-sm-2 pe-3 display-7" align="right">
                        <strong class="me-2">0</strong> <i class="bi-coin"></i>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="mx-auto center m-0 p-0" style="max-width: 710px;">
                    <div class="skill-progress " data-percent="0" data-speed="1100"
                        style="--cnvs-progress-height: 0.25rem; --cnvs-progress-trackcolor: #fff;">
                        <div class="skill-progress-bar">
                            <div class="skill-progress-percent bg-color skill-animated"
                                style="--cnvs-progress-speed: 1000ms; width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </header>
        <section id="content">
            <div class="content-wrap">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-sm-12 mx-auto" style="max-width: 750px;">
                        <div class="p2 pt4">
                            <input type="hidden" name="userId" value="{{ Auth::guard('child')->user()->id }}">
                            <input type="hidden" name="latest" value="{{ $latest->latest }}">
                            <input type="hidden" name="latest_type" value="{{ $latest->latest_type }}">
                            <input type="hidden" name="finished" value="{{ $latest->finished }}">
                            <div class="container-fluid  py-5">
                                <div class="lesson-body"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer id="footer" class="fixed-bottom p-3"
            style="position: fixed; bottom: 0px; z-index: 0; margin-top: 379px;">
            <div class="row justify-content-around">
                <div class="col-8">
                    <a href="javascript:" data-lightbox="inline" style="font-size: 22px;" class="me-2 ms-2">
                        <i class="fa fa-question-circle"></i>
                    </a>
                    <a href="javascript:" data-control="hint" data-lightbox="inline" class="button button-border button-rounded button-black hint-btn d-none">
                        <i class="fa fa-info-circle"></i> Hint
                    </a>
                    <a href="javascript:" data-lightbox="inline"
                        class="button button-border button-rounded button-black explanation-btn d-none"
                        data-control="explanation">
                        <span class="tooltip-highlighted" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="This view reveal the answer. Classic Demos with a Lot of Homepages Options.">
                            <i class="fa fa-check-circle"></i> Show Explanation </span>
                    </a>
                </div>
                <div class="col-4 justify-content-end">
                    <button type="button"
                        class="button button-black button-rounded tab-btn-control tab-btn-next m-0 me-2 float-end d-none"
                        data-control="next" data-scrollto="">
                        Next <i class="fa fa-chevron-right"></i>
                    </button>
                    <button type="button"
                        class="button button-black button-rounded tab-btn-control tab-btn-next m-0 me-2 float-end d-none"
                        data-control="back" data-scrollto="">
                        <i class="fa fa-chevron-left"></i> Back
                    </button>
                    <button type="button"
                        class="button button-primary button-rounded tab-btn-control tab-btn-next m-0 me-2 float-end d-none end-test"
                        data-control="end">
                        End Test <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </footer>
    </div>

    <div class="mfp-wrap mfp-auto-cursor mfp-no-margins mfp-fade mfp-ready mfp-hide" tabindex="-1"
        style="overflow: hidden scroll;">
        <div class="mfp-container mfp-s-ready mfp-inline-holder">
            <div class="mfp-content">
                <div class="modal1" id="explanationModal">
                    <div class="block mx-auto" style="background-color: #FFF; max-width: 800px;">
                        <div class="feature-box fbox-center fbox-effect fbox-lg border-0 mb-0" style="padding: 40px;">
                            <div class="center">
                                <div class="m-2"><img src="images/icon-panda.jpg"></div>
                            </div>
                            <div class="fbox-content">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mfp-preloader"></div>
        </div>
        <button title="Close (Esc)" type="button" class="mfp-close"><i class="bi-x-lg"></i></button>
    </div>

    <div class="modal1 mfp-hide" id="hintModal">
        <div class="block mx-auto" style="background-color: #FFF; max-width: 800px;">
            <div class="feature-box fbox-center fbox-effect fbox-lg border-0 mb-0" style="padding: 40px;">
                <div class="center">
                    <div class="m-2"><img src="images/icon-panda.jpg"></div>
                </div>
                <div class="fbox-content">
                    <div class="hint-content"></div>
                </div>
            </div>
        </div>
    </div>



    @include("$prefix.dashboard-child.layout.javascript")
    <script src="js/learning.js?v={{ time() }}"></script>
</body>

</html>
