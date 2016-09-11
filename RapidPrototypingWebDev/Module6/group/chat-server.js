

// Require the packages we will use:
var http = require("http"),
        url = require('url'),
        path = require('path'),
        mime = require('mime'),
        socketio = require("socket.io"),
        fs = require("fs");

// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html:
var app = http.createServer(function(req, resp){
        // This callback runs when a new connection is made to our HTTP server.
        var filePath = '.' + req.url;
        if (filePath == './'){
          fs.readFile("client.html", function(err, data){
                  // This callback runs when the client.html file has been read from the filesystem.

                  if(err) return resp.writeHead(500);
                  resp.writeHead(200);
                  resp.end(data);
          });
        } else {
        var filename = path.join(__dirname, "static", url.parse(req.url).pathname);
      	(fs.exists || path.exists)(filename, function(exists){
      		if (exists) {
      			fs.readFile(filename, function(err, data){
      				if (err) {
      					// File exists but is not readable (permissions issue?)
      					resp.writeHead(500, {
      						"Content-Type": "text/plain"
      					});
      					resp.write("Internal server error: could not read file");
      					resp.end();
      					return;
      				}

      				// File exists and is readable
      				var mimetype = mime.lookup(filename);
      				resp.writeHead(200, {
      					"Content-Type": mimetype
      				});
      				resp.write(data);
      				resp.end();
      				return;
      			});
      		}else{
      			// File does not exist
      			resp.writeHead(404, {
      				"Content-Type": "text/plain"
      			});
      			resp.write("Requested file not found: "+filename);
      			resp.end();
      			return;
      		}
      	});
      }

});
app.listen(8080);

// Do the Socket.IO magic:
var io = socketio.listen(app);
var users = ['server'];
var rooms = {};
rooms['lobby'] = {has_password: false, password: null, admin: [], bannedUsers: []};



io.sockets.on("connection", function(socket){
        // This callback runs when a new Socket.IO connection is established.

        //Elevate username to admin for the room
        socket.on('add_admin', function(username) {
          console.log("adding admin");
          rooms[socket.room]["admin"].push(username);
          var tempsockets = io.sockets.adapter.rooms[socket.room];
          var thisSocketUser;
          for(var socketID in tempsockets['sockets']){
            thisSocket = io.sockets.connected[socketID];
            if (thisSocket.username===username){
              thisSocket.emit('youAreAdmin');
              thisSocket.emit('message_to_client', {username: 'server', message: 'you have become admin of this room'});
              userListUpdated(socket.room);
            }
          }
        });

        //Add new user connection
        socket.on('adduser', function(data){
          console.log ('boolean is '+(users.indexOf(data)>-1)+' with '+data+' users is '+users);
          if (users.indexOf(data)>-1){
            socket.emit('message_to_client',{username: 'server', message: data+" your username isn't valid. Try again"} );
          }else{
            socket.username = data;
            users.push(data);
            users[data] = data;
            socket.room = 'lobby';
            socket.join('lobby');
            socket.emit('message_to_client',{username: 'server', message: data+' you have entered the lobby!'} );
            socket.broadcast.to('lobby').emit('message_to_client', {username: 'server', message: data+' has entered the lobby'});
            socket.emit('updaterooms',Object.keys(rooms));
            socket.emit('setUser', data);
            socket.emit('change_room', 'lobby');
            userListUpdated('lobby');
          }
        });

        //Client change room request
        socket.on('changeroom',function(data){
          changeRoom(data);
        });

        //Create new room
        socket.on('createroom', function(roomData){
          rooms[roomData["name"]] = {has_password: roomData["has_password"], password: roomData["password"], admin: [socket.username], bannedUsers:[]};
          console.log(Object.keys(rooms));
          io.sockets.emit('updaterooms',Object.keys(rooms));
          io.sockets.emit('message_to_client',{username: 'server', message: 'new room'});
          //socket.emit('message_to_client',{username: 'server', message: 'you are the admin of '+roomData["name"]});
        });

        //Drop room
        socket.on('dropRoom', function(room) {
          var tempsockets = io.sockets.adapter.rooms[room];
          var thisSocketUser;
          for (var socketID in tempsockets['sockets']){
            thisSocket = io.sockets.connected[socketID];
            thisSocket.emit("banned");
          }
          delete rooms[room];
          io.sockets.emit('updaterooms',Object.keys(rooms));
        });

        socket.on('getID', function() {
          console.log(socket["id"]);
          socket.emit('setID', socket["id"]);
        });


        //Receive message from client
        socket.on('message_to_server', function(data) {
          // This callback runs when the server receives a new message from the client.

          console.log(data["username"]+"message: "+data["message"]+ " chatroom is "+socket.room); // log it to the Node.JS output
          io.sockets.in(socket.room).emit("message_to_client",{username: socket.username, message:data["message"] }) // broadcast the message to other users
        });

        //Permanently ban user
        socket.on('permBanUser', function(user){
          console.log('attempting to ban '+user);
          var tempsockets = io.sockets.adapter.rooms[socket.room];
          var thisSocketUser;
          rooms[socket.room]['bannedUsers'].push(user);
          for(var socketID in tempsockets['sockets']){
            thisSocket = io.sockets.connected[socketID];
            if (thisSocket.username===user){
              thisSocket.emit('message_to_client', {username: socket.username, message: "You've been banned!" });
              thisSocket.emit("banned");
              break;
            }
          }
        });

        //Remove user from rooms on disconnect
        socket.on('disconnect', function(){
          //console.log ('disconnected here');
          var ind = users.indexOf(socket.username);
          if(ind>-1){
            users.splice(ind,1);
          }
          userListUpdated(socket.room);
        })

        //Ban user for 30 seconds
        socket.on('tempBanUser', function(user){
          var tempsockets = io.sockets.adapter.rooms[socket.room];
          var thisSocketUser;
          rooms[socket.room]['bannedUsers'].push(user);
          for(var socketID in tempsockets['sockets']){
            thisSocket = io.sockets.connected[socketID];
            if (thisSocket.username===user){
              thisSocket.emit('message_to_client', {username: socket.username, message: "You've been banned!" });
              thisSocket.emit("banned");
              console.log("temp banning "+user);
              setTimeout(function(){
                unBanUser(thisSocket);
                console.log("user unbanned");}, 30000);
              break;
            }
          }
        });

        //Send private message to user
        socket.on('private_message', function(data){
          console.log('sending private message to '+data["target_user"])
          var tempsockets = io.sockets.adapter.rooms[socket.room];
          var thisSocketUser;
          var user = data["target_user"];
          for(var socketID in tempsockets['sockets']){
            thisSocket = io.sockets.connected[socketID];
            console.log("who is "+thisSocket.username);
            if (thisSocket.username===user){
              thisSocket.emit('message_to_client',{username: 'server', message: 'private message from '+socket.username+ ' incomming'});
              thisSocket.emit('message_to_client',{username: socket.username, message:data["message"]});
              //notify here!
              socket.emit('message_to_client',{username: 'server', message: 'message sent to '+socket.username});
              break;
            }
          }
        });

        //Check password for room
        socket.on('receive_password', function(name_and_pass){
          console.log("Log in attempt: Room "+name_and_pass["room"]+", Password "+name_and_pass["password"]);
          console.log("Correct password: "+rooms[name_and_pass["room"]]["password"]);
          console.log("Success? "+(rooms[name_and_pass["room"]]["password"] === name_and_pass["password"]));
          if (rooms[name_and_pass["room"]]["password"] === name_and_pass["password"]){
            changeRoom(name_and_pass["room"]);
          } else {
            console.log("Incorrect password.");
            return;
          }
        });

        //Attempt to change rooms (pre password-validation)
        socket.on('request_room_change', function(room_name) {
          for(var i = 0; i < rooms[room_name]['bannedUsers'].length; i++){
            if (rooms[room_name]['bannedUsers'][i]===socket.username){
                socket.emit('message_to_client', {username:"Server", message: "You're banned from "+room_name});
                return;
            }
          }
          if (rooms[room_name]["has_password"]){
            socket.emit('request_password', room_name);
          }
          else {
            changeRoom(room_name);
          }
        });

        //Change room helper function
        function changeRoom(newRoom){
          console.log("Changing room!");
          var oldroom = socket.room;
          socket.leave(oldroom);
          io.sockets.in(oldroom).emit('message_to_client',{username: 'server', message: socket.username + ' has left the room.'});
          socket.room = newRoom;
          socket.join(newRoom);
          socket.emit('change_room',newRoom);
          if (rooms[newRoom]["admin"].indexOf(socket.username)>-1){
            socket.emit('message_to_client', {username: 'server', message: 'You are the admin of this room.'});
            socket.emit('youAreAdmin');
          }else{
            socket.emit('youAreNotAdmin');
          }
          userListUpdated(oldroom);
          userListUpdated(newRoom);
          io.sockets.in(newRoom).emit('message_to_client', {username: 'server', message: socket.username+' has entered '+newRoom});
        }

        //Function call when temp ban expires
        function unBanUser(thisSocket){
          var ind = rooms[socket.room]['bannedUsers'].indexOf(thisSocket.username);
          if(ind>-1){
            rooms[socket.room]['bannedUsers'].splice(ind,1);
          }
          thisSocket.emit('message_to_client',{username: 'server', message: 'unbanned'})
        }

        //Update the list of users in a room
        function userListUpdated(room){
            var tempsockets = io.sockets.adapter.rooms[room];
            var newRoomUsers = []
            if (!tempsockets){
                return;
            }
            for(var socketID in tempsockets['sockets']){
                var thisSocketUser = io.sockets.connected[socketID].username;
                newRoomUsers.push(thisSocketUser);
            }
            io.sockets.in(room).emit('refresh_users', newRoomUsers);
        }

});
