const PASSPORT_WIDTH = 413;
const PASSPORT_HEIGHT = 531;
const SHEET_WIDTH = 1772;
const SHEET_HEIGHT = 1181;

let initialized = false;
let passportOverlayObjects = [];

function setPassportStatus(statusElement, message, isError = false) {
    statusElement.textContent = message;
    statusElement.classList.remove('hidden');
    statusElement.classList.toggle('border-red-200', isError);
    statusElement.classList.toggle('bg-red-50', isError);
    statusElement.classList.toggle('text-red-700', isError);
}

function clearPassportStatus(statusElement, errorElement) {
    statusElement.textContent = '';
    statusElement.classList.add('hidden');
    errorElement.textContent = '';
    errorElement.classList.add('hidden');
}

export async function consumeCredit(operation = 'passport_sheet') {
    try {
        const response = await fetch('/credits/consume', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ operation }),
        });

        if (response.ok) return await response.json();

        return {
            success: false,
            message: 'Systém kreditů není zatím dostupný.',
        };
    } catch {
        return {
            success: false,
            message: 'Systém kreditů se nepodařilo kontaktovat.',
        };
    }
}

function removePassportOverlay(canvas) {
    passportOverlayObjects.forEach((object) => canvas.remove(object));
    passportOverlayObjects = [];
}

function createPassportOverlay(fabric, canvas) {
    removePassportOverlay(canvas);

    const width = canvas.getWidth();
    const height = canvas.getHeight();

    const centerX = width / 2;
    const ovalWidth = width * 0.52;
    const ovalHeight = height * 0.46;
    const ovalTop = height * 0.13;

    const oval = new fabric.Ellipse({
        left: centerX,
        top: ovalTop + ovalHeight / 2,
        rx: ovalWidth / 2,
        ry: ovalHeight / 2,
        fill: 'rgba(255,255,255,0.08)',
        stroke: 'rgba(56,189,248,0.95)',
        strokeWidth: 2,
        strokeDashArray: [8, 6],
        originX: 'center',
        originY: 'center',
        selectable: false,
        evented: false,
        excludeFromExport: true,
        isPassportOverlay: true,
    });

    const eyeLineY = height * 0.42;
    const eyeLine = new fabric.Line([width * 0.16, eyeLineY, width * 0.84, eyeLineY], {
        fill: 'rgba(56,189,248,0.95)',
        stroke: 'rgba(56,189,248,0.95)',
        strokeWidth: 2,
        strokeDashArray: [10, 6],
        selectable: false,
        evented: false,
        excludeFromExport: true,
        isPassportOverlay: true,
    });

    passportOverlayObjects = [oval, eyeLine];
    passportOverlayObjects.forEach((object) => canvas.add(object));
    canvas.requestRenderAll();
}

function fitCurrentImageToPassport(canvas, getCurrentImage) {
    const activeObject = canvas.getActiveObject();
    const currentImage = activeObject?.type === 'image'
        ? activeObject
        : getCurrentImage?.();

    if (!currentImage) return null;

    const canvasWidth = canvas.getWidth();
    const canvasHeight = canvas.getHeight();
    const scale = Math.min(
        canvasWidth / currentImage.width,
        canvasHeight / currentImage.height
    );

    currentImage.set({
        left: canvasWidth / 2,
        top: canvasHeight / 2,
        originX: 'center',
        originY: 'center',
        scaleX: scale,
        scaleY: scale,
    });
    currentImage.setCoords();
    canvas.requestRenderAll();

    return currentImage;
}

async function createPassportSheet(fabric, canvas, getCurrentImage) {
    const sourceImage = canvas.getActiveObject()?.type === 'image'
        ? canvas.getActiveObject()
        : getCurrentImage?.();

    if (!sourceImage || sourceImage.type !== 'image') {
        throw new Error('Na plátno nejprve nahrajte nebo vytvořte fotku.');
    }

    const overlayObjects = canvas.getObjects().filter((object) => object.isPassportOverlay);
    overlayObjects.forEach((object) => {
        object.visible = false;
    });

    const sheetCanvas = new fabric.StaticCanvas(null, {
        width: SHEET_WIDTH,
        height: SHEET_HEIGHT,
        backgroundColor: '#ffffff',
    });

    const sourceDataUrl = canvas.toDataURL({
        format: 'png',
        quality: 1,
        multiplier: 1,
    });

    await new Promise((resolve, reject) => {
        fabric.Image.fromURL(sourceDataUrl, (passportImage) => {
            if (!passportImage) {
                reject(new Error('Nebylo možné připravit průkazovou fotografii.'));
                return;
            }

            const gap = 18;
            const outerGapX = 53;
            const outerGapY = 70;
            const columnWidth = (SHEET_WIDTH - (outerGapX * 2) - (gap * 3)) / 4;

            for (let column = 0; column < 4; column++) {
                const copy = fabric.util.object.clone(passportImage);
                const scale = Math.min(
                    columnWidth / copy.width,
                    PASSPORT_HEIGHT / copy.height
                );

                copy.set({
                    left: outerGapX + (column * (columnWidth + gap)),
                    top: outerGapY,
                    originX: 'left',
                    originY: 'top',
                    scaleX: scale,
                    scaleY: scale,
                    selectable: false,
                    evented: false,
                });

                sheetCanvas.add(copy);

                const cutX = copy.left;
                const cutTop = copy.top;
                const cutBottom = cutTop + PASSPORT_HEIGHT;

                sheetCanvas.add(new fabric.Line([cutX - 8, cutTop, cutX + 8, cutTop], {
                    stroke: '#777777', strokeWidth: 1, selectable: false, evented: false,
                }));
                sheetCanvas.add(new fabric.Line([cutX - 8, cutBottom, cutX + 8, cutBottom], {
                    stroke: '#777777', strokeWidth: 1, selectable: false, evented: false,
                }));
                sheetCanvas.add(new fabric.Line([cutX, cutTop - 8, cutX, cutTop + 8], {
                    stroke: '#777777', strokeWidth: 1, selectable: false, evented: false,
                }));
            }

            sheetCanvas.renderAll();
            resolve();
        }, null, { crossOrigin: 'anonymous' });
    });

    createPassportOverlay(fabric, canvas);

    await new Promise((resolve, reject) => {
        sheetCanvas.toCanvasElement().toBlob((blob) => {
            overlayObjects.forEach((object) => {
                object.visible = true;
            });
            canvas.requestRenderAll();

            if (!blob) {
                reject(new Error('Nebylo možné vytvořit tiskový soubor.'));
                return;
            }

            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'tiskovy-arch-10x15cm-4-fotky.jpg';
            document.body.appendChild(link);
            link.click();
            link.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
            resolve();
        }, 'image/jpeg', 0.95);
    });
}

export function activatePassportPhotoMode(context = {}) {
    const { fabric, canvas, getCurrentImage, saveHistory, autoOpen = true, applyOnActivate = true } = context;

    if (!canvas || initialized) return;
    initialized = true;

    const panelButton = document.querySelector('[data-target="panelPassport"]');
    if (autoOpen) panelButton?.click();

    const sizeButton = document.getElementById('applyPassportSizeBtn');
    const backgroundButton = document.getElementById('preparePassportBackgroundBtn');
    const sheetButton = document.getElementById('createPassportSheetBtn');
    const statusElement = document.getElementById('passportStatus');
    const errorElement = document.getElementById('passportError');

    const applyPassportSize = () => {
        clearPassportStatus(statusElement, errorElement);

        canvas.setWidth(PASSPORT_WIDTH);
        canvas.setHeight(PASSPORT_HEIGHT);

        const workspace = document.getElementById('canvasWorkspace');
        if (canvas.wrapperEl && workspace) {
            canvas.wrapperEl.style.width = `${PASSPORT_WIDTH}px`;
            canvas.wrapperEl.style.height = `${PASSPORT_HEIGHT}px`;
        }

        canvas.backgroundColor = canvas.backgroundColor || '#ffffff';
        fitCurrentImageToPassport(canvas, getCurrentImage);
        createPassportOverlay(fabric, canvas);
        canvas.requestRenderAll();
        saveHistory?.('passport-size');

        setPassportStatus(statusElement, 'Formát 35 × 45 mm byl nastaven.');
    };

    sizeButton?.addEventListener('click', applyPassportSize);
    panelButton?.addEventListener('click', () => {
        applyPassportSize();
    });

    backgroundButton?.addEventListener('click', async () => {
        clearPassportStatus(statusElement, errorElement);

        const activeObject = canvas.getActiveObject();
        const targetImage = activeObject?.type === 'image'
            ? activeObject
            : getCurrentImage?.();

        if (!targetImage || targetImage.type !== 'image') {
            errorElement.textContent = 'Na plátno nejprve nahrajte nebo vytvořte fotografii.';
            errorElement.classList.remove('hidden');
            return;
        }

        const backgroundButtonLabel = backgroundButton.text;
        backgroundButton.disabled = true;
        setPassportStatus(statusElement, 'Odstraňuji pozadí a nastavujeme bílé pozadí...');

        try {
            const dataUrl = targetImage.toDataURL
                ? targetImage.toDataURL({ format: 'png', multiplier: 1 })
                : canvas.toDataURL({ format: 'png', multiplier: 1 });
            const response = await fetch(dataUrl);
            const blob = await response.blob();

            const backgroundModule = await import('@imgly/background-removal');
            const removeBackground = backgroundModule.removeBackground || backgroundModule.default;

            if (typeof removeBackground !== 'function') {
                throw new Error('AI modul se nepodařilo načíst.');
            }

            const resultBlob = await removeBackground(blob, {
                model: 'isnet_fp16',
                output: { format: 'image/png', quality: 1, type: 'foreground' },
            });

            const resultUrl = URL.createObjectURL(resultBlob);

            await new Promise((resolve, reject) => {
                fabric.Image.fromURL(resultUrl, (resultImage) => {
                    if (!resultImage) {
                        reject(new Error('Výsledek se nepodařilo vložit do editoru.'));
                        return;
                    }

                    const index = canvas.getObjects().indexOf(targetImage);
                    const properties = {
                        left: targetImage.left,
                        top: targetImage.top,
                        scaleX: targetImage.scaleX,
                        scaleY: targetImage.scaleY,
                        angle: targetImage.angle || 0,
                        flipX: targetImage.flipX || false,
                        flipY: targetImage.flipY || false,
                        originX: targetImage.originX,
                        originY: targetImage.originY,
                        selectable: true,
                        evented: true,
                        opacity: targetImage.opacity,
                        layer: 'background',
                    };

                    canvas.remove(targetImage);
                    resultImage.set(properties);
                    canvas.add(resultImage);
                    if (index >= 0) resultImage.moveTo(index);
                    canvas.backgroundColor = '#ffffff';
                    canvas.setActiveObject(resultImage);
                    canvas.requestRenderAll();
                    saveHistory?.('passport-white-background');
                    resolve();
                }, null, { crossOrigin: 'anonymous' });
            });

            setTimeout(() => URL.revokeObjectURL(resultUrl), 1000);
            setPassportStatus(statusElement, 'Pozadí bylo upraveno na čistě bílé.');
        } catch (error) {
            errorElement.textContent = `Úprava pozadí se nepodařila. ${error instanceof Error ? error.message : 'Zkuste to znovu.'}`;
            errorElement.classList.remove('hidden');
        } finally {
            backgroundButton.disabled = false;
            backgroundButton.textContent = backgroundButtonLabel;
        }
    });

    sheetButton?.addEventListener('click', async () => {
        clearPassportStatus(statusElement, errorElement);

        sheetButton.disabled = true;

        try {
            await consumeCredit('passport_sheet');

            await createPassportSheet(fabric, canvas, getCurrentImage);
            setPassportStatus(statusElement, 'Tiskový arch byl vytvořen a stažen jako kvalitní JPG.');
        } catch (error) {
            errorElement.textContent = `Tiskový arch se nepodařilo vytvořit. ${error instanceof Error ? error.message : 'Zkuste to znovu.'}`;
            errorElement.classList.remove('hidden');
        } finally {
            sheetButton.disabled = false;
        }
    });

    if (applyOnActivate) applyPassportSize();
}
