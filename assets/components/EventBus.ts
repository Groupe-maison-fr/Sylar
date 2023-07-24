import ReconnectingEventSource from 'reconnecting-eventsource';

type Callback = (callbackEventName: string, args: any)=>void

const subscriptions:{
  [eventName:string]:Callback[]
} = {};

const EventBus = {
  on: (eventName:string, callback: Callback) => {
    if (!subscriptions[eventName]) {
      subscriptions[eventName] = [];
    }

    subscriptions[eventName].push(callback);
  },
  remove: (eventName:string, callback:Callback) => {
    subscriptions[eventName] = subscriptions[eventName].filter((subscription) => subscription !== callback);
    if (Object.keys(subscriptions[eventName]).length === 0) {
      delete subscriptions[eventName];
    }
  },
  dispatch: (eventName:string, arg:any) => {
    if (!subscriptions[eventName]) {
      return;
    }

    Object.keys(subscriptions[eventName]).forEach((key) => {
      // @ts-ignore
      subscriptions[eventName][key](eventName, arg);
    });
  },
  handleEventSource: (eventSourceUrl:string) => {
    const eventSource = new ReconnectingEventSource(eventSourceUrl);
    eventSource.addEventListener('message', (rawEvent) => {
      const event = JSON.parse(rawEvent.data);
      // console.log('Event', event);
      EventBus.dispatch(`${event.type}:${event.action}`, event.data);
    }, false);
  }
};

export default EventBus;
