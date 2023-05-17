<?php
$PATH="https://advancedthinkingmethods.com/autogpt";
?>

<div class="menu">

        <ul>

          <?php
           echo "<li><a href='clients.php' id='clients_menu' onClick=\"change_view('clients_menu')\">CLIENTS</a></li>";

           echo "<li><a href='addfiles.php' id='files_menu' onClick=\"change_view('files_menu')\">ADD FILES</a></li>";
           
           echo "<li><a href='autogpt.php' id='autogpt_menu' onClick=\"change_view('autogpts_menu')\">AUTOGPT</a></li>";
           
           echo "<li id='log_out'><button class='iteration_button shadow' onclick='log_out()'>LOG OUT</button></li>";

          

          ?>

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