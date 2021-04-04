console.log('Sylar Service worker started');

self.addEventListener("push", event => {
  console.log(event);
  if (!event.data) {
    return;
  }
  const data = event.data.json()

  const title = data.title || "Sylar ";
  const body = data.message || "Message from Sylar";
  const icon = data.icon || `https://raw.githubusercontent.com/jarofghosts/kolombo/master/web/noun-dove.png`;

  self.registration.showNotification(title, {
    body,
    icon,
  });
})
