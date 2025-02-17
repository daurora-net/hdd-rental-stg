<div id="editUserModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('editUserModal').style.display='none'">
      <i class="fa-solid fa-xmark"></i>
    </span>
    <form method="post" class="form" action="actions/edit_user.php">
      <h3>USER編集</h3>
      <input type="hidden" id="editUserId" name="userId">
      <div class="form-content">
        <label for="editUsername" class="required">Username</label>
        <input type="text" id="editUsername" name="username" required>
      </div>
      <button type="submit" class="modal-btn">保存</button>
    </form>
  </div>
</div>
