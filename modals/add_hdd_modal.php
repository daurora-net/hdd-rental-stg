<div id="addHddModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addHddModal').style.display='none'">&times;</span>
        <form method="post" action="actions/add_hdd.php">
            <label for="hddName">HDD名称:</label>
            <input type="text" id="hddName" name="hddName" required>
            <button type="submit">追加</button>
        </form>
    </div>
</div>