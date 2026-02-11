document.addEventListener("click", function (e) {
    // Ignore clicks on buttons / links / icons inside actions
    if (e.target.closest("button, a, i, form")) return;

    const cell = e.target.closest("td, th");
    if (!cell) return;

    const table = cell.closest("table");
    if (!table) return;

    const row = cell.parentNode;
    if (!row || !row.cells) return;

    const lastIndex = row.cells.length - 1;
    if (cell.cellIndex === lastIndex) return;

    const expanded = document.querySelector("td.expanded, th.expanded");
    if (expanded && expanded !== cell) {
        expanded.classList.remove("expanded");
    }

    cell.classList.toggle("expanded");
});
