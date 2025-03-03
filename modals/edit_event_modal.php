<div id="editEventModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('editEventModal').style.display='none'">
      <i class="fa-solid fa-xmark"></i>
    </span>
    <form id="editEventForm" method="post" class="form">
      <h3>スケジュール編集</h3>
      <input type="hidden" id="editEventId" name="eventId">

      <div class="flex">
        <div class="form-content w-300px">
          <label for="editEventTitle" class="required">番組名</label>
          <input type="text" id="editEventTitle" name="eventTitle" required>
        </div>
        <div class="form-content w-200px">
          <label for="editEventManager" class="required">担当者</label>
          <input type="text" id="editEventManager" name="eventManager" required>
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-150px">
          <label for="editEventStart" class="required">開始日</label>
          <input type="date" id="editEventStart" name="eventStart" required>
        </div>
        <div class="form-content w-150px">
          <label for="editEventEnd" class="required">終了予定日</label>
          <input type="date" id="editEventEnd" name="eventEnd" required>
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-200px">
          <label for="editRentalHdd" class="required">HDD No.</label>
          <div class="custom-select-wrapper">
            <!-- ▼ JSで options を動的に注入 -->
            <select id="editRentalHdd" name="rentalHdd" required></select>
          </div>
        </div>
        <div class="form-content w-200px">
          <label for="editRentalLocation" class="required">使用場所</label>
          <div class="custom-select-wrapper">
            <select id="editRentalLocation" name="rentalLocation" required>
              <option value="104" selected>104</option>
              <option value="外部">外部</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-content w-200px">
        <label for="editRentalCable">ケーブル</label>
        <div class="custom-select-wrapper">
          <select id="editRentalCable" name="rentalCable">
            <option value="USB3.0" selected>USB</option>
            <option value="USB3.0">USB・Thunderbolt</option>
            <option value="Thunderbolt">Thunderbolt</option>
            <option value=""></option>
          </select>
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-150px">
          <label for="editReturnDate">返却日</label>
          <input type="date" id="editReturnDate" class="js-date-field" name="returnDate">
        </div>
        <div class="form-content w-150px">
          <label for="editRentalDuration">使用日数</label>
          <input id="editRentalDuration" name="rentalDuration" class="auto-input text-right" readonly>
        </div>
      </div>

      <div class="form-content">
        <label for="editEventNotes">メモ</label>
        <textarea id="editEventNotes" name="eventNotes" rows="2"></textarea>
      </div>
      <div class="flex">
        <button type="submit" class="modal-btn">保存</button>
        <button type="button" class="cancel-btn" id="editEventCancelBtn"
          onclick="document.getElementById('editEventModal').style.display='none';">キャンセル</button>
        <button type="submit" class="delete-btn" name="delete" value="1" onclick="return confirm('本当に削除してよろしいですか？');">
          削除
        </button>
      </div>
    </form>
  </div>
</div>