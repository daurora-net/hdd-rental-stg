<?php
// レンタル中でないHDDのリソースを取得
$stmt = $conn->prepare("
    SELECT r.id, r.name 
    FROM hdd_resources r
    LEFT JOIN hdd_rentals hr ON r.id = hr.resource_id AND hr.is_returned = FALSE 
    WHERE hr.resource_id IS NULL
");
$stmt->execute();
$hddResourcesForAdd = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="addRentalModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('addRentalModal').style.display='none'">&times;</span>
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
          <label for="addRentalStart">開始日</label>
          <input type="date" id="addRentalStart" name="rentalStart">
        </div>
        <div class="form-content w-150px">
          <label for="addRentalEnd">終了予定日</label>
          <input type="date" id="addRentalEnd" name="rentalEnd">
        </div>
      </div>

      <div class="flex">
        <div class="form-content w-200px">
          <label for="addRentalHdd" class="required">HDD No.</label>
          <div class="custom-select-wrapper">
            <select id="addRentalHdd" name="rentalHdd" required>
              <?php foreach ($hddResourcesForAdd as $resource) { ?>
                <option value="<?php echo $resource['id']; ?>"><?php echo $resource['name']; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-content w-200px">
          <label for="addRentalLocation">使用場所</label>
          <div class="custom-select-wrapper">
            <select id="addRentalLocation" name="rentalLocation">
              <option value="" selected></option>
              <option value="外部">外部</option>
              <option value="104">104</option>
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
          <input type="number" id="addRentalDuration" name="rentalDuration" class="auto-input text-right" readonly>
        </div>
      </div>
      <div class="form-content w-70">
        <label for="addRentalNotes">メモ</label>
        <textarea id="addRentalNotes" name="rentalNotes" rows="2"></textarea>
      </div>
      <button type="submit" class="modal-btn">追加</button>
    </form>
  </div>
</div>