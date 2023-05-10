<div class="menu">
        <ul>
            <li><a href="clients.php" id="clients_menu" onClick="change_view('client')">CLIENTS</a></li>
            <li><a href="files.php" id="files_menu" onClick="change_view('files')">ADD FILES</a></li>
            <li><a href="autogpt.php" id="autogpt_menu" onClick="change_view('autogpt') disabled">AUTOGPT</a></li>
        </ul>
</div>
<script>
function change_view(view){
    localStorage.actual_view=view;
    document.getElementById("clients_menu").addClass("selected")
}
</script>