// テーブルソート
function sortTable(header, n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.querySelector("table");

  var thElements = table.querySelectorAll("th");
  thElements.forEach(function (th) {
    var icon = th.querySelector("i");
    if (icon) {
      icon.className = "fa-solid fa-sort no-print";
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
      // 数値/文字列の判定用に変数を用意
      var xVal = x.innerText.toLowerCase();
      var yVal = y.innerText.toLowerCase();

      // 数値としてパース
      var xNum = parseFloat(xVal);
      var yNum = parseFloat(yVal);
      var xIsNum = !isNaN(xNum);
      var yIsNum = !isNaN(yNum);

      if (dir === "asc") {
        // 昇順
        if (xIsNum && yIsNum) {
          // 両方とも数値なら数値で比較
          if (xNum > yNum) {
            shouldSwitch = true;
            break;
          }
        } else {
          // それ以外は文字列で比較
          if (xVal > yVal) {
            shouldSwitch = true;
            break;
          }
        }
      } else if (dir === "desc") {
        // 降順
        if (xIsNum && yIsNum) {
          // 両方とも数値なら数値で比較
          if (xNum < yNum) {
            shouldSwitch = true;
            break;
          }
        } else {
          // それ以外は文字列で比較
          if (xVal < yVal) {
            shouldSwitch = true;
            break;
          }
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
// テーブル検索機能の実装
function filterTableBySearch(inputId, tableSelector) {
  var input = document.getElementById(inputId);
  if (!input) return;
  input.addEventListener('keyup', function () {
    var filter = input.value.toLowerCase();
    var rows = document.querySelectorAll(tableSelector + " tbody tr");
    rows.forEach(function (row) {
      var text = row.textContent.toLowerCase();
      if (text.indexOf(filter) > -1) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
}

// 検索入力欄に対する初期化呼び出し（対象：rental_listページのテーブル）
document.addEventListener('DOMContentLoaded', function () {
  filterTableBySearch('rentalTableSearchInput', '.rental-list table');
});
// 検索入力欄に対する初期化呼び出し（対象：billing_listページのテーブル）
document.addEventListener('DOMContentLoaded', function () {
  filterTableBySearch('billingTableSearchInput', '.billing-list table');
});