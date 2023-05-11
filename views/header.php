<div class="menu">
        <ul>
            <li><a href="clients.php" id="clients_menu" onClick="change_view('client')">CLIENTS</a></li>
            <li><a href="files.php" id="files_menu" onClick="change_view('files')">ADD FILES</a></li>
            <li><a href="autogpt.php" id="autogpt_menu" onClick="change_view('autogpt') ">AUTOGPT</a></li>
            <li id="log_out"><button  class="iteration_button shadow" onclick="log_out()">LOG OUT</button></li>
        </ul>
</div>
<script>
function change_view(view){
    localStorage.actual_view=view;
    document.getElementById("clients_menu").addClass("selected")
}
function log_out(event) {
  sessionStorage.clear(); // clear session storage
  window.location.href = "login_agpt.php"; // redirect to login page
}

</script>