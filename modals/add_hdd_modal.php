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
      <button type="submit" class="modal-btn">保存</button>
    </form>
  </div>
</div>