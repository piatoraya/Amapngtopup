importScripts("https://www.gstatic.com/firebasejs/7.20.0/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/7.20.0/firebase-messaging.js");
importScripts("https://www.gstatic.com/firebasejs/7.20.0/firebase-analytics.js");

firebase.initializeApp({
    messagingSenderId: "970359087823",
    apiKey: "AIzaSyBa4K43FB8qZhFU-g6LUDFlZhOqmSueuKs",
    projectId: "ampang-61a09",
    appId: "1:970359087823:web:e2e6595962705002458dba",
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log("[firebase-messaging-sw.js] Received background message",payload);
    const ShennData = JSON.parse(payload.data.notification);
    const notificationTitle = ShennData.title;
    const notificationOptions = {body: ShennData.body};
    return self.registration.showNotification(notificationTitle,notificationOptions);
});