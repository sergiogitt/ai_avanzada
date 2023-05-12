if (!sessionStorage.ID) {
    window.location.href = "login_agpt.php"; // redirect to login page
}
// Set a timeout of 5 minutes (300000 milliseconds)
setTimeout(function () {
    // Reload the current page
    location.reload();
}, 300000);

const TIME_SESSION_EXPIRED = 10;
let editing_user = [];
let name_editing_user;
let wrapper = document.getElementById("user_wrapp");
if (!sessionStorage.actual_view) {
    sessionStorage.setItem("actual_view", "users");
    console.log(sessionStorage)
} else {
    sessionStorage.actual_view = "users";
    console.log(document.getElementById("clients_menu"))
    document.getElementById("clients_menu").setAttribute("class", 'selected')
}
function load_users() {
    $.ajax({
        type: "POST",
        url: "back.php",
        data: { getUsers: true },
        success: function (res) {
            let answer = JSON.parse(res);
            if (res.not_users != "Users are empty") {
                var newTable = document.createElement("ul");
                newTable.id = "list_of_users";


                answer.forEach(element => {
                    var newRow = document.createElement("li");
                    newRow.setAttribute("class", "user");

                    var link = document.createElement("div");
                    var name = document.createTextNode(element.display_name);
                    var newColumn2 = document.createElement("div");
                    newColumn2.setAttribute("id", "name_user_" + element.ID);
                    var id = document.createTextNode(element.ID);
                    link.appendChild(name);



                    newColumn2.appendChild(link);
                    newRow.appendChild(newColumn2);
                    newTable.appendChild(newRow);
                    if (sessionStorage.rol == "admin") {
                        link.setAttribute("onclick", `security(create_session,['${element.ID}'])`);
                        let actionButton = "<div id='action_button_" + element.ID + "'><button class='button_link' onClick=\"security(edit_user,['" + element.ID + "','" + element.display_name + "'])\">Edit</button><button class='button_link' onClick=\"security(delete_user,['" + element.ID + "','" + element.display_name + "'])\">Delete</button></div>";
                        newRow.innerHTML += actionButton;
                    }
                    wrapper.appendChild(newTable);
                });

            }

        }
    });
}
function security(todo, params = null) {
    console.log(sessionStorage.last_action)
    if (((new Date() / 1000) - sessionStorage.last_action) < TIME_SESSION_EXPIRED * 60) {
        api_call("back.php", JSON.stringify({ functionName: 'logUser', args: [sessionStorage.user, sessionStorage.password] }), {}, analize_security_response, [todo, params]);

    }
    else {
        sessionStorage.clear();
        sessionStorage.setItem("error", "Session expired, log again");
        window.location.href = "login_agpt.php"; // redirect to login page

        console.log("no cumple tmepo")

    }

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
function create_session(id) {
    sessionStorage.setItem("auto_gpt_view_user", id);
    console.log(sessionStorage)
    window.location.href = 'autogpt.php';
}
function confirm_editing(user_id) {
    console.log(user_id[0] + name)
    let new_name_input = document.getElementById("new_name").value;
    $.ajax({
        type: "POST",
        url: "back.php",
        data: { edit_user: user_id[0], new_name: new_name_input },
        success: function (res) {
            location.reload();

        }
    });
}
function confirm_delete(user_id) {
    $.ajax({
        type: "POST",
        url: "back.php",
        data: { delete_user: user_id[0] },
        success: function (res) {
            location.reload();
        }
    });
}
function cancel_editing(ar) {
    document.getElementById("action_button_" + ar[0]).innerHTML = "<button class='button_link' onClick=\"security(edit_user,['" + ar[0] + "','" + ar[1] + "'])\">Edit</button><button class='button_link' onClick=\"security(delete_user,['" + ar[0] + "','" + ar[1] + "'])\">Delete</button>";
    document.getElementById("name_user_" + ar[0]).innerHTML = `<div onclick="security(create_session, [${+ar[0]}])">${ar[1]}</div>`;
}
function cancel_delete(ar) {
    console.log("cancelando " + ar[0] + ar[1])
    document.getElementById("action_button_" + ar[0]).innerHTML = "<button class='button_link' onClick=\"security(edit_user,['" + ar[0] + "','" + ar[1] + "'])\">Edit</button><button class='button_link' onClick=\"security(delete_user,['" + ar[0] + "','" + ar[1] + "'])\">Delete</button>";
    document.getElementById("name_user_" + ar[0]).innerHTML = `<div onclick="security(create_session, [${+ar[0]}])">${ar[1]}</div>`;
}
function delete_user(ar) {
    console.log(name_editing_user);
    if (editing_user.length > 0) {
        document.getElementById("name_user_" + +editing_user[0]).innerHTML = `<div onclick="security(create_session, [${+editing_user[0]}])">${name_editing_user}</div>`;
        document.getElementById("action_button_" + editing_user[0]).innerHTML = "<button  class='button_link' onClick=\"security(edit_user,['" + editing_user[0] + "','" + name_editing_user + "'])\">Edit</button><button  class='button_link' onClick=\"security(delete_user,['" + editing_user[0] + "','" + name_editing_user + "'])\">Delete</button>";
        editing_user = [];
        name_editing_user = "";
    }
    document.getElementById("action_button_" + ar[0]).innerHTML = "<button class='button_link' onClick=\"security(cancel_delete,['" + ar[0] + "','" + ar[1] + "'])\">Cancel</button><button class='button_link' onClick=\"security(confirm_delete,['" + ar[0] + "'])\">Confirm</button>";
    editing_user.push(ar[0]);
    name_editing_user = ar[1];

}
function edit_user(ar) {
    if (editing_user.length > 0) {
        console.log("antiguo cambio" + name_editing_user)
        document.getElementById("name_user_" + +editing_user[0]).innerHTML = `<div onclick="security(create_session, [${+editing_user[0]}])">${name_editing_user}</div>`;
        document.getElementById("action_button_" + editing_user[0]).innerHTML = "<button  class='button_link' onClick=\"security(edit_user,['" + editing_user[0] + "','" + name_editing_user + "'])\">Edit</button><button  class='button_link' onClick=\"security(delete_user,['" + editing_user[0] + "','" + name_editing_user + "'])\">Delete</button>";

        editing_user = [];
        name_editing_user = "";
    }

    document.getElementById("action_button_" + ar[0]).innerHTML = "<button class='button_link' onClick=\"security(cancel_editing, ['" + ar[0] + "','" + ar[1] + "'])\">Cancel</button><button class='button_link' onClick=\"security(confirm_editing, ['" + ar[0] + "','" + ar[1] + "'])\">Confirm</button>";
    editing_user.push(ar[0]);
    name_editing_user = ar[1];
    document.getElementById("name_user_" + ar[0]).innerHTML = "<input type='text' id='new_name' value=" + ar[1] + ">";
}
function create_user_form() {
    var newDiv = document.createElement("div");
    newDiv.setAttribute("id", "new_user_form");
    document.getElementById("new_user_button").setAttribute('disabled', true);
    newDiv.innerHTML = "<h3>New user Form</h3><p><label for='name'>Name:</label><input type='text' id='name'/></p><button class='btn btn-info' onClick='security(add_user,null)'>Create user</button>";
    wrapper.appendChild(newDiv);
}
function add_user() {
    if (document.getElementById("name").value != "") {
        let name_user = document.getElementById("name").value;
        $.ajax({
            type: "POST",
            url: "back.php",
            data: { new_user: name_user },
            success: function (res) {
                document.getElementById("new_user_form").remove();
                var newMessage = document.createElement("p");
                newMessage.setAttribute("id", "message");
                var text = document.createTextNode("User inserted");
                newMessage.appendChild(text);
                wrapper.appendChild(newMessage)
                document.getElementById("new_user_button").removeAttribute('disabled');
                location.reload();
            }
        });
    } else {

    }
}