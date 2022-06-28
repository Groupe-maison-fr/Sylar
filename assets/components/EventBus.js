import ReconnectingEventSource from 'reconnecting-eventsource';

const subscriptions = {}

const EventBus = {
    on(eventName, callback) {
        if (!subscriptions[eventName]) {
            subscriptions[eventName] = [];
        }

        subscriptions[eventName].push(callback);
    },
    remove(eventName, callback) {
        subscriptions[eventName] = subscriptions[eventName].filter((subscription) => subscription !== callback);
        if (Object.keys(subscriptions[eventName]).length === 0) {
            delete subscriptions[eventName]
        }
    },
    dispatch(eventName, arg) {
        if (!subscriptions[eventName]) {
            return;
        }

        Object.keys(subscriptions[eventName]).forEach(key => {
            subscriptions[eventName][key](eventName, arg);
        });
    },
    handleEventSource(eventSourceUrl) {
        const eventSource = new ReconnectingEventSource(eventSourceUrl);
        eventSource.addEventListener('message', (rawEvent) => {
            const event = JSON.parse(rawEvent.data);
            console.log('Event', event);
            EventBus.dispatch(`${event.type}:${event.action}`, event.data);
        }, false);
    }
};

export default EventBus;
