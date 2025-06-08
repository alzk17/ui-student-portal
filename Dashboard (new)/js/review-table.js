    document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchreview");
  const allTableRows = Array.from(document.querySelectorAll(".box-table tbody tr"));
  let currentDisplayableRows = allTableRows;
  const rowsPerPage = 20;
  let currentPage = 1;
  const paginationContainer = document.getElementById("pagination");

  function displayTableRows() {
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;

    allTableRows.forEach(row => row && (row.style.display = "none"));
    currentDisplayableRows.slice(startIndex, endIndex).forEach(row => row && (row.style.display = ""));

    updatePaginationButtons();
  }

  function createPageLink(text, pageNum, isActive, isDisabled, isControl = false) {
    const li = document.createElement("li");
    li.classList.add("page-item");
    if (isActive) li.classList.add("active");
    if (isDisabled) li.classList.add("disabled");

    const a = document.createElement("a");
    a.classList.add("page-link");
    a.innerText = text;

    if (isDisabled) {
      a.href = "javascript:void(0);";
      a.style.opacity = "0.4";
      a.style.cursor = "not-allowed";
      a.style.webkitUserSelect = "none";
      a.style.mozUserSelect = "none";
      a.style.msUserSelect = "none";
      a.style.userSelect = "none";
    } else {
      a.href = "#";
      a.addEventListener("click", e => {
        e.preventDefault();
        let newPage = currentPage;

        if (isControl) {
          if (pageNum === "prev" && currentPage > 1) newPage = currentPage - 1;
          else if (pageNum === "next" && currentPage < Math.ceil(currentDisplayableRows.length / rowsPerPage))
            newPage = currentPage + 1;
        } else {
          newPage = pageNum;
        }

        if (newPage !== currentPage) {
          currentPage = newPage;
          displayTableRows();
        }
      });
    }

    li.appendChild(a);
    return li;
  }

  function updatePaginationButtons() {
    if (!paginationContainer) return;

    const totalPages = Math.max(Math.ceil(currentDisplayableRows.length / rowsPerPage), currentDisplayableRows.length > 0 ? 1 : 0);
    paginationContainer.innerHTML = "";

    paginationContainer.appendChild(createPageLink("Previous", "prev", false, currentPage === 1, true));

    for (let i = 1; i <= totalPages; i++) {
      paginationContainer.appendChild(createPageLink(i, i, i === currentPage, false, false));
    }

    paginationContainer.appendChild(createPageLink("Next", "next", false, currentPage === totalPages || totalPages === 0, true));
  }

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const searchText = searchInput.value.toLowerCase().trim();

      currentDisplayableRows = searchText
        ? allTableRows.filter(row => {
            const title = row.cells[0]?.textContent.trim().toLowerCase() || "";
            const subject = row.cells[1]?.textContent.trim().toLowerCase() || "";
            return title.includes(searchText) || subject.includes(searchText);
          })
        : allTableRows;

      currentPage = 1;
      displayTableRows();
    });
  } else {
    console.warn("Search input with id 'searchreview' not found. Search functionality will not work.");
  }
  
  document.querySelectorAll('.sortable').forEach(header => {
  header.addEventListener('click', () => {
    const isAsc = header.classList.contains('asc');
    const sortType = header.dataset.type;
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);

    // Remove sort state from all headers
    document.querySelectorAll('.sortable').forEach(h => {
      h.classList.remove('asc', 'desc');
      const icon = h.querySelector('i');
      icon.style.display = 'none';
      icon.className = 'fas fa-sort sort-icon';
    });

    // Apply new sort state
    header.classList.add(isAsc ? 'desc' : 'asc');
    const icon = header.querySelector('i');
    icon.style.display = 'inline-block';
    icon.className = isAsc ? 'fas fa-sort-down sort-icon' : 'fas fa-sort-up sort-icon';

    // Sort rows
    sortColumn(columnIndex, sortType, !isAsc);
      });
    });


  function sortColumn(columnIndex, type, isAsc) {
    const rows = Array.from(document.querySelector('.task-table tbody').querySelectorAll('tr'));

    rows.sort((a, b) => {
      const aVal = a.cells[columnIndex].textContent.trim();
      const bVal = b.cells[columnIndex].textContent.trim();
      return type === 'date' ? compareDates(aVal, bVal, isAsc) :
       type === 'score' ? compareScore(aVal, bVal, isAsc) :
       type === 'status' ? compareStatus(aVal, bVal, isAsc) :
       compareText(aVal, bVal, isAsc);
    });

    const tbody = document.querySelector('.task-table tbody');
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
  }

  function compareText(a, b, isAsc) {
    return (a === '-' ? 1 : b === '-' ? -1 : a.localeCompare(b)) * (isAsc ? 1 : -1);
  }

  function compareDates(a, b, isAsc) {
    const parseDate = str => {
      if (str === '-') return new Date(0);
      const [day, month, year] = str.split('/');
      return new Date(year, month - 1, day);
    };

    const dateA = parseDate(a);
    const dateB = parseDate(b);
    return (dateA - dateB) * (isAsc ? 1 : -1);
  }

    function compareScore(a, b, isAsc) {
    const parseScore = s => s === '-' ? -1 : parseInt(s.replace('%', ''));
    return (parseScore(a) - parseScore(b)) * (isAsc ? 1 : -1);
    }

    function compareStatus(a, b, isAsc) {
    const rank = {
        'Start': 1,
        'Continue': 2,
        'Check Transcript': 3,
        'Review': 3 // just in case you still use this label
    };
    return ((rank[a] || 99) - (rank[b] || 99)) * (isAsc ? 1 : -1);
    }


  displayTableRows();
});