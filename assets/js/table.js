// テーブルソート
function sortTable(header, n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.querySelector("table");

  var thElements = table.querySelectorAll("th");
  thElements.forEach(function (th) {
    var icon = th.querySelector("i");
    if (icon) {
      icon.className = "fa-solid fa-sort";
    }
  });

  var icon = header.querySelector("i");
  if (icon && icon.classList.contains("fa-arrow-down-short-wide")) {
    dir = "desc";
  } else {
    dir = "asc";
  }

  switching = true;
  while (switching) {
    switching = false;
    rows = table.tBodies[0].rows;
    for (i = 0; i < rows.length - 1; i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      if (dir === "asc") {
        if (x.innerText.toLowerCase() > y.innerText.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (dir === "desc") {
        if (x.innerText.toLowerCase() < y.innerText.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount++;
    } else {
      if (switchcount === 0 && dir === "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }

  if (icon) {
    if (dir === "asc") {
      icon.className = "fa-solid fa-arrow-down-short-wide";
    } else {
      icon.className = "fa-solid fa-arrow-down-wide-short";
    }
  }
}