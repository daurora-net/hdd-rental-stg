document.addEventListener('DOMContentLoaded', function () {
  // HDD追加モーダルの表示
  var addHddBtn = document.getElementById("addHddBtn");
  if (addHddBtn) {
    addHddBtn.addEventListener("click", function () {
      var addHddModal = document.getElementById("addHddModal");
      if (addHddModal) {
        addHddModal.style.display = "block";
      }
    });
  }

  // レンタル追加モーダルの表示
  var addRentalBtn = document.getElementById("addRentalBtn");
  if (addRentalBtn) {
    addRentalBtn.addEventListener("click", function () {
      var addRentalModal = document.getElementById("addRentalModal");
      if (addRentalModal) {
        addRentalModal.style.display = "block";
      }
    });
  }

  // HDD編集モーダルの表示
  var editHddBtns = document.querySelectorAll(".edit-hdd-btn"); // HDD編集ボタン用クラス
  if (editHddBtns) {
    editHddBtns.forEach(function (editBtn) {
      editBtn.addEventListener("click", function () {
        var editHddModal = document.getElementById("editHddModal");
        if (editHddModal) {
          var hddId = editBtn.getAttribute("data-id");
          var hddName = editBtn.getAttribute("data-name");

          document.getElementById("editHddId").value = hddId;
          document.getElementById("editHddName").value = hddName;

          editHddModal.style.display = "block";
        }
      });
    });
  }

  // イベント編集モーダルの表示
  var editEventBtns = document.querySelectorAll(".edit-event-btn"); // レンタル編集ボタン用クラス
  if (editEventBtns) {
    editEventBtns.forEach(function (editBtn) {
      editBtn.addEventListener("click", function () {
        var editEventModal = document.getElementById("editEventModal");
        if (editEventModal) {
          // ボタンの data-* 属性からデータを取得
          var rentalId = editBtn.getAttribute('data-id');
          var title = editBtn.getAttribute('data-title');
          var manager = editBtn.getAttribute('data-manager');
          var start = editBtn.getAttribute('data-start');
          var end = editBtn.getAttribute('data-end');
          var resourceId = editBtn.getAttribute('data-resource-id');
          var location = editBtn.getAttribute('data-location');
          var isReturned = editBtn.getAttribute('data-is-returned') == '1' || editBtn.getAttribute('data-is-returned') == 'true';
          var returnDate = editBtn.getAttribute('data-return-date');
          var actualStart = editBtn.getAttribute('data-actual-start');
          var notes = editBtn.getAttribute('data-notes');

          // モーダル内のフォームにデータをセット
          document.getElementById('editEventId').value = rentalId;
          document.getElementById('editEventTitle').value = title;
          document.getElementById('editEventManager').value = manager;
          document.getElementById('editEventStart').value = start;
          document.getElementById('editEventEnd').value = end;
          document.getElementById('editRentalHdd').value = resourceId;
          document.getElementById('editRentalLocation').value = location;
          document.getElementById('editIsReturned').checked = isReturned;
          document.getElementById('editReturnDate').value = returnDate;
          document.getElementById('editActualStart').value = actualStart;
          document.getElementById('editEventNotes').value = notes;

          // モーダルを表示
          editEventModal.style.display = 'block';

          // 時間計算を初期化
          calculateDuration('editActualStart', 'editReturnDate', 'editRentalDuration');
        }
      });
    });
  }

  // モーダルの外側をクリックしたときにモーダルを閉じる処理
  window.addEventListener('click', function (event) {
    var addHddModal = document.getElementById("addHddModal");
    var addRentalModal = document.getElementById("addRentalModal");
    var editHddModal = document.getElementById("editHddModal");
    var editEventModal = document.getElementById("editEventModal");

    if (event.target === addHddModal) {
      addHddModal.style.display = "none";
    }
    if (event.target === addRentalModal) {
      addRentalModal.style.display = "none";
    }
    if (editHddModal && event.target === editHddModal) {
      editHddModal.style.display = "none";
    }
    if (editEventModal && event.target === editEventModal) {
      editEventModal.style.display = "none";
    }
  });

  // モーダルの閉じるボタンをクリックしたときの処理
  document.querySelectorAll('.close').forEach(function (closeBtn) {
    closeBtn.addEventListener("click", function () {
      closeBtn.closest('.modal').style.display = "none";
    });
  });

  // 時間計算（日単位）自動挿入
  function calculateDuration(actualStartId, returnDateId, durationId) {
    var actualStartEl = document.getElementById(actualStartId);
    var returnDateEl = document.getElementById(returnDateId);
    var durationEl = document.getElementById(durationId);
    if (!actualStartEl || !returnDateEl || !durationEl) return;

    function updateDuration() {
      console.log('updateDuration呼び出し'); // デバッグ用ログ
      var actualStartValue = actualStartEl.value;
      var returnDateValue = returnDateEl.value;
      console.log('実際の開始日:', actualStartValue, '返却日:', returnDateValue); // 入力値ログ
      if (actualStartValue && returnDateValue) {
        var actualStart = new Date(actualStartValue);
        var returnDate = new Date(returnDateValue);
        if (!isNaN(actualStart) && !isNaN(returnDate)) {
          var diffTime = returnDate - actualStart;
          var diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
          console.log('計算結果（日数）:', diffDays); // 計算結果ログ
          console.log('durationEl.value 設定前:', durationEl.value);
          durationEl.value = diffDays;
          console.log('durationEl.value 設定後:', durationEl.value);
        } else {
          durationEl.value = '';
        }
      } else {
        durationEl.value = '';
      }
    }

    // 修正点: 'change' に加え 'input' イベントを追加し、初期計算を実行
    actualStartEl.addEventListener('change', updateDuration);
    actualStartEl.addEventListener('input', updateDuration);
    returnDateEl.addEventListener('change', updateDuration);
    returnDateEl.addEventListener('input', updateDuration);
    updateDuration();
  }

  // 新規レンタル追加モーダル用
  calculateDuration('addActualStart', 'addReturnDate', 'addRentalDuration');
  // イベント編集モーダル用
  calculateDuration('editActualStart', 'editReturnDate', 'editRentalDuration');
});