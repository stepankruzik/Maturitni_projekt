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

                // pridani crop obdelniku
                cropRect = new fabric.Rect({
                    left: canvas.width / 2,
                    top: canvas.height / 2,
                    width: canvas.width,
                    height: canvas.height,
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
    </script>
</x-layout>
