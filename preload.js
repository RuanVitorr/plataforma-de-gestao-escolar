const { contextBridge, ipcRenderer } = require("electron");

contextBridge.exposeInMainWorld("schoolStore", {
  read: () => ipcRenderer.invoke("data:read"),
  write: (data) => ipcRenderer.invoke("data:write", data)
});
