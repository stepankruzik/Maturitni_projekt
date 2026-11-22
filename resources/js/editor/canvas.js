import { fabric } from 'fabric';

export function initCanvas(canvasId = 'canvas', workspaceId = 'canvasWorkspace') {
    const canvas = new fabric.Canvas(canvasId, {
        preserveObjectStacking: true,
    });

    const canvasWorkspace = document.getElementById(workspaceId);

    return { fabric, canvas, canvasWorkspace };
}

export function getEditorConfig(editorId = 'photo-editor') {
    const element = document.getElementById(editorId);

    return {
        element,
        defaultMode: element?.dataset.defaultMode || 'general',
        requiresAiCredits: element?.dataset.requiresAiCredits === 'true',
        imagePath: element?.dataset.imagePath || null,
    };
}
