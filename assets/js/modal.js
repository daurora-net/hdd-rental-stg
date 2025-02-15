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
          var hddNotes = editBtn.getAttribute("data-notes");

          var hddCapacity = editBtn.getAttribute("data-capacity");
          if (!hddCapacity) {
            hddCapacity = "";
          }

          document.getElementById("editHddId").value = hddId;
          document.getElementById("editHddName").value = hddName;
          document.getElementById("editHddCapacity").value = hddCapacity;
          document.getElementById("editHddNotes").value = hddNotes;

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
          var cable = editBtn.getAttribute('data-cable');
          var isReturned = editBtn.getAttribute('data-is-returned') == '1' || editBtn.getAttribute('data-is-returned') == 'true';
          var returnDate = editBtn.getAttribute('data-return-date');
          var notes = editBtn.getAttribute('data-notes');

          // モーダル内のフォームにデータをセット
          document.getElementById('editEventId').value = rentalId;
          document.getElementById('editEventTitle').value = title;
          document.getElementById('editEventManager').value = manager;
          document.getElementById('editEventStart').value = start;
          document.getElementById('editEventEnd').value = end;
          // 利用可能なHDDリソースを取得してセレクトボックスを更新
          if (resourceId) {
            fetch(`actions/fetch_available_resources.php?current_rental_id=${rentalId}`)
              .then(response => response.json())
              .then(data => {
                var hddSelect = document.getElementById("editRentalHdd");
                hddSelect.innerHTML = ''; // 既存のオプションをクリア

                data.forEach(function (resource) {
                  var option = document.createElement("option");
                  option.value = resource.id;
                  option.textContent = resource.name;
                  hddSelect.appendChild(option);
                });

                // 現在のリソースを選択状態にする
                hddSelect.value = resourceId;
              })
              .catch(error => {
                console.error("利用可能なHDDリソースの取得エラー:", error);
              });
          }
          document.getElementById('editRentalLocation').value = location;
          document.getElementById('editRentalCable').value = cable;
          // document.getElementById('editIsReturned').checked = isReturned;
          document.getElementById('editReturnDate').value = returnDate;
          document.getElementById('editEventNotes').value = notes;

          // モーダルを表示
          editEventModal.style.display = 'block';

          // 使用日数を初期化（開始日: editEventStart を使用）
          calculateDuration('editEventStart', 'editReturnDate', 'editRentalDuration');
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
      addHddModal.querySelector('form').reset();
    }
    if (event.target === addRentalModal) {
      addRentalModal.style.display = "none";
      addRentalModal.querySelector('form').reset();
    }
    if (editHddModal && event.target === editHddModal) {
      editHddModal.style.display = "none";
      editHddModal.querySelector('form').reset();
    }
    if (editEventModal && event.target === editEventModal) {
      editEventModal.style.display = "none";
      editEventModal.querySelector('form').reset();
    }
  });

  // モーダルの閉じるボタンをクリックしたときの処理
  document.querySelectorAll('.close').forEach(function (closeBtn) {
    closeBtn.addEventListener("click", function () {
      closeBtn.closest('.modal').style.display = "none";
    });
  });

  // 使用日数を自動挿入
  function calculateDuration(startId, returnDateId, durationId) {
    var startEl = document.getElementById(startId);
    var returnDateEl = document.getElementById(returnDateId);
    var durationEl = document.getElementById(durationId);
    if (!startEl || !returnDateEl || !durationEl) return;

    function updateDuration() {
      var startValue = startEl.value;
      var returnDateValue = returnDateEl.value;
      if (startValue && returnDateValue) {
        var startDate = new Date(startValue);
        var returnDate = new Date(returnDateValue);
        if (!isNaN(startDate) && !isNaN(returnDate)) {
          var diffTime = returnDate - startDate;
          var diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
          durationEl.value = diffDays;
        } else {
          durationEl.value = '';
        }
      } else {
        durationEl.value = '';
      }
    }

    // 修正点: 'change' に加え 'input' イベントを追加し、初期計算を実行
    startEl.addEventListener('change', updateDuration);
    startEl.addEventListener('input', updateDuration);
    returnDateEl.addEventListener('change', updateDuration);
    returnDateEl.addEventListener('input', updateDuration);
    updateDuration();
  }

  // 返却済自動チェック：新規レンタル追加モーダル用
  var addReturnDateEl = document.getElementById('addReturnDate');
  var addIsReturnedContainer = document.getElementById('addIsReturnedContainer');
  var addIsReturnedEl = document.getElementById('addIsReturned');

  if (addReturnDateEl) {
    addReturnDateEl.addEventListener('input', function () {
      if (this.value) {
        addIsReturnedContainer.style.display = 'block';
        addIsReturnedEl.checked = true;
        addIsReturnedEl.disabled = true;
      } else {
        addIsReturnedContainer.style.display = 'none';
        addIsReturnedEl.checked = false;
        addIsReturnedEl.disabled = false;
      }
    });
  }

  // 返却済自動チェック：イベント編集モーダル用
  var editReturnDateEl = document.getElementById('editReturnDate');
  var editIsReturnedEl = document.getElementById('editIsReturned');

  if (editReturnDateEl) {
    editReturnDateEl.addEventListener('input', function () {
      if (this.value) {
        editIsReturnedEl.checked = true;
        editIsReturnedEl.disabled = true;
      } else {
        editIsReturnedEl.checked = false;
        editIsReturnedEl.disabled = false;
      }
    });
  }

  // 新規レンタル追加モーダル用
  calculateDuration('addRentalStart', 'addReturnDate', 'addRentalDuration');
  // イベント編集モーダル用
  calculateDuration('editEventStart', 'editReturnDate', 'editRentalDuration');

});