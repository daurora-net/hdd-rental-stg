<div id="addHddModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('addHddModal').style.display='none'">&times;</span>
    <form method="post" action="actions/add_hdd.php">
      <h3>HDD追加</h3>

      <div class="flex">
        <div class="form-content w-300px">
          <label for="hddName" class="required">HDD名</label>
          <input type="text" id="hddName" name="hddName" required>
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