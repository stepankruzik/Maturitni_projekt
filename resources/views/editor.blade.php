<x-layout>
    <x-slot:heading>
        Editor obrázku
    </x-slot:heading>

    <div class="flex flex-col items-center space-y-4">
        <canvas id="canvas" class="border border-gray-300 shadow-lg"></canvas>

        <div class="flex gap-2">
            <button id="rotate" class="px-4 py-2 bg-blue-500 text-white rounded">Otočit 90°</button>
            <button id="grayscale" class="px-4 py-2 bg-gray-500 text-white rounded">Odstíny šedi</button>
            <button id="download" class="px-4 py-2 bg-green-500 text-white rounded">Stáhnout</button>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        img.src = "{{ $imagePath }}";
        img.onload = () => {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
        };

        document.getElementById('rotate').addEventListener('click', () => {
            const temp = document.createElement('canvas');
            const tctx = temp.getContext('2d');
            temp.width = canvas.height;
            temp.height = canvas.width;
            tctx.translate(temp.width/2, temp.height/2);
            tctx.rotate(Math.PI/2);
            tctx.drawImage(canvas, -canvas.width/2, -canvas.height/2);
            canvas.width = temp.width;
            canvas.height = temp.height;
            ctx.drawImage(temp, 0, 0);
        });

        document.getElementById('grayscale').addEventListener('click', () => {
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            for (let i = 0; i < data.length; i += 4) {
                const avg = (data[i] + data[i+1] + data[i+2]) / 3;
                data[i] = data[i+1] = data[i+2] = avg;
            }
            ctx.putImageData(imageData, 0, 0);
        });

        document.getElementById('download').addEventListener('click', () => {
            const link = document.createElement('a');
            link.download = 'edited.png';
            link.href = canvas.toDataURL();
            link.click();
        });
    </script>
</x-layout>
