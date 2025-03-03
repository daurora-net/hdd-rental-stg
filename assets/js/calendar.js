document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');
  var resourcesData = {}; // リソースデータを保持する変数

  var calendar = new FullCalendar.Calendar(calendarEl, {
    timeZone: 'UTC',
    initialView: 'resourceTimelineMonth',
    aspectRatio: 1.5,
    height: 'auto',
    headerToolbar: {
      left: '',
      right: 'today,prev,next'
    },
    editable: true,
    resourceAreaHeaderContent: 'HDD No.',
    resourceOrder: 'name',

    selectable: true,
    dateClick: function (info) {
      // 「新規追加モーダル (addRentalModal) 」を開き、開始日にクリックした日付を設定する
      var addRentalModal = document.getElementById("addRentalModal");
      if (addRentalModal) {
        // 取得した info.dateStr は "YYYY-MM-DDTHH:MM:SSZ" の形式なので、日付部分を切り出す
        document.getElementById("addRentalStart").value = info.dateStr.slice(0, 10);

        // 追加: HDDリソースを取得し、<select id="addRentalHdd"> にオプションを設定する
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

    resourceLabelContent: function (arg) {
      return resourcesData[arg.resource.id] ? resourcesData[arg.resource.id] : arg.resource.id;
    },

    resources: function (fetchInfo, successCallback, failureCallback) {
      fetch('actions/fetch_resources.php')
        .then(response => response.json())
        .then(data => {
          // 名前順（abc順）と番号順にソート
          data.sort((a, b) => {
            // 名前部分を抽出
            const aNameMatch = a.name.match(/^([A-Za-z]+)(\d+)$/);
            const bNameMatch = b.name.match(/^([A-Za-z]+)(\d+)$/);

            const aPrefix = aNameMatch ? aNameMatch[1] : '';
            const bPrefix = bNameMatch ? bNameMatch[1] : '';

            // 名前のアルファベット順を比較
            const prefixCompare = aPrefix.localeCompare(bPrefix);
            if (prefixCompare !== 0) {
              return prefixCompare;
            }

            // 数字部分を取得し、数値に変換して比較
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

      // イベントのタイトル、マネージャー、リソース名を表示
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

      // editEventModal がちゃんと取得できることを確認
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

      // フォームにデータをセットする
      document.getElementById("editEventTitle").value = eventObj.title;
      document.getElementById("editEventManager").value = eventObj.extendedProps.manager;
      document.getElementById("editEventStart").value = eventObj.start
        ? eventObj.start.toISOString().slice(0, 10)
        : "";
      // DBから取った本来の終了日
      var realEnd = eventObj.extendedProps.real_end;

      // もし DB の終了日が無い場合は、開始日と同じにするなど
      if (!realEnd) {
        realEnd = eventObj.start
          ? eventObj.start.toISOString().slice(0, 10)
          : "";
      }

      // ここでフォームにセット
      document.getElementById("editEventEnd").value = realEnd;
      document.getElementById("editEventId").value = eventObj.id;

      // 返却済表示
      // document.getElementById("editIsReturned").checked =
      //   eventObj.extendedProps.is_returned === 1;

      // 返却日をセット
      document.getElementById("editReturnDate").value =
        eventObj.extendedProps.return_date
          ? eventObj.extendedProps.return_date.slice(0, 10)
          : "";
      document.getElementById("editReturnDate").dispatchEvent(new Event("input"));

      // location をセット
      document.getElementById("editRentalLocation").value =
        eventObj.extendedProps.location || "";

      // cable をセット
      document.getElementById("editRentalCable").value =
        eventObj.extendedProps.cable || "";

      // duration が必要なら
      var durationField = document.getElementById("editRentalDuration");
      if (durationField) {
        if (eventObj.extendedProps.is_returned == 0) {
          durationField.value = "";
        } else {
          durationField.value = eventObj.extendedProps.duration || "";
        }
      }

      // notes をセット
      document.getElementById("editEventNotes").value =
        eventObj.extendedProps.notes || "";
    },
    eventClassNames: function (arg) {
      if (arg.event.extendedProps.is_returned == 1) {
        return ['returned-event'];
      }
      return [];
    }
  });

  window.calendar = calendar;
  calendar.render();
});