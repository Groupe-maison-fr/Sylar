import {
    deleteSubscription,
    isNotificationEnabled,
    getSubscription,
    persistConfiguration,
    sendPushNotification,
    setNotificationEnabled,
    setSubscription,
    publicKey
} from "./webpush.mjs";

const subscriptionGet = () => {
    return (req, res) => {
        res.send({
            subscription: getSubscription(),
            notificationEnabled: isNotificationEnabled(),
            applicationServerPublicKey: publicKey
        });
    }
}

const subscriptionDelete = () => {
    return (req, res) => {
        deleteSubscription();
        setSubscription(false);
        setNotificationEnabled(false);
        res.send({success: true});
    }
}

const subscriptionSet = () => {
    return (req, res) => {
        setSubscription(req.body.subscription);
        console.log('subscription received', getSubscription());
        persistConfiguration();
        if (isNotificationEnabled()) {
            sendPushNotification(JSON.stringify({title: 'subscription received'}))
                .catch(err => console.error(err));
        }

        res.send({notificationEnabled: isNotificationEnabled(), subscription: getSubscription()});
    }
}

const subscriptionEnabled = () => {
    return (req, res) => {
        setNotificationEnabled(req.body.notificationEnabled);
        console.log('subscriptionEnabled received', isNotificationEnabled());
        persistConfiguration();
        if (isNotificationEnabled()) {
            sendPushNotification(JSON.stringify({title: `Notification ${isNotificationEnabled() ? 'enabled' : 'disabled'}`}))
                .catch(err => console.error(err));
        }

        res.send({notificationEnabled: isNotificationEnabled(), subscription: getSubscription()});
    }
}

const vapidKeys = () => (req, res) => res.send({publicKey});

const sendNotification = () => {
    return (req, res) => {
        sendPushNotification(JSON.stringify(req.body))
            .then((response) => {
                //console.log('notification', req.body);
                res.send({success: true})
            })
            .catch(error => {
                console.error('notification', error);
                res.send({error});
            })
    }
}

export {
    subscriptionGet,
    subscriptionSet,
    subscriptionDelete,
    subscriptionEnabled,
    sendNotification,
    vapidKeys
};
