<x-layout>
    <x-slot:heading>Editor obrázku</x-slot:heading>

    <div class="flex flex-col items-center space-y-4">
        <!-- Canvas -->
        <canvas id="canvas" class="border border-gray-300 shadow-lg"></canvas>

        <!-- Tlačítka -->
        <div class="flex gap-2">
            <button id="toggleMode" class="px-4 py-2 bg-yellow-500 text-white rounded">Režim: Změnit velikost</button>
            <button id="cropBtn" class="px-4 py-2 bg-purple-500 text-white rounded">Oříznout</button>
            <button id="grayscale" class="px-4 py-2 bg-gray-500 text-white rounded">Odstíny šedi</button>
            <button id="download" class="px-4 py-2 bg-green-500 text-white rounded">Stáhnout</button>
        </div>
    </div>
        <!-- Jas mkontrast-->
    <div class="flex gap-4 items-center mt-4">
            <label>Jas:</label>
            <input type="range" id="brightness" min="-1" max="1" step="0.1" value="0">
            <label>Kontrast:</label>
            <input type="range" id="contrast" min="-1" max="1" step="0.1" value="0">
            <label>Sytost:</label>
            <input type="range" id="saturation" min="-1" max="1" step="0.1" value="0">
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    <script>
        const canvas = new fabric.Canvas('canvas');
        let currentImage = null;
        let cropRect = null;
        let mode = 'resize'; // resize nebo crop

        // Načtení obrázku 
        const imgUrl = "{{ asset($imagePath) }}";
        fabric.Image.fromURL(imgUrl, function(img) {
            // canvas podle velikosti obrázku
        const padding = 100;
        canvas.setWidth(img.width + padding);
        canvas.setHeight(img.height + padding);
            img.set({
                    left: (canvas.width) / 2,
                    top: (canvas.height) / 2
                     });

            img.set({
                left: canvas.width / 2,
                top: canvas.height / 2,
                originX: 'center',
                originY: 'center',
                selectable: true,
                hasRotatingPoint: true,
                cornerStyle: 'circle'
            });

            canvas.add(img);
            canvas.setActiveObject(img);
            currentImage = img;
            canvas.renderAll();
        });

        // rezimy resize / crop
        document.getElementById('toggleMode').addEventListener('click', () => {
            if(mode === 'resize') {
                mode = 'crop';
                canvas.selection = false;
                document.getElementById('toggleMode').textContent = 'Režim: Ořez';

                  if (!currentImage) return;
                  const bounds = currentImage.getBoundingRect(true);
                // pridani crop obdelniku
                cropRect = new fabric.Rect({
                    left: bounds.left + bounds.width / 2,
                    top: bounds.top + bounds.height / 2,
                    width: bounds.width * 0.8,
                    height: bounds.height * 0.8,
                    fill: 'rgba(0,0,0,0.3)',
                    originX: 'center',
                    originY: 'center',
                    selectable: true,
                    hasBorders: true,
                    cornerStyle: 'circle'
                });

                canvas.add(cropRect);
                canvas.setActiveObject(cropRect);
            } else {
                mode = 'resize';
                document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
                if(cropRect) canvas.remove(cropRect);
                canvas.selection = true;
            }
        });

        // Oříznutí
        document.getElementById('cropBtn').addEventListener('click', () => {
            if(mode !== 'crop' || !cropRect || !currentImage) return;

            const rect = cropRect.getBoundingRect();
            currentImage.cloneAsImage((clonedImg) => {
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = rect.width;
                tempCanvas.height = rect.height;
                const tempCtx = tempCanvas.getContext('2d');

                tempCtx.drawImage(
                    currentImage._element,
                    rect.left - currentImage.left + currentImage.width/2,
                    rect.top - currentImage.top + currentImage.height/2,
                    rect.width,
                    rect.height,
                    0, 0,
                    rect.width,
                    rect.height
                );

                const dataURL = tempCanvas.toDataURL();
                fabric.Image.fromURL(dataURL, (img) => {
                    canvas.remove(currentImage);
                    canvas.remove(cropRect);
                    currentImage = img;
                    img.set({
                        left: canvas.width/2,
                        top: canvas.height/2,
                        originX: 'center',
                        originY: 'center',
                        selectable: true,
                        hasRotatingPoint: true
                    });
                    canvas.add(img);
                    canvas.setActiveObject(img);
                    mode = 'resize';
                    document.getElementById('toggleMode').textContent = 'Režim: Změnit velikost';
                });
            });
        });

        // Grayscale filtr
        document.getElementById('grayscale').addEventListener('click', () => {
            if(!currentImage) return;
            currentImage.filters = [new fabric.Image.filters.Grayscale()];
            currentImage.applyFilters();
            canvas.renderAll();
        });

        // Download
        document.getElementById('download').addEventListener('click', () => {
            const dataURL = canvas.toDataURL({ format: 'png', quality: 1 });
            const link = document.createElement('a');
            link.href = dataURL;
            link.download = 'edited.png';
            link.click();
        });

        // --- zoom a posun --- //
let isDragging = false;
let lastPosX, lastPosY;

// Přibližování kolečkem myši
canvas.on('mouse:wheel', function(opt) {
    const delta = opt.e.deltaY;
    let zoom = canvas.getZoom();
    zoom *= 0.999 ** delta;
    if (zoom > 5) zoom = 5;
    if (zoom < 0.5) zoom = 0.5;
    canvas.zoomToPoint({ x: opt.e.offsetX, y: opt.e.offsetY }, zoom);
    opt.e.preventDefault();
    opt.e.stopPropagation();
});


canvas.on('mouse:move', function(opt) {
    if (isDragging) {
        const e = opt.e;
        const vpt = canvas.viewportTransform;
        vpt[4] += e.clientX - lastPosX;
        vpt[5] += e.clientY - lastPosY;
        canvas.requestRenderAll();
        lastPosX = e.clientX;
        lastPosY = e.clientY;
    }
});

canvas.on('mouse:up', function(opt) {
    isDragging = false;
    canvas.selection = true;
});

// Jas / Kontrast / Sytost --- //
const updateFilters = () => {
    if (!currentImage) return;
    const brightness = parseFloat(document.getElementById('brightness').value);
    const contrast = parseFloat(document.getElementById('contrast').value);
    const saturation = parseFloat(document.getElementById('saturation').value);

    currentImage.filters = [
        new fabric.Image.filters.Brightness({ brightness }),
        new fabric.Image.filters.Contrast({ contrast }),
        new fabric.Image.filters.Saturation({ saturation })
    ];

    currentImage.applyFilters();
    canvas.renderAll();
};

document.querySelectorAll('#brightness, #contrast, #saturation').forEach(input => {
    input.addEventListener('input', updateFilters);
});

    </script>
</x-layout>
