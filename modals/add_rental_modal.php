<?php
// modals/add_rental_modal.php
// ※ DB接続は不要（JSでfetchするため）
//   もし従来の「PHPで直接SELECT→<option>生成」だった場合、そちらを削除する

// ※ ここでは <select> は空の状態で用意し、JSで動的に埋め込む
?>

<div id="addRentalModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('addRentalModal').style.display='none'">
      <i class="fa-solid fa-xmark"></i>
    </span>
    <form method="post" class="form" action="actions/add_rental.php">
      <h3>スケジュール追加</h3>
      <div class="flex">
        <div class="form-content w-300px">
          <label for="addRentalTitle" class="required">番組名</label>
          <input type="text" id="addRentalTitle" name="rentalTitle" required>
        </div>
        <div class="form-content w-200px">
          <label for="addRentalManager" class="required">担当者</label>
          <input type="text" id="addRentalManager" name="rentalManager" required>
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-150px">
          <label for="addRentalStart" class="required">開始日</label>
          <input type="date" id="addRentalStart" name="rentalStart" required>
        </div>
        <div class="form-content w-150px">
          <label for="addRentalEnd" class="required">終了予定日</label>
          <input type="date" id="addRentalEnd" name="rentalEnd" required>
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-200px">
          <label for="addRentalHdd" class="required">HDD No.</label>
          <!-- ▼ 初期状態では空。JSで fetch し、<option> を注入 -->
          <select id="addRentalHdd" name="rentalHdd" required>
          </select>
        </div>
        <div class="form-content w-200px">
          <label for="addRentalLocation" class="required">使用場所</label>
          <div class="custom-select-wrapper">
            <select id="addRentalLocation" name="rentalLocation" required>
              <option value="104" selected>104</option>
              <option value="外部">外部</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-content w-200px">
        <label for="addRentalCable">ケーブル</label>
        <div class="custom-select-wrapper">
          <select id="addRentalCable" name="rentalCable">
            <option value="USB3.0" selected>USB</option>
            <option value="Thunderbolt">Thunderbolt</option>
            <option value=""></option>
          </select>
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-150px">
          <label for="addReturnDate">返却日</label>
          <input type="date" id="addReturnDate" name="returnDate">
        </div>
        <div class="form-content w-150px">
          <label for="addRentalDuration">使用日数</label>
          <input id="addRentalDuration" name="rentalDuration" class="auto-input text-right" readonly>
        </div>
      </div>
      <div class="form-content w-70">
        <label for="addRentalNotes">メモ</label>
        <textarea id="addRentalNotes" name="rentalNotes" rows="2"></textarea>
      </div>
      <div class="flex">
        <button type="submit" class="modal-btn">追加</button>
        <button type="button" class="cancel-btn"
          onclick="document.getElementById('addRentalModal').style.display='none';">
          キャンセル
        </button>
      </div>
    </form>
  </div>
</div>