const { app, BrowserWindow, ipcMain } = require("electron");
const fs = require("fs/promises");
const path = require("path");

function dataFilePath() {
  return path.join(app.getPath("userData"), "dados-escolares.json");
}

async function readData() {
  try {
    const content = await fs.readFile(dataFilePath(), "utf8");
    return JSON.parse(content);
  } catch {
    return null;
  }
}

async function writeData(data) {
  await fs.mkdir(path.dirname(dataFilePath()), { recursive: true });
  await fs.writeFile(dataFilePath(), JSON.stringify(data, null, 2), "utf8");
  return true;
}

function createWindow() {
  const win = new BrowserWindow({
    width: 1280,
    height: 820,
    minWidth: 980,
    minHeight: 660,
    title: "Plataforma de Gestao Escolar",
    backgroundColor: "#f4f7fb",
    webPreferences: {
      preload: path.join(__dirname, "preload.js"),
      contextIsolation: true,
      nodeIntegration: false
    }
  });

  win.removeMenu();
  win.loadFile(path.join(__dirname, "index.html"));
}

app.whenReady().then(createWindow);

app.on("window-all-closed", () => {
  if (process.platform !== "darwin") {
    app.quit();
  }
});

app.on("activate", () => {
  if (BrowserWindow.getAllWindows().length === 0) {
    createWindow();
  }
});

ipcMain.handle("data:read", readData);
ipcMain.handle("data:write", (_event, data) => writeData(data));
