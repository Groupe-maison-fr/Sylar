import {sendPushNotification} from "./modules/webpush/webpush.mjs";

const notification = JSON.stringify({
    title: "ISS test notification",
    message: "Hello from ISS!" + Date.now(),
})

sendPushNotification(notification)
    .then(() => console.log(notification))
    .catch((error) => console.error(error));


console.log('Notification sent', notification);

