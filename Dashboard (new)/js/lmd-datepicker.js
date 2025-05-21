document.addEventListener('DOMContentLoaded', () => {
  const dateInputs = document.querySelectorAll('.lambda-datepicker');

  dateInputs.forEach(input => {
    const calendar = input.nextElementSibling;
    let viewDate = new Date(); // Currently viewed month/year

    const today = new Date();

    const formatDate = date => date.toLocaleDateString('en-GB'); // dd/mm/yyyy

    const parseDate = str => {
      if (!str) return null;
      const [day, month, year] = str.split('/');
      return new Date(year, month - 1, day);
    };

    const isPastDate = date =>
      date < new Date(today.getFullYear(), today.getMonth(), today.getDate());

    const isBeforeTodayMonth = (year, month) =>
      year < today.getFullYear() || (year === today.getFullYear() && month < today.getMonth());

    const renderCalendar = (date) => {
      const selected = parseDate(input.value);
      const year = date.getFullYear();
      const month = date.getMonth();

      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      const startDay = firstDay.getDay();
      const daysInMonth = lastDay.getDate();

      let html = `
        <div class="lambda-calendar-header">
          <span class="lambda-calendar-nav" id="prev-month"><i class="fa-solid fa-chevron-left"></i></span>
          <span class="lambda-calendar-title">${firstDay.toLocaleString('default', { month: 'long' })} ${year}</span>
          <span class="lambda-calendar-nav" id="next-month"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <table>
          <thead>
            <tr>${['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'].map(d => `<th>${d}</th>`).join('')}</tr>
          </thead>
          <tbody><tr>
      `;

      // Blank padding before 1st
      for (let i = 0; i < startDay; i++) html += `<td class="blank"></td>`;

      // Days of current month
      for (let d = 1; d <= daysInMonth; d++) {
        const thisDate = new Date(year, month, d);

        const classes = [
          isPastDate(thisDate) ? 'disabled' : '',
          thisDate.toDateString() === today.toDateString() ? 'today' : '',
          selected && thisDate.toDateString() === selected.toDateString() ? 'selected' : ''
        ].filter(Boolean).join(' ');

        html += `<td class="${classes}" data-date="${thisDate.toISOString()}">${d}</td>`;

        if ((startDay + d) % 7 === 0) html += '</tr><tr>';
      }

      // Blank padding after last day
      const totalCells = startDay + daysInMonth;
      const blanksAfter = (7 - (totalCells % 7)) % 7;
      for (let i = 0; i < blanksAfter; i++) html += `<td class="blank"></td>`;

      html += '</tr></tbody></table>';
      calendar.innerHTML = html;

      // Month nav buttons
      const prevBtn = calendar.querySelector('#prev-month');
      const nextBtn = calendar.querySelector('#next-month');

      const disablePrev = isBeforeTodayMonth(year, month - 1);

      prevBtn.classList.toggle('disabled', disablePrev);
      prevBtn.style.pointerEvents = disablePrev ? 'none' : '';
      prevBtn.style.opacity = disablePrev ? '0.3' : '';

      prevBtn.onclick = () => {
        if (!disablePrev) {
          viewDate.setMonth(viewDate.getMonth() - 1);
          renderCalendar(viewDate);
        }
      };

      nextBtn.onclick = () => {
        viewDate.setMonth(viewDate.getMonth() + 1);
        renderCalendar(viewDate);
      };
    };

    input.addEventListener('click', () => {
  calendar.hidden = !calendar.hidden;

  if (!calendar.hidden) {
    const inputRect = input.getBoundingClientRect();
    const calendarHeight = calendar.offsetHeight || 300; // estimate before render
    const spaceBelow = window.innerHeight - inputRect.bottom;
    const spaceAbove = inputRect.top;

    // Flip direction if not enough space below and more space above
    if (spaceBelow < calendarHeight && spaceAbove > calendarHeight) {
      calendar.style.top = 'auto';
      calendar.style.bottom = '110%';
    } else {
      calendar.style.bottom = 'auto';
      calendar.style.top = '110%';
    }
  }

  const selected = parseDate(input.value);
  viewDate = selected || new Date();
  renderCalendar(viewDate);
});


    // Close when clicking outside
    document.addEventListener('click', e => {
      const path = e.composedPath ? e.composedPath() : (e.path || []);
      if (!path.includes(input) && !path.includes(calendar)) {
        calendar.hidden = true;
        viewDate = new Date();
      }
    });

    // Handle date selection
    calendar.addEventListener('click', e => {
      const target = e.target.closest('td[data-date]');
      if (!target || target.classList.contains('disabled')) return;

      const selectedDate = new Date(target.dataset.date);
      input.value = formatDate(selectedDate);
      calendar.hidden = true;
    });
  });
});
