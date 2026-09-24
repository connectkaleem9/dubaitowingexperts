/**
 * Responsive screenshot + horizontal-overflow checker (Chrome DevTools Protocol, no dependencies).
 *
 *   node tools/shots.js http://127.0.0.1:8080 / /services/car-recovery/ --widths=390,768,1440
 *
 * For each page and width it reports whether the layout scrolls sideways (and which elements are
 * too wide), then writes a full-page PNG to tools/shots/. Phone widths use mobile emulation, so
 * the viewport meta tag is honoured exactly as it is on a real device.
 */
'use strict';

const { spawn } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');
const os = require('node:os');

const CHROME_CANDIDATES = [
    process.env.CHROME_PATH,
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
    `${os.homedir()}/AppData/Local/Google/Chrome/Application/chrome.exe`,
    '/usr/bin/google-chrome',
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
].filter(Boolean);

const args = process.argv.slice(2);
const base = (args.find((a) => a.startsWith('http')) || 'http://127.0.0.1:8080').replace(/\/$/, '');
const widthArg = args.find((a) => a.startsWith('--widths='));
const widths = (widthArg ? widthArg.split('=')[1] : '390,768,1440').split(',').map(Number);
const paths = args.filter((a) => a.startsWith('/'));
const pages = paths.length ? paths : ['/'];
const outDir = path.join(__dirname, 'shots');

const chromePath = CHROME_CANDIDATES.find((p) => fs.existsSync(p));
if (!chromePath) {
    console.error('Chrome not found. Set CHROME_PATH to chrome.exe.');
    process.exit(1);
}
fs.mkdirSync(outDir, { recursive: true });

const PORT = 9333 + Math.floor(Math.random() * 300);
const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'dre-shots-'));
const chrome = spawn(chromePath, [
    '--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check',
    `--user-data-dir=${profile}`, `--remote-debugging-port=${PORT}`, 'about:blank',
], { stdio: 'ignore' });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function browserWs() {
    for (let i = 0; i < 50; i++) {
        try {
            const res = await fetch(`http://127.0.0.1:${PORT}/json/version`);
            return (await res.json()).webSocketDebuggerUrl;
        } catch {
            await sleep(200);
        }
    }
    throw new Error('Chrome did not expose a debugging port.');
}

function connect(url) {
    const ws = new WebSocket(url);
    const pending = new Map();
    const events = [];
    let id = 0;
    ws.addEventListener('message', (e) => {
        const msg = JSON.parse(e.data);
        if (msg.id && pending.has(msg.id)) {
            const { resolve, reject } = pending.get(msg.id);
            pending.delete(msg.id);
            msg.error ? reject(new Error(msg.error.message)) : resolve(msg.result);
        } else if (msg.method) {
            events.push(msg);
        }
    });
    const ready = new Promise((resolve) => ws.addEventListener('open', resolve));
    const send = (method, params = {}, sessionId) => new Promise((resolve, reject) => {
        id += 1;
        pending.set(id, { resolve, reject });
        ws.send(JSON.stringify({ id, method, params, ...(sessionId ? { sessionId } : {}) }));
    });
    const waitFor = async (method, timeout = 15000) => {
        const start = Date.now();
        while (Date.now() - start < timeout) {
            const i = events.findIndex((e) => e.method === method);
            if (i !== -1) { return events.splice(i, 1)[0]; }
            await sleep(50);
        }
        return null;
    };
    return { ws, ready, send, waitFor, close: () => ws.close() };
}

// Reports elements wider than the viewport — the usual cause of a sideways scroll on phones.
const OVERFLOW_PROBE = `(() => {
  const vw = document.documentElement.clientWidth;
  const offenders = [];
  document.querySelectorAll('body *').forEach((el) => {
    const r = el.getBoundingClientRect();
    if (r.width === 0 || getComputedStyle(el).position === 'fixed') return;
    if (el.closest('.hp, .sr-only')) return; // deliberately positioned off-screen
    // Skip anything inside a clipping/scrolling container (carousels, code blocks, tables):
    // it cannot make the page itself scroll sideways.
    for (let p = el.parentElement; p && p !== document.body; p = p.parentElement) {
      if (getComputedStyle(p).overflowX !== 'visible') return;
    }
    if (r.right > vw + 1) {
      offenders.push((el.tagName.toLowerCase() + (el.className && typeof el.className === 'string' ? '.' + el.className.trim().split(/\\s+/).join('.') : '')).slice(0, 70)
        + ' [' + Math.round(r.left) + '→' + Math.round(r.right) + ']');
    }
  });
  return { scrollWidth: document.documentElement.scrollWidth, clientWidth: vw, offenders: offenders.slice(0, 6) };
})()`;

(async () => {
    const { ready, send, waitFor, close } = connect(await browserWs());
    await ready;
    const { targetId } = await send('Target.createTarget', { url: 'about:blank' });
    const { sessionId } = await send('Target.attachToTarget', { targetId, flatten: true });
    await send('Page.enable', {}, sessionId);
    // Always render the current CSS/JS, never a cached copy from an earlier run.
    await send('Network.enable', {}, sessionId);
    await send('Network.setCacheDisabled', { cacheDisabled: true }, sessionId);

    let problems = 0;
    for (const page of pages) {
        for (const width of widths) {
            const mobile = width < 768;
            await send('Emulation.setDeviceMetricsOverride', {
                width, height: mobile ? 844 : 1000, deviceScaleFactor: 1, mobile,
                screenOrientation: { angle: 0, type: 'portraitPrimary' },
            }, sessionId);
            await send('Page.navigate', { url: base + page }, sessionId);
            await waitFor('Page.loadEventFired');
            await sleep(400);

            const { result } = await send('Runtime.evaluate', { expression: OVERFLOW_PROBE, returnByValue: true }, sessionId);
            const { scrollWidth, clientWidth, offenders } = result.value;
            const overflow = scrollWidth > clientWidth + 1;
            if (overflow) { problems += 1; }
            const label = `${page} @ ${width}px`;
            console.log(`${overflow ? 'OVERFLOW' : 'ok      '} ${label.padEnd(42)} scrollWidth ${scrollWidth} / ${clientWidth}`);
            offenders.forEach((o) => console.log(`         · ${o}`));

            const shot = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true }, sessionId);
            const name = (page === '/' ? 'home' : page.replace(/^\/|\/$/g, '').replace(/\//g, '-')) + `-${width}.png`;
            fs.writeFileSync(path.join(outDir, name), Buffer.from(shot.data, 'base64'));
        }
    }

    close();
    chrome.kill();
    try {
        fs.rmSync(profile, { recursive: true, force: true });
    } catch {
        /* Chrome may still hold a handle on Windows; the temp profile is cleaned up by the OS */
    }
    console.log(`\nScreenshots in ${outDir}`);
    process.exit(problems ? 1 : 0);
})().catch((err) => {
    console.error(err);
    chrome.kill();
    process.exit(1);
});
