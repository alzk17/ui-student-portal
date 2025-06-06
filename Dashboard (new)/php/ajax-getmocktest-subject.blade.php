@if(@$data)
  @foreach(@$data as $key => $item)
    <div class="setwork-mock-card">
      <div class="mock-card-image">
        <img src="{{ \Helper::getImage($item->image) }}" alt="{{ $item->name }}">
      </div>

      <div class="mock-card-info">
        <h5 class="mock-card-title">{{ $item->name }}</h5>
        <div class="mock-card-meta">
          <span class="mock-meta-item">
            <i class="fa-regular fa-circle-question"></i> {{ $item->amount_question }} questions
          </span>
          <span class="mock-meta-item">
            <i class="fa-regular fa-clock"></i> {{ $item->time_question }} minutes
          </span>
          <span class="mock-meta-item">
            <i class="fa-solid fa-graduation-cap"></i> {{ $item->subject_shortname ?? '—' }}
          </span>
        </div>
      </div>

      <div class="mock-card-action">
        <button
          type="button"
          class="portal-btn portal-btn--primary"
          onclick="openModal('mocktest-modal', this)"
          data-id="{{ $item->id }}"
          data-title="{{ $item->name }}"
          data-questions="{{ $item->amount_question }}"
          data-minutes="{{ $item->time_question }}"
          data-image="{{ \Helper::getImage($item->image) }}"
          data-subject="{{ $item->subject_shortname ?? '—' }}"
        >
          <i class="fa-regular fa-plus"></i> Add to task list
        </button>
      </div>
    </div>
  @endforeach
@endif
