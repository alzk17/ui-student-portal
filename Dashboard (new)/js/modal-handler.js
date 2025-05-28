function openModal(id, trigger = null) {
  const modal = document.getElementById(id);
  const overlay = document.getElementById('modal-overlay');
  if (!modal || !overlay) return;

  // Optional: populate dynamic content for specific modals
  if (id === 'mocktest-modal' && trigger?.dataset) {
    populateMocktestModal(trigger);
  }

  overlay.classList.add('active');
  modal.classList.add('active');
  document.body.setAttribute('data-current-modal', id);

  // Setup slide navigation if modal uses slides
  if (modal.querySelector('.modal-slide')) {
    setupModalSlides(id);
  }
}

function closeModal() {
  const modalId = document.body.getAttribute('data-current-modal');
  if (modalId) {
    const modal = document.getElementById(modalId);
    modal?.classList.remove('active');
    document.body.removeAttribute('data-current-modal');
  }

  document.getElementById('modal-overlay')?.classList.remove('active');
}

// Populate content for mocktest-modal using data attributes
function populateMocktestModal(trigger) {
  const { title, questions, minutes, subject } = trigger.dataset;

  const titleEl = document.getElementById('modal-title');
  const questionsEl = document.getElementById('modal-questions');
  const minutesEl = document.getElementById('modal-minutes');
  const subjectEl = document.getElementById('modal-subject');

  if (titleEl && title) titleEl.textContent = title;
  if (questionsEl && questions) questionsEl.textContent = `Questions: ${questions}`;
  if (minutesEl && minutes) minutesEl.textContent = `Time: ${minutes} minutes`;
  if (subjectEl && subject) subjectEl.textContent = `Subject: ${subject}`;
}

// Click outside modal to close
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.portal-modal').forEach(modal => {
    modal.addEventListener('click', event => {
      const content = modal.querySelector('.portal-modal-content');
      if (!content.contains(event.target)) {
        closeModal();
      }
    });
  });
});

// ESC key to close modal
document.addEventListener('keydown', event => {
  if (event.key === 'Escape') closeModal();
});

// Slide-based modal logic
function setupModalSlides(modalId) {
  const modal = document.getElementById(modalId);
  const slides = modal.querySelectorAll('.modal-slide');
  const navRow = modal.querySelector('.modal-navigation-row');
  const dotsContainer = modal.querySelector('.modal-pagination-dots');
  const prevBtn = modal.querySelector('.btn-prev');
  const nextBtn = modal.querySelector('.btn-next');

  if (!slides.length || !navRow || !dotsContainer || !prevBtn || !nextBtn) return;

  let currentSlide = 0;

  if (slides.length <= 1) {
    prevBtn.style.display = 'none';
    nextBtn.style.display = 'none';
    dotsContainer.style.display = 'none';
  } else {
    prevBtn.style.display = '';
    nextBtn.style.display = '';
    dotsContainer.style.display = '';
  }

  navRow.classList.remove('hidden');
  dotsContainer.innerHTML = '';

  slides.forEach((_, i) => {
    const dot = document.createElement('span');
    dot.className = 'dot' + (i === 0 ? ' active' : '');
    dot.addEventListener('click', () => {
      currentSlide = i;
      updateSlides();
    });
    dotsContainer.appendChild(dot);
  });

  prevBtn.onclick = () => {
    if (currentSlide > 0) {
      currentSlide--;
      updateSlides();
    }
  };

  nextBtn.onclick = () => {
    if (currentSlide < slides.length - 1) {
      currentSlide++;
      updateSlides();
    }
  };

  function updateSlides() {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === currentSlide);
    });

    const dots = dotsContainer.querySelectorAll('.dot');
    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === currentSlide);
    });

    prevBtn.disabled = currentSlide === 0;
    nextBtn.disabled = currentSlide === slides.length - 1;
  }

  updateSlides();
}
