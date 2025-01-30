<div id="editEventModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('editEventModal').style.display='none'">&times;</span>
    <form method="post" class="form" action="actions/edit_event.php">
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
          <label for="editEventStart">開始予定日</label>
          <input type="date" id="editEventStart" name="eventStart">
        </div>
        <div class="form-content w-150px">
          <label for="editEventEnd">終了予定日</label>
          <input type="date" id="editEventEnd" name="eventEnd">
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-200px custom-select">
          <label for="editRentalHdd" class="required">HDD No</label>
          <select id="editRentalHdd" name="rentalHdd" required>
            <!-- オプションはJavaScriptで動的に設定 -->
          </select>
        </div>
        <div class="form-content w-200px custom-select">
          <label for="editRentalLocation">使用場所</label>
          <select id="editRentalLocation" name="rentalLocation">
            <option value="" selected></option>
            <option value="外部">外部</option>
            <option value="104">104</option>
          </select>
        </div>
      </div>

      <div class="form-content w-200px custom-select">
        <label for="editRentalCable">ケーブル</label>
        <select id="editRentalCable" name="rentalCable">
          <option value="USB3.0" selected>USB</option>
          <option value="Thunderbolt">Thunderbolt</option>
          <option value=""></option>
        </select>
      </div>

      <!-- <div class="form-content">
        <label for="editIsReturned">返却済</label>
        <input type="checkbox" id="editIsReturned" name="isReturned" class="custom-checkbox">
        <label for="editIsReturned"></label>
      </div> -->

      <div class="flex">
        <div class="form-content w-150px">
          <label for="editReturnDate">返却日</label>
          <input type="date" id="editReturnDate" name="returnDate">
        </div>
        <div class="form-content w-150px">
          <label for="editActualStart">実際の開始日</label>
          <input type="date" id="editActualStart" name="actualStart">
        </div>
        <div class="form-content w-150px">
          <label for="editRentalDuration">時間計算（日単位）</label>
          <input type="number" id="editRentalDuration" name="rentalDuration" class="auto-input text-right" readonly>
        </div>
      </div>

      <div class="form-content w-70">
        <label for="editEventNotes">メモ</label>
        <textarea id="editEventNotes" name="eventNotes" rows="2"></textarea>
      </div>
      <button type="submit" class="modal-btn">保存</button>
    </form>
  </div>
</div>