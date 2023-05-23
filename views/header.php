<?php
session_start();
$PATH="https://advancedthinkingmethods.com/autogpt";
?>
<script>
  sessionStorage.setItem("user","<?php echo $_SESSION["user"];
  ?>");
   sessionStorage.setItem("password","<?php echo $_SESSION["password"];
  ?>");
   sessionStorage.setItem("ID","<?php echo $_SESSION["ID"];
  ?>");
   sessionStorage.setItem("rol","<?php echo $_SESSION["rol"];
  ?>");
     sessionStorage.setItem("last_action","<?php echo time();
  ?>");
 


</script>
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