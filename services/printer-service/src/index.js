'use strict';

// =======================
// Imports
// =======================
const express = require("express");
const bodyParser = require("body-parser");
const escpos = require("escpos");
const fs = require("fs");
const path = require("path");
const Jimp = require("jimp");

escpos.USB = require("escpos-usb");

// =======================
// Constants
// =======================
const VENDOR_ID = 8137;
const PRODUCT_ID = 8214;
const PORT = 3000;
const LOG_FILE = path.join(__dirname, "printer-service.log");
const MAX_LOG_SIZE = 5 * 1024 * 1024; // 5 MB

/**
 * Rotates the log file if it exceeds MAX_LOG_SIZE.
 * Keeps one backup file and prevents unlimited growth.
 */
function rotateLogIfNeeded() {
    try {
        if (fs.existsSync(LOG_FILE)) {
            const stats = fs.statSync(LOG_FILE);
            if (stats.size >= MAX_LOG_SIZE) {
                const oldLog = path.join(__dirname, "printer-service.old.log");
                if (fs.existsSync(oldLog)) fs.unlinkSync(oldLog);
                fs.renameSync(LOG_FILE, oldLog);
            }
        }
    } catch {
        // Silent fail to avoid service interruption
    }
}

/**
 * Writes a timestamped log entry.
 * @param {string} message
 * @param {string} [level="INFO"]
 */
function log(message, level = "INFO") {
    try {
        rotateLogIfNeeded();
        const line = `[${new Date().toISOString()}] [${level}] ${message}\n`;
        fs.appendFileSync(LOG_FILE, line);
    } catch {
        // Never crash service due to logging
    }
}

// =======================
// App Initialization
// =======================
const app = express();
app.use(bodyParser.json({ limit: "20mb" }));

/**
 * Converts a base64 image into ESC/POS printable format.
 * @param {string} base64
 * @returns {Promise<object>}
 */
async function loadImageForPrinter(base64) {
    const buffer = Buffer.from(base64, "base64");
    const image = await Jimp.read(buffer);

    image.resize(384, Jimp.AUTO).greyscale().dither565();

    const tmpPath = path.join(__dirname, `tmp_${Date.now()}.png`);
    await image.writeAsync(tmpPath);

    return new Promise((resolve, reject) => {
        escpos.Image.load(tmpPath, (img, err) => {
            try { fs.unlinkSync(tmpPath); } catch { }
            if (err) return reject(err);
            resolve(img);
        });
    });
}

/**
 * Sends formatted receipt data to the USB printer.
 * @param {object} payload
 * @returns {Promise<void>}
 */
async function printReceipt(payload) {
    const device = new escpos.USB(VENDOR_ID, PRODUCT_ID);
    const printer = new escpos.Printer(device, { encoding: "CP860" });

    return new Promise((resolve, reject) => {
        device.open(async (err) => {
            if (err) {
                log("USB open error: " + err, "ERROR");
                return reject(err);
            }

            try {
                if (payload.logo_base64) {
                    const logo = await loadImageForPrinter(payload.logo_base64);
                    printer.align('ct')
                    printer.image(logo, 'd24')
                    printer.newLine();
                }

                for (const line of payload.text_lines || []) {
                    if (!line?.text) continue;
                    const align = { left: 'lt', center: 'ct', right: 'rt' };
                    printer.align(align[line.align] || 'lt')
                    printer.style(line.emphasis ? "B" : "A")
                    printer.text(String(line.text));
                }

                printer.newLine();

                if (payload.barcode_base64) {
                    const barcode = await loadImageForPrinter(payload.barcode_base64);
                    printer.align('ct')
                    printer.image(barcode, 'd24')
                    printer.newLine();
                }

                if (payload.options?.school_copy) {
                    printer.newLine()
                    printer.align('ct')
                    printer.text("Assinatura do aluno:")
                    printer.newLine()
                    printer.text("_________________________________________")
                    printer.newLine();
                }

                printer.cut()
                printer.close();
                resolve();
            } catch (e) {
                printer.close();
                log("Print error: " + e.toString(), "ERROR");
                reject(e);
            }
        });
    });
}

// =======================
// Routes
// =======================

/**
 * Main print endpoint.
 * Triggers receipt printing and optional delayed copy.
 */
app.post("/print", async (req, res) => {
    try {
        await printReceipt(req.body);

        if (req.body.options?.school_copy && req.body.options?.school_copy_after_ms) {
            setTimeout(async () => {
                try {
                    const copyPayload = {
                        ...req.body,
                        options: { ...req.body.options, school_copy: false }
                    };
                    await printReceipt(copyPayload);
                } catch (err) {
                    log("School copy error: " + err.toString(), "ERROR");
                }
            }, req.body.options.school_copy_after_ms);
        }

        res.json({ success: true });
    } catch (err) {
        log("Route error: " + err.toString(), "ERROR");
        res.status(500).json({ success: false });
    }
});

// =======================
// Start Server
// =======================

/**
 * Starts the background HTTP service.
 */
app.listen(PORT, () => {
    log(`Printer service running on port ${PORT}`);
});



