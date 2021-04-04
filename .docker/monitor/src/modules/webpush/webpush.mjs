import webpush from 'web-push';
import fs from 'fs';

const vapidKeysPath = '/monitor/data/vapidkeys.json';
const subscriptionPath = '/monitor/data/subscription.json';

const getVapidkeys = () => {
    if (fs.existsSync(vapidKeysPath)) {
        return JSON.parse(fs.readFileSync(vapidKeysPath, {encoding: 'utf8', flag: 'r'}));
    } else {
        const vapidKeys = webpush.generateVAPIDKeys();
        fs.writeFileSync(vapidKeysPath, JSON.stringify(vapidKeys));
        return vapidKeys;
    }
}

const getConfiguration = () => {
    if (fs.existsSync(subscriptionPath)) {
        return JSON.parse(fs.readFileSync(subscriptionPath, {encoding: 'utf8', flag: 'r'}));
    }
    return {
        notificationEnabled: false,
        subscription: false
    };
}

const persistConfiguration = () => {
    try {
        fs.writeFileSync(subscriptionPath, JSON.stringify({
            notificationEnabled,
            subscription
        }));
    } catch (err) {
        console.error(err);
    }
}

const deleteSubscription = () => {
    if (fs.existsSync(subscriptionPath)) {
        fs.unlinkSync(subscriptionPath);
    }
}

const {publicKey, privateKey} = getVapidkeys();
let {subscription, notificationEnabled} = getConfiguration();

console.log({publicKey, privateKey, subscription, notificationEnabled});

webpush.setVapidDetails(
    "mailto:it@maison.fr",
    publicKey,
    privateKey
);

const setSubscription = (value) => subscription = value;
const getSubscription = () => subscription
const isNotificationEnabled = () => notificationEnabled;
const setNotificationEnabled = (value) => notificationEnabled = value;

const sendPushNotification = (notification) => webpush.sendNotification(subscription, notification);
export {
    persistConfiguration,
    deleteSubscription,
    getSubscription,
    setSubscription,
    isNotificationEnabled,
    setNotificationEnabled,
    publicKey,
    sendPushNotification
};
