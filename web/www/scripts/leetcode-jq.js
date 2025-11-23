// jQuery-powered behaviors for the LeetCode / Prep Tracker screen
// - Filters rows by search text and difficulty
// - Updates helper text with count
// - Adds hover highlight on problem rows

$(function () {
  const $problemsBody = $('#problems-body');
  const $rows = $problemsBody.find('tr');
  const $textFilter = $('#filter-text');
  const $diffFilter = $('#filter-difficulty');
  const $help = $('#p-help');

  function applyFilters() {
    const text = ($textFilter.val() || '').toString().toLowerCase();
    const diff = ($diffFilter.val() || '').toString();
    let visibleCount = 0;

    $rows.each(function () {
      const $row = $(this);
      const title = $row.find('th').text().toLowerCase();
      const rowDiff = $row.find('td:nth-child(3) .badge').text().trim();
      const matchesText = !text || title.includes(text);
      const matchesDiff = !diff || rowDiff === diff;
      const show = matchesText && matchesDiff;
      $row.toggle(show);
      if (show) visibleCount++;
    });

    if ($help.length) {
      $help.text(
        visibleCount
          ? `${visibleCount} problem${visibleCount === 1 ? '' : 's'} shown.`
          : 'No problems match your filters.'
      );
    }
  }

  $('#btn-filter').on('click', function () {
    applyFilters();
  });

  // Also filter when the user hits Enter in the search field
  $textFilter.on('keyup', function (event) {
    if (event.key === 'Enter') {
      applyFilters();
    }
  });

  // Row hover styling using jQuery event delegation
  $problemsBody
    .on('mouseenter', 'tr', function () {
      $(this).addClass('row-hover');
    })
    .on('mouseleave', 'tr', function () {
      $(this).removeClass('row-hover');
    });
});
