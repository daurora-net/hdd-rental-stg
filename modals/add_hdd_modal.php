<div id="addHddModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('addHddModal').style.display='none'"><i
        class="fa-solid fa-xmark"></i></span>
    <form method="post" class="form" action="actions/add_hdd.php">
      <h3>HDD追加</h3>

      <div class="flex">
        <div class="form-content w-300px">
          <label for="hddName" class="required">HDD No.</label>
          <input type="text" id="hddName" name="hddName" required>
        </div>
      </div>
      <div class="form-content w-200px">
        <label for="addHddCapacity" class="required">容量</label>
        <div class="custom-select-wrapper">
          <select id="addHddCapacity" name="hddCapacity" required>
            <option value="1TB">1TB</option>
            <option value="2TB">2TB</option>
            <option value="4TB">4TB</option>
            <option value="6TB">6TB</option>
            <option value="8TB">8TB</option>
          </select>
        </div>
      </div>
      <div class="form-content w-70">
        <label for="hddNotes">メモ</label>
        <textarea id="hddNotes" name="hddNotes" rows="3"></textarea>
      </div>
      <button type="submit" class="modal-btn">追加</button>
    </form>
  </div>
</div>