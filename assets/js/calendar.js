document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');
  var resourcesData = {}; // リソースデータを保持する変数

  // ▼ ドラッグ or リサイズした際の info を一時的に保存しておき、
  //   キャンセル時に revert() するための変数
  window.currentCalendarAction = null; // { type: 'drop'|'resize', info: ... }

  var calendar = new FullCalendar.Calendar(calendarEl, {
    timeZone: 'UTC',
    initialView: 'resourceTimelineMonth',
    aspectRatio: 1.5,
    height: 'auto',
    headerToolbar: {
      left: '',
      right: 'today,prev,next'
    },
    editable: true, // イベントをドラッグ移動、リサイズできる
    resourceAreaHeaderContent: 'HDD No.',
    resourceOrder: 'name',

    selectable: true,
    dateClick: function (info) {
      // 新規追加モーダルを開く。開始日にクリックした日付を設定
      var addRentalModal = document.getElementById("addRentalModal");
      if (addRentalModal) {
        document.getElementById("addRentalStart").value = info.dateStr.slice(0, 10);

        fetch('actions/fetch_available_resources.php?current_rental_id=0')
          .then(response => response.json())
          .then(data => {
            var hddSelect = document.getElementById("addRentalHdd");
            if (hddSelect) {
              hddSelect.innerHTML = '';
              data.forEach(function (resource) {
                var option = document.createElement("option");
                option.value = resource.id;
                option.textContent = resource.name;
                hddSelect.appendChild(option);
              });
            }
            addRentalModal.style.display = "block";
          })
          .catch(error => {
            console.error("利用可能なHDDリソース取得エラー (dateClick):", error);
            addRentalModal.style.display = "block";
          });
      }
    },

    // ---------------------------------------------
    // イベントをドラッグ移動した直後のコールバック
    // ---------------------------------------------
    eventDrop: function (info) {
      // info.event は移動後のイベント
      // info.newResource は移動先リソース
      // info.oldResource は元リソース
      // ここではモーダルを出し、新日付・HDDをセット

      // まず revert() 用に info を保管
      window.currentCalendarAction = { type: 'drop', info: info };

      var movedEvent = info.event;
      var newResource = info.newResource;
      var newResourceId = (newResource && newResource.id)
        ? newResource.id
        : (movedEvent.getResources()[0] ? movedEvent.getResources()[0].id : null);

      var newStart = movedEvent.start;
      var newEnd = movedEvent.end;
      var startStr = newStart ? newStart.toISOString().slice(0, 10) : '';

      // ▼ 1日多い問題を修正：end から1日引く
      var adjustedEnd = newEnd ? new Date(newEnd.getTime()) : null;
      if (adjustedEnd) {
        adjustedEnd.setDate(adjustedEnd.getDate() - 1);
      }
      var endStr = adjustedEnd
        ? adjustedEnd.toISOString().slice(0, 10)
        : startStr;

      var editModal = document.getElementById("editEventModal");
      if (!editModal) {
        console.error("editEventModal が見つかりません。");
        return;
      }
      editModal.style.display = "block";

      // フォームへセット
      document.getElementById("editEventId").value = movedEvent.id;
      document.getElementById("editEventTitle").value = movedEvent.title;
      document.getElementById("editEventManager").value = movedEvent.extendedProps.manager || "";
      document.getElementById("editEventStart").value = startStr;
      document.getElementById("editEventEnd").value = endStr;
      document.getElementById("editEventNotes").value = movedEvent.extendedProps.notes || "";
      document.getElementById("editRentalLocation").value
        = movedEvent.extendedProps.location || "";
      document.getElementById("editRentalCable").value
        = movedEvent.extendedProps.cable || "";

      var returnDateField = document.getElementById("editReturnDate");
      if (returnDateField) {
        if (movedEvent.extendedProps.return_date) {
          returnDateField.value = movedEvent.extendedProps.return_date.slice(0, 10);
        } else {
          returnDateField.value = '';
        }
        returnDateField.dispatchEvent(new Event("input"));
      }

      // HDDリストの再取得→新HDDを選択
      fetch(`actions/fetch_available_resources.php?current_rental_id=${movedEvent.id}`)
        .then(response => response.json())
        .then(data => {
          var hddSelect = document.getElementById("editRentalHdd");
          if (!hddSelect) return;
          hddSelect.innerHTML = '';
          data.forEach(function (res) {
            var option = document.createElement("option");
            option.value = res.id;
            option.textContent = res.name;
            hddSelect.appendChild(option);
          });
          hddSelect.value = newResourceId;
        })
        .catch(err => {
          console.error("fetch_available_resourcesエラー (eventDrop):", err);
        });
    },

    // ---------------------------------------------
    // イベントのリサイズ後（両端をドラッグで日付拡縮）
    // ---------------------------------------------
    eventResize: function (info) {
      window.currentCalendarAction = { type: 'resize', info: info };

      var resizedEvent = info.event;
      var newStart = resizedEvent.start;
      var newEnd = resizedEvent.end;

      // リソースは変わってないはず
      var resources = resizedEvent.getResources();
      var resourceId = resources.length > 0 ? resources[0].id : null;

      var startStr = newStart ? newStart.toISOString().slice(0, 10) : '';

      // ▼ 1日多い問題を修正：end から1日引く
      var adjustedEnd = newEnd ? new Date(newEnd.getTime()) : null;
      if (adjustedEnd) {
        adjustedEnd.setDate(adjustedEnd.getDate() - 1);
      }
      var endStr = adjustedEnd
        ? adjustedEnd.toISOString().slice(0, 10)
        : startStr;

      var editModal = document.getElementById("editEventModal");
      if (!editModal) {
        console.error("editEventModal が見つかりません。");
        return;
      }
      editModal.style.display = "block";

      // フォームへセット
      document.getElementById("editEventId").value = resizedEvent.id;
      document.getElementById("editEventTitle").value = resizedEvent.title;
      document.getElementById("editEventManager").value = resizedEvent.extendedProps.manager || "";
      document.getElementById("editEventStart").value = startStr;
      document.getElementById("editEventEnd").value = endStr;
      document.getElementById("editEventNotes").value = resizedEvent.extendedProps.notes || "";
      document.getElementById("editRentalLocation").value
        = resizedEvent.extendedProps.location || "";
      document.getElementById("editRentalCable").value
        = resizedEvent.extendedProps.cable || "";

      var returnDateField = document.getElementById("editReturnDate");
      if (returnDateField) {
        if (resizedEvent.extendedProps.return_date) {
          returnDateField.value = resizedEvent.extendedProps.return_date.slice(0, 10);
        } else {
          returnDateField.value = '';
        }
        returnDateField.dispatchEvent(new Event("input"));
      }

      // HDDセレクト（リソースIDは変わらない想定）
      fetch(`actions/fetch_available_resources.php?current_rental_id=${resizedEvent.id}`)
        .then(response => response.json())
        .then(data => {
          var hddSelect = document.getElementById("editRentalHdd");
          if (!hddSelect) return;
          hddSelect.innerHTML = '';
          data.forEach(function (res) {
            var option = document.createElement("option");
            option.value = res.id;
            option.textContent = res.name;
            hddSelect.appendChild(option);
          });
          hddSelect.value = resourceId;
        })
        .catch(err => {
          console.error("fetch_available_resourcesエラー (eventResize):", err);
        });
    },

    resourceLabelContent: function (arg) {
      return resourcesData[arg.resource.id]
        ? resourcesData[arg.resource.id]
        : arg.resource.id;
    },

    resources: function (fetchInfo, successCallback, failureCallback) {
      fetch('actions/fetch_resources.php')
        .then(response => response.json())
        .then(data => {
          // 名前順（abc順）と番号順にソート
          data.sort((a, b) => {
            const aNameMatch = a.name.match(/^([A-Za-z]+)(\d+)$/);
            const bNameMatch = b.name.match(/^([A-Za-z]+)(\d+)$/);

            const aPrefix = aNameMatch ? aNameMatch[1] : '';
            const bPrefix = bNameMatch ? bNameMatch[1] : '';
            const prefixCompare = aPrefix.localeCompare(bPrefix);
            if (prefixCompare !== 0) {
              return prefixCompare;
            }

            const aNum = aNameMatch ? parseInt(aNameMatch[2], 10) : 0;
            const bNum = bNameMatch ? parseInt(bNameMatch[2], 10) : 0;
            return aNum - bNum;
          });

          resourcesData = data.reduce((acc, resource) => {
            acc[resource.id] = resource.name + (resource.capacity ? '＿' + resource.capacity : '');
            return acc;
          }, {});
          successCallback(data);
        })
        .catch(error => {
          failureCallback(error);
        });
    },

    events: 'actions/fetch_events.php',
    slotLabelFormat: [
      { year: 'numeric', month: 'numeric' },
      { day: 'numeric' }
    ],
    slotLabelContent: ({ date, level }) => {
      const year = date.getUTCFullYear();
      const month = date.getUTCMonth() + 1;
      const day = date.getUTCDate();
      return { html: level === 0 ? `${year}-${month}` : `${day}` };
    },
    eventTimeFormat: {
      hour: false,
      minute: false,
      meridiem: false
    },
    eventContent: function (arg) {
      // イベントに関連付けられたリソースを取得
      var resources = arg.event.getResources();
      var resourceName = '';
      if (resources.length > 0) {
        resourceName = resources[0].title || resources[0].name || resourcesData[resources[0].id];
      }

      var eventTitle = document.createElement('div');
      eventTitle.innerHTML = arg.event.title;

      var eventManager = document.createElement('div');
      eventManager.innerHTML = arg.event.extendedProps.manager;

      var eventResource = document.createElement('div');
      eventResource.innerHTML = resourceName;

      return { domNodes: [eventTitle, eventManager, eventResource] };
    },

    eventClick: function (info) {
      var eventObj = info.event;
      var resources = eventObj.getResources();
      var resourceId = resources.length > 0 ? resources[0].id : undefined;

      var modal = document.getElementById("editEventModal");
      if (!modal) {
        console.log("editEventModal がページに存在しません。");
        return;
      }
      modal.style.display = "block";

      // 利用可能なHDDリソースを取得してセレクトボックスを更新
      if (resourceId) {
        fetch(`actions/fetch_available_resources.php?current_rental_id=${eventObj.id}`)
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

      document.getElementById("editEventTitle").value = eventObj.title;
      document.getElementById("editEventManager").value = eventObj.extendedProps.manager;
      document.getElementById("editEventStart").value = eventObj.start
        ? eventObj.start.toISOString().slice(0, 10)
        : "";

      // realEnd
      var realEnd = eventObj.extendedProps.real_end;
      if (!realEnd) {
        realEnd = eventObj.start
          ? eventObj.start.toISOString().slice(0, 10)
          : "";
      }
      document.getElementById("editEventEnd").value = realEnd;
      document.getElementById("editEventId").value = eventObj.id;

      var retField = document.getElementById("editReturnDate");
      if (retField) {
        retField.value = eventObj.extendedProps.return_date
          ? eventObj.extendedProps.return_date.slice(0, 10)
          : "";
        retField.dispatchEvent(new Event("input"));
      }

      document.getElementById("editRentalLocation").value =
        eventObj.extendedProps.location || "";
      document.getElementById("editRentalCable").value =
        eventObj.extendedProps.cable || "";

      var durationField = document.getElementById("editRentalDuration");
      if (durationField) {
        if (eventObj.extendedProps.is_returned == 0) {
          durationField.value = "";
        } else {
          durationField.value = eventObj.extendedProps.duration || "";
        }
      }

      document.getElementById("editEventNotes").value =
        eventObj.extendedProps.notes || "";
    },

    // ---------------------------------------------
    // 「使用場所」項目に応じたクラス名を付与
    // ---------------------------------------------
    eventClassNames: function (arg) {
      var classes = [];
      if (arg.event.extendedProps.is_returned == 1) {
        classes.push('returned-event');
      }
      var loc = arg.event.extendedProps.location;
      if (loc === '104') {
        classes.push('location-104');
      } else if (loc === '外部') {
        classes.push('location-gaibu');
      }
      return classes;
    }
  });

  window.calendar = calendar;
  calendar.render();

  // ---------------------------------------------
  // hdd_rentalsテーブルにデータが存在する月全てを取得してセレクトボックスに表示
  // ---------------------------------------------
  (function () {
    var todayButton = document.querySelector('.fc-today-button');
    if (!todayButton) return;

    var monthSelect = document.createElement('select');
    monthSelect.id = 'monthSelect';

    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    var currentMonth = currentDate.getMonth() + 1;
    if (currentMonth < 10) {
      currentMonth = '0' + currentMonth;
    }
    var currentMonthStr = currentYear + '-' + currentMonth;
    fetch('actions/fetch_rental_months.php')
      .then(response => response.json())
      .then(data => {
        // フォーマット統一：月部分が1桁の場合は0埋めする
        var formattedData = data.map(function (monthValue) {
          var parts = monthValue.split('-');
          if (parts[1].length === 1) {
            parts[1] = '0' + parts[1];
          }
          return parts[0] + '-' + parts[1];
        });
        formattedData.sort(function (a, b) { return b.localeCompare(a); });
        formattedData.forEach(function (monthValue) {
          var option = document.createElement('option');
          option.value = monthValue;
          option.textContent = monthValue;
          if (monthValue === currentMonthStr) {
            option.selected = true;
          }
          monthSelect.appendChild(option);
        });
      })
      .catch(error => {
        console.error("Error fetching rental months:", error);
      });

    monthSelect.addEventListener('change', function () {
      if (this.value !== '') {
        var parts = this.value.split('-');
        var newDate = new Date(Date.UTC(parseInt(parts[0]), parseInt(parts[1]) - 1, 1));
        calendar.gotoDate(newDate);
      }
    });

    todayButton.parentNode.insertBefore(monthSelect, todayButton.nextSibling);
  })();
});