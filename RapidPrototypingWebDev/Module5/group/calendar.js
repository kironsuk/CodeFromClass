
    var days_labels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var month_labels = ['January','February','March','April', 'May', 'June','July','August','September','October','November','Decemeber'];
    var currentDay = new Date();
    var currentMonth = new Month(currentDay.getFullYear(), currentDay.getMonth());
    var username;



    function tableCreate(){
      $('#bigTable').remove();
      var body = document.body;
      tbl  = document.createElement('table');
      tbl.id = 'bigTable';

      var currentDay = new Date();
      document.getElementById("currentMonth").innerHTML = month_labels[currentMonth.month]+" "+currentMonth.year;

      var weeks = currentMonth.getWeeks();

      //day names top row
      var toptr = tbl.insertRow();
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


      if (username){
        loadUserData(username);
      }
      //create_or_login_user('l');
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
      $(td_div).click(function(){onBoxClick(event, day.getDate());});
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
      else if (whichWay==-1){
        $('#bigTable').remove();
        currentMonth = currentMonth.prevMonth();
        tableCreate();
      }
    }

    function checkButton(radio_name){
      var operation_radio_pointers = document.getElementsByName(radio_name);
      var which_operation = null;
      for (var i = 0; i < operation_radio_pointers.length; i++){
        if (operation_radio_pointers[i].checked){
          which_operation = operation_radio_pointers[i].value;
        }
      }
      return(which_operation);
    }

    function loadUserData(user){
      //Go to the create event page
      var this_month = parseInt(currentMonth.month) + 1;
      var this_year = currentMonth.year;
      var token = $("#token").val();
      var which_button = checkButton('operation');
      switch(which_button){
        case "a":
          this_type = "\%";
          break;
        case "w":
          this_type = "w";
          break;
        case "s":
          this_type = "s";
          break;
      }
      $.post("loadEventsForMonth.php", {username: user, month: this_month, year: this_year, type: this_type, token: token}).
        done(
          function(rawdata){
            if (!rawdata){
              return;
            }
            var data = JSON.parse(rawdata);

            for (var i in data){
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

    function onBoxClick(e, date){
      $('#create_event').attr('date',date);
      $('#create_event').find('input:text').val('');
      var right_edge = $('#create_event').width() + e.pageX;
      var screen_width = $(window).width();
      if (right_edge >= screen_width){
        $('#create_event').slideToggle().css('top', e.pageY).css('left',e.pageX-$('#create_event').width());
      }
      else {
        $('#create_event').slideToggle().css('top', e.pageY).css('left',e.pageX);
      }
    }

    function onEventClick(e, eventData){
      var google_location = eventData.location.replace(/ /g,"+");
      $('#google_maps').click(function() {
        window.open('http://www.google.com/maps/place/'+google_location,'_blank');
      });
      e.stopPropagation();
      var current_data = eventData;
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
      if(eventData.hour !== 0 || eventData.minute !== 0){
        children[4].setAttribute('value',eventData.hour+":"+eventData.minute);
      }
      if(eventData.type == "w"){
        $('#edit_work').attr('checked','checked');
      } else {
        $('#edit_social').attr('checked','checked');
      }
      $('#description').val(eventData.description);
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

    function createEvent(){
      $('#bigTable').remove();
      var this_datetime = currentMonth.getDateObject($('#create_event').attr('date'));
      var this_date = this_datetime.getFullYear() + "/" + (parseInt(this_datetime.getMonth())+1) + "/" + this_datetime.getDate();
      var this_time = $('#create_when').val();
      if (this_time === ""){
        this_time = "00:00:00";
      } else {
        this_time = this_time + ":00";
      }
      this_datetime = this_date + " " + this_time;
      var this_loc = $('#create_where').val();
      var this_des = $('#create_description').val();
      var this_type = checkButton("create_type");
      var token = $('#token').val();
      $.post("createEvent.php", {datetime: this_datetime, description: this_des, location: this_loc, username: username, type: this_type, token: token}).done(function(data){
        tableCreate();
        if(data){alert(data);}

      });
      $('#create_event').slideToggle();
    }

    function createEventforUser(){
      $('#bigTable').remove();
      var this_user = $('#send_event').val();
      var this_event = $('#edit_event').attr('eventID');
      var date_obj = $('#dp').datepicker('getDate');
      var this_date = date_obj.getFullYear() + "/" + (parseInt(date_obj.getMonth())+1) + "/" + date_obj.getDate();
      var this_time = $('#when').val();
      if (this_time === ""){
        this_time = "00:00:00";
      } else {
        this_time = this_time + ":00";
      }
      this_datetime = this_date + " " + this_time;
      var this_loc = $('#where').val();
      var this_des = $('#description').val();
      var this_type = checkButton("edit_type");
      var token = $('#token').val();
      $.post("updateEvent.php", {eventID: this_event, clone_user: this_user, datetime: this_datetime, description: this_des, location: this_loc, username: username, type: this_type, token: token}).done(function(data){
        tableCreate();
        if(data){alert(data);}
      });
      $('#edit_event').slideToggle();

    }

    function editEvent(){
      $('#bigTable').remove();
      var this_event = $('#edit_event').attr('eventID');
      var date_obj = $('#dp').datepicker('getDate');
      var this_date = date_obj.getFullYear() + "/" + (parseInt(date_obj.getMonth())+1) + "/" + date_obj.getDate();
      //alert("This date is " +this_date);
      var this_location = $('#where').val();
      var this_time = $('#when').val();
      if(this_time === ""){
        this_time = "00:00:00";
      } else {
        this_time += ":00";
      }
      this_datetime = this_date + " " + this_time;
      var this_des = $('#description').val();
      var this_type = checkButton("edit_type");
      var token = $('#token').val();
      $.post("updateEvent.php", {eventID: this_event, datetime: this_datetime, location: this_location, description: this_des, type: this_type, username: username, token: token}).
      done(function(data){
        //alert("return data is :"+data);
        tableCreate();
        if(data){alert(data);}
      });
      $('#edit_event').slideToggle();
    }

    function deleteEvent(){
      $('#bigTable').remove();
      var this_event =  $('#edit_event').attr('eventID');
      var token = $('#token').val();
      $.post("deleteEvent.php", {eventID: this_event, username: username, token: token}).
        done(function(){
          tableCreate();
          alert("Event deleted.");
        });
      $('#edit_event').slideToggle();
    }


    function create_or_login_user(type){
        //get user provided credentials
        var user = $("#username").val();
        var pass = $("#password").val();


        if (type=='l'){
          $.post("validate.php", {username: user, password: pass, login: type}).
          done(function(data){
            user_logged_in(data);
            });
        }else if (type=='c'){
          $.post("validate.php", {username: user, password: pass, create: type}).
          done(function(data){
            user_logged_in(data);
            });
        }
    }

    function user_logged_in(data){
      if (!data){
        alert("login failed");
        return;
      }
      var d = JSON.parse(data);
      username = d.username;
      $('#bigTable').remove();
      $('#token').val(d.token);
      tableCreate();
      $('#create_or_login_box').hide();
      $('#logged_in').show();
      $('#usernameHere').text("Hi "+username+"!");
      $('#calendar_type').show();
    }

    function logout(){
      username=null;
      $('#token').val('');
      $('#bigTable').remove();
      tableCreate();
      $('#create_or_login_box').show();
      $('#logged_in').hide();
      $('#calendar_type').hide();

    }



    //Create or Login User

    document.getElementById("login_user").addEventListener("click", function(){
      create_or_login_user('l');
    },false);
    document.getElementById("create_user").addEventListener("click", function(){
      create_or_login_user('c');
    },false);

    document.getElementById("logout").addEventListener("click", function(){
      logout();
    },false);




    //make table
    tableCreate();



    //Change month
    document.getElementById("forward").addEventListener("click", function(){
      updateCalendar(1);},false);
    document.getElementById("backward").addEventListener("click", function(){
      updateCalendar(-1);},false);

    //Close, Save, or Delete Events
    document.getElementById("close").addEventListener("click",function(){
      $('#edit_event').slideToggle();},false);
    document.getElementById("save_event").addEventListener("click",editEvent, false);
    document.getElementById("delete_event").addEventListener("click",deleteEvent, false);

    //Create or close events
    document.getElementById("create_close").addEventListener("click",function(){
      $('#create_event').slideToggle();},false);
    document.getElementById("create_save").addEventListener("click",createEvent,false);

    //Clone events
    document.getElementById("send_event_button").addEventListener("click",createEventforUser, false);

    //Refresh table
    document.getElementById("refresh_button").addEventListener("click",tableCreate, false);

    var operation_radio_pointers = document.getElementsByName("operation");
    for (var i = 0; i < operation_radio_pointers.length; i++){
      operation_radio_pointers[i].addEventListener("change", tableCreate, false);
    }
