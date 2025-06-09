<!-- resources/views/dashboard-child/review-scorecard-modal.blade.php -->
<div id="modal-overlay" class="portal-modal-overlay"></div>
<div id="scorecard-modal" class="portal-modal scorecard-modal">
  <div class="portal-modal-content scorecard-modal-content">
    <div class="portal-modal-body">

      <!-- Modal Header Row -->
      <div class="modal-header-row">
        <h2 class="portal-modal-title scorecard-modal-title" id="scorecard-modal-title">Results</h2>
        <div class="exit-button-wrapper">
          <button type="button" class="btn-close-modal" aria-label="Close modal" onclick="closeModal();">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>

      <!-- Scorecard Summary Row -->
      <div class="scorecard-summary-row" id="scorecard-summary-row">
        <!-- Filled dynamically -->
      </div>

      <!-- Answers Accordion -->
      <div class="scorecard-answers-section">
        <div class="scorecard-answers-title">Your answers</div>
        <div class="scorecard-answers-list" id="scorecard-answers-list">
          <!-- Filled dynamically -->
        </div>
      </div>
    </div>
  </div>
</div>