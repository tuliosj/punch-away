function dismiss_alerts() {
  const alert = document.getElementsByClassName("alert");
  setTimeout(function () {
    for (let i = 0; i < alert.length; i++) {
      alert.item(i).style.visibility = "hidden";
      alert.item(i).style.height = "0";
      alert.item(i).style.width = "0";
      alert.item(i).style.padding = "0";
      alert.item(i).innerHTML = "";
    }
  }, 3000);
}

window.onload = dismiss_alerts();

function edit(date) {
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("modal__editor").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("GET", "_edit-date.php?q=" + date);
  xmlhttp.send();
}
