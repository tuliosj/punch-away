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

function month_total() {
  const day_hours = document.getElementsByClassName("table__day_hours");
  var hours = 0,
    minutes = 0,
    seconds = 0;
  for (let i = 0; i < day_hours.length; i++) {
    time = day_hours.item(i).innerHTML.split(":");
    hours += parseInt(time[0]);
    minutes += parseInt(time[1]);
    seconds += parseInt(time[2]);
  }
  document.getElementById("table__total").innerHTML =
    ("0" + hours).slice(-2) +
    ":" +
    ("0" + minutes).slice(-2) +
    ":" +
    ("0" + seconds).slice(-2);
}

window.addEventListener("load", function () {
  dismiss_alerts();
  month_total();
});

function edit(date) {
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("modal__editor").innerHTML = this.responseText;
    }
  };
  const urlParams = new URLSearchParams(window.location.search);
  xmlhttp.open(
    "GET",
    "_edit-date.php?q=" +
      date +
      (urlParams.get("month") ? "&month=" + urlParams.get("month") : "")
  );
  xmlhttp.send();
}

function fill_now(attribute) {
  const d = new Date();
  const _24hclock = document.getElementById("_24hclock").value;
  if (_24hclock == "0") {
    document.getElementById(attribute + "_hours").value =
      d.getHours() % 12 ||
      12 + 12 * parseInt(document.getElementById(attribute + "_ampm"));
  } else {
    document.getElementById(attribute + "_hours").value = d.getHours();
  }
  document.getElementById(attribute + "_minutes").value = d.getMinutes();
  document.getElementById(attribute + "_seconds").value = d.getSeconds();
}

function get_seconds(hours, minutes, seconds, pm, _24hclock) {
  if (hours == 12 && _24hclock == 0) {
    hours = 0;
  }
  return (
    (pm * 12 + parseInt(hours)) * 3600 +
    parseInt(minutes) * 60 +
    parseInt(seconds)
  );
}

function fill_next(i) {
  const attributes = ["start", "lunch_start", "lunch_end", "end"];
  const _24hclock = document.getElementById("_24hclock").value;
  for (let j = 1; j < 4 - i; j++) {
    var hours = document.getElementById(attributes[i + j - 1] + "_hours").value;
    var minutes = document.getElementById(attributes[i + j - 1] + "_minutes")
      .value;
    var seconds = document.getElementById(attributes[i + j - 1] + "_seconds")
      .value;
    var next_hours = document.getElementById(attributes[i + j] + "_hours");
    var next_minutes = document.getElementById(attributes[i + j] + "_minutes");
    var next_seconds = document.getElementById(attributes[i + j] + "_seconds");
    if (_24hclock == "0") {
      var ampm = document.getElementById(attributes[i + j - 1] + "_ampm").value;

      var next_ampm = document.getElementById(attributes[i + j] + "_ampm");
      if (
        get_seconds(hours, minutes, seconds, ampm, 0) >
        get_seconds(
          next_hours.value,
          next_minutes.value,
          next_seconds.value,
          next_ampm.value,
          0
        )
      ) {
        next_hours.value = hours;
        next_minutes.value = minutes;
        next_seconds.value = seconds;
        next_ampm.value = ampm;
      }
    } else {
      if (
        get_seconds(hours, minutes, seconds, 0, 1) >
        get_seconds(
          next_hours.value,
          next_minutes.value,
          next_seconds.value,
          0,
          1
        )
      ) {
        next_hours.value = hours;
        next_minutes.value = minutes;
        next_seconds.value = seconds;
      }
    }
  }
}
