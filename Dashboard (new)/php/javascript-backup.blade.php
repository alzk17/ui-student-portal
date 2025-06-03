<div class="modal fade" id="myQuestionModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="feature-box fbox-center fbox-lg border-0 mb-0" style="padding: 30px;">
                    <div class="fbox-icon"> 
                         <i class="bi-display bi-info-circle"></i> 
                    </div>
                    <div class="fbox-content">
                        <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
                        <div class="elfsight-app-f346732a-c738-46a2-b54f-60fa27c671d1" data-elfsight-app-lazy></div>
                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>
<script src="{{ asset('assets_dashboard/js/bootstrap.min.js') }}?v={{ time() }}"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="{{ asset('assets_dashboard/js/slick.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets_dashboard/js/main.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/cusmike/sweet2/sweetalert2.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('assets/cusmike/toastr/toastr.min.js') }}?v={{ time() }}"></script>
<script>
    $(document).ready(function() {
        const handleSidebar = () => {
            if (window.matchMedia("screen and (max-width: 1024px)").matches) {
                const _sidebar = $("#sidebar");
                if (_sidebar.length) {
                    _sidebar.removeClass("expand");
                }
            } else {
                const _sidebar = $("#sidebar");
                if (_sidebar.length) {
                    _sidebar.addClass("expand");
                }
            }
        };

        handleSidebar();

        $(window).resize(function() {
            handleSidebar();
        });
    });

    const _boxJourneys = document.querySelectorAll('.box-item-journey');
    _boxJourneys.forEach(box => {
        box.addEventListener('click', () => {
            _boxJourneys.forEach(b => b.classList.remove('active-journey'));
            box.classList.add('active-journey');
        });
    });


    const _boxContentChoices = document.querySelectorAll('.box-content-choice');
    _boxContentChoices.forEach(box => {
        box.addEventListener('click', () => {
            _boxContentChoices.forEach(b => b.classList.remove('answer-success'));
            box.classList.add('answer-success');
        });
    });

    const _boxContentFilterReview = document.querySelectorAll('.box-border');
    _boxContentFilterReview.forEach(box => {
        box.addEventListener('click', () => {
            _boxContentFilterReview.forEach(b => b.classList.remove('active-box-filter-review'));
            box.classList.add('active-box-filter-review');
        });
    });

    function logoutChild() {
        var parent_id = localStorage.getItem('parent_id');
        if (parent_id != 0) {
            $.ajax({
                type: 'POST',
                url: "dashboard-child/parent/logout",
                data: {
                    "_token": "{{ csrf_token() }}",
                    parent_id: parent_id,
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        localStorage.removeItem('parent_id');
                        location.href = "{{ url('dashboard') }}";
                    } else {
                        localStorage.removeItem('parent_id');
                        location.reload();
                    }
                }
            });
        }

    }

    function openQuestionModal() {
        var myModal = new bootstrap.Modal(document.getElementById('myQuestionModal'));
        myModal.show();
    }
</script>
