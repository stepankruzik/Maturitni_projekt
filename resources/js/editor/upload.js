export function getImagePath(editorId = 'photo-editor') {
    return document.getElementById(editorId)?.dataset.imagePath || null;
}

export function loadUploadedImage(fabric, url, callback) {
    if (!url) return;

    fabric.Image.fromURL(url, callback);
}
