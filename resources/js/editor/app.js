import { fabric } from 'fabric';
import { initCanvas, getEditorConfig } from './canvas.js';
import { activateGeneralMode } from './modes/general.js';
import { activateBackgroundRemovalMode } from './modes/background-removal.js';
import { activatePassportPhotoMode } from './modes/passport-photo.js';
import { activateResolutionMode } from './modes/resolution.js';


const FABRIC_FILTER_TEXTURE_SIZE = 4096;
fabric.textureSize = FABRIC_FILTER_TEXTURE_SIZE;

const { canvas, canvasWorkspace } = initCanvas('canvas', 'canvasWorkspace');

// Toast notifikačního systému
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    // Stylování dle typu
    const typeStyles = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    };
    
    const bgClass = typeStyles[type] || typeStyles['info'];
    
    toast.className = `${bgClass} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 animate-slide-in`;
    toast.innerHTML = `
        <span>${message}</span>
        <button class="ml-auto text-white hover:opacity-70" data-toast-close>✕</button>
    `;
    
    container.appendChild(toast);
    toast.querySelector('[data-toast-close]')?.addEventListener('click', () => toast.remove());
    
    // Automatické skrytí
    if (duration > 0) {
        setTimeout(() => {
            toast.style.animation = 'fade-out 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// CSS animace (přidáno do style tagu)
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes fade-out {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
`;
document.head.appendChild(style);

// Globální proměnná pro cílový objekt kreslení
let drawContextTarget = null;

// UNDO / REDO 
const HISTORY = {
  undoStack: [],
  redoStack: [],
  isRestoring: false,
  max: 50,
  extraProps: [
    'layer', 'erasable', 'excludeFromExport', 'isGrid',
    'selectable', 'evented'
  ],
};

function isHistoryObject(obj) {
  if (!obj) return false;
  if (obj === eraserCursor) return false;
  if (typeof snapIndicator !== 'undefined' && obj === snapIndicator) return false;
  if (typeof lengthIndicator !== 'undefined' && obj === lengthIndicator) return false;
  if (obj.isGrid) return false;
  if (obj === cropRect) return false;
  return true;
}

function saveHistoryState(reason = '') {
  if (HISTORY.isRestoring) return;
  if (historyBatch) return;

  const json = canvas.toDatalessJSON(HISTORY.extraProps);

  const str = JSON.stringify(json);

  // anti-duplicate: neukládej stejný stav dvakrát po sobě
  const last = HISTORY.undoStack[HISTORY.undoStack.length - 1];
  if (last === str) return;

  HISTORY.undoStack.push(str);
  if (HISTORY.undoStack.length > HISTORY.max) HISTORY.undoStack.shift();

  HISTORY.redoStack.length = 0;

  updateUndoRedoButtons();
}

function cleanupAfterRestore() {
    // skryj indikátory
    if (typeof snapIndicator !== 'undefined' && snapIndicator) snapIndicator.visible = false;
    if (typeof lengthIndicator !== 'undefined' && lengthIndicator) lengthIndicator.visible = false;
    hideEraserCursor?.();

    canvas.getObjects().forEach(o => {
        if (o.isGrid) canvas.remove(o);
    });

    canvas.getObjects().forEach(o => {
        if (o.type === 'line' && o.layer === 'draw') {
            enableLineEndpointsControls(o);
            o.setCoords();
        }

        if (o.type === 'image') {
            applyImageTransformControls(o);
            o.setCoords();
        }
    });

    resetCanvasToWorkspaceSize(false);

    canvas.requestRenderAll();
    updateUndoRedoButtons();
}

function restoreFromString(str) {
  HISTORY.isRestoring = true;

  const json = JSON.parse(str);
  canvas.loadFromJSON(json, () => {
    // Po načtení z historie znovu najdi obrázek a nastav currentImage
    currentImage = canvas.getObjects().find(o => o.type === 'image' && o.layer === 'background')
      || canvas.getObjects().find(o => o.type === 'image')
      || null;
    HISTORY.isRestoring = false;
    requestAnimationFrame(() => {
      cleanupAfterRestore();
      if (currentImage) {
        fitObjectToViewport(currentImage);
      }
      updateUndoRedoButtons();
    });
  }, (o, object) => {

  });
}

function undo() {
  if (HISTORY.undoStack.length <= 1) return; 
  const current = HISTORY.undoStack.pop();
  HISTORY.redoStack.push(current);

  const prev = HISTORY.undoStack[HISTORY.undoStack.length - 1];
  restoreFromString(prev);
}

function redo() {
  if (!HISTORY.redoStack.length) return;
  const next = HISTORY.redoStack.pop();
  HISTORY.undoStack.push(next);
  restoreFromString(next);
}

function updateUndoRedoButtons() {
  const undoBtn = document.getElementById('undoBtn');
  const redoBtn = document.getElementById('redoBtn');
  if (undoBtn) undoBtn.disabled = HISTORY.undoStack.length <= 1;
  if (redoBtn) redoBtn.disabled = HISTORY.redoStack.length === 0;
}


let currentImage = null;

let cropRect = null;
let cropTargetImage = null;
let mode = 'resize';
const MIN_WORKSPACE_WIDTH = 320;
const MIN_WORKSPACE_HEIGHT = 320;
const VIEWPORT_PADDING = 64;
const MIN_ZOOM = 0.05;
const MAX_ZOOM = 8;
let previousAngle = 0;
//kresleni
let isPanning = false;
let isDown = false;
let lastPosX, lastPosY;
let workspaceResizeFrame = null;
let panStateBeforeDrag = {
    selection: true,
    skipTargetFind: false,
};

function getWorkspaceDimensions() {
    const rect = canvasWorkspace?.getBoundingClientRect();
    const fallbackWidth = Math.max(window.innerWidth - 48, MIN_WORKSPACE_WIDTH);
    const fallbackHeight = Math.max(window.innerHeight - 160, MIN_WORKSPACE_HEIGHT);

    return {
        width: Math.max(Math.floor(rect?.width || fallbackWidth), MIN_WORKSPACE_WIDTH),
        height: Math.max(Math.floor(rect?.height || fallbackHeight), MIN_WORKSPACE_HEIGHT),
    };
}

function styleCanvasWrapper() {
    if (!canvas.wrapperEl) return;

    Object.assign(canvas.wrapperEl.style, {
        position: 'absolute',
        inset: '0px',
        width: `${canvas.getWidth()}px`,
        height: `${canvas.getHeight()}px`,
    });
}

function getViewportCenterPoint(width = canvas.getWidth(), height = canvas.getHeight()) {
    const screenCenter = new fabric.Point(width / 2, height / 2);
    return fabric.util.transformPoint(screenCenter, fabric.util.invertTransform(canvas.viewportTransform));
}

function centerViewportOnPoint(point, zoom = canvas.getZoom()) {
    const safeZoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, zoom || 1));
    canvas.setViewportTransform([
        safeZoom,
        0,
        0,
        safeZoom,
        (canvas.getWidth() / 2) - (point.x * safeZoom),
        (canvas.getHeight() / 2) - (point.y * safeZoom),
    ]);
}

function restoreCanvasCursor() {
    if (drawMode === 'eraser') {
        canvas.defaultCursor = 'none';
        canvas.hoverCursor = 'none';
        return;
    }

    if (drawMode && drawMode !== 'select' && drawMode !== 'textSelect') {
        canvas.defaultCursor = 'crosshair';
        canvas.hoverCursor = 'crosshair';
        return;
    }

    canvas.defaultCursor = 'default';
    canvas.hoverCursor = 'move';
}

function refreshFloatingToolbars() {
    const activeObject = canvas.getActiveObject();
    const textToolbarEl = document.getElementById('textToolbar');

    if (
        activeObject &&
        (activeObject.type === 'i-text' || activeObject.type === 'textbox') &&
        textToolbarEl &&
        !textToolbarEl.classList.contains('hidden')
    ) {
        showTextToolbar(activeObject);
        return;
    }

    updateDrawFloatingToolbarVisibility();
}

function resetCanvasToWorkspaceSize(preserveViewport = true) {
    const defaultMode = document.getElementById('photo-editor')?.dataset.defaultMode;
    const workspaceDimensions = getWorkspaceDimensions();
    const width = defaultMode === 'passport' ? 413 : workspaceDimensions.width;
    const height = defaultMode === 'passport' ? 531 : workspaceDimensions.height;
    const viewportCenter = preserveViewport ? getViewportCenterPoint() : null;
    const zoom = canvas.getZoom() || 1;

    canvas.setWidth(width);
    canvas.setHeight(height);
    styleCanvasWrapper();

    if (preserveViewport && viewportCenter) {
        centerViewportOnPoint(viewportCenter, zoom);
    } else {
        canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
    }

    canvas.calcOffset();
    refreshFloatingToolbars();
    canvas.requestRenderAll();
}

function scheduleCanvasResize(preserveViewport = true) {
    if (workspaceResizeFrame) {
        cancelAnimationFrame(workspaceResizeFrame);
    }

    workspaceResizeFrame = requestAnimationFrame(() => {
        workspaceResizeFrame = null;
        resetCanvasToWorkspaceSize(preserveViewport);
    });
}

function startCanvasPan(e) {
    panStateBeforeDrag = {
        selection: canvas.selection,
        skipTargetFind: canvas.skipTargetFind,
    };

    isPanning = true;
    lastPosX = e.clientX;
    lastPosY = e.clientY;
    canvas.selection = false;
    canvas.skipTargetFind = true;
    canvas.defaultCursor = 'grabbing';
    canvas.hoverCursor = 'grabbing';
}

function endCanvasPan() {
    if (!isPanning) return;

    isPanning = false;
    canvas.selection = panStateBeforeDrag.selection;
    canvas.skipTargetFind = panStateBeforeDrag.skipTargetFind;
    restoreCanvasCursor();
    canvas.calcOffset();
    canvas.requestRenderAll();
}

if (canvas.upperCanvasEl) {
    canvas.upperCanvasEl.addEventListener('mousedown', (event) => {
        if (event.button === 1) {
            event.preventDefault();
        }
    });
}

window.addEventListener('mouseup', () => {
    endCanvasPan();
});

window.addEventListener('resize', () => {
    scheduleCanvasResize(true);
});

if (typeof ResizeObserver !== 'undefined' && canvasWorkspace) {
    const workspaceObserver = new ResizeObserver(() => {
        scheduleCanvasResize(true);
    });
    workspaceObserver.observe(canvasWorkspace);
}

requestAnimationFrame(() => {
    resetCanvasToWorkspaceSize(false);
    restoreCanvasCursor();
});

let drawMode = null; // 'line' | 'circle' | 'angle'
let line, circle, rect, triangle, rightTriangle, ellipse, star, heart, arrow, speechBubble, roundedSpeechBubble, roundedRect, curvedArrow, hexagon, cross;
let textBox = null;
let origX, origY;
let lastCreatedObject = null; // Spolehlivá reference na právě vytvořený objekt

let formatClipboard = null;
let isFormatPainterActive = false;

// Vizuální kurzor gumy
let snapIndicator = null;
let lengthIndicator = null;
let historyBatch = false;
let filterHistoryTimer = null;

const SNAP_RADIUS = 12;
let snapLineIsActive = false;
let snapCurrentLine = null;
let lineIsDown = false;

// Pomocná funkce: vypočítá koncové body čáry v canvas souřadnicích
function getLineEndpointCanvasPos(lineObj, which) {
    const p = lineObj.calcLinePoints();
    const matrix = lineObj.calcTransformMatrix();
    
    let localX, localY;
    if (which === 'p1') {
        localX = p.x1;
        localY = p.y1;
    } else {
        localX = p.x2;
        localY = p.y2;
    }
    
    const localPoint = new fabric.Point(localX, localY);
    return fabric.util.transformPoint(localPoint, matrix);
}

// Pomocná funkce: nastaví koncový bod čáry z canvas souřadnic
function setLineEndpointFromCanvasPos(lineObj, which, canvasPos) {
    const otherWhich = which === 'p1' ? 'p2' : 'p1';
    const otherPos = getLineEndpointCanvasPos(lineObj, otherWhich);
    
    let p1, p2;
    if (which === 'p1') {
        p1 = canvasPos;
        p2 = otherPos;
    } else {
        p1 = otherPos;
        p2 = canvasPos;
    }
    
    setLineByCanvasPoints(lineObj, p1, p2);
}

// Pomocná funkce: nastaví čáru podle dvou canvas bodů
function setLineByCanvasPoints(lineObj, p1, p2) {
    const centerX = (p1.x + p2.x) / 2;
    const centerY = (p1.y + p2.y) / 2;
    
    const halfWidth = (p2.x - p1.x) / 2;
    const halfHeight = (p2.y - p1.y) / 2;
    
    lineObj.set({
        x1: -halfWidth,
        y1: -halfHeight,
        x2: halfWidth,
        y2: halfHeight,
        left: centerX,
        top: centerY,
        originX: 'center',
        originY: 'center',
        angle: 0,
        scaleX: 1,
        scaleY: 1
    });
    
    lineObj.setCoords();
}

function transformObjectLocalPoint(obj, x, y) {
    return fabric.util.transformPoint(new fabric.Point(x, y), obj.calcTransformMatrix());
}

function transformObjectPathPoint(obj, x, y) {
    const offsetX = obj.pathOffset ? obj.pathOffset.x : 0;
    const offsetY = obj.pathOffset ? obj.pathOffset.y : 0;
    return transformObjectLocalPoint(obj, x - offsetX, y - offsetY);
}

function pushSnapPoint(points, point) {
    if (!point || !Number.isFinite(point.x) || !Number.isFinite(point.y)) {
        return;
    }

    const alreadyExists = points.some(existing =>
        Math.abs(existing.x - point.x) < 0.5 && Math.abs(existing.y - point.y) < 0.5
    );

    if (!alreadyExists) {
        points.push({ x: point.x, y: point.y });
    }
}

function extractPathSnapPoints(obj) {
    const commands = obj.path || [];
    const points = [];
    let currentX = 0;
    let currentY = 0;
    let startX = 0;
    let startY = 0;

    for (const segment of commands) {
        if (!segment || !segment.length) continue;

        const command = segment[0];
        const lower = command.toLowerCase();
        const isRelative = command === lower;

        const getX = (value) => isRelative ? currentX + value : value;
        const getY = (value) => isRelative ? currentY + value : value;

        if (lower === 'm') {
            currentX = getX(segment[1]);
            currentY = getY(segment[2]);
            startX = currentX;
            startY = currentY;
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 'l') {
            currentX = getX(segment[1]);
            currentY = getY(segment[2]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 'h') {
            currentX = getX(segment[1]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 'v') {
            currentY = getY(segment[1]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 'c') {
            currentX = getX(segment[5]);
            currentY = getY(segment[6]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 's') {
            currentX = getX(segment[3]);
            currentY = getY(segment[4]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 'q') {
            currentX = getX(segment[3]);
            currentY = getY(segment[4]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 't') {
            currentX = getX(segment[1]);
            currentY = getY(segment[2]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 'a') {
            currentX = getX(segment[6]);
            currentY = getY(segment[7]);
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
            continue;
        }

        if (lower === 'z') {
            currentX = startX;
            currentY = startY;
            pushSnapPoint(points, transformObjectPathPoint(obj, currentX, currentY));
        }
    }

    return points;
}

// Najde nejbližší snap bod (koncový bod existující čáry)
function findSnapPoint(pointer) {
    const objects = canvas.getObjects().filter(o => o.layer === 'draw' && o !== snapCurrentLine);
    let closest = null;
    let minDist = SNAP_RADIUS;

    for (const obj of objects) {
        let snapPoints = [];

        // Čáry - 2 koncové body
        if (obj.type === 'line') {
            for (const which of ['p1', 'p2']) {
                const pos = getLineEndpointCanvasPos(obj, which);
                snapPoints.push(pos);
            }
        }
        // Kruhy - 4 body (nahoře, vpravo, dole, vlevo)
        else if (obj.type === 'circle') {
            const center = obj.getCenterPoint();
            const radius = obj.radius * obj.scaleX;
            const angle = obj.angle * Math.PI / 180;

            for (let i = 0; i < 4; i++) {
                const a = angle + (i * Math.PI / 2);
                snapPoints.push({
                    x: center.x + radius * Math.cos(a),
                    y: center.y + radius * Math.sin(a)
                });
            }
        }
        // Obdélníky - 4 rohy
        else if (obj.type === 'rect') {
            const w = obj.width / 2;
            const h = obj.height / 2;

            for (const [lx, ly] of [[-w, -h], [w, -h], [w, h], [-w, h]]) {
                pushSnapPoint(snapPoints, transformObjectLocalPoint(obj, lx, ly));
            }
        }
        // Elipsy - 4 body
        else if (obj.type === 'ellipse') {
            const center = obj.getCenterPoint();
            const rx = obj.rx * obj.scaleX;
            const ry = obj.ry * obj.scaleY;
            const angle = obj.angle * Math.PI / 180;

            for (let i = 0; i < 4; i++) {
                const a = angle + (i * Math.PI / 2);
                snapPoints.push({
                    x: center.x + rx * Math.cos(a),
                    y: center.y + ry * Math.sin(a)
                });
            }
        }
        // Polygony - všechny vrcholy
        else if (obj.type === 'polygon') {
            for (const point of obj.points || []) {
                pushSnapPoint(snapPoints, transformObjectPathPoint(obj, point.x, point.y));
            }
        }
        // Path objekty - skutečné body tvaru z path příkazů
        else if (obj.type === 'path') {
            for (const point of extractPathSnapPoints(obj)) {
                pushSnapPoint(snapPoints, point);
            }
        }
        // Group objekty - 4 body z bounding boxu
        else if (obj.type === 'group') {
            const matrix = obj.calcTransformMatrix();
            const w = obj.width / 2;
            const h = obj.height / 2;

            for (const [lx, ly] of [[0, -h], [w, 0], [0, h], [-w, 0]]) {
                const point = fabric.util.transformPoint({ x: lx, y: ly }, matrix);
                snapPoints.push(point);
            }
        }

        // Najdi nejbližší bod
        for (const pos of snapPoints) {
            const dist = Math.hypot(pointer.x - pos.x, pointer.y - pos.y);
            if (dist < minDist) {
                minDist = dist;
                closest = { x: pos.x, y: pos.y, obj: obj };
            }
        }
    }

    return closest;
}

// Zobrazí výrazný snap indikátor
function showSnapIndicator(pos) {
    if (!snapIndicator) {
        snapIndicator = new fabric.Group([
            new fabric.Circle({
                radius: 11,
                fill: 'rgba(255, 255, 255, 0.14)',
                stroke: 'rgba(255, 255, 255, 0.95)',
                strokeWidth: 3,
                originX: 'center',
                originY: 'center'
            }),
            new fabric.Circle({
                radius: 7,
                fill: 'rgba(16, 185, 129, 0.35)',
                stroke: '#10b981',
                strokeWidth: 3,
                originX: 'center',
                originY: 'center'
            }),
            new fabric.Circle({
                radius: 2.5,
                fill: '#ecfdf5',
                originX: 'center',
                originY: 'center'
            })
        ], {
            selectable: false,
            evented: false,
            excludeFromExport: true,
            originX: 'center',
            originY: 'center',
            objectCaching: false,
            shadow: new fabric.Shadow({
                color: 'rgba(16, 185, 129, 0.65)',
                blur: 16,
                offsetX: 0,
                offsetY: 0,
            })
        });
        canvas.add(snapIndicator);
    }
    
    snapIndicator.set({ left: pos.x, top: pos.y, visible: true });
    snapIndicator.bringToFront();
}

// Skryje snap indikátor
function hideSnapIndicator() {
    if (snapIndicator) {
        snapIndicator.visible = false;
    }
}

// Zobrazí HUD s délkou a úhlem
function showLineHUD(p1, p2, mouseEvent) {
    const hudEl = document.getElementById('lineHUD');
    const textEl = document.getElementById('lineHUDText');
    
    const dx = p2.x - p1.x;
    const dy = p2.y - p1.y;
    const length = Math.round(Math.hypot(dx, dy));
    const angleRad = Math.atan2(dy, dx);
    let angleDeg = Math.round(angleRad * 180 / Math.PI);
    if (angleDeg < 0) angleDeg += 360;
    
    textEl.textContent = `${length} px | ${angleDeg}°`;
    
    hudEl.style.left = (mouseEvent.clientX + 15) + 'px';
    hudEl.style.top = (mouseEvent.clientY + 15) + 'px';
    hudEl.classList.remove('hidden');
}

// Skryje HUD
function hideLineHUD() {
    document.getElementById('lineHUD').classList.add('hidden');
}

// Vytvoří vlastní controls pro koncové body čáry
function enableLineEndpointsControls(lineObj) {
    lineObj.controls = {};
    
    // P1 control
    lineObj.controls.p1 = new fabric.Control({
        positionHandler: function(dim, finalMatrix, fabricObject) {
            const p = fabricObject.calcLinePoints();
            const point = new fabric.Point(p.x1, p.y1);
            return fabric.util.transformPoint(point, finalMatrix);
        },
        actionHandler: function(eventData, transform, x, y) {
            const target = transform.target;
            const newPos = { x, y };
            setLineEndpointFromCanvasPos(target, 'p1', newPos);
            return true;
        },
        actionName: 'moveP1',
        cursorStyle: 'crosshair',
        render: function(ctx, left, top, styleOverride, fabricObject) {
            ctx.save();
            ctx.translate(left, top);
            ctx.beginPath();
            ctx.arc(0, 0, 6, 0, Math.PI * 2);
            ctx.fillStyle = '#3b82f6';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();
            ctx.restore();
        }
    });
    
    // P2 control
    lineObj.controls.p2 = new fabric.Control({
        positionHandler: function(dim, finalMatrix, fabricObject) {
            const p = fabricObject.calcLinePoints();
            const point = new fabric.Point(p.x2, p.y2);
            return fabric.util.transformPoint(point, finalMatrix);
        },
        actionHandler: function(eventData, transform, x, y) {
            const target = transform.target;
            const newPos = { x, y };
            setLineEndpointFromCanvasPos(target, 'p2', newPos);
            return true;
        },
        actionName: 'moveP2',
        cursorStyle: 'crosshair',
        render: function(ctx, left, top, styleOverride, fabricObject) {
            ctx.save();
            ctx.translate(left, top);
            ctx.beginPath();
            ctx.arc(0, 0, 6, 0, Math.PI * 2);
            ctx.fillStyle = '#3b82f6';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();
            ctx.restore();
        }
    });
    
    // Zakázat standardní transformace
    lineObj.lockScalingX = true;
    lineObj.lockScalingY = true;
    lineObj.lockRotation = true;
    lineObj.hasRotatingPoint = false;
    lineObj.hasBorders = false;
}

// Sledování aktuálně editované čáry (pro endpoint controls)
let currentEditingLine = null;

// Vypne endpoint controls a obnoví standardní controls
function disableLineEndpointsControls(lineObj) {
    if (!lineObj || lineObj.type !== 'line') return;
    
    // Obnovit standardní controls
    lineObj.controls = fabric.Line.prototype.controls;
    lineObj.lockScalingX = false;
    lineObj.lockScalingY = false;
    lineObj.lockRotation = false;
    lineObj.hasRotatingPoint = true;
    lineObj.hasBorders = true;
    lineObj.setCoords();
}

// Vypne endpoint controls na všech čárách
function disableAllLineEndpoints() {
    canvas.getObjects('line').forEach(line => {
        disableLineEndpointsControls(line);
    });
    currentEditingLine = null;
}

const defaultFabricObjectControls = fabric.Object.prototype.controls || {};

function isBlankImageObject(obj) {
    const source = obj?._element?.src || obj?._originalElement?.src || '';
    return source.includes('blank_');
}

function renderImageRotateControl(ctx, left, top, iconRotation = 0) {
    ctx.save();
    ctx.translate(left, top);

    ctx.beginPath();
    ctx.arc(0, 0, 7.2, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(255, 255, 255, 0.96)';
    ctx.fill();
    ctx.strokeStyle = '#2563eb';
    ctx.lineWidth = 1.8;
    ctx.stroke();

    ctx.save();
    ctx.rotate((iconRotation * Math.PI) / 180);

    const iconRadius = 3.8;
    const startAngle = Math.PI * 0.18;
    const endAngle = Math.PI * 1.32;

    const drawArrowHead = (angle, directionAngle) => {
        const x = Math.cos(angle) * iconRadius;
        const y = Math.sin(angle) * iconRadius;
        const headLength = 1.9;
        const wingAngle = Math.PI / 5;

        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.lineTo(
            x - Math.cos(directionAngle - wingAngle) * headLength,
            y - Math.sin(directionAngle - wingAngle) * headLength
        );
        ctx.moveTo(x, y);
        ctx.lineTo(
            x - Math.cos(directionAngle + wingAngle) * headLength,
            y - Math.sin(directionAngle + wingAngle) * headLength
        );
        ctx.stroke();
    };

    ctx.beginPath();
    ctx.strokeStyle = '#2563eb';
    ctx.lineWidth = 1.55;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.arc(0, 0, iconRadius, startAngle, endAngle);
    ctx.stroke();

    drawArrowHead(startAngle, startAngle - Math.PI / 2);
    drawArrowHead(endAngle, endAngle + Math.PI / 2);

    ctx.restore();

    ctx.restore();
}

function createImageRotateCornerControl(x, y, offsetX, offsetY, iconRotation = 0) {
    const defaultRotateControl = defaultFabricObjectControls.mtr;

    return new fabric.Control({
        x,
        y,
        offsetX,
        offsetY,
        actionHandler: defaultRotateControl?.actionHandler,
        cursorStyleHandler: defaultRotateControl?.cursorStyleHandler,
        cursorStyle: 'crosshair',
        actionName: defaultRotateControl?.actionName || 'rotate',
        render: function(ctx, left, top) {
            renderImageRotateControl(ctx, left, top, iconRotation);
        },
        withConnection: false,
        sizeX: 14,
        sizeY: 14,
        touchSizeX: 22,
        touchSizeY: 22,
    });
}

function applyImageTransformControls(imageObj) {
    if (!imageObj || imageObj.type !== 'image' || isBlankImageObject(imageObj)) return;

    imageObj.controls = {
        tl: defaultFabricObjectControls.tl,
        tr: defaultFabricObjectControls.tr,
        bl: defaultFabricObjectControls.bl,
        br: defaultFabricObjectControls.br,
        mt: defaultFabricObjectControls.mt,
        mb: defaultFabricObjectControls.mb,
        ml: defaultFabricObjectControls.ml,
        mr: defaultFabricObjectControls.mr,
        rtl: createImageRotateCornerControl(-0.5, -0.5, -22, -22, 90),
        rtr: createImageRotateCornerControl(0.5, -0.5, 22, -22, 180),
        rbr: createImageRotateCornerControl(0.5, 0.5, 22, 22, 270),
        rbl: createImageRotateCornerControl(-0.5, 0.5, -22, 22, 0),
    };

    imageObj.set({
        transparentCorners: false,
        cornerStyle: 'circle',
        cornerColor: '#ffffff',
        cornerStrokeColor: '#2563eb',
        cornerSize: 12,
        padding: 4,
        hasRotatingPoint: false,
    });

    imageObj.setCoords();
}

function scheduleFilterHistory() {
    clearTimeout(filterHistoryTimer);
    filterHistoryTimer = setTimeout(() => {
        saveHistoryState('filters');
    }, 300);
}

let textHistoryTimer = null;
function scheduleTextHistory() {
    clearTimeout(textHistoryTimer);
    textHistoryTimer = setTimeout(() => {
        saveHistoryState('text-style');
    }, 400);
}

let eraserCursor = null;
let ERASER_RADIUS = 20;

function showEraserCursor(pointer) {
    if (!eraserCursor) {
        eraserCursor = new fabric.Circle({
            radius: ERASER_RADIUS,
            fill: 'rgba(255, 255, 255, 1)',
            stroke: 'red',
            strokeWidth: 1,
            selectable: false,
            evented: false,
            excludeFromExport: true,
            originX: 'center',
            originY: 'center'
        });
        canvas.add(eraserCursor);
    }
    eraserCursor.set({ left: pointer.x, top: pointer.y, visible: true });
    eraserCursor.bringToFront();
    canvas.requestRenderAll();
}

function hideEraserCursor() {
    if (eraserCursor) {
        eraserCursor.visible = false;
        canvas.requestRenderAll();
    }
}

let snapLineStartPoint = null;

function snapLineMouseDown(e) {
    if (!snapLineIsActive) return;
    
    const pointer = canvas.getPointer(e.e);
    const snap = findSnapPoint(pointer);
    
    snapLineStartPoint = snap ? { x: snap.x, y: snap.y } : { x: pointer.x, y: pointer.y };
    if (snap) {
        showSnapIndicator(snapLineStartPoint);
    } else {
        hideSnapIndicator();
    }
    
    const width = parseInt(document.getElementById('brushWidth').value);
    
    snapCurrentLine = new fabric.Line([0, 0, 0, 0], {
        stroke: getStrokeColor(),
        strokeWidth: width,
        strokeDashArray: getDashFromUIForWidth(width),
        strokeLineCap: document.getElementById('strokeCap').value,
        strokeUniform: true,
        originX: 'center',
        originY: 'center',
        selectable: false,
        evented: false,
        layer: 'draw'
    });
    
    setLineByCanvasPoints(snapCurrentLine, snapLineStartPoint, snapLineStartPoint);
    canvas.add(snapCurrentLine);
    
    lineIsDown = true;
    historyBatch = true;
}

function snapLineMouseMove(e) {
    if (!snapLineIsActive) return;
    
    const pointer = canvas.getPointer(e.e);
    const snap = findSnapPoint(pointer);
    
    if (snap) {
        showSnapIndicator(snap);
    } else {
        hideSnapIndicator();
    }
    
    if (!lineIsDown || !snapCurrentLine) return;
    
    const endPoint = snap ? { x: snap.x, y: snap.y } : { x: pointer.x, y: pointer.y };
    
    setLineByCanvasPoints(snapCurrentLine, snapLineStartPoint, endPoint);
    
    showLineHUD(snapLineStartPoint, endPoint, e.e);
    
    canvas.requestRenderAll();
}

function snapLineMouseUp(e) {
    if (!snapLineIsActive || !lineIsDown) return;
    
    const pointer = canvas.getPointer(e.e);
    const snap = findSnapPoint(pointer);
    const endPoint = snap ? { x: snap.x, y: snap.y } : { x: pointer.x, y: pointer.y };
    
    // Zkontroluj zda je čára dostatečně dlouhá
    const dist = Math.hypot(endPoint.x - snapLineStartPoint.x, endPoint.y - snapLineStartPoint.y);
    
    if (dist < 5) {
        canvas.remove(snapCurrentLine);
    } else {
        setLineByCanvasPoints(snapCurrentLine, snapLineStartPoint, endPoint);
        
        // Během aktivního nástroje nechej čáru nevybíratelnou
        // (aby šlo kreslit další čáry a napojovat se)
        snapCurrentLine.set({
            selectable: false,
            evented: false
        });
        // Endpoint controls se aktivují až na double-click
    }
    
    hideSnapIndicator();
    hideLineHUD();
    
    lineIsDown = false;
    historyBatch = false;
    snapCurrentLine = null;
    snapLineStartPoint = null;
    
    saveHistoryState('draw-line');
    canvas.requestRenderAll();
}

function activateSnapLineTool() {
    // Deaktivuj jiné režimy
    deactivateSnapLineTool();
    
    snapLineIsActive = true;
    drawMode = null;
    canvas.isDrawingMode = false;
    canvas.selection = false;
    
    lockImage(true);
    
    canvas.defaultCursor = 'crosshair';
    canvas.hoverCursor = 'crosshair';
    
    // Zakázat výběr objektů během kreslení
    canvas.getObjects().forEach(obj => {
        if (obj !== snapIndicator) {
            obj.selectable = false;
            obj.evented = false;
        }
    });
    
    canvas.discardActiveObject();
    canvas.requestRenderAll();
    updateDrawFloatingToolbarVisibility();
}

function deactivateSnapLineTool() {
    if (!snapLineIsActive) return;
    
    snapLineIsActive = false;
    
    hideSnapIndicator();
    hideLineHUD();
    
    if (snapCurrentLine && lineIsDown) {
        canvas.remove(snapCurrentLine);
    }
    
    // Obnov vybíratelnost všech čar
    canvas.getObjects().forEach(obj => {
        if (obj.type === 'line' && obj.layer === 'draw') {
            obj.selectable = true;
            obj.evented = true;
        }
    });
    
    snapCurrentLine = null;
    snapLineStartPoint = null;
    lineIsDown = false;
    historyBatch = false;
    updateDrawFloatingToolbarVisibility();
}


function setDrawMode(newMode, button) {
    drawMode = newMode;
    canvas.isDrawingMode = (newMode === 'brush');
    
    // Při přepnutí nástroje vypnout endpoint controls na čárách
    disableAllLineEndpoints();
    
    if (newMode !== 'eraser') {
        hideEraserCursor();
    }
    
    if (newMode === 'brush') {
        canvas.freeDrawingBrush.width = parseInt(document.getElementById('brushWidth').value, 10);
        canvas.freeDrawingBrush.color = document.getElementById('drawColor').value;
    } else if (newMode === 'eraser') {
        ERASER_RADIUS = parseInt(document.getElementById('eraserSize').value, 10);
        if(eraserCursor) eraserCursor.set('radius', ERASER_RADIUS);
    }

    if (!newMode || newMode === 'select' || newMode === 'textSelect') {
        const drawToolbar = document.getElementById('drawFloatingToolbar');
        if (drawToolbar) drawToolbar.classList.add('hidden');
    }

    if (newMode && newMode !== 'select' && newMode !== 'textSelect') {
        canvas.selection = false;
        lockImage(true);
        canvas.discardActiveObject();
        canvas.defaultCursor = 'crosshair';
        canvas.hoverCursor = 'crosshair';
        // Při kreslení VŠECHNY objekty neselectable
        canvas.getObjects().forEach(obj => {
            obj.selectable = false;
            obj.evented = false;
        });
    } else {
        canvas.selection = true;
        lockImage(false);
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        canvas.getObjects().forEach(obj => {
            const isBlank = obj._element?.src?.includes('blank_');
            let canSelect = false;
            if (newMode === 'select') {
                canSelect = !isBlank && (obj.layer === 'draw' || obj.layer === 'image');
            } else if (newMode === 'textSelect') {
                canSelect = obj.layer === 'text';
            }
            obj.selectable = canSelect;
            obj.evented = canSelect;
        });
    }

    setActiveTool(button);
    updateDrawFloatingToolbarVisibility();
}

// Aktualizace viditelnosti a hodnot plovoucího toolbaru
function updateDrawFloatingToolbarVisibility(targetOverride = null) {
    const el = document.getElementById('drawFloatingToolbar');
    if (!el) return;
    
    const activeObj = targetOverride || canvas.getActiveObject();
    const hasDrawSelection = activeObj && (activeObj.layer === 'draw' || 
        (activeObj.type === 'activeSelection' && activeObj.getObjects().some(o => o.layer === 'draw')));
    const shouldShow = (drawMode !== null && drawMode !== 'select' && drawMode !== 'textSelect') || 
                       canvas.isDrawingMode === true || 
                       hasDrawSelection;
    
    if (shouldShow) {
        if (activeObj && (activeObj.layer === 'draw' || activeObj.type === 'activeSelection')) {
            positionFixedOverlayNearObject(el, activeObj, { gap: 14 });
        } else {
            const rect = canvas.upperCanvasEl.getBoundingClientRect();
            positionFixedOverlay(el, rect.left + 12, rect.top + 12, 12);
        }
        el.classList.remove('hidden');

        if (activeObj && activeObj.layer === 'draw') {
            drawContextTarget = activeObj;  // Nastav cílový objekt
            if (activeObj.stroke) document.getElementById('drawFloatingStrokeColor').value = activeObj.stroke;
            if (activeObj.fill && activeObj.fill !== 'transparent') {
                document.getElementById('drawFloatingFillColor').value = activeObj.fill;
                document.getElementById('drawFloatingTransparent').checked = false;
            } else {
                document.getElementById('drawFloatingTransparent').checked = true;
            }
            if (activeObj.strokeWidth) document.getElementById('drawFloatingStrokeWidth').value = activeObj.strokeWidth;
            if (activeObj.strokeLineCap) document.getElementById('drawFloatingStrokeCap').value = activeObj.strokeLineCap;
            // Sync dash style
            const dash = activeObj.strokeDashArray;
            if (!dash || dash.length === 0) {
                document.getElementById('drawFloatingStrokeStyle').value = 'solid';
            } else if (dash[0] > dash[1]) {
                document.getElementById('drawFloatingStrokeStyle').value = 'dashed';
            } else {
                document.getElementById('drawFloatingStrokeStyle').value = 'dotted';
            }
        } else if (activeObj && activeObj.type === 'activeSelection') {
            // Pro multi-selection nastav target jako activeSelection
            drawContextTarget = activeObj;
            // Použij hodnoty z prvního draw objektu ve výběru
            const firstDraw = activeObj.getObjects().find(o => o.layer === 'draw');
            if (firstDraw) {
                if (firstDraw.stroke) document.getElementById('drawFloatingStrokeColor').value = firstDraw.stroke;
                if (firstDraw.fill && firstDraw.fill !== 'transparent') {
                    document.getElementById('drawFloatingFillColor').value = firstDraw.fill;
                    document.getElementById('drawFloatingTransparent').checked = false;
                } else {
                    document.getElementById('drawFloatingTransparent').checked = true;
                }
                if (firstDraw.strokeWidth) document.getElementById('drawFloatingStrokeWidth').value = firstDraw.strokeWidth;
                if (firstDraw.strokeLineCap) document.getElementById('drawFloatingStrokeCap').value = firstDraw.strokeLineCap;
                const dash = firstDraw.strokeDashArray;
                if (!dash || dash.length === 0) {
                    document.getElementById('drawFloatingStrokeStyle').value = 'solid';
                } else if (dash[0] > dash[1]) {
                    document.getElementById('drawFloatingStrokeStyle').value = 'dashed';
                } else {
                    document.getElementById('drawFloatingStrokeStyle').value = 'dotted';
                }
            }
        } else {
            const strokeColor = document.getElementById('drawColor')?.value || '#000000';
            const fillColor = document.getElementById('fillColor')?.value || 'transparent';
            document.getElementById('drawFloatingStrokeColor').value = strokeColor;
            document.getElementById('drawFloatingFillColor').value = fillColor === 'transparent' ? '#ffffff' : fillColor;
            document.getElementById('drawFloatingStrokeWidth').value = document.getElementById('brushWidth')?.value || 3;
            document.getElementById('drawFloatingStrokeStyle').value = document.getElementById('strokeStyle')?.value || 'solid';
            document.getElementById('drawFloatingStrokeCap').value = document.getElementById('strokeCap')?.value || 'butt';
            document.getElementById('drawFloatingTransparent').checked = (fillColor === 'transparent');
            drawContextTarget = null;  // Není vybraný konkrétní objekt
        }
    } else {
        el.classList.add('hidden');
        drawContextTarget = null;  // Reset při skrytí
    }
}

// Globální funkce pro aplikaci vlastností na kreslený objekt
function applyToDrawTarget(props) {
    const target = drawContextTarget || canvas.getActiveObject();
    if (!target) return;
    
    function applyPropsRecursive(obj) {
        if (!obj) return;
        obj.set(props);
        obj.dirty = true;
        if (obj._objects && obj._objects.length) {
            obj._objects.forEach(child => applyPropsRecursive(child));
        }
    }
    
    if (target.type === 'activeSelection') {
        target.getObjects().forEach(obj => {
            if (obj.layer === 'draw' || obj.type === 'path' || obj.type === 'group') {
                applyPropsRecursive(obj);
            }
        });
    } else {
        applyPropsRecursive(target);
    }
    
    if (target.setCoords) target.setCoords();
    canvas.requestRenderAll();
    saveHistoryState('draw-style');
}

document.addEventListener('DOMContentLoaded', () => {
    const sc = document.getElementById('drawFloatingStrokeColor');
    if (sc) sc.addEventListener('input', (e) => {
        const v = e.target.value;
        const drawColorEl = document.getElementById('drawColor');
        if (drawColorEl) drawColorEl.value = v;
        if (canvas.freeDrawingBrush) canvas.freeDrawingBrush.color = v;
        applyToDrawTarget({ stroke: v });
    });

    const fw = document.getElementById('drawFloatingStrokeWidth');
    if (fw) fw.addEventListener('input', (e) => {
        const v = parseInt(e.target.value, 10) || 1;
        const brushWidthEl = document.getElementById('brushWidth');
        if (brushWidthEl) brushWidthEl.value = v;
        if (canvas.freeDrawingBrush) canvas.freeDrawingBrush.width = v;
        applyToDrawTarget({ strokeWidth: v });
    });

    const styleEl = document.getElementById('drawFloatingStrokeStyle');
    if (styleEl) styleEl.addEventListener('change', (e) => {
        const v = e.target.value;
        document.getElementById('strokeStyle').value = v;
        const target = drawContextTarget || canvas.getActiveObject();
        if (target) {
            let dash = null;
            const widthVal = target.strokeWidth || 1;
            if (v === 'dashed') dash = [widthVal * 2, widthVal];
            if (v === 'dotted') dash = [widthVal, widthVal * 1.5];
            applyToDrawTarget({ strokeDashArray: dash });
        }
    });

    const capEl = document.getElementById('drawFloatingStrokeCap');
    if (capEl) capEl.addEventListener('change', (e) => {
        const v = e.target.value;
        document.getElementById('strokeCap').value = v;
        applyToDrawTarget({ strokeLineCap: v });
    });
    
    const fillEl = document.getElementById('drawFloatingFillColor');
    if (fillEl) fillEl.addEventListener('input', (e) => {
        const v = e.target.value;
        document.getElementById('fillColor').value = v;
        document.getElementById('fillTransparent').checked = false;
        applyToDrawTarget({ fill: v });
    });
    
    const transEl = document.getElementById('drawFloatingTransparent');
    if (transEl) transEl.addEventListener('change', (e) => {
        const checked = e.target.checked;
        document.getElementById('fillTransparent').checked = checked;
        const fillVal = checked ? 'transparent' : (document.getElementById('drawFloatingFillColor')?.value || '#ffffff');
        applyToDrawTarget({ fill: fillVal });
    });
});

function isAltConstrainedShapeMode(mode) {
    return ['rect', 'roundedRect', 'ellipse', 'circle'].includes(mode);
}

function getConstrainedDrawDelta(pointer, startX, startY, shouldConstrain) {
    const deltaX = pointer.x - startX;
    const deltaY = pointer.y - startY;

    if (!shouldConstrain) {
        return {
            width: deltaX,
            height: deltaY,
        };
    }

    const size = Math.max(Math.abs(deltaX), Math.abs(deltaY));
    return {
        width: deltaX < 0 ? -size : size,
        height: deltaY < 0 ? -size : size,
    };
}

canvas.on('mouse:down', (o) => {
    if (isFormatPainterActive && o.target) {
        const target = o.target;
        if (target && formatClipboard) {
            const propsToApply = {};
            for (const key in formatClipboard) {
                if (target.hasOwnProperty(key) && formatClipboard[key] !== undefined) {
                    propsToApply[key] = formatClipboard[key];
                }
            }
            target.set(propsToApply);

            if (target.type === 'i-text' || target.type === 'textbox') {
                 updateTextControlsUI(target);
            }
            if (target.layer === 'draw') {
                syncDrawingControls(target);
            }

            canvas.requestRenderAll();
            saveHistoryState('format-paste');
        }
        
        isFormatPainterActive = false;
        formatClipboard = null;
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        document.getElementById('copyFormatBtn').classList.remove('bg-green-500', 'text-white');
        
        // Zastav zpracování dalšího kreslení
        o.e.stopPropagation();
        o.e.preventDefault();
        return;
    }

    const e = o.e;
    const pointer = canvas.getPointer(e);
    const activeObject = canvas.getActiveObject();
    const isEditingText = activeObject &&
        (activeObject.type === 'i-text' || activeObject.type === 'textbox') &&
        activeObject.isEditing;
    const usesAltShapeConstraint = e.altKey && isAltConstrainedShapeMode(drawMode);

    if (((e.altKey && !usesAltShapeConstraint) || e.button === 1) && !isEditingText) {
        e.preventDefault();
        startCanvasPan(e);
        return;
    }

    if (snapLineIsActive) {
        snapLineMouseDown(o);
        return;
    }

    if (drawMode === 'eraser') {
        isDown = true;
        historyBatch = true;
        eraseAtPoint(pointer);
        return;
    }

    if (drawMode === 'textBox') {
        isDown = true;
        origX = pointer.x;
        origY = pointer.y;
        textBox = new fabric.Rect({
            left: origX,
            top: origY,
            width: 1,
            height: 1,
            fill: 'rgba(255, 105, 180, 0.3)',
            stroke: 'hotpink',
            strokeWidth: 1,
            strokeDashArray: [5, 5],
            selectable: false,
            evented: false,
            excludeFromExport: true,
        });
        canvas.add(textBox);
        return;
    }

    // Kreslení - ale ne když je režim 'select' nebo 'textSelect'
    if (drawMode && drawMode !== 'select' && drawMode !== 'textSelect') {
        isDown = true;
        historyBatch = true;

        // Snapping při začátku kreslení
        const snap = findSnapPoint(pointer);
        if (snap) {
            origX = snap.x;
            origY = snap.y;
            showSnapIndicator(snap);
        } else {
            origX = pointer.x;
            origY = pointer.y;
        }

        const width = parseInt(document.getElementById('brushWidth').value);

        if (drawMode === 'circle') {
            circle = new fabric.Circle({
                left: origX,
                top: origY,
                originX: 'left',
                originY: 'top',
                radius: 1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(circle);
            lastCreatedObject = circle;
        }

        if (drawMode === 'rect') {
            rect = new fabric.Rect({
                left: origX,
                top: origY,
                width: 1,
                height: 1,
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(rect);
            lastCreatedObject = rect;
        }

        if (drawMode === 'triangle') {
            triangle = new fabric.Polygon([
                { x: 0.5, y: 0 }, { x: 1, y: 1 }, { x: 0, y: 1 }
            ], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(triangle);
            lastCreatedObject = triangle;
        }

        if (drawMode === 'rightTriangle') {
             rightTriangle = new fabric.Polygon([
                { x: 0, y: 0 }, { x: 1, y: 1 }, { x: 0, y: 1 }
            ], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(rightTriangle);
            lastCreatedObject = rightTriangle;
        }

        if (drawMode === 'ellipse') {
            ellipse = new fabric.Ellipse({
                left: origX, top: origY, rx: 1, ry: 1, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(ellipse);
            lastCreatedObject = ellipse;
        }

        if (drawMode === 'star') {
            star = new fabric.Polygon([], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(star);
            lastCreatedObject = star;
        }

        if (drawMode === 'heart') {
            const path = 'M -10 -2.5 a 5.5 5.5 0 0 1 9.591 -3.676 a 0.56 0.56 0 0 0 0.818 0 A 5.49 5.49 0 0 1 10 -2.5 c 0 2.29 -1.5 4 -3 5.5 l -5.492 5.313 a 2 2 0 0 1 -3 0.019 L -7 3 c -1.5 -1.5 -3 -3.2 -3 -5.5';
            heart = new fabric.Path(path, {
                left: origX,
                top: origY,
                originX: 'center',
                originY: 'center',
                fill: getFillColor(),
                stroke: getStrokeColor(),
                strokeWidth: width,
                strokeUniform: true,
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
                selectable: false,
                evented: false,
                layer: 'draw'
            });
            canvas.add(heart);
            lastCreatedObject = heart;
        }

        if (drawMode === 'arrow') {
            arrow = new fabric.Polygon([
                { x: 0, y: 5 }, { x: 15, y: 5 }, { x: 15, y: 0 }, { x: 20, y: 7.5 }, { x: 15, y: 15 }, { x: 15, y: 10 }, { x: 0, y: 10 }
            ], {
                left: origX, top: origY, scaleX: 0.1, scaleY: 0.1, fill: getFillColor(), stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(arrow);
            lastCreatedObject = arrow;
        }

        if (drawMode === 'speechBubble') {
            speechBubble = new fabric.Polygon([
                { x: 0, y: 0 }, { x: 100, y: 0 }, { x: 100, y: 70 }, { x: 20, y: 70 }, { x: 10, y: 90 }, { x: 15, y: 70 }, { x: 0, y: 70 }
            ], {
                left: origX, top: origY, scaleX: 0.1, scaleY: 0.1, fill: getFillColor(), stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(speechBubble);
            lastCreatedObject = speechBubble;
        }

        if (drawMode === 'roundedSpeechBubble') {
            const path = 'M4,5 C4,3.89543 4.89543,3 6,3 H18 C19.1046,3 20,3.89543 20,5 V15 C20,16.1046 19.1046,17 18,17 H6L4,19V5Z';
            roundedSpeechBubble = new fabric.Path(path, {
                left: origX, top: origY, scaleX: 0.1, scaleY: 0.1, fill: getFillColor(), stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(roundedSpeechBubble);
            lastCreatedObject = roundedSpeechBubble;
        }

        if (drawMode === 'roundedRect') {
            roundedRect = new fabric.Rect({
                left: origX, top: origY, width: 1, height: 1, rx: 10, ry: 10, fill: getFillColor(),
                stroke: getStrokeColor(), strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width),
                strokeUniform: true, strokeLineCap: document.getElementById('strokeCap').value,
                selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(roundedRect);
            lastCreatedObject = roundedRect;
        }

        if (drawMode === 'arrowRight') {
            const path = 'M 0,10 Q 10,10 10,0 M 10,0 L 5,5 M 10,0 L 15,5';
            curvedArrow = new fabric.Path(path, {
                left: origX, top: origY, scaleX: 1, scaleY: 1, fill: 'transparent', stroke: getStrokeColor(),
                strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(curvedArrow);
            lastCreatedObject = curvedArrow;
        }

        if (drawMode === 'hexagon') {
            hexagon = new fabric.Polygon([], {
                left: origX, top: origY, fill: getFillColor(), stroke: getStrokeColor(), strokeWidth: width,
                strokeDashArray: getDashFromUIForWidth(width), strokeUniform: true,
                strokeLineCap: document.getElementById('strokeCap').value, selectable: false, evented: false, layer: 'draw'
            });
            canvas.add(hexagon);
            lastCreatedObject = hexagon;
        }

        if (drawMode === 'cross') {
            cross = new fabric.Group([
                new fabric.Line([0, 0, 100, 100], { stroke: getStrokeColor(), strokeWidth: width, strokeLineCap: 'round' }),
                new fabric.Line([100, 0, 0, 100], { stroke: getStrokeColor(), strokeWidth: width, strokeLineCap: 'round' })
            ], {
                left: origX,
                top: origY,
                scaleX: 0.01,
                scaleY: 0.01,
                selectable: false,
                evented: false,
                layer: 'draw',
                strokeUniform: true
            });
            canvas.add(cross);
            lastCreatedObject = cross;
        }

        return;
    }
});
// Kreslení tužkou
canvas.on('path:created', function (e) {
    e.path.set({
        layer: 'draw',
        erasable: true,
        selectable: false,
        evented: false,
        objectCaching: false
    });
});


canvas.on('mouse:move', (o) => {
    const e = o.e;
    if (isPanning) {
        const vpt = canvas.viewportTransform;
        vpt[4] += e.clientX - lastPosX;
        vpt[5] += e.clientY - lastPosY;
        canvas.requestRenderAll();

        lastPosX = e.clientX;
        lastPosY = e.clientY;
        return;
    }

    const pointer = canvas.getPointer(e);

    if (snapLineIsActive) {
        snapLineMouseMove(o);
        return;
    }

    // Zobrazit snap indikátor u všech nástrojů (ale ne v režimu select/textSelect)
    if (drawMode && drawMode !== 'eraser' && drawMode !== 'textBox' && drawMode !== 'select' && drawMode !== 'textSelect') {
        const snap = findSnapPoint(pointer);
        if (snap) {
            showSnapIndicator(snap);
        } else {
            hideSnapIndicator();
        }
    }

    if (drawMode === 'textBox' && isDown) {
        const width = pointer.x - origX;
        const height = pointer.y - origY;

        textBox.set({
            width: Math.abs(width),
            height: Math.abs(height),
            left: width < 0 ? pointer.x : origX,
            top: height < 0 ? pointer.y : origY
        });
        canvas.requestRenderAll();
        return;
    }

    if (drawMode === 'eraser') {
        showEraserCursor(pointer);
        canvas.defaultCursor = 'none';
        canvas.hoverCursor = 'none';
    }
    
    if (drawMode === 'eraser' && isDown) {
        eraseAtPoint(pointer);
        return;
    }

    if (drawMode && isDown) {
        const shouldConstrainShape = e.altKey && isAltConstrainedShapeMode(drawMode);

        if (drawMode === 'circle') {
            const radius = Math.sqrt(Math.pow(origX - pointer.x, 2) + Math.pow(origY - pointer.y, 2));
            circle.set({ radius });
        }
        
        if (drawMode === 'rect') {
            const { width, height } = getConstrainedDrawDelta(pointer, origX, origY, shouldConstrainShape);
            rect.set({
                width: Math.abs(width), height: Math.abs(height),
                left: width < 0 ? origX + width : origX,
                top: height < 0 ? origY + height : origY
            });
        }
        
        if (drawMode === 'triangle') {
            const w = pointer.x - origX;
            const h = pointer.y - origY;
            const absW = Math.abs(w);
            const absH = Math.abs(h);
            triangle.set({
                points: [ { x: absW / 2, y: 0 }, { x: absW, y: absH }, { x: 0, y: absH } ]
            });
            triangle._setPositionDimensions({});
            triangle.set({
                left: (w < 0 ? pointer.x : origX) + triangle.width / 2,
                top: (h < 0 ? pointer.y : origY) + triangle.height / 2
            });
            triangle.setCoords();
        }

        if (drawMode === 'rightTriangle') {
            const w = pointer.x - origX;
            const h = pointer.y - origY;
            const absW = Math.abs(w);
            const absH = Math.abs(h);
            rightTriangle.set({
                points: [ { x: 0, y: 0 }, { x: absW, y: absH }, { x: 0, y: absH } ]
            });
            rightTriangle._setPositionDimensions({});
            rightTriangle.set({
                left: (w < 0 ? pointer.x : origX) + rightTriangle.width / 2,
                top: (h < 0 ? pointer.y : origY) + rightTriangle.height / 2
            });
            rightTriangle.setCoords();
        }

        if (drawMode === 'ellipse') {
            const { width, height } = getConstrainedDrawDelta(pointer, origX, origY, shouldConstrainShape);
            const rx = Math.abs(width) / 2;
            const ry = Math.abs(height) / 2;
            ellipse.set({
                rx, ry,
                left: Math.min(origX, origX + width),
                top: Math.min(origY, origY + height)
            });
        }

        if (drawMode === 'star') {
            const w = pointer.x - origX;
            const h = pointer.y - origY;
            const size = Math.max(Math.abs(w), Math.abs(h));
            const points = [];
            const spikes = 5;
            for (let i = 0; i < spikes * 2; i++) {
                const radius = i % 2 === 0 ? size / 2 : size / 4;
                const angle = (i * Math.PI) / spikes - Math.PI / 2;
                points.push({ x: size / 2 + Math.cos(angle) * radius, y: size / 2 + Math.sin(angle) * radius });
            }
            star.set({ points });
            star._setPositionDimensions({});
            star.set({
                left: (w < 0 ? pointer.x : origX) + star.width / 2,
                top: (h < 0 ? pointer.y : origY) + star.height / 2
            });
            star.setCoords();
        }

        if (drawMode === 'heart' && heart) {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 22;
            heart.set({ scaleX: scale, scaleY: scale });
            canvas.requestRenderAll();
        }
        
        if (drawMode === 'arrow') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 20;
            arrow.set({ scaleX: scale, scaleY: scale });
        }

        if (drawMode === 'speechBubble') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 100;
            speechBubble.set({ scaleX: scale, scaleY: scale });
        }

        if (drawMode === 'roundedSpeechBubble') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 20;
            roundedSpeechBubble.set({ scaleX: scale, scaleY: scale });
        }

        if (drawMode === 'roundedRect') {
            const { width, height } = getConstrainedDrawDelta(pointer, origX, origY, shouldConstrainShape);
            roundedRect.set({
                width: Math.abs(width), height: Math.abs(height),
                left: width < 0 ? origX + width : origX,
                top: height < 0 ? origY + height : origY
            });
        }
        
        if (drawMode === 'arrowRight') {
            const scale = Math.max(Math.abs(pointer.x - origX), Math.abs(pointer.y - origY)) / 15;
            curvedArrow.set({ scaleX: scale, scaleY: scale });
        }
        
        if (drawMode === 'hexagon') {
            const w = pointer.x - origX;
            const h = pointer.y - origY;
            const size = Math.max(Math.abs(w), Math.abs(h)) / 2;
            const hexPoints = [];
            for (let i = 0; i < 6; i++) {
                const angle = (Math.PI / 3) * i - Math.PI / 6;
                hexPoints.push({ x: size + Math.cos(angle) * size, y: size + Math.sin(angle) * size });
            }
            hexagon.set({ points: hexPoints });
            hexagon._setPositionDimensions({});
            hexagon.set({
                left: (w < 0 ? pointer.x : origX) + hexagon.width / 2,
                top: (h < 0 ? pointer.y : origY) + hexagon.height / 2
            });
            hexagon.setCoords();
        }

        if (drawMode === 'cross') {
            const w = Math.abs(pointer.x - origX);
            const h = Math.abs(pointer.y - origY);
            const size = Math.max(w, h);
            if (size > 1) {
                // Škálování od 0.01 (1 pixel) do size/100
                const scale = size / 100;
                cross.set({
                    scaleX: scale,
                    scaleY: scale
                });
                cross.setCoords();
            }
        }

        canvas.requestRenderAll();
        return;
    }
});

canvas.on('mouse:up', (o) => {
    if (snapLineIsActive) {
        snapLineMouseUp(o);
        return;
    }

    if (isPanning) {
        endCanvasPan();
        return;
    }

    if (drawMode === 'textBox' && isDown) {
        isDown = false;
        
        let textObjectData;

        if (textBox.width < 10 || textBox.height < 10) {
            textObjectData = {
                left: origX,
                top: origY,
                width: 200, 
            };
        } else {
            textObjectData = {
                left: textBox.left,
                top: textBox.top,
                width: textBox.width,
            };
        }

        canvas.remove(textBox); 
        textBox = null;

        const text = new fabric.Textbox(document.getElementById('textInput').value || 'Text', {
            ...textObjectData,
            fontSize: parseInt(document.getElementById('textSize').value),
            fill: document.getElementById('textColor').value,
            fontFamily: document.getElementById('textFont').value,
            textAlign: currentTextAlign,
            fontWeight: textStyles.bold ? 'bold' : 'normal',
            fontStyle: textStyles.italic ? 'italic' : 'normal',
            underline: textStyles.underline,
            linethrough: textStyles.linethrough,
            layer: 'text',
            erasable: false,
        });
        canvas.add(text);
        
        setTextEditingMode('select');
        
        text.on('editing:entered', () => {
            if (text.hiddenTextarea) {
                text.hiddenTextarea.focus();
            }
            // synchronizuj UI při pohybu kurzoru nebo změně výběru
            text.on('selection:changed', () => syncTextToolbarFromObject(text));
        });

        text.on('editing:exited', () => {
            hideTextToolbar();
        });
        
        canvas.setActiveObject(text);
        text.enterEditing();
        text.selectAll(); // Select all text
        canvas.requestRenderAll();
        
        saveHistoryState('text-draw');
        return;
    }

    // Dokončení kreslení - ale ne v režimu 'select' nebo 'textSelect'
    if (drawMode && drawMode !== 'select' && drawMode !== 'textSelect' && isDown) {
        const objToSelect = lastCreatedObject;
        
        if (objToSelect) {
            // Změnit origin na center pro všechny tvary (kromě čar)
            if (objToSelect.type !== 'line') {
                const center = objToSelect.getCenterPoint();
                objToSelect.set({
                    originX: 'center',
                    originY: 'center',
                    left: center.x,
                    top: center.y
                });
                objToSelect.setCoords();
            }

        }
        
        line = circle = rect = triangle = rightTriangle = ellipse = star = heart = arrow = speechBubble = roundedSpeechBubble = roundedRect = curvedArrow = hexagon = cross = null;
        lastCreatedObject = null;
        isDown = false;
        historyBatch = false;
        hideSnapIndicator(); // Skryj snap indikátor po dokončení kreslení
        saveHistoryState('draw-finished');
        canvas.requestRenderAll();
    }
});

canvas.on('mouse:wheel', (o) => {
    const e = o.e;
    let zoom = canvas.getZoom();

    zoom *= 0.999 ** e.deltaY;
    zoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, zoom));

    const pointer = new fabric.Point(e.offsetX, e.offsetY);
    canvas.zoomToPoint(pointer, zoom);

    e.preventDefault();
    e.stopPropagation();
    canvas.requestRenderAll();
});

// Double-click na čáru aktivuje endpoint controls
canvas.on('mouse:dblclick', (o) => {
    const target = o.target;
    
    // Pouze pro čáry v draw layer
    if (target && target.type === 'line' && target.layer === 'draw') {
        // Vypnout endpoint controls na předchozí editované čáře
        if (currentEditingLine && currentEditingLine !== target) {
            disableLineEndpointsControls(currentEditingLine);
        }
        
        // Aktivovat endpoint controls na této čáře
        enableLineEndpointsControls(target);
        currentEditingLine = target;
        
        canvas.setActiveObject(target);
        canvas.requestRenderAll();
    }
});


document.getElementById('drawSelectBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('select', document.getElementById('drawSelectBtn'));
});
document.getElementById('drawLineBtn').addEventListener('click', () => {
    activateSnapLineTool();
    setActiveTool(document.getElementById('drawLineBtn'));
});
document.getElementById('drawCircleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('circle', document.getElementById('drawCircleBtn'));
});
document.getElementById('drawRectBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('rect', document.getElementById('drawRectBtn'));
});
document.getElementById('drawTriangleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('triangle', document.getElementById('drawTriangleBtn'));
});
document.getElementById('drawRightTriangleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('rightTriangle', document.getElementById('drawRightTriangleBtn'));
});
document.getElementById('drawEllipseBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('ellipse', document.getElementById('drawEllipseBtn'));
});
document.getElementById('drawStarBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('star', document.getElementById('drawStarBtn'));
});
document.getElementById('drawHeartBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('heart', document.getElementById('drawHeartBtn'));
});
document.getElementById('drawArrowBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('arrow', document.getElementById('drawArrowBtn'));
});
document.getElementById('drawSpeechBubbleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('speechBubble', document.getElementById('drawSpeechBubbleBtn'));
});
document.getElementById('drawRoundedSpeechBubbleBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('roundedSpeechBubble', document.getElementById('drawRoundedSpeechBubbleBtn'));
});
document.getElementById('drawRoundedRectBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('roundedRect', document.getElementById('drawRoundedRectBtn'));
});
document.getElementById('drawArrowRightBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('arrowRight', document.getElementById('drawArrowRightBtn'));
});
document.getElementById('drawHexagonBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('hexagon', document.getElementById('drawHexagonBtn'));
});
document.getElementById('drawCrossBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('cross', document.getElementById('drawCrossBtn'));
});
document.getElementById('drawBrushBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('brush', document.getElementById('drawBrushBtn'));
});
document.getElementById('drawEraserBtn').addEventListener('click', () => {
    deactivateSnapLineTool();
    setDrawMode('eraser', document.getElementById('drawEraserBtn'));
    canvas.defaultCursor = 'none';
    canvas.hoverCursor = 'none';
});

document.getElementById('brushWidth').addEventListener('input', (e) => {
    if (drawMode === 'brush') {
        canvas.freeDrawingBrush.width = parseInt(e.target.value, 10);
    }
});
document.getElementById('drawColor').addEventListener('input', (e) => {
    if (drawMode === 'brush') {
        canvas.freeDrawingBrush.color = e.target.value;
    }
});
document.getElementById('eraserSize').addEventListener('input', (e) => {
    if (drawMode === 'eraser') {
        ERASER_RADIUS = parseInt(e.target.value, 10);
        if(eraserCursor) eraserCursor.set('radius', ERASER_RADIUS);
    }
});

// Úprava stylu čáry
function applyToActiveObject(props) {
    const obj = canvas.getActiveObject();
    if (!obj || !obj.stroke) return;

    if (props.strokeDashArray) {
        const scale = Math.max(obj.scaleX || 1, obj.scaleY || 1);
        props.strokeDashArray = props.strokeDashArray.map(v => v / scale);
    }

    obj.set(props);
    obj.setCoords();
    canvas.requestRenderAll();
}

// Pomocná funkce pro aplikaci vlastností na objekt včetně skupin
function applyStrokePropsToObject(obj, props) {
    if (!obj) return;
    
    // Pro skupiny aplikuj na všechny děti
    if (obj.type === 'group' && obj._objects) {
        obj._objects.forEach(child => applyStrokePropsToObject(child, props));
        return;
    }
    
    // Zkontroluj že objekt podporuje stroke
    if (obj.stroke === undefined && obj.strokeWidth === undefined) return;
    
    obj.set(props);
}

// Pomocná funkce pro aplikaci stroke vlastností na výběr (včetně activeSelection a groups)
function applyStrokePropsToSelection(props) {
    const activeObj = canvas.getActiveObject();
    if (!activeObj) return false;
    
    if (activeObj.type === 'activeSelection') {
        activeObj.getObjects().forEach(obj => applyStrokePropsToObject(obj, props));
    } else {
        applyStrokePropsToObject(activeObj, props);
    }
    
    canvas.requestRenderAll();
    return true;
}

// Styl čáry
document.getElementById('strokeStyle').addEventListener('change', e => {
    const activeObj = canvas.getActiveObject();
    if (!activeObj) return;
    
    // Funkce pro získání dash array podle šířky objektu
    const applyDash = (obj) => {
        if (!obj || obj.stroke === undefined) return;
        const width = obj.strokeWidth || 1;
        obj.set({ strokeDashArray: getDashFromUIForWidth(width) });
    };
    
    if (activeObj.type === 'activeSelection') {
        activeObj.getObjects().forEach(obj => {
            if (obj.type === 'group' && obj._objects) {
                obj._objects.forEach(applyDash);
            } else {
                applyDash(obj);
            }
        });
    } else if (activeObj.type === 'group' && activeObj._objects) {
        activeObj._objects.forEach(applyDash);
    } else {
        applyDash(activeObj);
    }
    
    canvas.requestRenderAll();
});

// Zakončení čáry
document.getElementById('strokeCap').addEventListener('change', e => {
    const capValue = e.target.value;
    applyStrokePropsToSelection({ strokeLineCap: capValue });
});

function getDashFromUI() {
    const val = document.getElementById('strokeStyle').value;
    if (val === 'dotted') return [2, 6];
    return null;
}

function getDashFromUIForWidth(width) {
    const type = document.getElementById('strokeStyle').value;

    if (type === 'dashed') return [width * 2, width];
    if (type === 'dotted') return [width, width * 1.5];
    return null;
}

canvas.on("object:rotating", function(e) {
    const obj = e.target;
    if (!obj) return;

    obj.setCoords();
    keepInsideCanvas(obj);
    updateImageSize();
});


// Načtení obrázku z URL poslané z indexu
const editorConfig = getEditorConfig();
const imageUrl = editorConfig.imagePath;
if (imageUrl) loadImage(imageUrl);

// Funkce pro načtení obrázku do Fabric canvasu

let originalImageWidth, originalImageHeight;
let originalCropWidth, originalCropHeight;

function loadImage(url) {
    fabric.Image.fromURL(url, (img) => {
        if (currentImage) canvas.remove(currentImage);

        currentImage = img;
        img.layer = 'background';
        originalImageWidth = img.width;
        originalImageHeight = img.height;

     const isBlank = url.includes('blank_');
img.set({
    originX: 'center',
    originY: 'center',
    selectable: !isBlank, // pokud je blank, není selectovatelné
    evented: !isBlank,    // pokud je blank, nereaguje na události
    lockMovementX: isBlank,
    lockMovementY: isBlank,
    lockScalingX: isBlank,
    lockScalingY: isBlank,
    lockRotation: isBlank,
    hasRotatingPoint: !isBlank,
    cornerStyle: 'circle'
});

if (!isBlank) {
    applyImageTransformControls(img);
}

img.sendToBack();

// Skrýt/zobrazit tlačítka mřížky a uzamykání podle typu obrázku
document.getElementById('toggleGridBtn').style.display = isBlank ? 'none' : '';
document.getElementById('lockObjectBtn').style.display = isBlank ? 'none' : '';

// Vypnout mřížku pokud je aktivní a přepneme na šablonu
if (isBlank && gridEnabled) {
    gridEnabled = false;
    document.getElementById('toggleGridBtn').classList.remove('active');
    removeGrid();
}

        canvas.add(img);


        fitImageToCanvas(img);
        fitObjectToViewport(img);
        updateImageSize();
        
        // Generovat náhledy filtrů po načtení obrázku
        setTimeout(() => generateFilterPreviews(), 100);
    }, { crossOrigin: 'anonymous' });
}

// Přizpůsobení obrázku canvasu
function fitImageToCanvas(img) {
    img.set({ scaleX: 1, scaleY: 1 });
    resetCanvasToWorkspaceSize(false);

    img.left = canvas.getWidth() / 2;
    img.top = canvas.getHeight() / 2;
    img.setCoords();
    canvas.requestRenderAll();
}

// Zoom a centrování objektu
function fitObjectToViewport(obj) {
    if (!obj) return;

    resetCanvasToWorkspaceSize(false);
    canvas.renderAll();

    const bbox = obj.getBoundingRect(true);
    if (!bbox.width || !bbox.height) return;

    const padding = Math.min(120, Math.max(VIEWPORT_PADDING, Math.round(Math.min(canvas.getWidth(), canvas.getHeight()) * 0.08)));
    const viewportWidth = Math.max(canvas.getWidth() - (padding * 2), 1);
    const viewportHeight = Math.max(canvas.getHeight() - (padding * 2), 1);
    const scaleX = viewportWidth / bbox.width;
    const scaleY = viewportHeight / bbox.height;
    const scale = Math.min(scaleX, scaleY, 1);

    const center = new fabric.Point(canvas.getWidth() / 2, canvas.getHeight() / 2);
    canvas.zoomToPoint(center, scale);

    const objCenter = obj.getCenterPoint();
    const zoom = canvas.getZoom();
    canvas.viewportTransform[4] = (canvas.getWidth() / 2) - (objCenter.x * zoom);
    canvas.viewportTransform[5] = (canvas.getHeight() / 2) - (objCenter.y * zoom);

    canvas.requestRenderAll();
}

function getCropTargetImage() {
    const activeObject = canvas.getActiveObject();

    if (activeObject && activeObject.type === 'image') {
        return activeObject;
    }

    if (activeObject && activeObject.type === 'activeSelection') {
        const selectedImage = activeObject.getObjects().find(obj => obj.type === 'image');
        if (selectedImage) return selectedImage;
    }

    return currentImage;
}

function exitCropMode() {
    if (cropRect) {
        canvas.remove(cropRect);
        cropRect = null;
    }

    cropTargetImage = null;
    mode = 'resize';
    document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
    canvas.selection = true;
}

function getImageLocalBoundsForRect(targetImage, rect) {
    if (!targetImage || !rect || !targetImage.width || !targetImage.height) return null;

    const relativeMatrix = fabric.util.multiplyTransformMatrices(
        fabric.util.invertTransform(targetImage.calcTransformMatrix()),
        rect.calcTransformMatrix()
    );
    const halfRectWidth = (rect.width || 0) / 2;
    const halfRectHeight = (rect.height || 0) / 2;
    const localPoints = [
        new fabric.Point(-halfRectWidth, -halfRectHeight),
        new fabric.Point(halfRectWidth, -halfRectHeight),
        new fabric.Point(halfRectWidth, halfRectHeight),
        new fabric.Point(-halfRectWidth, halfRectHeight),
    ].map(point => fabric.util.transformPoint(point, relativeMatrix));

    const localXs = localPoints.map(point => point.x + (targetImage.width / 2));
    const localYs = localPoints.map(point => point.y + (targetImage.height / 2));

    return {
        left: Math.min(...localXs),
        top: Math.min(...localYs),
        right: Math.max(...localXs),
        bottom: Math.max(...localYs),
    };
}

function clampLocalBoundsToImage(targetImage, bounds) {
    if (!targetImage || !bounds || !targetImage.width || !targetImage.height) return null;

    const imageWidth = targetImage.width;
    const imageHeight = targetImage.height;
    const width = Math.min(Math.max(bounds.right - bounds.left, 1), imageWidth);
    const height = Math.min(Math.max(bounds.bottom - bounds.top, 1), imageHeight);
    const left = Math.min(Math.max(bounds.left, 0), imageWidth - width);
    const top = Math.min(Math.max(bounds.top, 0), imageHeight - height);

    return {
        left,
        top,
        right: left + width,
        bottom: top + height,
        width,
        height,
    };
}

function getCanvasPointFromImageLocalPoint(targetImage, localPoint) {
    return fabric.util.transformPoint(localPoint, targetImage.calcTransformMatrix());
}

function applyLocalBoundsToCropRect(targetImage, rect, bounds) {
    if (!targetImage || !rect || !bounds) return;

    const localCenter = new fabric.Point(
        ((bounds.left + bounds.right) / 2) - (targetImage.width / 2),
        ((bounds.top + bounds.bottom) / 2) - (targetImage.height / 2)
    );
    const canvasCenter = getCanvasPointFromImageLocalPoint(targetImage, localCenter);

    rect.set({
        left: canvasCenter.x,
        top: canvasCenter.y,
        angle: targetImage.angle || 0,
        width: Math.max(1, bounds.width),
        height: Math.max(1, bounds.height),
        scaleX: Math.abs(targetImage.scaleX || 1),
        scaleY: Math.abs(targetImage.scaleY || 1),
        flipX: false,
        flipY: false,
    });
    rect.setCoords();
}

function constrainCropRectToTargetImage() {
    if (!cropRect || !cropTargetImage) return;

    const rawBounds = getImageLocalBoundsForRect(cropTargetImage, cropRect);
    const bounded = clampLocalBoundsToImage(cropTargetImage, rawBounds);
    if (!bounded) return;

    applyLocalBoundsToCropRect(cropTargetImage, cropRect, bounded);
}

function getCropDataForImage(targetImage, rect) {
    if (!targetImage || !rect) return null;

    const rawBounds = getImageLocalBoundsForRect(targetImage, rect);
    const bounded = clampLocalBoundsToImage(targetImage, rawBounds);
    if (!bounded) return null;

    if (bounded.width <= 0 || bounded.height <= 0) {
        return null;
    }

    const localCenter = new fabric.Point(
        ((bounded.left + bounded.right) / 2) - (targetImage.width / 2),
        ((bounded.top + bounded.bottom) / 2) - (targetImage.height / 2)
    );
    const canvasCenter = getCanvasPointFromImageLocalPoint(targetImage, localCenter);

    return {
        cropX: (targetImage.cropX || 0) + bounded.left,
        cropY: (targetImage.cropY || 0) + bounded.top,
        width: Math.max(1, bounded.width),
        height: Math.max(1, bounded.height),
        left: canvasCenter.x,
        top: canvasCenter.y,
    };
}

// Resize / Crop
document.getElementById('toggleMode').addEventListener('click', () => {
    if (mode === 'resize') {
        const targetImage = getCropTargetImage();
        if (!targetImage) return;

        mode = 'crop';
        cropTargetImage = targetImage;
        canvas.selection = false;
        document.getElementById('toggleMode').textContent = 'Režim: Ořez';

        cropRect = new fabric.Rect({
            left: targetImage.left,
            top: targetImage.top,
            width: 1,
            height: 1,
            scaleX: 1,
            scaleY: 1,
            fill: 'rgba(0,0,0,0.3)',
            strokeWidth: 0,
            originX: 'center',
            originY: 'center',
            angle: targetImage.angle,
            selectable: true,
            hasBorders: true,
            cornerStyle: 'circle',
            lockRotation: true,
            lockScalingFlip: true,
            excludeFromExport: true,
        });

        canvas.add(cropRect);
        applyLocalBoundsToCropRect(targetImage, cropRect, {
            left: 0,
            top: 0,
            right: targetImage.width,
            bottom: targetImage.height,
            width: targetImage.width,
            height: targetImage.height,
        });
        cropRect.bringToFront();
        canvas.setActiveObject(cropRect);
        canvas.requestRenderAll();
    } else {
        exitCropMode();
        canvas.requestRenderAll();
    }
});

// Crop obrázku - ořízne jen vybraný obrázek, ne celý canvas
document.getElementById('cropBtn').addEventListener('click', async () => {
    const targetImage = cropTargetImage || getCropTargetImage();
    if (!targetImage || !cropRect) return;

    historyBatch = true;
    const cropData = getCropDataForImage(targetImage, cropRect);

    if (!cropData) {
        historyBatch = false;
        showToast('Oblast ořezu je mimo vybraný obrázek.', 'error');
        return;
    }

    targetImage.set({
        cropX: cropData.cropX,
        cropY: cropData.cropY,
        width: cropData.width,
        height: cropData.height,
        left: cropData.left,
        top: cropData.top,
    });
    targetImage.dirty = true;
    targetImage.setCoords();

    if (targetImage === currentImage) {
        originalImageWidth = cropData.width;
        originalImageHeight = cropData.height;
        fitObjectToViewport(targetImage);
    }

    exitCropMode();
    canvas.setActiveObject(targetImage);
    updateImageSize();
    canvas.requestRenderAll();

    historyBatch = false;
    saveHistoryState('crop');
});

canvas.on('object:modified', function(e) {
    if (e.target === cropRect && cropTargetImage) {
        constrainCropRectToTargetImage();
        cropRect.setCoords();
        canvas.requestRenderAll();
    }
});


// Filtry / Jas / Kontrast / Sytost
let activeFilter = null;

function applyFilters() {
    const filterImage = document.getElementById('filterLayerImage')?.checked ?? true;
    const filterDraw = document.getElementById('filterLayerDraw')?.checked ?? false;
    const filterText = document.getElementById('filterLayerText')?.checked ?? false;
    
    const brightness = parseFloat(document.getElementById('brightness').value);
    const contrast = parseFloat(document.getElementById('contrast').value);
    const saturation = parseFloat(document.getElementById('saturation').value);

    const baseFilters = [
        new fabric.Image.filters.Brightness({ brightness }),
        new fabric.Image.filters.Contrast({ contrast }),
        new fabric.Image.filters.Saturation({ saturation })
    ];

    if (activeFilter) baseFilters.push(activeFilter);
    
    // Aplikovat filtry na obrázky (včetně přidaných)
    canvas.getObjects().forEach(obj => {
        if (obj === currentImage || obj.layer === 'image') {
            if (filterImage && obj.type === 'image') {
                obj.filters = [...baseFilters];
                obj.applyFilters();
            } else if (obj.type === 'image') {
                obj.filters = [];
                obj.applyFilters();
            }
        }
    });
    
    // Aplikovat/resetovat filtry na kresby
    canvas.getObjects().forEach(obj => {
        if (obj.layer === 'draw') {
            if (filterDraw) {
                applyFiltersToObject(obj, baseFilters, brightness, contrast, saturation);
            } else {
                resetObjectColors(obj);
            }
        }
    });
    
    // Aplikovat/resetovat filtry na text
    canvas.getObjects().forEach(obj => {
        if (obj.layer === 'text' || obj.type === 'i-text' || obj.type === 'textbox') {
            if (filterText) {
                applyFiltersToTextObject(obj, baseFilters, brightness, contrast, saturation);
            } else {
                resetTextObjectColors(obj);
            }
        }
    });
    
    canvas.requestRenderAll();
}

// Resetuje barvy objektu na původní
function resetObjectColors(obj) {
    if (!obj) return;
    if (obj._originalStroke) {
        obj.set({ stroke: obj._originalStroke });
        delete obj._originalStroke;
    }
    if (obj._originalFill) {
        obj.set({ fill: obj._originalFill });
        delete obj._originalFill;
    }
}

// Pomocná funkce pro aplikaci filtrů na jednotlivé objekty
function applyFiltersToObject(obj, filters, brightness = 0, contrast = 0, saturation = 0) {
    if (!obj) return;
    
    // Pro jednoduchost měníme pouze barvu podle filtrů
    const hasGrayscale = filters.some(f => f && f.type === 'Grayscale');
    const hasSepia = filters.some(f => f && f.type === 'Sepia');
    const hasInvert = filters.some(f => f && f.type === 'Invert');
    
    // Uložit původní barvu pokud není uložena
    if (!obj._originalStroke && obj.stroke) obj._originalStroke = obj.stroke;
    if (!obj._originalFill && obj.fill) obj._originalFill = obj.fill;
    
    // Funkce pro aplikaci všech filtrů na barvu
    const applyAllFilters = (color) => {
        if (!color || color === 'transparent') return color;
        let rgb = hexToRgb(color);
        if (!rgb) return color;
        
        // Brightness (-1 to 1, přidává/ubírá hodnotu)
        if (brightness !== 0) {
            const b = Math.round(brightness * 255);
            rgb.r = Math.max(0, Math.min(255, rgb.r + b));
            rgb.g = Math.max(0, Math.min(255, rgb.g + b));
            rgb.b = Math.max(0, Math.min(255, rgb.b + b));
        }
        
        // Contrast (-1 to 1)
        if (contrast !== 0) {
            const factor = (259 * (contrast * 255 + 255)) / (255 * (259 - contrast * 255));
            rgb.r = Math.max(0, Math.min(255, Math.round(factor * (rgb.r - 128) + 128)));
            rgb.g = Math.max(0, Math.min(255, Math.round(factor * (rgb.g - 128) + 128)));
            rgb.b = Math.max(0, Math.min(255, Math.round(factor * (rgb.b - 128) + 128)));
        }
        
        // Saturation (-1 to 1)
        if (saturation !== 0) {
            const gray = 0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b;
            const sat = 1 + saturation;
            rgb.r = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.r - gray))));
            rgb.g = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.g - gray))));
            rgb.b = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.b - gray))));
        }
        
        // Grayscale
        if (hasGrayscale) {
            const gray = Math.round(0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b);
            rgb = { r: gray, g: gray, b: gray };
        }
        
        // Sepia
        if (hasSepia) {
            const r = Math.min(255, Math.round(0.393 * rgb.r + 0.769 * rgb.g + 0.189 * rgb.b));
            const g = Math.min(255, Math.round(0.349 * rgb.r + 0.686 * rgb.g + 0.168 * rgb.b));
            const b = Math.min(255, Math.round(0.272 * rgb.r + 0.534 * rgb.g + 0.131 * rgb.b));
            rgb = { r, g, b };
        }
        
        // Invert
        if (hasInvert) {
            rgb = { r: 255 - rgb.r, g: 255 - rgb.g, b: 255 - rgb.b };
        }
        
        return rgbToHex(rgb.r, rgb.g, rgb.b);
    };
    
    // Aplikovat filtry na stroke a fill
    if (obj._originalStroke) {
        obj.set({ stroke: applyAllFilters(obj._originalStroke) });
    }
    if (obj._originalFill && obj._originalFill !== 'transparent') {
        obj.set({ fill: applyAllFilters(obj._originalFill) });
    }
}

// Speciální funkce pro aplikaci filtrů na textové objekty
function applyFiltersToTextObject(obj, filters, brightness = 0, contrast = 0, saturation = 0) {
    if (!obj) return;
    
    const hasGrayscale = filters.some(f => f && f.type === 'Grayscale');
    const hasSepia = filters.some(f => f && f.type === 'Sepia');
    const hasInvert = filters.some(f => f && f.type === 'Invert');
    
    // Uložit původní barvu textu
    if (!obj._originalFill && obj.fill) obj._originalFill = obj.fill;
    
    const applyAllFilters = (color) => {
        if (!color || color === 'transparent') return color;
        let rgb = hexToRgb(color);
        if (!rgb) return color;
        
        if (brightness !== 0) {
            const b = Math.round(brightness * 255);
            rgb.r = Math.max(0, Math.min(255, rgb.r + b));
            rgb.g = Math.max(0, Math.min(255, rgb.g + b));
            rgb.b = Math.max(0, Math.min(255, rgb.b + b));
        }
        
        if (contrast !== 0) {
            const factor = (259 * (contrast * 255 + 255)) / (255 * (259 - contrast * 255));
            rgb.r = Math.max(0, Math.min(255, Math.round(factor * (rgb.r - 128) + 128)));
            rgb.g = Math.max(0, Math.min(255, Math.round(factor * (rgb.g - 128) + 128)));
            rgb.b = Math.max(0, Math.min(255, Math.round(factor * (rgb.b - 128) + 128)));
        }
        
        if (saturation !== 0) {
            const gray = 0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b;
            const sat = 1 + saturation;
            rgb.r = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.r - gray))));
            rgb.g = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.g - gray))));
            rgb.b = Math.max(0, Math.min(255, Math.round(gray + sat * (rgb.b - gray))));
        }
        
        if (hasGrayscale) {
            const gray = Math.round(0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b);
            rgb = { r: gray, g: gray, b: gray };
        }
        
        if (hasSepia) {
            const r = Math.min(255, Math.round(0.393 * rgb.r + 0.769 * rgb.g + 0.189 * rgb.b));
            const g = Math.min(255, Math.round(0.349 * rgb.r + 0.686 * rgb.g + 0.168 * rgb.b));
            const b = Math.min(255, Math.round(0.272 * rgb.r + 0.534 * rgb.g + 0.131 * rgb.b));
            rgb = { r, g, b };
        }
        
        if (hasInvert) {
            rgb = { r: 255 - rgb.r, g: 255 - rgb.g, b: 255 - rgb.b };
        }
        
        return rgbToHex(rgb.r, rgb.g, rgb.b);
    };
    
    // Aplikovat pouze na fill (barva textu)
    if (obj._originalFill) {
        obj.set({ fill: applyAllFilters(obj._originalFill) });
        obj.dirty = true;
    }
}

// Reset barev textového objektu
function resetTextObjectColors(obj) {
    if (!obj) return;
    if (obj._originalFill) {
        obj.set({ fill: obj._originalFill });
        delete obj._originalFill;
        obj.dirty = true;
    }
}

// Pomocné funkce pro konverzi barev
function toGrayscale(color) {
    const rgb = hexToRgb(color);
    if (!rgb) return color;
    const gray = Math.round(0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b);
    return rgbToHex(gray, gray, gray);
}

function invertColor(color) {
    const rgb = hexToRgb(color);
    if (!rgb) return color;
    return rgbToHex(255 - rgb.r, 255 - rgb.g, 255 - rgb.b);
}

function toSepia(color) {
    const rgb = hexToRgb(color);
    if (!rgb) return color;
    const r = Math.min(255, Math.round(0.393 * rgb.r + 0.769 * rgb.g + 0.189 * rgb.b));
    const g = Math.min(255, Math.round(0.349 * rgb.r + 0.686 * rgb.g + 0.168 * rgb.b));
    const b = Math.min(255, Math.round(0.272 * rgb.r + 0.534 * rgb.g + 0.131 * rgb.b));
    return rgbToHex(r, g, b);
}

function hexToRgb(hex) {
    if (!hex || typeof hex !== 'string') return null;
    
    // Handle rgb()/rgba() format
    const rgbMatch = hex.match(/^rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
    if (rgbMatch) {
        return {
            r: parseInt(rgbMatch[1], 10),
            g: parseInt(rgbMatch[2], 10),
            b: parseInt(rgbMatch[3], 10)
        };
    }
    
    // Handle named colors
    const namedColors = {
        'black': '#000000', 'white': '#ffffff', 'red': '#ff0000', 'green': '#008000',
        'blue': '#0000ff', 'yellow': '#ffff00', 'cyan': '#00ffff', 'magenta': '#ff00ff',
        'gray': '#808080', 'grey': '#808080', 'orange': '#ffa500', 'pink': '#ffc0cb',
        'purple': '#800080', 'brown': '#a52a2a', 'navy': '#000080', 'teal': '#008080'
    };
    if (namedColors[hex.toLowerCase()]) {
        hex = namedColors[hex.toLowerCase()];
    }
    
    hex = hex.replace('#', '');
    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
    if (hex.length !== 6) return null;
    return {
        r: parseInt(hex.substr(0, 2), 16),
        g: parseInt(hex.substr(2, 2), 16),
        b: parseInt(hex.substr(4, 2), 16)
    };
}

function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
}

// Generování dynamických náhledů filtrů z aktuálního obrázku
function createFilterPreviewSource(sourceEl, width, height) {
    const previewSource = document.createElement('canvas');
    previewSource.width = width;
    previewSource.height = height;

    const previewSourceCtx = previewSource.getContext('2d');
    if (!previewSourceCtx) return null;

    previewSourceCtx.imageSmoothingEnabled = true;
    previewSourceCtx.imageSmoothingQuality = 'high';
    previewSourceCtx.drawImage(sourceEl, 0, 0, width, height);

    return previewSource;
}

function generateFilterPreviews() {
    if (!currentImage || !currentImage._element) return;
    
    const sourceEl = currentImage._element;
    const sourceWidth = sourceEl.naturalWidth || sourceEl.width;
    const sourceHeight = sourceEl.naturalHeight || sourceEl.height;
    if (!sourceWidth || !sourceHeight) return;

    const maxPreviewWidth = 100;
    const maxPreviewHeight = 75;
    const previewScale = Math.min(maxPreviewWidth / sourceWidth, maxPreviewHeight / sourceHeight, 1);
    const previewWidth = Math.max(1, Math.round(sourceWidth * previewScale));
    const previewHeight = Math.max(1, Math.round(sourceHeight * previewScale));
    const basePreviewSource = createFilterPreviewSource(sourceEl, previewWidth, previewHeight);
    if (!basePreviewSource) return;
    
    const filterTypes = {
        'original': null,
        'grayscale': new fabric.Image.filters.Grayscale(),
        'sepia': new fabric.Image.filters.Sepia(),
        'invert': new fabric.Image.filters.Invert(),
        'blur': new fabric.Image.filters.Blur({ blur: 0.3 }),
        'sharpen': new fabric.Image.filters.Convolute({ matrix: [0, -1, 0, -1, 5, -1, 0, -1, 0] })
    };
    
    document.querySelectorAll('.filter-thumb-wrap').forEach(wrap => {
        const filterName = wrap.getAttribute('data-filter');
        const previewCanvas = wrap.querySelector('.filter-preview-canvas');
        if (!previewCanvas) return;
        
        previewCanvas.width = previewWidth;
        previewCanvas.height = previewHeight;
        previewCanvas.style.width = `${previewWidth}px`;
        previewCanvas.style.height = `${previewHeight}px`;

        const previewSource = createFilterPreviewSource(basePreviewSource, previewWidth, previewHeight);
        if (!previewSource) return;
        
        // Vytvořit dočasný fabric image už ze zmenšené kopie, ne z plného originálu
        const tempFabricImg = new fabric.Image(previewSource, {
            left: 0,
            top: 0,
        });
        
        if (filterTypes[filterName]) {
            tempFabricImg.filters = [filterTypes[filterName]];
            tempFabricImg.applyFilters();
        }
        
        const ctx = previewCanvas.getContext('2d');
        if (!ctx) return;

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.clearRect(0, 0, previewWidth, previewHeight);

        tempFabricImg.render(ctx);
    });
}

// Kliknutí na náhled filtru
document.querySelectorAll('.filter-thumb-wrap').forEach(wrap => {
    wrap.addEventListener('click', () => {
        document.querySelectorAll('.filter-thumb-wrap').forEach(w => w.classList.remove('ring-2', 'ring-blue-500'));
        wrap.classList.add('ring-2', 'ring-blue-500');

        const type = wrap.getAttribute('data-filter');
        switch(type){
            case 'grayscale': activeFilter = new fabric.Image.filters.Grayscale(); break;
            case 'sepia': activeFilter = new fabric.Image.filters.Sepia(); break;
            case 'invert': activeFilter = new fabric.Image.filters.Invert(); break;
            case 'blur': activeFilter = new fabric.Image.filters.Blur({ blur: 0.3 }); break;
            case 'sharpen': activeFilter = new fabric.Image.filters.Convolute({ matrix: [0, -1, 0, -1, 5, -1, 0, -1, 0] }); break;
            default: activeFilter = null; break;
        }

        applyFilters();
        scheduleFilterHistory();
    });
});

// Checkboxy pro vrstvy filtrů
document.querySelectorAll('.filterLayerCheck').forEach(checkbox => {
    checkbox.addEventListener('change', applyFilters);
});

// Posuvníky
document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input => {
    input.addEventListener('input', () => { applyFilters(); scheduleFilterHistory(); });
});

// Update size label
function updateImageSize() {
    const imageSizeLabel = document.getElementById('imageSize');
    if (!imageSizeLabel) return;

    if (!currentImage) {
        imageSizeLabel.textContent = 'Velikost: —';
        return;
    }

    const width = Math.round(currentImage.width * currentImage.scaleX);
    const height = Math.round(currentImage.height * currentImage.scaleY);
    imageSizeLabel.textContent = `Velikost: ${width} × ${height} px`;
}

canvas.on('object:modified', () => {
    if (!currentImage) return;
    const angle = currentImage.angle || 0;
    if (angle !== previousAngle) {
        previousAngle = angle;
    }
    updateImageSize();
    updateRotationAngle();
});

function updateRotationAngle() {
    const rotationAngleLabel = document.getElementById('rotationAngle');
    if (!rotationAngleLabel) return;

    if (!currentImage) {
        rotationAngleLabel.textContent = 'Otočení: —';
        return;
    }

    const angle = Math.round(currentImage.angle || 0);
    rotationAngleLabel.textContent = `Otočení: ${angle}°`;
}
// aby crop a img nešel mimo canvas
function keepInsideCanvas(obj) {
    const padding = 0;
    const canvasWidth = canvas.getWidth();
    const canvasHeight = canvas.getHeight();
    const bound = obj.getBoundingRect(true, true);

    let offsetX = 0;
    let offsetY = 0;

    // horizontální omezení
    if (bound.width <= canvasWidth) {
        if (bound.left < padding) offsetX = padding - bound.left;
        if (bound.left + bound.width > canvasWidth - padding)
            offsetX = (canvasWidth - padding) - (bound.left + bound.width);
    } else {
        // pokud je širší než canvas, drž střed uvnitř
        if (obj.left < canvasWidth/2) offsetX = (canvasWidth/2) - obj.left;
        if (obj.left > canvasWidth/2) offsetX = (canvasWidth/2) - obj.left;
    }

    // vertikální omezení
    if (bound.height <= canvasHeight) {
        if (bound.top < padding) offsetY = padding - bound.top;
        if (bound.top + bound.height > canvasHeight - padding)
            offsetY = (canvasHeight - padding) - (bound.top + bound.height);
    } else {
        // pokud je vyšší než canvas, drž střed uvnitř
        if (obj.top < canvasHeight/2) offsetY = (canvasHeight/2) - obj.top;
        if (obj.top > canvasHeight/2) offsetY = (canvasHeight/2) - obj.top;
    }

    obj.left += offsetX;
    obj.top += offsetY;
    obj.setCoords();
}

// download
document.getElementById('startDownloadBtn').addEventListener('click', () => {
    if (!currentImage) {
        alert("Nejdříve načtěte obrázek, který chcete exportovat!");
        return;
    }

    hideEraserCursor();

    const format = document.getElementById('exportFormat').value;
    const contentType = document.querySelector('input[name="exportContent"]:checked').value;
    const quality = 0.9;
    const mimeType = `image/${format === 'jpeg' ? 'jpeg' : format === 'webp' ? 'webp' : 'png'}`;
    let filenameInput = document.getElementById('exportFileName').value.trim();
    let filename = filenameInput ? filenameInput.replace(/\.[^.]+$/, '') + '.' + format : `export.${format}`;
    let dataURL;

    if (contentType === 'canvas') {
        if (!filenameInput) filename = `canvas_export.${format}`;

        // Dočasně skrýt objekty, které se nemají exportovat
        const objectsToHide = canvas.getObjects().filter(obj => obj.excludeFromExport || obj.isGrid);
        objectsToHide.forEach(obj => obj.visible = false);

        dataURL = canvas.toDataURL({ format: format, quality: quality, multiplier: 1 });

        // Vrátit viditelnost objektů
        objectsToHide.forEach(obj => obj.visible = true);
        canvas.requestRenderAll();
    } else {
        if (!filenameInput) filename = `image_only.${format}`;
    const img = currentImage;

    // bounding box obrázku (včetně rotace)
    const bbox = img.getBoundingRect(true);

    const exportCanvas = document.createElement('canvas');
    exportCanvas.width = Math.ceil(bbox.width);
    exportCanvas.height = Math.ceil(bbox.height);
    const exportCtx = exportCanvas.getContext('2d');

    exportCtx.save();
    exportCtx.translate(-bbox.left, -bbox.top);

    // vykreslení všech objektů kromě pozadí canvasu, kurzoru gumy a mřížky
    canvas.getObjects().forEach(obj => {
        if (obj.visible === false) return;
        if (obj === eraserCursor) return; // Přeskočíme kurzor gumy
        if (obj.excludeFromExport) return; // Přeskočíme objekty označené jako excludeFromExport
        if (obj.isGrid) return; // Přeskočíme mřížku

        exportCtx.save();
        obj.render(exportCtx);
        exportCtx.restore();
    });

    exportCtx.restore();

    dataURL = exportCanvas.toDataURL(mimeType, quality);
}
    
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = filename;
    link.click();
    
//Ukládní na server
   fetch('/save-image', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            image: dataURL,
            format: format
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Uloženo do uploads:', data.path);
    })
    .catch(error => console.error('Chyba při ukládání na server:', error));
});

function getFillColor() {
    return document.getElementById('fillTransparent').checked ? 'transparent' : document.getElementById('fillColor').value;
}

function getStrokeColor() {
    return document.getElementById('drawColor').value;
}

function clamp(v, min, max) {
    return Math.min(Math.max(v, min), max);
}

function fitObjectIntoCanvas(obj, padding = 20, allowUpscale = false) {
    if (!obj) return;

    const canvasW = canvas.width;
    const canvasH = canvas.height;

    const objW = obj.width * obj.scaleX;
    const objH = obj.height * obj.scaleY;

    const maxW = canvasW - padding * 2;
    const maxH = canvasH - padding * 2;

    let scale = Math.min(maxW / objW, maxH / objH);

    if (!allowUpscale && scale > 1) {
        scale = 1;
    }

    obj.set({
        scaleX: obj.scaleX * scale,
        scaleY: obj.scaleY * scale,
        left: canvasW / 2,
        top: canvasH / 2,
        originX: 'center',
        originY: 'center'
    });

    obj.setCoords();
}

function anyChecked(selectorClass) {
    const checkboxes = document.querySelectorAll(`.${selectorClass}`);
    for (let cb of checkboxes) {
        if (cb.checked) return true;
    }
    return false;
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {

        // vypnout kreslení
        drawMode = null;
        isDown = false;
        
        // Deaktivovat snap-line nástroj
        deactivateSnapLineTool();
        
        // Vypnout endpoint controls na všech čárách
        disableAllLineEndpoints();
        
        // Skrýt kurzor gumy
        hideEraserCursor();
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        canvas.isDrawingMode = false;
        
        const drawToolbar = document.getElementById('drawFloatingToolbar');
        if (drawToolbar) drawToolbar.classList.add('hidden');
        
        // Skrýt text toolbar
        const textTb = document.getElementById('textToolbar');
        if (textTb) textTb.classList.add('hidden');
        
        // Skrýt kontextová menu
        const ctxMenu = document.getElementById('contextMenu');
        if (ctxMenu) ctxMenu.classList.add('hidden');
        const textCtxMenu = document.getElementById('textContextMenu');
        if (textCtxMenu) textCtxMenu.classList.add('hidden');

        // vypnout crop
        if (cropRect) {
            canvas.remove(cropRect);
            cropRect = null;
            cropTargetImage = null;
            mode = 'resize';
            document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
        }

        canvas.selection = true;
        
        // Odemknout obrázek při opuštění sekce kreslení (pokud není šablona)
        if (currentImage) {
            const isBlank = currentImage._element?.src?.includes('blank_');
            if (!isBlank) {
                currentImage.set({
                    lockMovementX: false,
                    lockMovementY: false,
                    lockScalingX: false,
                    lockScalingY: false,
                    lockRotation: false,
                    hasControls: true,
                    hasBorders: true,
                    selectable: true,
                    evented: true
                });
                // Resetovat stav tlačítka zámku
                document.getElementById('lockObjectBtn').classList.remove('active');
            }
        }

        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById(btn.dataset.target).classList.remove('hidden');

        if (btn.dataset.target === 'panelDraw') {
    drawMode = null;
    canvas.selection = true;
    lockImage(false);

    setActiveTool(document.getElementById('drawSelectBtn'));

    canvas.getObjects().forEach(obj => {
        const isBlank = obj._element?.src?.includes('blank_');
        obj.selectable = !isBlank;
        obj.evented = !isBlank;
    });

    canvas.discardActiveObject();
    canvas.requestRenderAll();
}
    });
});

function enableDrawing(mode) {
    canvas.isDrawingMode = false;
    drawMode = mode;
    canvas.selection = false;
    canvas.discardActiveObject();

    // Zakázat výběr všech objektů během kreslení
    canvas.getObjects().forEach(obj => {
        const isBlank = obj._element?.src?.includes('blank_');
        obj.selectable = false;
        obj.evented = false;
    });

    if (currentImage) {
        const isBlank = currentImage._element?.src?.includes('blank_');
        currentImage.selectable = !isBlank;
        currentImage.evented = !isBlank;
    }
}

document.getElementById('drawLineBtn').addEventListener('click', function() {
    activateSnapLineTool();
    setActiveTool(this);
    lockImage(true);
});
document.getElementById('drawCircleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('circle');
    lockImage(true);
});

document.getElementById('drawRectBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('rect');
    lockImage(true);
});
document.getElementById('drawTriangleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('triangle');
    lockImage(true);
});
document.getElementById('drawRightTriangleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('rightTriangle');
    lockImage(true);
});
document.getElementById('drawEllipseBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('ellipse');
    lockImage(true);
});
document.getElementById('drawStarBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('star');
    lockImage(true);
});
document.getElementById('drawHeartBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('heart');
    lockImage(true);
});
document.getElementById('drawArrowBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('arrow');
    lockImage(true);
});
document.getElementById('drawSpeechBubbleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('speechBubble');
    lockImage(true);
});
document.getElementById('drawRoundedSpeechBubbleBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('roundedSpeechBubble');
    lockImage(true);
});
document.getElementById('drawRoundedRectBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('roundedRect');
    lockImage(true);
});
document.getElementById('drawArrowRightBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('arrowRight');
    lockImage(true);
});
document.getElementById('drawHexagonBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('hexagon');
    lockImage(true);
});
document.getElementById('drawCrossBtn').addEventListener('click', function() {
    setActiveTool(this);
    enableDrawing('cross');
    lockImage(true);
});
document.getElementById('drawSelectBtn').onclick = () => {
    deactivateSnapLineTool();
    canvas.isDrawingMode = false;
    drawMode = null;
    canvas.selection = true;
    lockImage(false);
    setActiveTool(document.getElementById('drawSelectBtn'));

    canvas.getObjects().forEach(obj => {
        const isBlank = obj._element?.src?.includes('blank_');
        obj.selectable = !isBlank;
        obj.evented = !isBlank;
    });

    canvas.discardActiveObject();
    canvas.requestRenderAll();
};
document.getElementById('drawBrushBtn').addEventListener('click', function () {
    deactivateSnapLineTool();
    setActiveTool(this);

    drawMode = null;
    canvas.selection = false;
    lockImage(true);

    canvas.isDrawingMode = true;
    canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
    canvas.freeDrawingBrush.color = getStrokeColor();
    canvas.freeDrawingBrush.width = parseInt(document.getElementById('brushWidth').value)
});

// Funkce pro mazání
function eraseAtPoint(pointer) {
    const eraserRadius = ERASER_RADIUS;
    const objects = canvas.getObjects().slice();

    for (let i = objects.length - 1; i >= 0; i--) {
        const obj = objects[i];

        if (!obj.erasable || obj.type !== 'path') continue;

        const pathData = obj.path;
        if (!pathData || pathData.length < 2) continue;

        // Získá transformační matici
        const matrix = obj.calcTransformMatrix();
        const offsetX = obj.pathOffset ? obj.pathOffset.x : 0;
        const offsetY = obj.pathOffset ? obj.pathOffset.y : 0;

        const eraseIndices = new Set();

        for (let j = 0; j < pathData.length; j++) {
            const seg = pathData[j];
            let px, py;

            if (seg[0] === 'M' || seg[0] === 'L') {
                px = seg[1];
                py = seg[2];
            } else if (seg[0] === 'Q') {
                px = seg[3];
                py = seg[4];
            } else if (seg[0] === 'C') {
                px = seg[5];
                py = seg[6];
            } else {
                continue;
            }

            // odečte offset a aplikuje matici
            const localPoint = new fabric.Point(px - offsetX, py - offsetY);
            const canvasPoint = fabric.util.transformPoint(localPoint, matrix);
            
            const dist = Math.hypot(pointer.x - canvasPoint.x, pointer.y - canvasPoint.y);

            if (dist <= eraserRadius) {
                // Maže body které jsou pod kurzorem
                eraseIndices.add(j);
            }
        }

        if (eraseIndices.size === 0) continue;

        canvas.remove(obj);

        if (eraseIndices.size >= pathData.length - 2) {
            canvas.requestRenderAll();
            continue;
        }

        const newPathData = [];
        let needNewMove = false;

        for (let j = 0; j < pathData.length; j++) {
            if (eraseIndices.has(j)) {
                needNewMove = true;
                continue;
            }

            const seg = [...pathData[j]];

            if (needNewMove && seg[0] !== 'M') {
                if (seg[0] === 'L') {
                    newPathData.push(['M', seg[1], seg[2]]);
                } else if (seg[0] === 'Q') {
                    newPathData.push(['M', seg[3], seg[4]]);
                } else if (seg[0] === 'C') {
                    newPathData.push(['M', seg[5], seg[6]]);
                }
                needNewMove = false;
                continue;
            }

            newPathData.push(seg);
        }

        if (newPathData.length >= 2) {
            try {
                const pathString = newPathData.map(seg => seg.join(' ')).join(' ');
                const newPath = new fabric.Path(pathString, {
                    stroke: obj.stroke,
                    strokeWidth: obj.strokeWidth,
                    fill: null,
                    // Zachovat původní styl čáry
                    strokeLineCap: obj.strokeLineCap || 'round',
                    strokeLineJoin: obj.strokeLineJoin || 'round',
                    strokeDashArray: obj.strokeDashArray || null,
                    strokeUniform: obj.strokeUniform !== undefined ? obj.strokeUniform : true,
                    selectable: false,
                    evented: false,
                    erasable: true,
                    layer: 'draw',
                    objectCaching: false
                });
                canvas.add(newPath);
            } catch (e) {}
        }

        canvas.requestRenderAll();
    }
}

document.getElementById('drawEraserBtn').addEventListener('click', function () {
    setActiveTool(this);
    drawMode = 'eraser';
    canvas.isDrawingMode = false;
    canvas.selection = false;
    canvas.defaultCursor = 'none';
    canvas.hoverCursor = 'none';
    lockImage(true);
});

// Transformace - otočení a převrácení
document.getElementById('rotate90CW').addEventListener('click', function() {
    if (!currentImage) return;
    currentImage.rotate((currentImage.angle + 90) % 360);
    canvas.requestRenderAll();
    updateRotationAngle();
    saveHistoryState('rotate-90-cw');
});

document.getElementById('rotate90CCW').addEventListener('click', function() {
    if (!currentImage) return;
    currentImage.rotate((currentImage.angle - 90 + 360) % 360);
    canvas.requestRenderAll();
    updateRotationAngle();
    saveHistoryState('rotate-90-ccw');
});

document.getElementById('rotate180').addEventListener('click', function() {
    if (!currentImage) return;
    currentImage.rotate((currentImage.angle + 180) % 360);
    canvas.requestRenderAll();
    updateRotationAngle();
    saveHistoryState('rotate-180');
});

document.getElementById('flipHorizontal').addEventListener('click', function() {
    if (!currentImage) return;
    currentImage.set('flipX', !currentImage.flipX);
    canvas.requestRenderAll();
    saveHistoryState('flip-horizontal');
});

document.getElementById('flipVertical').addEventListener('click', function() {
    if (!currentImage) return;
    currentImage.set('flipY', !currentImage.flipY);
    canvas.requestRenderAll();
    saveHistoryState('flip-vertical');
});

document.getElementById('applyCustomAngle').addEventListener('click', function() {
    if (!currentImage) return;
    const angle = parseFloat(document.getElementById('customAngle').value) || 0;
    currentImage.rotate((currentImage.angle + angle) % 360);
    canvas.requestRenderAll();
    updateRotationAngle();
    saveHistoryState('rotate-custom');
    document.getElementById('customAngle').value = '';
});

// Mřížka - pomůcka pro kreslení
let gridEnabled = false;
let gridLines = [];

document.getElementById('toggleGridBtn').addEventListener('click', function() {
    gridEnabled = !gridEnabled;
    this.classList.toggle('active', gridEnabled);

    if (gridEnabled) {
        drawGrid();
    } else {
        removeGrid();
    }
});

// Vypnout mřížku při pohybu/transformaci obrázku
canvas.on('object:moving', function(e) {
    if (e.target === currentImage && gridEnabled) {
        gridEnabled = false;
        document.getElementById('toggleGridBtn').classList.remove('active');
        removeGrid();
    }
});

canvas.on('object:scaling', function(e) {
    if (e.target === currentImage && gridEnabled) {
        gridEnabled = false;
        document.getElementById('toggleGridBtn').classList.remove('active');
        removeGrid();
    }
});

canvas.on('object:rotating', function(e) {
    if (e.target === currentImage && gridEnabled) {
        gridEnabled = false;
        document.getElementById('toggleGridBtn').classList.remove('active');
        removeGrid();
    }
});

function drawGrid() {
    removeGrid();

    if (!currentImage) return;

    // Získat rozměry obrázku
    const imgBounds = currentImage.getBoundingRect(true);
    const imgLeft = imgBounds.left;
    const imgTop = imgBounds.top;
    const imgWidth = imgBounds.width;
    const imgHeight = imgBounds.height;

    // Velikost buňky mřížky (50px)
    const gridSize = 50;

    // Vertikální čáry
    for (let x = imgLeft; x <= imgLeft + imgWidth; x += gridSize) {
        const line = new fabric.Line([x, imgTop, x, imgTop + imgHeight], {
            stroke: 'rgba(100, 150, 255, 0.3)',
            strokeWidth: 1,
            selectable: false,
            evented: false,
            excludeFromExport: true,
            isGrid: true
        });
        canvas.add(line);
        gridLines.push(line);
    }

    // Horizontální čáry
    for (let y = imgTop; y <= imgTop + imgHeight; y += gridSize) {
        const line = new fabric.Line([imgLeft, y, imgLeft + imgWidth, y], {
            stroke: 'rgba(100, 150, 255, 0.3)',
            strokeWidth: 1,
            selectable: false,
            evented: false,
            excludeFromExport: true,
            isGrid: true
        });
        canvas.add(line);
        gridLines.push(line);
    }

    // Poslat mřížku dozadu (ale před background)
    gridLines.forEach(line => {
        line.moveTo(1); // Za background (index 0)
    });

    canvas.requestRenderAll();
}

function removeGrid() {
    gridLines.forEach(line => canvas.remove(line));
    gridLines = [];
    canvas.requestRenderAll();
}

// Překreslit mřížku při změně zoomu nebo panu
canvas.on('after:render', function() {
    if (gridEnabled && gridLines.length > 0) {
        // Mřížka zůstává na místě, nemusíme ji překreslovat
    }
});

// Zamknutí/odemknutí obrázku
document.getElementById('lockObjectBtn').addEventListener('click', function() {
    if (!currentImage) {
        alert('Není načten žádný obrázek');
        return;
    }
    
    // Šablony (blank) nelze odemknout
    const isBlank = currentImage._element?.src?.includes('blank_');
    if (isBlank) {
        alert('Šablonu nelze odemknout');
        return;
    }
    
    const isLocked = currentImage.lockMovementX && currentImage.lockMovementY;
    
    currentImage.set({
        lockMovementX: !isLocked,
        lockMovementY: !isLocked,
        lockScalingX: !isLocked,
        lockScalingY: !isLocked,
        lockRotation: !isLocked,
        hasControls: isLocked,
        hasBorders: isLocked,
        selectable: isLocked,
        evented: isLocked
    });
    
    // Vizualizace stavu tlačítka
    this.classList.toggle('active', !isLocked);
    
    canvas.discardActiveObject();
    canvas.requestRenderAll();
});

// Skrytí kurzoru gumy při změně nástroje
function setActiveTool(btn) {
    hideEraserCursor();
    canvas.defaultCursor = 'default';
    canvas.hoverCursor = 'move';
    
    document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

//zamknutí obrázku
function lockImage(lock = true) {
    if (!currentImage) return;

    currentImage.set({
        selectable: !lock,
        evented: !lock,
        lockMovementX: lock,
        lockMovementY: lock,
        lockScalingX: lock,
        lockScalingY: lock,
        lockRotation: lock
    });
}
//odstranění objektu klávesou Delete/Backspace
document.addEventListener('keydown', (e) => {
    if (e.key === 'Delete' || e.key === 'Backspace') {
        const activeObj = canvas.getActiveObject();
        if (!activeObj) return;
        
        // Pokud je to text v editačním režimu, necháme Fabric.js zpracovat klávesu
        if ((activeObj.type === 'i-text' || activeObj.type === 'textbox') && activeObj.isEditing) {
            return; // Nemazat objekt, nechat smazat písmeno
        }

        if (targetContainsCurrentImage(activeObj)) {
            return;
        }

        const didRemove = removeCanvasTarget(activeObj);
        if (didRemove) {
            canvas.requestRenderAll();
            hideContextMenu();
            hideTextToolbar();
            updateDrawFloatingToolbarVisibility();
            saveHistoryState(activeObj.type === 'activeSelection' ? 'delete multiple' : 'delete');
        }
    }
});

function updateLayersVisibility() {
    // Use anyChecked to handle multiple checkboxes in different panels
    const showImage = anyChecked('layerImageCheck');
    const showDraw = anyChecked('layerDrawCheck');
    const showText = anyChecked('layerTextCheck');

    canvas.getObjects().forEach(obj => {
        // Both 'background' and 'image' layers controlled by layerImageCheck
        if (obj.layer === 'background' || obj.layer === 'image') obj.visible = showImage;
        if (obj.layer === 'draw') obj.visible = showDraw;
        if (obj.layer === 'text') obj.visible = showText;
    });

    canvas.requestRenderAll();
}

// Synchronizace všech checkboxů vrstev
function syncLayerCheckboxes(layerClass, checked) {
    document.querySelectorAll(`.${layerClass}`).forEach(cb => cb.checked = checked);
    updateLayersVisibility();
}

document.querySelectorAll('.layerImageCheck').forEach(cb => {
    cb.addEventListener('change', (e) => syncLayerCheckboxes('layerImageCheck', e.target.checked));
});
document.querySelectorAll('.layerDrawCheck').forEach(cb => {
    cb.addEventListener('change', (e) => syncLayerCheckboxes('layerDrawCheck', e.target.checked));
});
document.querySelectorAll('.layerTextCheck').forEach(cb => {
    cb.addEventListener('change', (e) => syncLayerCheckboxes('layerTextCheck', e.target.checked));
});

// Synchronizace UI s vybraným objektem
canvas.on('selection:created', handleSelectionChange);
canvas.on('selection:updated', handleSelectionChange);
canvas.on('selection:cleared', handleSelectionChange);

canvas.on('mouse:dblclick', function(opt) {
    const target = opt.target;

    if (!target || target.type !== 'image' || target.layer !== 'image') return;

    if (currentImage && currentImage !== target) {
        currentImage.layer = 'image';
        currentImage.set({
            selectable: true,
            evented: true
        });
    }

    target.layer = 'background';
    currentImage = target;

    // poslat dozadu
    currentImage.sendToBack();

    // Update UI
    updateImageSize();
    updateRotationAngle();
    canvas.requestRenderAll();

    showToast('Obrázek nastaven jako základní', 'success');
});

function handleSelectionChange(e) {
    const activeObject = canvas.getActiveObject();
    
    // Vypnout endpoint controls pokud se vybere jiný objekt než čára
    if (currentEditingLine && activeObject !== currentEditingLine) {
        disableLineEndpointsControls(currentEditingLine);
        currentEditingLine = null;
    }

    if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox')) {
        updateTextControlsUI(activeObject);
        hideDrawFloatingToolbar();
    } else {
        if (activeObject) {
            syncDrawingControls(activeObject);
            if (activeObject.layer === 'draw' || 
                (activeObject.type === 'activeSelection' && activeObject.getObjects().some(o => o.layer === 'draw'))) {
                updateDrawFloatingToolbarVisibility();
            }
        } else {
            if (!drawMode || drawMode === 'select' || drawMode === 'textSelect') {
                hideDrawFloatingToolbar();
            }
        }
        resetTextControlsUI();
    }
}

function syncDrawingControls(obj) {
    if (!obj || obj === currentImage || obj === cropRect || obj === eraserCursor) return;

    // Synchronizace barvy obrysu
    if (obj.stroke) {
        document.getElementById('drawColor').value = obj.stroke;
    }

    // Synchronizace barvy výplně
    if (obj.fill === '' || obj.fill === null || obj.fill === 'transparent') {
        document.getElementById('fillTransparent').checked = true;
    } else if (obj.fill) {
        document.getElementById('fillColor').value = obj.fill;
        document.getElementById('fillTransparent').checked = false;
    }

    // Synchronizace šířky čáry
    if (obj.strokeWidth) {
        document.getElementById('brushWidth').value = obj.strokeWidth;
    }

    // Synchronizace stylu čáry
    const dash = obj.strokeDashArray;
    if (!dash || dash.length === 0) {
        document.getElementById('strokeStyle').value = 'solid';
    } else if (dash[0] > dash[1]) {
        document.getElementById('strokeStyle').value = 'dashed';
    } else {
        document.getElementById('strokeStyle').value = 'dotted';
    }

    // Synchronizace zakončení čáry
    if (obj.strokeLineCap) {
        document.getElementById('strokeCap').value = obj.strokeLineCap;
    }
}

document.getElementById('drawColor').addEventListener('input', () => {
    const color = document.getElementById('drawColor').value;
    
    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.stroke !== undefined) obj.set({ stroke: color });
                if (obj.type === 'group' && obj._objects) {
                    obj._objects.forEach(child => {
                        if (child.stroke !== undefined) child.set({ stroke: color });
                    });
                }
            });
        } else {
            if (activeObj.stroke !== undefined) activeObj.set({ stroke: color });
            if (activeObj.type === 'group' && activeObj._objects) {
                activeObj._objects.forEach(child => {
                    if (child.stroke !== undefined) child.set({ stroke: color });
                });
            }
        }
        canvas.requestRenderAll();
    }

    if (
        canvas.isDrawingMode &&
        canvas.freeDrawingBrush instanceof fabric.PencilBrush
    ) {
        canvas.freeDrawingBrush.color = color;
    }
});

// Aktualizace barvy výplně aktivního objektu
document.getElementById('fillColor').addEventListener('input', () => {
    const fillColor = document.getElementById('fillColor').value;
    const isTransparent = document.getElementById('fillTransparent').checked;
    const fillVal = isTransparent ? 'transparent' : fillColor;
    
    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.fill !== undefined) obj.set({ fill: fillVal });
            });
        } else {
            if (activeObj.fill !== undefined) activeObj.set({ fill: fillVal });
        }
        canvas.requestRenderAll();
    }
});

// Aktualizace průhledné výplně aktivního objektu
document.getElementById('fillTransparent').addEventListener('change', () => {
    const isTransparent = document.getElementById('fillTransparent').checked;
    const fillVal = isTransparent ? 'transparent' : document.getElementById('fillColor').value;
    
    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.fill !== undefined) obj.set({ fill: fillVal });
            });
        } else {
            if (activeObj.fill !== undefined) activeObj.set({ fill: fillVal });
        }
        canvas.requestRenderAll();
    }
});

// velikost tužky
document.getElementById('brushWidth').addEventListener('input', (e) => {
    const width = parseInt(e.target.value);

    const activeObj = canvas.getActiveObject();
    if (activeObj) {
        if (activeObj.type === 'activeSelection') {
            activeObj.getObjects().forEach(obj => {
                if (obj.strokeWidth !== undefined) {
                    obj.set({ strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width) });
                }
                if (obj.type === 'group' && obj._objects) {
                    obj._objects.forEach(child => {
                        if (child.strokeWidth !== undefined) child.set({ strokeWidth: width });
                    });
                }
            });
        } else {
            if (activeObj.strokeWidth !== undefined) {
                activeObj.set({ strokeWidth: width, strokeDashArray: getDashFromUIForWidth(width) });
            }
            if (activeObj.type === 'group' && activeObj._objects) {
                activeObj._objects.forEach(child => {
                    if (child.strokeWidth !== undefined) child.set({ strokeWidth: width });
                });
            }
        }
        canvas.requestRenderAll();
    }

    if (
        canvas.isDrawingMode &&
        canvas.freeDrawingBrush instanceof fabric.PencilBrush
    ) {
        canvas.freeDrawingBrush.width = width;
    }
});
// velikost gumy
document.getElementById('eraserSize').addEventListener('input', (e) => {
    ERASER_RADIUS = parseInt(e.target.value);

    if (eraserCursor) {
        eraserCursor.set({
            radius: ERASER_RADIUS
        });
        canvas.requestRenderAll();
    }
});

// Úprava vlastností textu
function applyToActiveText(props) {
    const obj = canvas.getActiveObject();
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;

    // Klíče, které Fabric.js podporuje pro selection styles
    const selectionKeys = ['fontWeight', 'fontStyle', 'underline', 'linethrough', 'fill', 'fontFamily', 'fontSize'];
    const selectionProps = {};
    let hasSelectionProp = false;
    Object.keys(props).forEach(key => {
        if (selectionKeys.includes(key)) {
            selectionProps[key] = props[key];
            hasSelectionProp = true;
        }
    });

    // Pokud je textový objekt v editačním módu a má aktivní výběr, aplikuj styl jen na výběr
    if ((obj.type === 'i-text' || obj.type === 'textbox') && obj.isEditing && obj.selectionStart !== obj.selectionEnd && hasSelectionProp) {
        obj.setSelectionStyles(selectionProps, obj.selectionStart, obj.selectionEnd);
        obj.text = obj.text; // force re-render
        obj.setCoords();
        canvas.requestRenderAll();
        scheduleTextHistory();
        return;
    }
    // Jinak aplikuj na celý textbox
    obj.set(props);
    obj.setCoords();
    canvas.requestRenderAll();
    scheduleTextHistory();
}

// Synchronizace textových ovladačů s aktivním objektem
function updateTextControlsUI(obj) {
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) {
        resetTextControlsUI();
        return;
    }

    document.getElementById('textInput').value = obj.text || '';
    document.getElementById('textSize').value = obj.fontSize;
    document.getElementById('textColor').value = obj.fill;
    setFontPickerValue(obj.fontFamily || 'Arial');

    // Pokud je textový objekt v editačním módu a má aktivní výběr, načti styl z výběru
    if ((obj.type === 'i-text' || obj.type === 'textbox') && obj.isEditing && obj.selectionStart !== obj.selectionEnd) {
        const styles = obj.getSelectionStyles(obj.selectionStart, obj.selectionEnd)[0] || {};
        textStyles.bold = styles.fontWeight === 'bold';
        textStyles.italic = styles.fontStyle === 'italic';
        textStyles.underline = styles.underline === true;
        textStyles.linethrough = styles.linethrough === true;
        document.getElementById('textSize').value = styles.fontSize || obj.fontSize;
        document.getElementById('textColor').value = styles.fill || obj.fill;
        setFontPickerValue(styles.fontFamily || obj.fontFamily || 'Arial');
    } else {
        textStyles.bold = obj.fontWeight === 'bold';
        textStyles.italic = obj.fontStyle === 'italic';
        textStyles.underline = obj.underline === true;
        textStyles.linethrough = obj.linethrough === true;
    }
    updateStyleButtons();
    currentTextAlign = obj.textAlign || 'left';
    updateAlignButtons();
}

function resetTextControlsUI() {
    document.getElementById('textInput').value = '';
    document.getElementById('textSize').value = 32;
    document.getElementById('textColor').value = '#000000';
    setFontPickerValue('Arial');

    textStyles.bold = false;
    textStyles.italic = false;
    textStyles.underline = false;
    textStyles.linethrough = false;
    updateStyleButtons();
    
    currentTextAlign = 'left';
    updateAlignButtons();
}

document.getElementById('textInput').addEventListener('input', e => {
    applyToActiveText({ text: e.target.value });
});
document.getElementById('textSize').addEventListener('input', e => {
    const obj = canvas.getActiveObject();
    if (obj) applyStyleToSelectionOrAll(obj, { fontSize: parseInt(e.target.value) });
});
document.getElementById('textColor').addEventListener('input', e => {
    const obj = canvas.getActiveObject();
    if (obj) {
        const newColor = e.target.value;
        
        // Aktualizovat _originalFill na novou barvu, aby filtry fungovaly správně
        obj._originalFill = newColor;
        
        // Vypni editing mód dočasně pro správné nastavení fill
        const wasEditing = obj.isEditing;
        if (wasEditing) {
            obj.exitEditing();
        }
        
        // Nastav barvu na celý objekt
        obj.set({ fill: newColor });
        obj.dirty = true;
        
        // Synchronizuj s horním toolbarem
        const topToolbarColor = document.getElementById('textToolbarColor');
        if (topToolbarColor) topToolbarColor.value = newColor;

        // Pokud jsou zapnuté filtry na text, znovu je aplikovat na novou barvu
        const filterText = document.getElementById('filterLayerText')?.checked ?? false;
        if (filterText) {
            applyFilters();
        }
        
        canvas.requestRenderAll();
        
        // Vrať editing mód
        if (wasEditing) {
            obj.enterEditing();
        }
        
        scheduleTextHistory();
    }
});

// Zarovnání textu
let currentTextAlign = 'left';

function updateAlignButtons() {
    document.querySelectorAll('.text-align-btn').forEach(btn => btn.classList.remove('bg-pink-200'));
    const align = currentTextAlign.charAt(0).toUpperCase() + currentTextAlign.slice(1);
    const activeBtn = document.getElementById('align' + align);
    if(activeBtn) activeBtn.classList.add('bg-pink-200');
}

function setTextAlign(align) {
    currentTextAlign = align;
    updateAlignButtons();
    applyToActiveText({ textAlign: align });
}

document.getElementById('alignLeft').addEventListener('click', () => setTextAlign('left'));
document.getElementById('alignCenter').addEventListener('click', () => setTextAlign('center'));
document.getElementById('alignRight').addEventListener('click', () => setTextAlign('right'));

// Styly textu (tučné, kurzíva, podtržení, přeškrtnutí)
let textStyles = {
    bold: false,
    italic: false,
    underline: false,
    linethrough: false
};

function updateStyleButtons() {
    document.getElementById('textBold').classList.toggle('bg-pink-200', textStyles.bold);
    document.getElementById('textItalic').classList.toggle('bg-pink-200', textStyles.italic);
    document.getElementById('textUnderline').classList.toggle('bg-pink-200', textStyles.underline);
    document.getElementById('textLinethrough').classList.toggle('bg-pink-200', textStyles.linethrough);
}

// Aplikuje styly na vybraný text nebo celý objekt
function applyStyleToSelectionOrAll(obj, styleProps) {
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;

    const start = obj.selectionStart;
    const end = obj.selectionEnd;

    // Pokud je v editačním módu a má výběr, aplikuj jen na výběr
    if (obj.isEditing && typeof start !== 'undefined' && typeof end !== 'undefined' && start !== end) {
        obj.setSelectionStyles(styleProps, start, end);
    } else {
        // Jinak aplikuj na celý text
        obj.set(styleProps);
    }

    obj.dirty = true;
    canvas.requestRenderAll();
    scheduleTextHistory();
}

function toggleTextStyle(style) {
    const activeObject = canvas.getActiveObject();
    let currentState = textStyles[style];
    // Pokud je textový objekt v editačním módu a má aktivní výběr, zjisti styl z výběru
    if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox') && activeObject.isEditing && activeObject.selectionStart !== activeObject.selectionEnd) {
        const styles = activeObject.getSelectionStyles(activeObject.selectionStart, activeObject.selectionEnd)[0] || {};
        if (style === 'bold') currentState = styles.fontWeight === 'bold';
        if (style === 'italic') currentState = styles.fontStyle === 'italic';
        if (style === 'underline') currentState = styles.underline === true;
        if (style === 'linethrough') currentState = styles.linethrough === true;
    } else if (activeObject && (activeObject.type === 'i-text' || activeObject.type === 'textbox')) {
        if (style === 'bold') currentState = activeObject.fontWeight === 'bold';
        if (style === 'italic') currentState = activeObject.fontStyle === 'italic';
        if (style === 'underline') currentState = activeObject.underline === true;
        if (style === 'linethrough') currentState = activeObject.linethrough === true;
    }

    const newState = !currentState;
    textStyles[style] = newState;
    updateStyleButtons();
    const props = {};
    if (style === 'bold') props.fontWeight = newState ? 'bold' : 'normal';
    if (style === 'italic') props.fontStyle = newState ? 'italic' : 'normal';
    if (style === 'underline') props.underline = newState;
    if (style === 'linethrough') props.linethrough = newState;
    const obj = canvas.getActiveObject();
    if (obj) applyStyleToSelectionOrAll(obj, props);
    scheduleTextHistory();
    syncTextToolbarFromObject(obj);
}

document.getElementById('textBold').addEventListener('click', () => toggleTextStyle('bold'));
document.getElementById('textItalic').addEventListener('click', () => toggleTextStyle('italic'));
document.getElementById('textUnderline').addEventListener('click', () => toggleTextStyle('underline'));
document.getElementById('textLinethrough').addEventListener('click', () => toggleTextStyle('linethrough'));


// Font Picker Dropdown
const fontCategories = {
    'Bezpatkové': ['Arial', 'Verdana', 'Tahoma', 'Trebuchet MS', 'Segoe UI', 'Open Sans', 'Roboto', 'Lato', 'Montserrat'],
    'Patkové': ['Times New Roman', 'Georgia', 'Palatino Linotype', 'Book Antiqua', 'Garamond', 'Playfair Display'],
    'Neproporcionální': ['Courier New', 'Consolas', 'Monaco', 'Lucida Console', 'Source Code Pro'],
    'Rukopisné': ['Comic Sans MS', 'Brush Script MT'],
    'Dekorativní': ['Impact', 'Oswald', 'Lobster']
};

const fontPickerBtn = document.getElementById('fontPickerBtn');
const fontPickerDropdown = document.getElementById('fontPickerDropdown');
const fontPickerArrow = document.getElementById('fontPickerArrow');
const fontPickerLabel = document.getElementById('fontPickerLabel');
const fontList = document.getElementById('fontList');
const fontSearch = document.getElementById('fontSearch');
const textFontInput = document.getElementById('textFont');

function renderFontList(filter = '') {
    fontList.innerHTML = '';
    const filterLower = filter.toLowerCase();
    
    for (const [category, fonts] of Object.entries(fontCategories)) {
        const filteredFonts = fonts.filter(f => f.toLowerCase().includes(filterLower));
        if (filteredFonts.length === 0) continue;
        
        const categoryEl = document.createElement('div');
        categoryEl.className = 'text-xs font-semibold text-gray-500 uppercase mt-2 mb-1 px-2';
        categoryEl.textContent = category;
        fontList.appendChild(categoryEl);
        
        filteredFonts.forEach(font => {
            const fontEl = document.createElement('div');
            fontEl.className = 'font-item px-3 py-2 rounded cursor-pointer hover:bg-pink-100 transition text-lg';
            fontEl.style.fontFamily = font;
            fontEl.textContent = font;
            fontEl.dataset.font = font;
            
            if (font === textFontInput.value) {
                fontEl.classList.add('bg-pink-200');
            }
            
            fontEl.addEventListener('click', () => selectFont(font));
            fontList.appendChild(fontEl);
        });
    }
}

function selectFont(font) {
    textFontInput.value = font;
    fontPickerLabel.textContent = font;
    fontPickerLabel.style.fontFamily = font;
    closeFontPicker();
    
    // Aktualizace aktivního textu
    applyToActiveText({ fontFamily: font });
    
    textFontInput.dispatchEvent(new Event('change'));
}

function setFontPickerValue(font) {
    textFontInput.value = font;
    fontPickerLabel.textContent = font;
    fontPickerLabel.style.fontFamily = font;
}

function openFontPicker() {
    renderFontList();
    fontSearch.value = '';
    fontPickerDropdown.classList.remove('hidden');
    fontPickerArrow.classList.add('rotate-180');
    fontSearch.focus();
}

function closeFontPicker() {
    fontPickerDropdown.classList.add('hidden');
    fontPickerArrow.classList.remove('rotate-180');
}

function toggleFontPicker() {
    if (fontPickerDropdown.classList.contains('hidden')) {
        openFontPicker();
    } else {
        closeFontPicker();
    }
}

fontPickerBtn.addEventListener('click', toggleFontPicker);

fontSearch.addEventListener('input', (e) => {
    renderFontList(e.target.value);
});

// Zavření při kliknutí mimo dropdown
document.addEventListener('click', (e) => {
    if (!fontPickerBtn.contains(e.target) && !fontPickerDropdown.contains(e.target)) {
        closeFontPicker();
    }
});

// ========== KONTEXTOVÉ MENU ==========
function targetContainsCurrentImage(target) {
    return !!(target && (
        target === currentImage ||
        (target.type === 'activeSelection' && target.getObjects().includes(currentImage))
    ));
}

function clearCurrentImageState() {
    if (cropTargetImage === currentImage) {
        exitCropMode();
    }

    if (gridEnabled) {
        gridEnabled = false;
        document.getElementById('toggleGridBtn')?.classList.remove('active');
        removeGrid();
    }

    currentImage = null;
    previousAngle = 0;
    updateImageSize();
    updateRotationAngle();
}

function withMeasuredElement(element, callback) {
    if (!element) return null;

    const wasHidden = element.classList.contains('hidden');
    const previousVisibility = element.style.visibility;

    if (wasHidden) {
        element.style.visibility = 'hidden';
        element.classList.remove('hidden');
    }

    const rect = element.getBoundingClientRect();
    const result = callback(rect);

    if (wasHidden) {
        element.classList.add('hidden');
    }

    element.style.visibility = previousVisibility;
    return result;
}

function getWorkspaceViewportBounds(padding = 12) {
    const workspaceRect = canvasWorkspace?.getBoundingClientRect();

    return {
        left: workspaceRect ? Math.max(workspaceRect.left + padding, padding) : padding,
        top: workspaceRect ? Math.max(workspaceRect.top + padding, padding) : padding,
        right: workspaceRect ? Math.min(workspaceRect.right - padding, window.innerWidth - padding) : window.innerWidth - padding,
        bottom: workspaceRect ? Math.min(workspaceRect.bottom - padding, window.innerHeight - padding) : window.innerHeight - padding,
    };
}

function clampOverlayPosition(desiredLeft, desiredTop, width, height, padding = 12) {
    const bounds = getWorkspaceViewportBounds(padding);
    const maxLeft = Math.max(bounds.left, bounds.right - width);
    const maxTop = Math.max(bounds.top, bounds.bottom - height);

    return {
        left: Math.min(Math.max(desiredLeft, bounds.left), maxLeft),
        top: Math.min(Math.max(desiredTop, bounds.top), maxTop),
    };
}

function getObjectViewportBounds(target) {
    if (!target) return null;

    const canvasRect = canvas.upperCanvasEl.getBoundingClientRect();
    const viewportTransform = canvas.viewportTransform || [1, 0, 0, 1, 0, 0];
    let coords = [];

    try {
        if (typeof target.calcACoords === 'function') {
            const absoluteCoords = target.calcACoords();
            coords = [absoluteCoords.tl, absoluteCoords.tr, absoluteCoords.br, absoluteCoords.bl].filter(Boolean);
        } else if (typeof target.getCoords === 'function') {
            coords = target.getCoords();
        }
    } catch (err) {
        coords = [];
    }

    if (!coords.length) {
        const targetRect = target.getBoundingRect();

        return {
            left: canvasRect.left + targetRect.left,
            top: canvasRect.top + targetRect.top,
            width: targetRect.width,
            height: targetRect.height,
            right: canvasRect.left + targetRect.left + targetRect.width,
            bottom: canvasRect.top + targetRect.top + targetRect.height,
            centerX: canvasRect.left + targetRect.left + (targetRect.width / 2),
        };
    }

    const viewportPoints = coords.map(function(point) {
        return fabric.util.transformPoint(new fabric.Point(point.x, point.y), viewportTransform);
    });
    const xs = viewportPoints.map(point => point.x);
    const ys = viewportPoints.map(point => point.y);
    const left = Math.min(...xs);
    const right = Math.max(...xs);
    const top = Math.min(...ys);
    const bottom = Math.max(...ys);

    return {
        left: canvasRect.left + left,
        top: canvasRect.top + top,
        width: right - left,
        height: bottom - top,
        right: canvasRect.left + right,
        bottom: canvasRect.top + bottom,
        centerX: canvasRect.left + ((left + right) / 2),
    };
}

function positionFixedOverlay(element, desiredLeft, desiredTop, padding = 12) {
    return withMeasuredElement(element, function(rect) {
        const clamped = clampOverlayPosition(desiredLeft, desiredTop, rect.width, rect.height, padding);
        element.style.left = clamped.left + 'px';
        element.style.top = clamped.top + 'px';
        return clamped;
    });
}

function positionFixedOverlayNearObject(element, target, options = {}) {
    const objectBounds = getObjectViewportBounds(target);
    if (!element || !objectBounds) return false;

    const gap = options.gap ?? 12;

    return withMeasuredElement(element, function(rect) {
        const desiredLeft = objectBounds.centerX - (rect.width / 2);
        const bounds = getWorkspaceViewportBounds(gap);
        const fallbackTop = objectBounds.bottom + gap;
        let desiredTop = objectBounds.top - rect.height - gap;

        if (desiredTop < bounds.top && (fallbackTop + rect.height) <= bounds.bottom) {
            desiredTop = fallbackTop;
        }

        const clamped = clampOverlayPosition(desiredLeft, desiredTop, rect.width, rect.height, gap);
        element.style.left = clamped.left + 'px';
        element.style.top = clamped.top + 'px';
        return clamped;
    });
}

function positionWorkspaceMenuNearObject(element, target, options = {}) {
    const workspaceRect = canvasWorkspace?.getBoundingClientRect();
    const objectBounds = getObjectViewportBounds(target);
    if (!element || !workspaceRect || !objectBounds) return false;

    const gap = options.gap ?? 10;

    return withMeasuredElement(element, function(rect) {
        const desiredLeft = objectBounds.centerX - (rect.width / 2);
        const bounds = getWorkspaceViewportBounds(gap);
        const fallbackTop = objectBounds.bottom + gap;
        let desiredTop = objectBounds.top - rect.height - gap;

        if (desiredTop < bounds.top && (fallbackTop + rect.height) <= bounds.bottom) {
            desiredTop = fallbackTop;
        }

        const clamped = clampOverlayPosition(desiredLeft, desiredTop, rect.width, rect.height, gap);
        element.style.left = Math.max(0, clamped.left - workspaceRect.left) + 'px';
        element.style.top = Math.max(0, clamped.top - workspaceRect.top) + 'px';
        return clamped;
    });
}

function syncContextMenuActionAvailability(target) {
    if (!contextMenu) return;

    const lockMainImageActions = targetContainsCurrentImage(target);

    contextMenu.querySelectorAll('[data-action="copy"], [data-action="delete"], [data-action="bringForward"], [data-action="sendBackward"], [data-action="bringToFront"], [data-action="sendToBack"]').forEach(function(button) {
        button.disabled = lockMainImageActions;
        button.classList.toggle('opacity-50', lockMainImageActions);
        button.classList.toggle('cursor-not-allowed', lockMainImageActions);
    });
}

function cloneObjectToClipboard(target) {
    if (!target || targetContainsCurrentImage(target)) return;

    target.clone(function(cloned) {
        cloned.layer = target.layer;
        cloned.erasable = target.erasable;
        cloned._originalStroke = target._originalStroke;
        cloned._originalFill = target._originalFill;
        clipboard = cloned;
    }, HISTORY.extraProps);
}

function removeCanvasTarget(target) {
    if (!target) return false;

    if (targetContainsCurrentImage(target)) return false;

    if (target.type === 'activeSelection') {
        const objectsToRemove = target.getObjects().filter(obj => !obj._element?.src?.includes('blank_'));
        if (!objectsToRemove.length) return false;

        canvas.discardActiveObject();
        objectsToRemove.forEach(obj => canvas.remove(obj));

        return true;
    }

    if (target._element?.src?.includes('blank_')) return false;

    canvas.remove(target);
    canvas.discardActiveObject();

    return true;
}

function applyContextMenuAction(target, action) {
    if (!target) return false;

    const containsCurrentImage = targetContainsCurrentImage(target);

    switch (action) {
        case 'bringForward':
            if (containsCurrentImage) return false;
            canvas.bringForward(target);
            return true;
        case 'sendBackward':
            if (containsCurrentImage) return false;
            canvas.sendBackwards(target);
            return true;
        case 'bringToFront':
            if (containsCurrentImage) return false;
            canvas.bringToFront(target);
            return true;
        case 'sendToBack':
            if (containsCurrentImage) return false;
            canvas.sendToBack(target);
            return true;
        case 'delete':
            if (containsCurrentImage) return false;
            return removeCanvasTarget(target);
        case 'copy':
            if (containsCurrentImage) return false;
            cloneObjectToClipboard(target);
            return false;
        case 'paste':
            pasteObject();
            return false;
        default:
            return false;
    }
}

const contextMenu = document.getElementById('contextMenu');
let contextTarget = null;

// Zobrazení kontextového menu na pravé tlačítko
canvas.upperCanvasEl.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    hideContextMenu();
    
    const target = canvas.findTarget(e, false);
    
    if (target && !target._element?.src?.includes('blank_')) {
        contextTarget = target;
        canvas.setActiveObject(target);
        canvas.requestRenderAll();

        if (target.type === 'i-text' || target.type === 'textbox') {
            const menu = document.getElementById('textContextMenu');
            positionWorkspaceMenuNearObject(menu, target, { gap: 10 });
            menu.classList.remove('hidden');
        } 
        else if (
            targetContainsCurrentImage(target) ||
            target.layer === 'draw' ||
            target.layer === 'image' ||
            (target.type === 'activeSelection' && target.getObjects().some(o => o.layer === 'draw' || o.layer === 'image' || o === currentImage))
        ) {
            syncContextMenuActionAvailability(target);
            positionWorkspaceMenuNearObject(contextMenu, target, { gap: 10 });
            contextMenu.classList.remove('hidden');
            // Sync hodnoty pro styl čáry
            try { syncDrawContextMenuFromObject(target); } catch (err) {}
        }
    } else {
        hideContextMenu();
    }
});

// Skrytí menu při kliknutí jinam
document.addEventListener('click', function(e) {
    const textCtxMenu = document.getElementById('textContextMenu');
    if (!contextMenu.contains(e.target) && (!textCtxMenu || !textCtxMenu.contains(e.target))) {
        hideContextMenu();
    }
});

// Skrytí při scrollu nebo klávese Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideContextMenu();
        if (isFormatPainterActive) {
            isFormatPainterActive = false;
            formatClipboard = null;
            canvas.defaultCursor = 'default';
            canvas.hoverCursor = 'move';
            document.getElementById('copyFormatBtn').classList.remove('bg-green-500', 'text-white');
        }
    }
});

function hideContextMenu() {
    contextMenu.classList.add('hidden');
    const t = document.getElementById('textContextMenu');
    if (t) t.classList.add('hidden');
    contextTarget = null;
    syncContextMenuActionAvailability(null);
}

function syncTextContextMenuFromObject(obj) {
    // Text context menu now only has layer actions, no need to sync styling
}

function syncDrawContextMenuFromObject(obj) {
    if (!obj) return;
    try {
        // Pro multi-selection použij první draw objekt
        let targetObj = obj;
        if (obj.type === 'activeSelection') {
            targetObj = obj.getObjects().find(o => o.layer === 'draw') || obj.getObjects()[0];
        }
        
        const strokeColorEl = document.getElementById('ctxDrawStrokeColor');
        const fillColorEl = document.getElementById('ctxDrawFillColor');
        const fillTransparentEl = document.getElementById('ctxDrawFillTransparent');
        const strokeWidthEl = document.getElementById('ctxDrawStrokeWidth');
        const strokeStyleEl = document.getElementById('ctxDrawStrokeStyle');
        
        if (strokeColorEl && targetObj.stroke) strokeColorEl.value = targetObj.stroke;
        if (fillColorEl) {
            if (targetObj.fill && targetObj.fill !== 'transparent') {
                fillColorEl.value = targetObj.fill;
                if (fillTransparentEl) fillTransparentEl.checked = false;
            } else {
                if (fillTransparentEl) fillTransparentEl.checked = true;
            }
        }
        if (strokeWidthEl) strokeWidthEl.value = targetObj.strokeWidth || 1;
        if (strokeStyleEl) {
            const dash = targetObj.strokeDashArray;
            if (!dash || dash.length === 0) {
                strokeStyleEl.value = 'solid';
            } else if (dash[0] > dash[1]) {
                strokeStyleEl.value = 'dashed';
            } else {
                strokeStyleEl.value = 'dotted';
            }
        }
    } catch (err) {}
}

function showDrawFloatingAt(clientX, clientY, targetObj) {
    const el = document.getElementById('drawFloatingToolbar');
    if (!el) return;
    drawContextTarget = targetObj;

    if (targetObj) {
        positionFixedOverlayNearObject(el, targetObj, { gap: 14 });
    } else {
        positionFixedOverlay(el, clientX + 12, clientY + 12, 12);
    }

    el.classList.remove('hidden');

    try {
        const stroke = targetObj.stroke || '#000000';
        const fill = targetObj.fill || 'transparent';
        const width = targetObj.strokeWidth || 1;
        const dash = targetObj.strokeDashArray || null;
        const cap = targetObj.strokeLineCap || 'butt';

        document.getElementById('drawFloatingStrokeColor').value = stroke;
        document.getElementById('drawFloatingFillColor').value = (fill === 'transparent' ? '#ffffff' : fill);
        document.getElementById('drawFloatingStrokeWidth').value = width;
        if (dash && dash.length) {
            const w = parseFloat(width) || 1;
            if (dash[0] === w*2) document.getElementById('drawFloatingStrokeStyle').value = 'dashed';
            else document.getElementById('drawFloatingStrokeStyle').value = 'dotted';
        } else {
            document.getElementById('drawFloatingStrokeStyle').value = 'solid';
        }
        document.getElementById('drawFloatingStrokeCap').value = cap;
        document.getElementById('drawFloatingTransparent').checked = (fill === 'transparent');
    } catch (err) {}
}

function hideDrawFloatingToolbar() {
    const el = document.getElementById('drawFloatingToolbar');
    if (!el) return;
    el.classList.add('hidden');
    drawContextTarget = null;
}

const textContextMenu = document.getElementById('textContextMenu');
if (textContextMenu) {
    // Event handlery pro textové kontextové menu (layer actions)
    document.querySelectorAll('.text-context-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = contextTarget || canvas.getActiveObject();
            if (!target) return;
            
            const action = this.dataset.action;

            const didChange = applyContextMenuAction(target, action);
            if (didChange) saveHistoryState(`text-context-${action}`);
            
            canvas.requestRenderAll();
            hideContextMenu();
        });
    });
}

// Event handler pro font picker v text toolbaru
document.getElementById('tbFont')?.addEventListener('change', (e) => {
    const obj = canvas.getActiveObject();
    if (!obj) return;
    applyToActiveText({ fontFamily: e.target.value });
    setFontPickerValue(e.target.value);
    scheduleTextHistory();
    syncTextToolbarFromObject(obj);
});

// Clipboard pro kopírování/vkládání
let clipboard = null;

function copyObject() {
    const obj = canvas.getActiveObject();
    if (!obj || targetContainsCurrentImage(obj)) return;

    cloneObjectToClipboard(obj);
}

function pasteObject() {
    if (!clipboard) return;
    
    clipboard.clone(function(clonedObj) {
        canvas.discardActiveObject();
        
        clonedObj.layer = clipboard.layer;
        clonedObj.erasable = clipboard.erasable;
        clonedObj._originalStroke = clipboard._originalStroke;
        clonedObj._originalFill = clipboard._originalFill;
        
        clonedObj.set({
            left: clonedObj.left + 20,
            top: clonedObj.top + 20,
            evented: true,
            selectable: true
        });
        
        if (clonedObj.type === 'activeSelection') {
            clonedObj.canvas = canvas;
            clonedObj.forEachObject(function(obj, i) {
                if (clipboard._objects && clipboard._objects[i]) {
                    obj.layer = clipboard._objects[i].layer;
                    obj.erasable = clipboard._objects[i].erasable;
                    obj._originalStroke = clipboard._objects[i]._originalStroke;
                    obj._originalFill = clipboard._objects[i]._originalFill;
                }
                canvas.add(obj);
            });
            clonedObj.setCoords();
        } else {
            canvas.add(clonedObj);
        }
        
        clipboard.top += 20;
        clipboard.left += 20;
        canvas.setActiveObject(clonedObj);
        canvas.requestRenderAll();
        saveHistoryState('paste');
        applyFilters();
    }, HISTORY.extraProps);
}

// Klávesové zkratky Ctrl+C a Ctrl+V
document.addEventListener('keydown', function(e) {
    // Ctrl+C - kopírovat
    if (e.ctrlKey && e.key === 'c') {
        const obj = canvas.getActiveObject();
        if (obj && !targetContainsCurrentImage(obj)) {
            e.preventDefault();
            copyObject();
        }
    }
    
    // Ctrl+V - vložit
    if (e.ctrlKey && e.key === 'v') {
        if (clipboard) {
            e.preventDefault();
            pasteObject();
        }
    }
});

// Akce kontextového menu
document.querySelectorAll('.context-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!contextTarget) return;
        
        const action = this.dataset.action;

        const didChange = applyContextMenuAction(contextTarget, action);
        if (didChange) saveHistoryState(`context-${action}`);
        
        canvas.requestRenderAll();
        hideContextMenu();
    });
});

// Event handlery pro styl čáry v kontextovém menu
document.getElementById('ctxDrawStrokeColor')?.addEventListener('input', (e) => {
    if (!contextTarget) return;
    applyToContextDrawTarget({ stroke: e.target.value });
});

document.getElementById('ctxDrawFillColor')?.addEventListener('input', (e) => {
    if (!contextTarget) return;
    document.getElementById('ctxDrawFillTransparent').checked = false;
    applyToContextDrawTarget({ fill: e.target.value });
});

document.getElementById('ctxDrawFillTransparent')?.addEventListener('change', (e) => {
    if (!contextTarget) return;
    const fillVal = e.target.checked ? 'transparent' : (document.getElementById('ctxDrawFillColor')?.value || '#ffffff');
    applyToContextDrawTarget({ fill: fillVal });
});

document.getElementById('ctxDrawStrokeWidth')?.addEventListener('input', (e) => {
    if (!contextTarget) return;
    applyToContextDrawTarget({ strokeWidth: parseInt(e.target.value, 10) || 1 });
});

document.getElementById('ctxDrawStrokeStyle')?.addEventListener('change', (e) => {
    if (!contextTarget) return;
    const v = e.target.value;
    const widthVal = parseInt(document.getElementById('ctxDrawStrokeWidth')?.value, 10) || 1;
    let dash = null;
    if (v === 'dashed') dash = [widthVal * 2, widthVal];
    if (v === 'dotted') dash = [widthVal, widthVal * 1.5];
    applyToContextDrawTarget({ strokeDashArray: dash });
});

function applyToContextDrawTarget(props) {
    if (!contextTarget) return;
    
    function applyPropsRecursive(obj) {
        if (!obj) return;
        obj.set(props);
        obj.dirty = true;
        if (obj._objects && obj._objects.length) {
            obj._objects.forEach(child => applyPropsRecursive(child));
        }
    }
    
    if (contextTarget.type === 'activeSelection') {
        contextTarget.getObjects().forEach(obj => {
            if (obj.layer === 'draw' || obj.type === 'path' || obj.type === 'group' || obj.type === 'line') {
                applyPropsRecursive(obj);
            }
        });
    } else {
        applyPropsRecursive(contextTarget);
    }
    
    if (contextTarget.setCoords) contextTarget.setCoords();
    canvas.requestRenderAll();
    saveHistoryState('draw-style');
}

document.getElementById('undoBtn')?.addEventListener('click', undo);
document.getElementById('redoBtn')?.addEventListener('click', redo);

document.addEventListener('keydown', (e) => {
  const isMac = navigator.platform.toUpperCase().includes('MAC');
  const ctrl = isMac ? e.metaKey : e.ctrlKey;

  if (!ctrl) return;

  // když uživatel edituje text (IText) – neber mu Ctrl+Z
  const active = canvas.getActiveObject();
  if (active && active.type === 'i-text' && active.isEditing) return;

  if (e.key.toLowerCase() === 'z') {
    e.preventDefault();
    if (e.shiftKey) redo();
    else undo();
  }

  if (e.key.toLowerCase() === 'y') {
    e.preventDefault();
    redo();
  }
});

// Automatické ukládání historie
canvas.on('object:added', (e) => {
  if (HISTORY.isRestoring) return;
  if (!isHistoryObject(e.target)) return;
  saveHistoryState('added');
});

canvas.on('object:removed', (e) => {
  if (HISTORY.isRestoring) return;
  if (!isHistoryObject(e.target)) return;
  saveHistoryState('removed');
});

canvas.on('object:modified', (e) => {
  if (HISTORY.isRestoring) return;
  if (!isHistoryObject(e.target)) return;
  
  // Po přesunutí čáry resetuj její transformace aby zůstala stabilní
  const obj = e.target;
  if (obj && obj.type === 'line' && obj.layer === 'draw') {
    // Získej aktuální koncové body v canvas souřadnicích
    const p1 = getLineEndpointCanvasPos(obj, 'p1');
    const p2 = getLineEndpointCanvasPos(obj, 'p2');
    // Nastav čáru znovu s resetovanými transformacemi
    setLineByCanvasPoints(obj, p1, p2);
  }
  
  saveHistoryState('modified');
});

// Přidání dalšího obrázku
document.getElementById('addImageBtn').addEventListener('click', () => {
    document.getElementById('addImageInput').click();
});
document.getElementById('addImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const fileName = (file.name || '').toLowerCase();
    const fileType = (file.type || '').toLowerCase();

    if (fileName.endsWith('.heic') || fileName.endsWith('.heif') || fileType.includes('heic') || fileType.includes('heif')) {
        showToast('HEIC/HEIF tato verze editoru zatím nepodporuje. Převeď obrázek na JPG, PNG nebo WebP.', 'error', 7000);
        e.target.value = '';
        return;
    }
    
    // Validace velikosti souboru (max 50 MB)
    const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50 MB
    if (file.size > MAX_FILE_SIZE) {
        showToast(`Soubor je příliš velký (${(file.size / 1024 / 1024).toFixed(1)} MB). Maximum je 50 MB.`, 'error', 5000);
        e.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(evt) {
        // Vytvoř dočasný Image element pro zjištění rozlišení
        const img = new Image();
        img.onload = function() {
            // Validace rozlišení (běžná mobilní fotka 12 Mpx)
            const MAX_WIDTH = 4032;
            const MAX_HEIGHT = 3024;
            const longSide = Math.max(img.width, img.height);
            const shortSide = Math.min(img.width, img.height);

            if (longSide > MAX_WIDTH || shortSide > MAX_HEIGHT) {
                showToast(
                    `Rozlišení je příliš velké (${img.width}×${img.height}). Maximum je ${MAX_WIDTH}×${MAX_HEIGHT} v libovolné orientaci.`,
                    'error',
                    5000
                );
                e.target.value = '';
                return;
            }

            // Všechny validace prošly - načti obrázek jako overlay
            fabric.Image.fromURL(evt.target.result, function(fabricImg) {
                fabricImg.set({
                    originX: 'center',
                    originY: 'center',
                    scaleX: 1,
                    scaleY: 1,
                    selectable: true,
                    evented: true,
                    cornerStyle: 'circle',
                    hasRotatingPoint: true,
                    layer: 'image',
                });

	                applyImageTransformControls(fabricImg);

                fitObjectIntoCanvas(fabricImg, 20, false);

                canvas.add(fabricImg);
                canvas.setActiveObject(fabricImg);
                canvas.requestRenderAll();
                saveHistoryState('add-image');

                // Zobraz úspěšné oznámení
                showToast(`Obrázek vložen (${img.width}×${img.height})`, 'success');
            }, { crossOrigin: 'anonymous' });
        };
        img.onerror = function() {
            showToast('Nelze načíst obrázek. Zkuste jiný soubor.', 'error');
            e.target.value = '';
        };
        img.src = evt.target.result;
    };
    reader.onerror = function() {
        showToast('Chyba při čtení souboru. Zkuste znovu.', 'error');
        e.target.value = '';
    };
    reader.readAsDataURL(file);
    // Reset input pro možnost vložit stejný obrázek znovu
    e.target.value = '';
});

// Režimy úpravy textu: výběr vs. kreslení
function setTextEditingMode(mode) {
    if (mode === 'select') {
        drawMode = null;
        canvas.isDrawingMode = false;
        canvas.selection = true;
        if(currentImage) lockImage(false);
        setActiveTool(document.getElementById('textSelectBtn'));
        canvas.defaultCursor = 'default';
        canvas.getObjects().forEach(obj => {
            obj.selectable = true;
            obj.evented = true;
        });
        canvas.requestRenderAll();

        const drawToolbar = document.getElementById('drawFloatingToolbar');
        if (drawToolbar) drawToolbar.classList.add('hidden');
    } else if (mode === 'draw') {
        drawMode = 'textBox';
        canvas.isDrawingMode = false;
        canvas.selection = false;
        if(currentImage) lockImage(true);
        setActiveTool(document.getElementById('drawTextBoxBtn'));
        canvas.defaultCursor = 'crosshair';
        canvas.getObjects().forEach(o => {
            o.selectable = false;
            o.evented = false;
        });
        canvas.discardActiveObject();
        canvas.requestRenderAll();
    }
}

document.getElementById('textSelectBtn').addEventListener('click', () => setTextEditingMode('select'));
document.getElementById('drawTextBoxBtn').addEventListener('click', () => setTextEditingMode('draw'));

document.getElementById('copyFormatBtn').addEventListener('click', () => {
    const activeObject = canvas.getActiveObject();
    if (!activeObject) return;

    if (isFormatPainterActive) {
        isFormatPainterActive = false;
        formatClipboard = null;
        canvas.defaultCursor = 'default';
        canvas.hoverCursor = 'move';
        document.getElementById('copyFormatBtn').classList.remove('bg-green-500', 'text-white');
        return;
    }

    formatClipboard = {
        
        fill: activeObject.fill,
        stroke: activeObject.stroke,
        strokeWidth: activeObject.strokeWidth,
        strokeDashArray: activeObject.strokeDashArray,
        strokeLineCap: activeObject.strokeLineCap,

        
        fontSize: activeObject.fontSize,
        fontFamily: activeObject.fontFamily,
        fontWeight: activeObject.fontWeight,
        fontStyle: activeObject.fontStyle,
        underline: activeObject.underline,
        linethrough: activeObject.linethrough,
        textAlign: activeObject.textAlign,
        fill: activeObject.fill,
    };
    
    isFormatPainterActive = true;
    canvas.defaultCursor = 'copy'; 
    canvas.hoverCursor = 'copy';
    document.getElementById('copyFormatBtn').classList.add('bg-green-500', 'text-white');
});

// TEXT TOOLBAR
const textToolbar = document.getElementById('textToolbar');

function showTextToolbar(textObj) {
    if (!textToolbar || !textObj) return;

    positionFixedOverlayNearObject(textToolbar, textObj, { gap: 12 });
    textToolbar.classList.remove('hidden');
    syncTextToolbarFromObject(textObj);
    hideDrawFloatingToolbar();
}

function hideTextToolbar() {
    if (!textToolbar) return;
    textToolbar.classList.add('hidden');
}

function refreshActiveFloatingOverlayPosition(preferredTarget = null) {
    const obj = preferredTarget || canvas.getActiveObject();

    if (obj && (obj.type === 'i-text' || obj.type === 'textbox')) {
        showTextToolbar(obj);
        return;
    }

    if (obj && (obj.layer === 'draw' || (obj.type === 'activeSelection' && obj.getObjects().some(o => o.layer === 'draw')))) {
        updateDrawFloatingToolbarVisibility(obj);
    }
}

function getFirstSelectionStyleValue(obj, prop) {
    if (!obj) return null;
    if (obj.isEditing) {
        const start = obj.selectionStart;
        const end = obj.selectionEnd;
        if (typeof start !== 'undefined' && start !== end) {
            const styles = obj.getSelectionStyles(start, end);
            if (!styles) return null;
            if (Array.isArray(styles)) return styles[0] ? styles[0][prop] : null;
            const key = Object.keys(styles)[0];
            return styles[key] ? styles[key][prop] : null;
        }
    }
    return obj[prop];
}

function syncTextToolbarFromObject(obj) {
    if (!obj) return;
    const isIText = obj.type === 'i-text' || obj.type === 'textbox';
    if (!isIText) return;

    // Bold
    const weight = getFirstSelectionStyleValue(obj, 'fontWeight') || obj.fontWeight;
    document.getElementById('textBold').classList.toggle('bg-gray-200', weight === 'bold');
    document.getElementById('tbBold')?.classList.toggle('bg-gray-200', weight === 'bold');
    // Italic
    const style = getFirstSelectionStyleValue(obj, 'fontStyle') || obj.fontStyle;
    document.getElementById('textItalic').classList.toggle('bg-gray-200', style === 'italic');
    document.getElementById('tbItalic')?.classList.toggle('bg-gray-200', style === 'italic');
    // Underline
    const underline = getFirstSelectionStyleValue(obj, 'underline');
    document.getElementById('textUnderline').classList.toggle('bg-gray-200', !!underline);
    document.getElementById('tbUnderline')?.classList.toggle('bg-gray-200', !!underline);
    // Linethrough
    const linethrough = getFirstSelectionStyleValue(obj, 'linethrough');
    document.getElementById('textLinethrough').classList.toggle('bg-gray-200', !!linethrough);
    document.getElementById('tbLinethrough')?.classList.toggle('bg-gray-200', !!linethrough);
    // Color
    const color = getFirstSelectionStyleValue(obj, 'fill') || obj.fill || '#000000';
    document.getElementById('textToolbarColor').value = color;
    // Font size
    const fontSize = obj.fontSize || 32;
    document.getElementById('textSize').value = fontSize;
    const tbSizeVal = document.getElementById('tbSizeVal');
    if (tbSizeVal) tbSizeVal.textContent = fontSize;
    // Font family
    const fontFamily = obj.fontFamily || 'Arial';
    const tbFontEl = document.getElementById('tbFont');
    if (tbFontEl) tbFontEl.value = fontFamily;
    setFontPickerValue(fontFamily);
}

// Event handlery pro floating text toolbar
document.addEventListener('DOMContentLoaded', () => {
    const tbBold = document.getElementById('tbBold');
    const tbItalic = document.getElementById('tbItalic');
    const tbUnderline = document.getElementById('tbUnderline');
    const tbLinethrough = document.getElementById('tbLinethrough');
    const tbSizeMinus = document.getElementById('tbSizeMinus');
    const tbSizePlus = document.getElementById('tbSizePlus');
    const tbAlignLeft = document.getElementById('tbAlignLeft');
    const tbAlignCenter = document.getElementById('tbAlignCenter');
    const tbAlignRight = document.getElementById('tbAlignRight');
    
    if (tbBold) tbBold.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('bold');
    });
    if (tbItalic) tbItalic.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('italic');
    });
    if (tbUnderline) tbUnderline.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('underline');
    });
    if (tbLinethrough) tbLinethrough.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleTextStyle('linethrough');
    });
    
    if (tbSizeMinus) tbSizeMinus.addEventListener('click', (e) => {
        e.preventDefault();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        const newSize = Math.max(8, (obj.fontSize || 32) - 2);
        obj.set({ fontSize: newSize });
        canvas.requestRenderAll();
        syncTextToolbarFromObject(obj);
        scheduleTextHistory();
    });
    
    if (tbSizePlus) tbSizePlus.addEventListener('click', (e) => {
        e.preventDefault();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        const newSize = Math.min(200, (obj.fontSize || 32) + 2);
        obj.set({ fontSize: newSize });
        canvas.requestRenderAll();
        syncTextToolbarFromObject(obj);
        scheduleTextHistory();
    });
    
    // Align handlers v DOMContentLoaded
    if (tbAlignLeft) tbAlignLeft.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        obj.set({ textAlign: 'left' });
        obj.dirty = true;
        canvas.requestRenderAll();
        scheduleTextHistory();
    });
    
    if (tbAlignCenter) tbAlignCenter.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        obj.set({ textAlign: 'center' });
        obj.dirty = true;
        canvas.requestRenderAll();
        scheduleTextHistory();
    });
    
    if (tbAlignRight) tbAlignRight.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const obj = canvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;
        obj.set({ textAlign: 'right' });
        obj.dirty = true;
        canvas.requestRenderAll();
        scheduleTextHistory();
    });
    
});

canvas.on('selection:created', () => {
  const obj = canvas.getActiveObject();
  if (obj && (obj.type === 'i-text' || obj.type === 'textbox')) {
    showTextToolbar(obj);
  } else {
    hideTextToolbar();
  }
});

canvas.on('selection:updated', () => {
  const obj = canvas.getActiveObject();
  if (obj && (obj.type === 'i-text' || obj.type === 'textbox')) {
    showTextToolbar(obj);
  } else {
    hideTextToolbar();
  }
});

canvas.on('selection:cleared', hideTextToolbar);

canvas.on('object:moving', (event) => {
    refreshActiveFloatingOverlayPosition(event?.target);
});

canvas.on('object:scaling', (event) => {
    refreshActiveFloatingOverlayPosition(event?.target);
});

canvas.on('object:rotating', (event) => {
    refreshActiveFloatingOverlayPosition(event?.target);
});

document.getElementById('textToolbarColor').oninput = (e) => {
    const obj = canvas.getActiveObject();
    if (!obj || (obj.type !== 'i-text' && obj.type !== 'textbox')) return;

    const newColor = e.target.value;
    
    // Aktualizovat _originalFill na novou barvu, aby filtry fungovaly správně
    obj._originalFill = newColor;

    // Vypni editing mód dočasně pro správné nastavení fill
    const wasEditing = obj.isEditing;
    if (wasEditing) {
        obj.exitEditing();
    }

    // Nastav barvu na celý objekt
    obj.set({ fill: newColor });
    obj.dirty = true;

    // Synchronizuj s levým panelem
    const leftPanelColor = document.getElementById('textColor');
    if (leftPanelColor) leftPanelColor.value = newColor;

    // Pokud jsou zapnuté filtry na text, znovu je aplikovat na novou barvu
    const filterText = document.getElementById('filterLayerText')?.checked ?? false;
    if (filterText) {
        applyFilters();
    }

    canvas.requestRenderAll();

    // Vrať editing mód
    if (wasEditing) {
        obj.enterEditing();
    }

    scheduleTextHistory();
};

document.getElementById('deleteTextBtn').onclick = () => {
    const obj = canvas.getActiveObject();
    if (obj && removeCanvasTarget(obj)) {
        canvas.requestRenderAll();
        hideTextToolbar();
        saveHistoryState('delete text');
    }
};

window.addEventListener('beforeunload', function (e) { // alert při opuštění stránky
  e.preventDefault();
  e.returnValue = '';
});

switch (editorConfig.defaultMode) {
    case 'background':
        activateBackgroundRemovalMode({
            fabric,
            canvas,
            getCurrentImage: () => currentImage,
            saveHistory: (reason = 'background-removal') => saveHistoryState(reason),
            beginHistoryBatch: () => { historyBatch = true; },
            endHistoryBatch: () => { historyBatch = false; },
        });
        break;
    case 'passport':
        activatePassportPhotoMode({
            fabric,
            canvas,
            getCurrentImage: () => currentImage,
            saveHistory: (reason = 'passport-photo') => saveHistoryState(reason),
        });
        break;
    case 'resolution':
        activateResolutionMode();
        break;
    default:
        activateGeneralMode();
        activateBackgroundRemovalMode({
            fabric,
            canvas,
            getCurrentImage: () => currentImage,
            saveHistory: (reason = 'background-removal') => saveHistoryState(reason),
            beginHistoryBatch: () => { historyBatch = true; },
            endHistoryBatch: () => { historyBatch = false; },
            autoOpen: false,
        });
        activatePassportPhotoMode({
            fabric,
            canvas,
            getCurrentImage: () => currentImage,
            saveHistory: (reason = 'passport-photo') => saveHistoryState(reason),
            autoOpen: false,
            applyOnActivate: false,
        });
        break;
}

