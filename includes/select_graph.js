function sendReq() {
    var valore = document.getElementById("graphs1").value;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../includes/select_graph.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log("Graph1 Response:", xhr.responseText);
            location.reload();
        } else {
            console.error("Graph1 Error:", xhr.status);
        }
    };
    xhr.send("graphs1=" + encodeURIComponent(valore));
}