let initialized = false;
let resultUrl = null;
let resultBlob = null;
let isProcessing = false;

function setStatus(statusElement, progressWrap, progressElement, message, progress = null) {
    statusElement.textContent = message;
    progressWrap.classList.remove('hidden');

    if (progress !== null) {
        progressElement.style.width = `${Math.max(0, Math.min(100, progress))}%`;
    }
}

function setError(errorElement, message) {
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');}

function clearError(errorElement) {
    errorElement.textContent = '';
    errorElement.classList.add('hidden');
}

function objectToBlob(object) {
    return new Promise((resolve, reject) => {
        try {
            const canvasElement = object.toCanvasElement
                ? object.toCanvasElement({ multiplier: 1 })
                : null;

            if (!canvasElement) {
                reject(new Error('Nebylo možné připravit obrázek k zpracování.'));
                return;
            }

            canvasElement.toBlob((blob) => {
                if (blob) resolve(blob);
                else reject(new Error('Nebylo možné přečíst vybraný obrázek.'));
            }, 'image/png');
        } catch (error) {
            reject(error);
        }
    });
}

function imageProperties(object) {
    return {
        left: object.left,
        top: object.top,
        scaleX: object.scaleX,
        scaleY: object.scaleY,
        angle: object.angle || 0,
        flipX: object.flipX || false,
        flipY: object.flipY || false,
        originX: object.originX,
        originY: object.originY,
        selectable: object.selectable,
        evented: object.evented,
        opacity: object.opacity,
        layer: 'background',
    };
}

export function activateBackgroundRemovalMode(context = {}) {
    const { fabric, canvas, getCurrentImage, saveHistory, beginHistoryBatch, endHistoryBatch, autoOpen = true } = context;
    const panelButton = document.querySelector('[data-target="panelBackground"]');
    const processButton = document.getElementById('removeBackgroundBtn');

    if (autoOpen) panelButton?.click();

    if (!processButton || initialized) return;
    initialized = true;

    const progressWrap = document.getElementById('backgroundRemovalProgressWrap');
    const progressElement = document.getElementById('backgroundRemovalProgress');
    const statusElement = document.getElementById('backgroundRemovalStatus');
    const errorElement = document.getElementById('backgroundRemovalError');
    const resultElement = document.getElementById('backgroundRemovalResult');
    const downloadButton = document.getElementById('downloadBackgroundResultBtn');
    const colorPicker = document.getElementById('backgroundColorPicker');

    processButton.addEventListener('click', async () => {
        if (isProcessing) return;

        clearError(errorElement);

        if (!canvas) {
            setError(errorElement, 'Editorové plátno není připravené. Obnovte stránku a zkuste to znovu.');
            return;
        }

        const activeObject = canvas.getActiveObject();
        let targetImage = activeObject?.type === 'image'
            ? activeObject
            : getCurrentImage?.();

        if (!targetImage && canvas.getObjects) {
            targetImage = canvas.getObjects().find((object) => object.type === 'image' && object.layer === 'background')
                || canvas.getObjects().find((object) => object.type === 'image')
                || null;
        }

        if (!targetImage || targetImage.type !== 'image') {
            setError(errorElement, 'Na plátno nejprve nahrajte nebo vytvořte obrázek.');
            return;
        }

        isProcessing = true;
        processButton.disabled = true;
        resultElement.classList.add('hidden');
        progressElement.style.width = '0%';
        setStatus(statusElement, progressWrap, progressElement, 'Stahuji AI model do prohlížeče...');

        let downloadedBlob;

        try {
            downloadedBlob = await objectToBlob(targetImage);

            const backgroundModule = await import('@imgly/background-removal');
            const removeBackground = backgroundModule.removeBackground || backgroundModule.default;

            if (typeof removeBackground !== 'function') {
                throw new Error('AI modul pro odstranění pozadí nebyl načten.');
            }

            const backgroundBlob = await removeBackground(downloadedBlob, {
                model: 'isnet_fp16',
                output: {
                    format: 'image/png',
                    quality: 1,
                    type: 'foreground',
                },
                progress: (key, current, total) => {
                    const progress = total > 0 ? Math.round((current / total) * 100) : 5;

                    if (key && String(key).toLowerCase().includes('model')) {
                        setStatus(statusElement, progressWrap, progressElement, 'Stahuji AI model do prohlížeče...', progress);
                    } else {
                        setStatus(statusElement, progressWrap, progressElement, 'Odstraňuji pozadí z fotky...', progress);
                    }
                },
            });

            setStatus(statusElement, progressWrap, progressElement, 'Odstraňuji pozadí z fotky...', 100);

            if (resultUrl) URL.revokeObjectURL(resultUrl);
            resultBlob = backgroundBlob;
            resultUrl = URL.createObjectURL(backgroundBlob);

            await new Promise((resolve, reject) => {
                fabric.Image.fromURL(resultUrl, (resultImage) => {
                    if (!resultImage) {
                        reject(new Error('Nebylo možné vložit výsledek na plátno.'));
                        return;
                    }

                    const index = canvas.getObjects().indexOf(targetImage);
                    const properties = imageProperties(targetImage);

                    beginHistoryBatch?.();
                    try {
                        canvas.remove(targetImage);
                        resultImage.set(properties);
                        canvas.add(resultImage);

                        if (index >= 0) resultImage.moveTo(index);

                        canvas.setActiveObject(resultImage);
                        canvas.requestRenderAll();
                    } finally {
                        endHistoryBatch?.();
                    }

                    saveHistory?.('remove background');
                    resolve();
                }, null, { crossOrigin: 'anonymous' });
            });

            statusElement.textContent = 'Pozadí bylo odstraněno.';
            progressElement.style.width = '100%';
            resultElement.classList.remove('hidden');
        } catch (error) {
            const message = error instanceof Error ? error.message : 'Neznámá chyba při zpracování.';
            setError(errorElement, `Pozadí se nepodařilo odstranit. ${message}`);
            statusElement.textContent = 'Zpracování bylo ukončeno s chybou.';
        } finally {
            isProcessing = false;
            processButton.disabled = false;
        }
    });

    document.querySelectorAll('[data-background-color]').forEach((button) => {
        button.addEventListener('click', () => {
            const color = button.dataset.backgroundColor;
            canvas.backgroundColor = color === 'transparent' ? null : color;
            canvas.requestRenderAll();
        });
    });

    colorPicker?.addEventListener('input', () => {
        canvas.backgroundColor = colorPicker.value;
        canvas.requestRenderAll();
    });

    downloadButton?.addEventListener('click', () => {
        if (!resultUrl || !resultBlob) {
            setError(errorElement, 'Nejprve odstranění pozadí dokončete.');
            return;
        }

        const link = document.createElement('a');
        link.href = resultUrl;
        link.download = 'bez-pozadi.png';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });
}
