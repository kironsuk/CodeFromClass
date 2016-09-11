<?php
session_start();
$_SESSION['test']='hi';
echo $_SESSION["test"];
?>

<!DOCTYPE html>
<head>
  <meta charset="utf-8"/>
  <title>Calendar</title>

  <link rel="stylesheet" type="text/css" href="style.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js" type="text/javascript"></script>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script src= "http://classes.engineering.wustl.edu/cse330/content/calendar.min.js" type ='text/javascript'></script>

</head>
<body>

  <div id="create_or_login_wrapper">
    <button type="button" id="login_user">Login</button>
    <button type="button" id="create_user">Create User</button>
    <div id="create_or_login_box">
      <input type="text" id="username"></input>
      <input type="text" id="password"></input>
    </div>
  </div>

  <div id="currentMonth"></div>
  <button type="button" id ="backward">Previous Month</button>
  <button type="button" id ="forward">Next Month</button>
  <div id='tblhere'></div>

  <div id='edit_event'>
    <input type="text" id="dp"></input> <br>
    Where: <input type="text" id="where" value=""></input> <br>
    When: <input type="text" id="when" value=""></input> <br>
    <input type="textarea" rows="4" cols="50" id="description" value=""></input> <br>
    <button type="button" id="close">Close</button>
    <button type="button" id="save_event">Save</button>
    <button type="button" id="delete_event">Delete</button>
  </div>



  <script type="text/javascript">

  var days_labels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  var month_labels = ['January','February','March','April', 'May', 'June','July','August','September','October','November','Decemeber']
  var currentDay = new Date();
  var currentMonth = new Month(currentDay.getFullYear(), currentDay.getMonth());
  //var test = '<%= Session["test"] %>';
  var test = '@Session["test"]';
  alert(test);


  function tableCreate(){

    var body = document.body,
    tbl  = document.createElement('table');
    tbl.id = 'bigTable';

    var currentDay = new Date();
    document.getElementById("currentMonth").innerHTML = month_labels[currentMonth.month]+" "+currentMonth.year;

    var weeks = currentMonth.getWeeks();

    //day names top row
    var toptr = tbl.insertRow()
    toptr.id = 'weekdays';
    for (var day in days_labels){
      var td = toptr.insertCell();
      td.appendChild(document.createTextNode(days_labels[day]));
    }

    //updating each cell
    for(var w in weeks){
      var tr = tbl.insertRow();
      var days = weeks[w].getDates();
      for(var d in days){
        makeCell(tr,days[d]);
      }
    }


    $('#tblhere').append(tbl);

    highlightCurrent();


    loadData(parseInt(currentMonth.month) + 1, currentMonth.year);
  }



  function makeCell(tr,day){

    var td = tr.insertCell();
    td.className = 'cell';
    td.setAttribute('id',day.getDate()+' '+day.getMonth());


    var td_day = document.createElement('div');
    td_day.className = 'day_num';
    $(td_day).append(day.getDate());
    td.appendChild(td_day);
    if (day.getMonth()!= currentMonth.month){
      td_day.id = 'wrongMonth';
    }



    var td_div = document.createElement('div');
    td_div.className = 'box';
    td.appendChild(td_div);
    //$(td_div).append('row one <br>');
    //$(td_div).append('row two <br>');
    //$(td_div).append('row three <br>');



    //td.addEventListener("click", function(){alert(day);}, false);
  }

  function highlightCurrent(){
    //alert('hi');
    var x = currentMonth.getDateObject(currentDay.getDate());
    if (currentDay.getMonth()== x.getMonth() && currentDay.getFullYear() == x.getFullYear()){
        var cel = document.getElementById(currentDay.getDate()+' '+currentDay.getMonth());
        cel.style.border = '3px solid green';
    }
  }

  function updateCalendar (whichWay){
    if (whichWay==1){
      $('#bigTable').remove();
      currentMonth = currentMonth.nextMonth();
      tableCreate();
    }
    else if (whichWay=-1){
      $('#bigTable').remove();
      currentMonth = currentMonth.prevMonth();
      tableCreate();
    }
  }





  function loadData(this_month, this_year){
    //Go to the create event page
    $.post("loadEventsForMonth.php", {username: "test", month: this_month, year: this_year}).
      done(
        function(rawdata){
          data = JSON.parse(rawdata);

          for (i in data){
            addEvents(data[i]);
          }
        });
  }

  function addEvents(data){
      var cell = document.getElementById(data.day+' '+(data.month));
      cell.style.border = '3px solid pink';
      //$(cell).children().eq(1).append(data[i].hour+data[i].description+'<br>');
      var box = $(cell).children().eq(1);

      var aEvent = document.createElement("div");
      $(aEvent).addClass("event");
      aEvent.innerHTML = data.description+'<br>';
      $(aEvent).click(function(){onEventClick(event, data);});
      $(box).append(aEvent);
  }

  function onEventClick(e, eventData){
    $current_data = eventData;
    //Set event data
    $('#edit_event').attr('eventID',eventData.eventID);

    var children = $('#edit_event').children();
    //children[0].innerHTML = month_labels[eventData.month-1]+" "+eventData.day;
    var d = new Date(eventData.year, eventData.month, eventData.day);
    //$('#datepicker').datepicker({ defaultDate: new Date(eventData.year, eventData.month, eventData.day)});
    $('#dp').datepicker({defaultDate: d});
    $('#dp').attr('value',(d.getMonth() + 1) + '/' + d.getDate() + '/' +  d.getFullYear());
    if(eventData.loc){
      children[2].setAttribute('value', eventData.loc);
    }
    if(eventData.hour != 0 || eventData.minute != 0){
      children[4].setAttribute('value',eventData.hour+":"+eventData.minute);
    }
    children[6].setAttribute('value',eventData.description);
    //Check to see if the div would go outside the screen width
    var right_edge = $('#edit_event').width() + e.pageX;
    var screen_width = $(window).width();
    if (right_edge >= screen_width){
      $('#edit_event').slideToggle().css('top', e.pageY).css('left',e.pageX-$('#edit_event').width());
    }
    else {
      $('#edit_event').slideToggle().css('top', e.pageY).css('left',e.pageX);
    }
  }

  function editEvent(){
    $('#bigTable').remove();
    var this_event = $('#edit_event').attr('eventID');
    var this_date = $('#dp').val();
    date_array = this_date.split("/");
    if (date_array[0] < 10){
      this_date = "0"+this_date;
    }
    alert(this_date);
    var this_location = $('#where').val();
    var this_time = $('#when').val();
    if(this_time == ""){
      this_time = "00:00:00"
    } else {
      this_time += ":00";
    }
    var this_des = $('#description').val();
    $.post("updateEvent.php", {eventID: this_event, date: this_date, location: this_location, time: this_time, description: this_des}).
    done(function(data){
      tableCreate();
    });
    $('#edit_event').slideToggle();
  }

  function deleteEvent(){
    $('#bigTable').remove();
    var this_event =  $('#edit_event').attr('eventID');
    alert("Deleting "+this_event);
    $.post("deleteEvent.php", {eventID: this_event}).
      done(function(){
        tableCreate();
      });
    $('#edit_event').slideToggle();
  }

  function create_or_login_user(type){

  }

  tableCreate();

  //Creat or Login User
  document.getElementById("forward").addEventListener("click", function(){
    updateCalendar(1)},false);

  //Change month
  document.getElementById("forward").addEventListener("click", function(){
    updateCalendar(1)},false);
  document.getElementById("backward").addEventListener("click", function(){
    updateCalendar(-1)},false);

  //Close, Save, or Delete Events
  document.getElementById("close").addEventListener("click",function(){
    $('#edit_event').slideToggle()},false);
  document.getElementById("save_event").addEventListener("click",editEvent, false);
  document.getElementById("delete_event").addEventListener("click",deleteEvent, false);

</script>


</body>
</html>
