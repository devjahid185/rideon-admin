@extends('vendor.layout')
@section('styles')

<style>
.custom-textarea {
   all: unset;  /* Resets all inherited styles, making it easier to apply custom styles */
   width: 100%;  /* Ensure it spans the full width */
   height: 150px;  /* Custom height */
   padding: 10px;
   border: 1px solid #ccc;
   border-radius: 5px;
   font-size: 14px;
   background-color: white;
   resize: vertical;  /* Allow resizing */
}
/* Ensure the camera button is properly styled */
.image-upload-btn {
   display: flex;
   align-items: center;
   justify-content: center;
   width: 40px; /* Set width to fit icon */
   height: 40px; /* Set height to fit icon */
   border-radius: 50%; /* Make it circular */
   background-color: #f1f1f1; /* Background for better visibility */
   cursor: pointer;
   position: relative;
   overflow: hidden;
}

.image-upload-btn i {
   font-size: 18px; /* Adjust size of the camera icon */
   color: #555; /* Icon color */
}

/* Prevent overlapping with hidden file input */
.image-upload-btn input {
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   opacity: 0; /* Keep it hidden */
   cursor: pointer;
}
/* Position loader inside the panel-footer */
#loader {
    display: none; /* Hidden by default */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999; /* Ensure it's on top */
    width: 100%; /* Full width of the panel footer */
    text-align: center;
}

/* The loader spinner styles */
.loader {
    border: 8px solid #f3f3f3; /* Light grey background */
    border-top: 8px solid #3498db; /* Blue color for the spinner */
    border-radius: 50%;
    width: 50px; /* Size of the loader */
    height: 50px;
    animation: spin 1s linear infinite; /* Infinite spinning animation */
}

/* Spinning animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Message text style for "Sending message..." */
#loader p {
    color: white;
    font-size: 18px;
    margin-top: 10px;
    font-family: Arial, sans-serif;
    font-weight: bold;
}


</style>
@endsection
@section('content')


<div class="container">
   <div class="row" style="margin-top: 10px;">
      <!-- Left Side: Chat Tabs -->
      <div class="col-md-4">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h4 class="panel-title">Chats</h4>
            </div>
            <div class="list-group" id="chat-list">
               <!-- Chat list will be dynamically populated here -->
            </div>
         </div>
      </div>

      <!-- Right Side: Chat Window -->
      <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading">
               <h4 class="panel-title" id="chat-heading"></h4>
            </div>
            <div class="panel-body" id="chat-body" style="height: 400px; overflow-y: auto;">
               <!-- Messages will be dynamically populated here -->
            </div>
            <div class="panel-footer">
               
               <form id="chatForm">
               @csrf <!-- CSRF Token -->
               <div class="input-group">
                  <!-- Textarea for message input -->
                  <textarea class="form-control custom-textarea" id="chat-input" name="messageInput" placeholder="Write a message..." autocomplete="off"></textarea>
                  
                  <!-- Image upload section -->
                  <div class="input-group-btn">
                     <label class="btn btn-secondary image-upload-btn" id="image-upload-btn">
                        <i class="fas fa-camera"></i>
                        <!-- Hidden file input for image upload -->
                        <input type="file" id="imageFileInput" name="image" accept="image/*" style="display: none;">
                        
                     </label>
                  </div>
                 

                  <!-- Send button -->
                  <span class="input-group-btn" style = "font-size: 61px; margin-left: -11px;">
                     <button class="btn btn-primary" type="button" id="send-button">
                        <i class="fas fa-paper-plane"></i>
                     </button>
                  </span>
               </div>
               <!-- Display for uploaded file name -->
               <span id="fileNameDisplay" style="margin-top: 10px; display: none;"></span>
            </form>
            <input type="hidden" id="playerid_vendor" value="" />
            <div id="loader" style="display: none;">
    <div class="loader-overlay">
        <div class="loader"></div>
        <p>Sending message...</p>
    </div>
</div>
            </div>
         </div>
      </div>

   </div>
</div>
@endsection
@section('scripts')
<!-- Firebase App (compatibility mode) -->
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-storage-compat.js"></script>



<script>
   try {
         const firebaseConfig = {
         apiKey: "AIzaSyDJmfSsQoJwx9OCf6t3m-0tcXNT6NilbcI",
         authDomain: "unibookervehicle.firebaseapp.com",
         databaseURL: "https://unibookervehicle-default-rtdb.firebaseio.com",
         projectId: "unibookervehicle",
         storageBucket: "unibookervehicle.appspot.com",
         messagingSenderId: "951328556833",
         appId: "1:951328556833:web:4a922ffcaa3df4341de060"
         };
            firebase.initializeApp(firebaseConfig);
            var database = firebase.database();
         } catch (error) {
            Swal.fire('Error', 'Failed to initialize Firebase. Please check your configuration or try again later.', 'error');
         }
      const vendorId = {{ Auth::user()->id }};// Replace with the vendor's ID
const chatListRef = database.ref(`chatList/${vendorId}`);

// Function to fetch chat list and render it
function loadChatList() {
   console.log("Loading chat list...");
   chatListRef.once('value', snapshot => {
      const chatListContainer = document.getElementById('chat-list');
      chatListContainer.innerHTML = '';

      if (snapshot.exists()) {
         snapshot.forEach(childSnapshot => {
    const chatKey = childSnapshot.key;
    const chatData = childSnapshot.val();

    const [itemId, userId] = chatKey.split('_');
    const { itemName, image, timestamp, from, message, attachment } = chatData;
    const formattedTime = new Date(timestamp).toLocaleString();
    const chatPanel = document.querySelector('.col-md-6');
   const chatForm = document.getElementById('chatForm');

   function disableChat() {
      chatPanel.style.pointerEvents = 'none';
      chatPanel.style.opacity = '0.5';
   }

   function enableChat() {
      chatPanel.style.pointerEvents = 'auto';
      chatPanel.style.opacity = '1';
   }

   disableChat();
    // Create the chat preview element
    const chatElement = document.createElement('a');
    chatElement.href = `#`;
    chatElement.className = 'list-group-item';
    chatElement.dataset.chatKey = chatKey;
    chatElement.dataset.userId = userId;
    const messageContent = attachment && attachment.image ? "Image" : message; 
    // Update the content to include image, item name, and message
    chatElement.innerHTML = `
        <div style="display: flex; align-items: center;">
            <img src="${image}" alt="${itemName}" style="width: 60px; height: 40px; border-radius: 5px; margin-right: 10px;">
            <div>
                <h5 style="margin: 0; font-size: 14px;">${itemName}</h5>
                <small style="color: gray;">${formattedTime}</small>
                <p style="margin: 0; font-size: 12px; color: black;">
                    <strong>${from}:</strong> ${messageContent}
                </p>
            </div>
        </div>
    `;

    // Add click event listener
    chatElement.addEventListener('click', event => {
        event.preventDefault();
        loadChatMessages(chatKey, chatData, userId);
        enableChat();
    });

    chatListContainer.appendChild(chatElement);
});

      } else {
         chatListContainer.innerHTML = `<p>No chats available.</p>`;
      }
   }).catch(error => {
      console.error("Error loading chat list:", error);
   });
}

let currentChatKey = null; // To store the currently active chat key
let lastMessageTimestamp = 0;
function loadChatMessages(chatKey,chatData, userid) {
   currentChatKey = chatKey; // Set the active chat key

   console.log("Loading messages for chat:", chatKey);
   var playerid_vendor = document.getElementById('playerid_vendor').value;
   if(playerid_vendor === "" || !playerid_vendor){
      playerid_vendor ="null";
   }
   const [itemIdPart, userId] = chatKey.split('_');
   const firebaseChatRef = `${userId}_${itemIdPart}_${vendorId}`;
   console.log("Image URL:", chatData.attachment.image);
   const chatHeading = document.getElementById('chat-heading');
   chatHeading.innerHTML = `<input type="hidden" id="hiddenItemId" value="${chatData.itemId}">
   <input type="hidden" id="hiddenImageUrl" value="${chatData.image}">
   <input type="hidden" id="hiddenItemName" value="${chatData.itemName}"><span id="itemName">${chatData.itemName}</span> from <input type="hidden" id="hiddenUserId" value="${userId}"> <span id=""> {{ Auth::user()->first_name }}</span> `;
   const chatBody = document.getElementById('chat-body');
   const imageUrl = document.getElementById('hiddenImageUrl').value;
   
   chatBody.innerHTML = '';
   
   
   const messagesRef = database.ref(`chats/${firebaseChatRef}`);
   
   messagesRef.on('child_added', messageSnapshot => {  // Listen only for new messages
      const messageData = messageSnapshot.val();

      const { from, message, timestamp, senderId } = messageData;

      if (timestamp === lastMessageTimestamp) {
         return; // Skip this message as it has already been added
      }

      // Update the last message timestamp
      lastMessageTimestamp = timestamp;
      const messageAlignment = senderId === userId ? 'media' : 'media text-right';
      const messageBackgroundColor = senderId === userId ? '#18bebd' : '#989d9d';
     // const formattedTime = new Date(timestamp).toLocaleString();
     const formattedTime = new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true // to get AM/PM format
}).format(new Date(timestamp));


      const words = message.split(' ');
      let chunkedMessage = '';
      let currentChunk = '';

      words.forEach(word => {
         if ((currentChunk + word).length <= 30) {
            currentChunk += (currentChunk ? ' ' : '') + word;
         } else {
            chunkedMessage += (chunkedMessage ? '<br>' : '') + currentChunk;
            currentChunk = word;
         }
      });

      if (currentChunk) {
         chunkedMessage += (chunkedMessage ? '<br>' : '') + currentChunk;
      }

      const messageElement = document.createElement('div');
      messageElement.className = messageAlignment;

      messageElement.innerHTML = `
   <div class="media-body">
      ${chunkedMessage !== "" ? `
         <p style="display: inline-block; background-color: ${messageBackgroundColor}; padding: 5px 10px; color: white; border-radius: 5px;">
            ${chunkedMessage}
         </p>
      ` : ''}
      ${messageData.attachment.image ? `
         <img src="${messageData.attachment.image}" alt="Image" style="max-width: 40%; max-height: 200px; margin-top: 10px; border-radius: 5px;">
      ` : ''}
      <small style="display: block; margin-top: ${messageData.attachment.image ? '0' : '-10px'};">${formattedTime}</small>
   </div>
`;



      if (chatKey === currentChatKey) {  // Only update the chat body for the active chat
         const chatBody = document.getElementById('chat-body');
         chatBody.appendChild(messageElement);
         chatBody.scrollTop = chatBody.scrollHeight; // Auto scroll
      }
   });

   messagesRef.on('child_removed', messageSnapshot => {
      const messageData = messageSnapshot.val();
      console.log("message removed for chat:", messageData);
      // You can also handle message removal if needed
   });

   messagesRef.on('child_changed', messageSnapshot => {
      const messageData = messageSnapshot.val();
      console.log("message updated for chat:", messageData);
      // You can handle updated messages if needed
   });

}


// Call the function to load the chat list
loadChatList();

document.getElementById('chat-input').addEventListener('keydown', function(event) {
  
   if (event.key === 'Enter') {
      event.preventDefault();  
      document.getElementById('send-button').click(); 
   }
});

document.getElementById('send-button').addEventListener('click', function() {
   const chatInput = document.getElementById('chat-input');
   const messageText = chatInput.value.trim();
   var playerid_vendor = document.getElementById('playerid_vendor').value;

   if(playerid_vendor === "" || !playerid_vendor){
      playerid_vendor ="null";
   }
   console.log(" plyer idddddd ", playerid_vendor);
   // Get the image file if selected
   const imageFileInput = document.getElementById('imageFileInput');
   const file = imageFileInput.files[0];  // Get the first selected file (image)
   
   if (messageText !== '' || file) {
      const chatHeading = document.getElementById('chat-heading');
      const itemId = document.getElementById('hiddenItemId').value;
      const itemName = document.getElementById('hiddenItemName').value;
      const hiddenImageUrlElement = document.getElementById('hiddenImageUrl');
      const imageUrlProfile = hiddenImageUrlElement && hiddenImageUrlElement.value ? hiddenImageUrlElement.value : "";  // Fallback to "" if empty

      const userId = document.getElementById('hiddenUserId').value;
      const vendorId = {{ Auth::user()->id }};
      const vendorName = "{{ Auth::user()->first_name }}";
      console.log("vendor name ", vendorName);

      const firebaseChatRef = `${userId}_P${itemId}_${vendorId}`;
      const chatListKey = `P${itemId}_${userId}`;
      console.log(chatListKey);
      const loader = document.getElementById('loader');  // Assuming you have a loader with the ID 'loader'
      loader.style.display = 'block';

      const userRef = database.ref(`users/${userId}`);
        const vendorRef = database.ref(`users/${vendorId}`);

        Promise.all([userRef.once('value'), vendorRef.once('value')])
            .then(([userSnapshot, vendorSnapshot]) => {
                const userPlayerId = userSnapshot.val()?.playerId || "";
                const vendorPlayerId = vendorSnapshot.val()?.playerId || playerid_vendor;
                console.log('userPlayerId == ', userPlayerId);
                console.log('vendorPlayerId == ', vendorPlayerId);

               //  if (!userPlayerId || !vendorPlayerId) {
               //      console.error('Player IDs missing for user or vendor.');
               //      loader.style.display = 'none';
               //      return;
               //  }

                // Step 2: Handle image upload if file exists
                if (file) {
                    const uniqueFileName = generateUniqueFileName(file.name);

                    uploadImage(file, uniqueFileName)
                        .then(downloadURL => {
                            sendMessage(messageText, downloadURL, vendorName, vendorId, userId, itemId, firebaseChatRef, chatListKey, userPlayerId, vendorPlayerId);
                        })
                        .catch(error => {
                            console.error('Error uploading image:', error);
                            loader.style.display = 'none';
                        });
                } else {
                    // If no image, just send the text message
                    sendMessage(messageText, '', vendorName, vendorId, userId, itemId, firebaseChatRef, chatListKey, userPlayerId, vendorPlayerId);
                }
            })
            .catch(error => {
                console.error("Error fetching user or vendor details:", error);
                loader.style.display = 'none';
            });

      function sendMessage(messageText, imageUrl, vendorName, vendorId, userId, itemId, firebaseChatRef, chatListKey, userPlayerId, vendorPlayerId) 
      {


         const messageData = {
            from: vendorName,
            message: messageText,
            timestamp: Date.now(),
            senderId: vendorId,
            receiverId: userId,
            roomId: `${userId}_P${itemId}_${vendorId}`,
            attachment: {
               image: imageUrl,
            },
            itemId: itemId,
            itemName: itemName,  // Change this as needed
            seen: false,
            playerid_user1: vendorPlayerId,
            playerid_user2: userPlayerId,
            timeZone: "",
         };

         const chatListMessageData = {
            from: vendorName,
            image: imageUrlProfile,
            message: messageText,
            timestamp: Date.now(),
            senderId: vendorId,
            receiverId: userId,
            roomId: `${userId}_P${itemId}_${vendorId}`,
            attachment: {
               image: imageUrl,
            },
            itemId: itemId,
            itemName: itemName,  // Change this as needed
            seen: false,
            playerid_user1: vendorPlayerId,
            playerid_user2: userPlayerId,
            timeZone: "",
         };

         const messagesRef = database.ref(`chats/${firebaseChatRef}`);
         messagesRef.push(messageData)
            .then(() => {
               chatInput.value = '';  // Clear the input after sending the message
               $('#imageFileInput').val('');  // Clear the file input value
               $('#fileNameDisplay').hide();
               const chatBody = document.getElementById('chat-body');
               chatBody.scrollTop = chatBody.scrollHeight;
               updateChatList(vendorId, chatListKey, chatListMessageData);
               updateUsersRef(vendorId, vendorPlayerId);
               loader.style.display = 'none';
            })
            .catch(error => {
               console.error("Error sending message:", error);
               loader.style.display = 'none';
            });

         function updateChatList(vendorId, chatListKey, messageData) {
            const chatListRef = database.ref(`chatList/${vendorId}/${chatListKey}`);
            chatListRef.set(messageData)
               .then(() => {
                  console.log("Chat list updated successfully.");
               })
               .catch(error => {
                  console.error("Error updating chat list:", error);
                  loader.style.display = 'none';
               });
         }

         function updateUsersRef(vendorId, vendorPlayerId) {
               const usersRef = database.ref(`users/${vendorId}`);
               usersRef.set({
                     playerId: vendorPlayerId,
                     userId: vendorId,
               })
                     .then(() => {
                        console.log("User information added to 'users' reference.");
                     })
                     .catch(error => {
                        console.error("Error adding user information:", error);
                     });
            }
      }
   }
});

function generateUniqueFileName(originalName) {
   const timestamp = Date.now();
   const randomString = Math.random().toString(36).substring(2, 15);
   const extension = originalName.split('.').pop();
   //return `${timestamp}_${randomString}.${extension}`;
   return `${timestamp}.${extension}`;
}

// Function to upload image to Firebase Storage
function uploadImage(file, uniqueFileName) {
   const storageRef = firebase.storage().ref();
   const imageRef = storageRef.child('images/' + uniqueFileName);
   return imageRef.put(file).then(function(snapshot) {
      return snapshot.ref.getDownloadURL();  // Get the download URL after upload
   });
}

$('#imageFileInput').change(function() {
            const fileName = $(this).val().split('\\').pop(); // Get the file name
            const fileNameDisplay = $('#fileNameDisplay');
            if (fileName) {
                  fileNameDisplay.text(fileName); // Set the file name text
                  fileNameDisplay.show(); // Show the file name element
            } else {
                  fileNameDisplay.hide(); // Hide if no file is selected
            }
         });
</script>

<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>

<script>
  // Initialize OneSignal
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    const onesignalAppId = @json($onesignalAppId);
      OneSignalDeferred.push(async function(OneSignal) {
         const chatList = document.getElementById('chat-list');
         try {
               if (onesignalAppId) {
                     await OneSignal.init({
                        appId: onesignalAppId,
                     });
                     console.log("OneSignal initialized successfully.");
               } else {
                     throw new Error("OneSignal App ID is missing.");
               }
            } catch (error) {
               
               if (error && error.message) {
                     Swal.fire('Initialization Failed', `Error: ${error.message}`, 'error');
                     disableChatList(chatList);
                     
               } else {
                     Swal.fire('Initialization Failed', 'An unknown error occurred during OneSignal initialization.', 'error');
               }
               
               console.error("OneSignal initialization error:", error);
            }

            OneSignal.push(function() {
         // Check if the user has opted in for push notifications
         if (OneSignal.User.PushSubscription.optedIn) {
            console.log("User has opted in for push notifications.");
         
         } else {
            console.log("User has not opted in for push notifications.");
            OneSignal.Notifications.requestPermission()
         }
         });
      OneSignal.Notifications.addEventListener('permissionChange', function(permission) {
         console.log("Push notification permission changed:", permission);

         // If permission is granted, capture the subscription ID
         if (permission === true) {
         console.log("User granted permission for push notifications.");

         var pushSubscriptionId = OneSignal.User.PushSubscription.id;
         document.getElementById("playerid_vendor").value = pushSubscriptionId;
      console.log("User's Push Subscription ID:", pushSubscriptionId);
      
         }
         else{
            document.getElementById("playerid_vendor").value ="null";
         }
      });
      OneSignal.push(function() {
      // Check if the user has opted in for push notifications
      if (OneSignal.User.PushSubscription.optedIn) {
         // Retrieve the push subscription ID
         var pushSubscriptionId = OneSignal.User.PushSubscription.id;
         document.getElementById("playerid_vendor").value = pushSubscriptionId;
         console.log("User's Push Subscription ID:", pushSubscriptionId);
      } else {
         console.log("User has not opted in for push notifications.");
      }
      });

  });

</script>



<!-- Register the service worker -->
<script>
//   if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register('/OneSignalSDK.sw.js')
//       .then(function (registration) {
//         console.log('Service Worker registered with scope:', registration.scope);
//       })
//       .catch(function (error) {
//         console.error('Service Worker registration failed:', error);
//       });
//   }

if ('serviceWorker' in navigator) {
    
    navigator.serviceWorker.register('/OneSignalSDKWorker.js')
      .then(function (registration) {
        console.log('Service Worker registered with scope:', registration.scope);
      })
      .catch(function (error) {
        console.warn('Service Worker registration for OneSignalSDKWorker.js failed, falling back to OneSignalSDK.sw.js:', error);
 
        navigator.serviceWorker.register('/OneSignalSDK.sw.js')
          .then(function (registration) {
            console.log('Fallback Service Worker registered with scope:', registration.scope);
          })
          .catch(function (error) {
            console.error('Fallback Service Worker registration failed:', error);
          });
      });
}
</script>







@endsection