

if (!sessionStorage.ID) {
   // window.location.href = "login_agpt.php"; // redirect to login page
}
// Set a timeout of 5 minutes (300000 milliseconds)
setTimeout(function () {
    // Reload the current page
    location.reload();
}, 300000);
if (!sessionStorage.actual_view) {
    sessionStorage.setItem("actual_view", "files");
} else {
    sessionStorage.actual_view = "users";
    document.getElementById("files_menu").setAttribute("class", 'selected')
}
const TIME_SESSION_EXPIRED = 10;
function security(todo, params = null) {
    if (((new Date() / 1000) - sessionStorage.last_action) < TIME_SESSION_EXPIRED * 60) {
        api_call("back.php", JSON.stringify({ functionName: 'logUser', args: [sessionStorage.user, sessionStorage.password] }), {}, analize_security_response, [todo, params]);
    }
    else {
        console.log(sessionStorage)
        sessionStorage.clear();
        sessionStorage.setItem("error", "Session expired, log again");

       // window.location.href = "login_agpt.php"; // redirect to login page


    }
}
function converPDFintoText() {
    var fileInput = document.getElementById("fileInput");
    var file = fileInput.files[0];

    var reader = new FileReader();
    var content="";
    reader.onload = function (event) {
        var buffer = event.target.result;

        // Load the PDF from the ArrayBuffer
        pdfjsLib.getDocument(buffer).promise.then(function (pdf) {
            var numPages=pdf.numPages;
            for(let i=1;i<=numPages;i++){
                pdf.getPage(i).then(function (page) {
                    // Extract the text content from the page
                    page.getTextContent().then(function (textContent) {
                        // Concatenate the text items to form the complete text
                        var text = "";
                        textContent.items.forEach(function (item) {
                            text += item.str + " ";
                        });
                        content+=text;

                        
                    });
                });
            }
            console.log(content)
            api_call('back.php', JSON.stringify({ functionName: 'saveFile', args: [content, file.name, sessionStorage.ID] }), {}, reload_page, null);
            // Get the first page of the PDF
            
        }).catch(function (error) {
            console.error("Error loading PDF: " + error);
        });
    };

    reader.readAsArrayBuffer(file);
}
function merge_files(){
    api_call('back.php', JSON.stringify({ functionName: 'mergeFiles', args: [sessionStorage.ID] }), {},reload_page,null);
}
function add_file() {

    var file = document.getElementById("fileInput");
    // let file=document.getElementById("fileInput");
    let error_message = document.getElementById("error_message");
    let files_allowed = ["application/pdf", "text/plain", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
    console.log("inserting submit")
    if (file.files.length > 0) {

        if (files_allowed.includes(file.files[0].type)) {
            switch (file.files[0].type) {
                case "application/pdf":
                    converPDFintoText()
                    break;

                case "text/plain":
                    console.log("txt")
                    var file = document.getElementById("fileInput").files[0];

                    var reader = new FileReader();

                    reader.onload = function (event) {
                        var fileContent = event.target.result;
                        api_call('back.php', JSON.stringify({ functionName: 'saveFile', args: [fileContent, file.name, sessionStorage.ID] }), {}, reload_page, null);
                        // Perform further operations with the file content
                    };

                    reader.readAsText(file);


                    break;
            }




        } else {

            error_message.innerHTML = "File not supported";
        }

    } else {
        error_message.innerHTML = "File not selected";
    }
}
function select_file() {
    document.getElementById("fileInput").click();
}
function add_file_form() {
    var newDiv = document.createElement("div");
    var wrapper = document.getElementById("user_wrapp");
    newDiv.setAttribute("id", "new_user_form");
    document.getElementById("form_file_button").setAttribute('disabled', true);
    newDiv.innerHTML = "<h3>New File Form</h3><button onCLick='select_file()' id='file'/>Select a file</button><p id='error_message'></p><form action='back.php' id='form' method='post' enctype='multipart/form-data'><input type='file' id='fileInput' name='fileInput' hidden/><input type='hidden' name='id' value='" + sessionStorage.ID + "'></form><button class='btn btn-info' onClick='security(add_file,null)'>Upload file</button>";
    wrapper.appendChild(newDiv);
}
function api_call(url, body_content, header_content, todo = null, args) {
    fetch(url, {
        headers: header_content,
        method: 'POST',
        body: body_content,
    })
        .then(response => response.json())
        .then(data => {
            if (todo) {
                todo(data, args);
            }

        }
        )
        .catch(error => console.error(error));
}

function analize_security_response(data, ar) {
    if (data.ID) {
        sessionStorage.last_action = new Date() / 1000;
        if (ar[0]) {
            ar[0](ar[1])
        }

    } else if (data.not_found) {
        window.location.href = "login_agpt.php"; // redirect to login page
        sessionStorage.clear();
    } else if (data.internal_error) {
        sessionStorage.clear();
    }
}
function delete_file(file) {
    api_call('back.php', JSON.stringify({ functionName: 'deleteFile', args: [file, sessionStorage.ID] }), {}, reload_page, null);
}
function reload_page() {
    console.log("recargando");
    //window.location.href = "addfiles.php";
}
function show_files(data) {
    var wrapper = document.getElementById("user_wrapp");
    var newTable = document.createElement("ul");
    newTable.id = "list_of_users";
    if(data.length>1){
        document.getElementById("merge_files_button").removeAttribute("disabled");
    }
    data.forEach(element => {
        var newRow = document.createElement("li");
        newRow.setAttribute("class", "user");

        var link = document.createElement("div");
        var name = document.createTextNode(element);
        var newColumn2 = document.createElement("div");
        newColumn2.setAttribute("id", "name_user_" + element.ID);
        var id = document.createTextNode(element.ID);
        link.appendChild(name);



        newColumn2.appendChild(link);
        newRow.appendChild(newColumn2);
        newTable.appendChild(newRow);
        if (sessionStorage.rol == "admin") {

            let actionButton = "<div id='action_button_" + element.ID + "'><button class='button_link' onClick=\"security(delete_file,['" + element + "'])\">Delete</button></div>";
            newRow.innerHTML += actionButton;
        }
        wrapper.appendChild(newTable);
    });
}
function load_files() {
    api_call('back.php', JSON.stringify({ functionName: 'getFiles', args: [sessionStorage.ID] }), {}, show_files);
}