<?php
$PATH="";?>
<div class="menu">
        <ul>
        <?php
        if($view=="clients"){
          echo "<li><a href='clients.php' id='clients_menu' class='selected' onClick=\"change_view('clients_menu')\">CLIENTS</a></li>";
        }else{
          echo "<li><a href='clients.php' id='clients_menu' onClick=\"change_view('clients_menu')\">CLIENTS</a></li>";

        }
        if($view=="files"){
          echo "<li><a href='addfiles.php' id='files_menu' onClick=\"change_view('files_menu')\">ADD FILES</a></li>";
        }else{
          echo "<li><a href='addfiles.php' id='files_menu' onClick=\"change_view('files_menu')\">ADD FILES</a></li>";
        }
        if($view=="gpt"){
          echo "<li><a href='autogpt.php' id='autogpt_menu' onClick=\"change_view('autogpts_menu')\">AUTOGPT</a></li>";
        }else{
          echo "<li><a href='autogpt.php' id='autogpt_menu' onClick=\"change_view('autogpts_menu')\">AUTOGPT</a></li>";

        }
      
         echo "<li id='log_out'><button class='iteration_button shadow' onclick='log_out()'>LOG OUT</button></li>";

       
        








?>

        </ul>
</div>
<script>
  let old_menu="clients_menu";
function change_view(view){
    localStorage.actual_view=view;
    document.getElementById(old_menu).remove("selected")
    document.getElementById(view).addClass("selected")
    old_menu=view;
}
function log_out(event) {
  sessionStorage.clear(); // clear session storage
  window.location.href = "login_agpt.php"; // redirect to login page
}

</script>