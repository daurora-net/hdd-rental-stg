<div id="editHddModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('editHddModal').style.display='none'">&times;</span>
    <form method="post" class="form" action="actions/edit_hdd.php">
      <h3>HDD編集</h3>
      <input type="hidden" id="editHddId" name="hddId">

      <div class="flex">
        <div class="form-content w-300px">
          <label for="editHddName" class="required">HDD名</label>
          <input type="text" id="editHddName" name="hddName" required>
        </div>
      </div>

      <div class="form-content w-70">
        <label for="editHddNotes">メモ</label>
        <textarea id="editHddNotes" name="hddNotes" rows="3"></textarea>
      </div>

      <button type="submit" class="modal-btn">保存</button>
    </form>
  </div>
</div>