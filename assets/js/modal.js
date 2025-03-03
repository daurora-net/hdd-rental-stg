document.addEventListener('DOMContentLoaded', function () {

  // ---------------------------------------------
  //  HDD追加モーダル
  // ---------------------------------------------
  var addHddBtn = document.getElementById("addHddBtn");
  if (addHddBtn) {
    addHddBtn.addEventListener("click", function () {
      var addHddModal = document.getElementById("addHddModal");
      if (addHddModal) {
        addHddModal.style.display = "block";
      }
    });
  }

  // ---------------------------------------------
  //  HDD編集モーダル
  // ---------------------------------------------
  var editHddBtns = document.querySelectorAll(".edit-hdd-btn"); // HDD編集ボタン
  if (editHddBtns) {
    editHddBtns.forEach(function (editBtn) {
      editBtn.addEventListener("click", function () {
        var editHddModal = document.getElementById("editHddModal");
        if (!editHddModal) return;

        // ボタンの data-* 属性から情報を取得してフォームへセット
        var hddId = editBtn.getAttribute("data-id");
        var hddName = editBtn.getAttribute("data-name");
        var hddCapacity = editBtn.getAttribute("data-capacity") || "";
        var hddNotes = editBtn.getAttribute("data-notes") || "";

        document.getElementById("editHddId").value = hddId;
        document.getElementById("editHddName").value = hddName;
        document.getElementById("editHddCapacity").value = hddCapacity;
        document.getElementById("editHddNotes").value = hddNotes;

        // モーダル表示
        editHddModal.style.display = "block";
      });
    });
  }

  // ---------------------------------------------
  //  レンタル追加モーダル
  //   - 「未使用HDD」リストを取得して <select> に注入
  //   - 開始日/終了予定日が入力される度に再取得
  // ---------------------------------------------
  var addRentalBtn = document.getElementById("addRentalBtn");
  if (addRentalBtn) {
    addRentalBtn.addEventListener("click", function () {
      var addRentalModal = document.getElementById("addRentalModal");
      if (!addRentalModal) return;

      // モーダルを表示する（先に表示してもOK）
      addRentalModal.style.display = "block";

      // ▼ リソース取得用の関数
      function fetchHddListForAdd() {
        var startVal = document.getElementById("addRentalStart").value;
        var endVal = document.getElementById("addRentalEnd").value;
        if (!startVal) startVal = "";
        if (!endVal) endVal = "";

        // 「開始日」「終了予定日」をクエリパラメータで送る
        var url = "actions/fetch_available_resources.php?current_rental_id=0"
          + "&start=" + encodeURIComponent(startVal)
          + "&end=" + encodeURIComponent(endVal);

        fetch(url)
          .then(response => response.json())
          .then(data => {
            var hddSelect = document.getElementById("addRentalHdd");
            if (!hddSelect) return;
            hddSelect.innerHTML = ''; // クリア

            data.forEach(function (resource) {
              var option = document.createElement("option");
              option.value = resource.id;
              option.textContent = resource.name;
              hddSelect.appendChild(option);
            });
          })
          .catch(error => {
            console.error("未使用HDD取得エラー (addRentalModal):", error);
          });
      }

      // ▼ まず一度呼び出す
      fetchHddListForAdd();

      // ▼ 開始日・終了予定日が変わるたびに再呼び出し
      var addRentalStart = document.getElementById("addRentalStart");
      var addRentalEnd = document.getElementById("addRentalEnd");
      if (addRentalStart && addRentalEnd) {
        addRentalStart.addEventListener('change', fetchHddListForAdd);
        addRentalEnd.addEventListener('change', fetchHddListForAdd);
        addRentalStart.addEventListener('input', fetchHddListForAdd);
        addRentalEnd.addEventListener('input', fetchHddListForAdd);
      }
    });
  }

  // ---------------------------------------------
  //  追記：レンタル追加フォームを非同期送信し、
  //       サーバー側( add_rental.php )が"登録できません"などを返したら
  //       アラートを出して保存中断する
  // ---------------------------------------------
  var addRentalForm = document.getElementById('addRentalForm');
  if (addRentalForm) {
    addRentalForm.addEventListener('submit', function (e) {
      e.preventDefault(); // 通常のフォーム送信を抑制

      var formData = new FormData(addRentalForm);
      fetch('actions/add_rental.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(data => {
          // ▼ 修正：より明確に判定
          if (data.trim() === 'OK') {
            // 成功 → ページリロード
            window.location.reload();
          } else {
            // "登録できません" や "エラー" 等が含まれていたらalert
            alert(data);
          }
        })
        .catch(error => {
          console.error('レンタル追加エラー:', error);
        });
    });
  }

  // ---------------------------------------------
  //  レンタル編集モーダル
  //   「未使用HDD + このレンタルのリソース」を取得して <select> に注入
  // ---------------------------------------------
  var editEventBtns = document.querySelectorAll(".edit-event-btn"); // レンタル編集ボタン
  if (editEventBtns.length > 0) {
    editEventBtns.forEach(function (editBtn) {
      editBtn.addEventListener("click", function () {
        var editEventModal = document.getElementById("editEventModal");
        if (!editEventModal) return;

        // ボタンの data-* 属性から各データを取得
        var rentalId = editBtn.getAttribute('data-id');
        var title = editBtn.getAttribute('data-title');
        var manager = editBtn.getAttribute('data-manager');
        var start = editBtn.getAttribute('data-start');
        var end = editBtn.getAttribute('data-end');
        var resourceId = editBtn.getAttribute('data-resource-id');
        var location = editBtn.getAttribute('data-location') || "";
        var cable = editBtn.getAttribute('data-cable') || "";
        var returnDate = editBtn.getAttribute('data-return-date') || "";
        var notes = editBtn.getAttribute('data-notes') || "";

        // ▼ フォームへセット
        document.getElementById("editEventId").value = rentalId;
        document.getElementById("editEventTitle").value = title;
        document.getElementById("editEventManager").value = manager;
        document.getElementById("editEventStart").value = start;
        document.getElementById("editEventEnd").value = end;
        document.getElementById("editRentalLocation").value = location;
        document.getElementById("editRentalCable").value = cable;
        document.getElementById("editReturnDate").value = returnDate;
        document.getElementById("editReturnDate").dispatchEvent(new Event("input"));
        document.getElementById("editEventNotes").value = notes;

        // ▼ 「未使用HDD + 現在のresourceId」を取得
        //    current_rental_id = rentalId
        var url = 'actions/fetch_available_resources.php?current_rental_id=' + rentalId;
        fetch(url)
          .then(response => response.json())
          .then(data => {
            var hddSelect = document.getElementById("editRentalHdd");
            hddSelect.innerHTML = '';

            data.forEach(function (resource) {
              var option = document.createElement("option");
              option.value = resource.id;
              option.textContent = resource.name;
              hddSelect.appendChild(option);
            });
            // ▼ このレンタルが利用中のresourceを選択状態に
            hddSelect.value = resourceId;
          })
          .catch(error => {
            console.error("未使用HDD取得エラー (editEventModal):", error);
          });

        // ▼ モーダル表示
        editEventModal.style.display = 'block';
      });
    });
  }

  // ---------------------------------------------
  //  USER編集モーダル
  // ---------------------------------------------
  var editUserBtns = document.querySelectorAll(".edit-user-btn");
  if (editUserBtns) {
    editUserBtns.forEach(function (editBtn) {
      editBtn.addEventListener("click", function () {
        var editUserModal = document.getElementById("editUserModal");
        if (editUserModal) {
          var userId = editBtn.getAttribute("data-id");
          var username = editBtn.getAttribute("data-username");

          document.getElementById("editUserId").value = userId;
          document.getElementById("editUsername").value = username;

          editUserModal.style.display = "block";
        }
      });
    });
  }

  // ---------------------------------------------
  //  モーダルの外側クリックで閉じる処理
  // ---------------------------------------------
  window.addEventListener('click', function (event) {
    // 各モーダルを取得
    var addHddModal = document.getElementById("addHddModal");
    var addRentalModal = document.getElementById("addRentalModal");
    var editHddModal = document.getElementById("editHddModal");
    var editEventModal = document.getElementById("editEventModal");
    var editUserModal = document.getElementById("editUserModal");

    // クリック対象が各モーダルそのものだった場合に閉じる
    if (addHddModal && event.target === addHddModal) {
      addHddModal.style.display = "none";
      addHddModal.querySelector('form').reset();
    }
    if (addRentalModal && event.target === addRentalModal) {
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
    if (editUserModal && event.target === editUserModal) {
      editUserModal.style.display = "none";
      editUserModal.querySelector('form').reset();
    }
  });

  // ---------------------------------------------
  //  モーダル内の「×」閉じるボタン
  // ---------------------------------------------
  document.querySelectorAll('.close').forEach(function (closeBtn) {
    closeBtn.addEventListener("click", function () {
      closeBtn.closest('.modal').style.display = "none";
    });
  });

  // ---------------------------------------------
  // 「使用日数」自動計算（開始日と返却日から）
  // ---------------------------------------------
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

    startEl.addEventListener('change', updateDuration);
    startEl.addEventListener('input', updateDuration);
    returnDateEl.addEventListener('change', updateDuration);
    returnDateEl.addEventListener('input', updateDuration);
    // 初回にも計算実行
    updateDuration();
  }

  // ▼ add_rental_modal の使用日数計算
  calculateDuration('addRentalStart', 'addReturnDate', 'addRentalDuration');
  // ▼ edit_event_modal の使用日数計算
  calculateDuration('editEventStart', 'editReturnDate', 'editRentalDuration');

  // ---------------------------------------------
  //  カレンダーplaceholder非表示（必須項目以外：js-date-fieldクラス付与）
  // ---------------------------------------------
  // すべての date 入力要素を取得（必須・任意問わず）
  const dateFields = document.querySelectorAll("input.js-date-field");

  dateFields.forEach(field => {
    function updateEmptyClass() {
      if (!field.value) {
        // 未入力なら .is-empty を付与
        field.classList.add("is-empty");
      } else {
        // 値があるならクラスを外す
        field.classList.remove("is-empty");
      }
    }

    // 初期表示時に判定
    updateEmptyClass();

    // 値が変わる度に判定し直す
    field.addEventListener("input", updateEmptyClass);
  });

  // ---------------------------------------------
  //  追記：編集イベントモーダルのフォームを非同期送信する処理
  // ---------------------------------------------
  var editEventForm = document.getElementById('editEventForm');
  if (editEventForm) {
    editEventForm.addEventListener('submit', function (e) {
      e.preventDefault();  // 通常のフォーム送信を防止
      var formData = new FormData(editEventForm);
      fetch('actions/edit_event.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(data => {
          if (data.trim() === 'OK') {
            // 正常時 → モーダル閉じ & カレンダー更新
            document.getElementById('editEventModal').style.display = 'none';
            if (window.calendar) {
              window.calendar.refetchEvents();
            }
          } else {
            // エラー時: ダイアログ表示
            alert(data);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    });
  }
});
