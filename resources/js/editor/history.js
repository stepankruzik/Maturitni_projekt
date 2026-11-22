export function createHistoryStore(maxStates = 50) {
    return {
        undoStack: [],
        redoStack: [],
        max: maxStates,
        isRestoring: false,
        extraProps: ['id', 'layer', 'isGrid', 'isSnapIndicator'],
    };
}
