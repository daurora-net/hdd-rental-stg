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
    resourceOrder: 'name', // 追記

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
            acc[resource.id] = resource.name;
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
      var resources = eventObj.getResources(); // リソースを取得
      var resourceId = resources.length > 0 ? resources[0].id : undefined;

      // 編集モーダルを開く
      var modal = document.getElementById("editEventModal");
      modal.style.display = "block";

      // フォームにデータをセットする
      document.getElementById("editEventTitle").value = eventObj.title;
      document.getElementById("editEventManager").value = eventObj.extendedProps.manager;
      document.getElementById("editEventStart").value = eventObj.start ? eventObj.start.toISOString().slice(0, 10) : '';
      document.getElementById("editEventEnd").value = eventObj.end ? eventObj.end.toISOString().slice(0, 10) : '';
      document.getElementById("editEventId").value = eventObj.id;
      document.getElementById("editIsReturned").checked = (eventObj.extendedProps.is_returned == 1);

      // 返却日と実際の開始日をセット
      document.getElementById("editReturnDate").value = eventObj.extendedProps.return_date ? eventObj.extendedProps.return_date.slice(0, 10) : '';
      document.getElementById("editActualStart").value = eventObj.extendedProps.actual_start ? eventObj.extendedProps.actual_start.slice(0, 10) : '';

      // フォームにデータをセットする
      document.getElementById("editEventNotes").value = eventObj.extendedProps.notes || '';

      var durationField = document.getElementById("editRentalDuration");
      if (durationField) {
        durationField.value = eventObj.extendedProps.duration || '';
        console.log("editRentalDurationに値を設定しました:", durationField.value);
      } else {
        console.log("editRentalDuration要素が見つかりませんでした。");
      }

      // HDD Noの表示
      if (resourceId) {
        document.getElementById("editRentalHdd").value = resourceId;
        console.log('Set HDD No to:', document.getElementById("editRentalHdd").value);
      } else {
        console.log('resourceId is undefined or null');
      }
    }
  });

  calendar.render();
});