self.addEventListener("install", (event) => {
  console.log("Service Worker installing...");
});

self.addEventListener("fetch", (event) => {
  // អាចដាក់ cache resources នៅទីនេះ
});
