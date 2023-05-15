
if (!sessionStorage.ID) {
    window.location.href = "login_agpt.php"; // redirect to login page
}
// Set a timeout of 5 minutes (300000 milliseconds)
setTimeout(function () {
    // Reload the current page
    location.reload();
}, 300000);
function security(todo,params=null){  
    console.log(sessionStorage.last_action)
    if(((new Date()/1000)-sessionStorage.last_action)<TIME_SESSION_EXPIRED*60){
        api_call("back.php",JSON.stringify({functionName: 'logUser', args: [sessionStorage.user,sessionStorage.password]}),{},analize_security_response,[todo,params]);
    }
    else
    {
        sessionStorage.clear();
        sessionStorage.setItem("error","Session expired, log again");
        window.location.href = "login_agpt.php"; // redirect to login page

        console.log("no cumple tmepo")
        
    }
}
function add_file(){
    let file=document.getElementById(file_id);
    let error_message=document.getElementById("error_message");
    let files_allowed=["application/pdf","text/plain","application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
    
    if(file.files.length>0){
        if(files_allowed.includes(file.files[0].type)){
            console.log("inserting file")
            /*const formData = new FormData();
            formData.append('file', file);
            api_call("back.php", JSON.stringify({functionName: 'save_file', args: [formData]}))*/
            const formData = new FormData();
            formData.append('file', file.files[0]);
            formData.append("name", "file");
            api_call('back.php', JSON.stringify({functionName: 'save_file', args: [formData]}), {});

        }else{
            error_message.innerHTML="File not supported";
        }
        
    }
}
function add_file_form() {
    var newDiv = document.createElement("div");
    newDiv.setAttribute("id", "new_file_form");
    document.getElementById("new_file_button").setAttribute('disabled', true);
    newDiv.innerHTML = "<h3>New File Form</h3><input type='file' id='new_file'/><button class='btn btn-info' onClick='security(add_file,null)'>Upload file</button>";
    wrapper.appendChild(newDiv);
}
function load_files(){
    console.log("cargando archivos")
}