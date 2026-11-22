export function canvasToDataUrl(canvas, format = 'png', quality = 1) {
    const mimeFormat = format === 'jpg' ? 'jpeg' : format;

    return canvas.toDataURL({
        format: mimeFormat,
        quality,
        multiplier: 1,
    });
}

export async function saveImageToServer(dataUrl, format = 'png') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const response = await fetch('/save-image', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ image: dataUrl, format }),
    });

    if (!response.ok) {
        throw new Error(`Ukládání selhalo (${response.status}).`);
    }

    return response.json();
}
