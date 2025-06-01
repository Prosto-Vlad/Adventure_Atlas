<?= view('header') ?>

<section class="flex-grow-1">
    <div class="container-fluid d-flex flex-column h-100">
        <div class="row flex-fill">
            <nav class="col-auto bg-secondary text-white p-3" style="min-width: 200px; max-width: 250px;">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <button class="btn btn-secondary w-100 my-2" id="toolbarButton">Інструменти</button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-secondary w-100 my-2" id="notesButton">Нотатки</button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-secondary w-100 my-2" id="saveButton">Зберегти</button>
                        </li>
                        <li class="nav-item">
                            <button id="downloadButton" class="btn btn-success w-100 my-2">Скачати</button>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col d-flex flex-column flex-grow-1">
                    <div id="mapContainer" class="panzoom" style="width: 100%; height: 100%; position: relative;">
                        <img id="mapImage" src="<?= base_url($map['image_path']) ?>" alt="Вашу мапу вкрали гобліни" style="max-width: none; height: auto; display: block;" 
                        data-map-id="<?= $id ?>" 
                        data-map-name="<?= $map['name']?>">

                        <div id="notesContainer" class="position-absolute" style="top: 0; left: 0; width: 100%;">
                            <?php if (!empty($notes)): ?>
                                <?php foreach ($notes as $note): ?>
                                    <?php $style = json_decode($note['style'], true); ?>
                                    <div contenteditable="true" class="note" style="
                                        position: absolute; 
                                        left: <?= $style['x'] ?>px; 
                                        top: <?= $style['y'] ?>px; 
                                        width: <?= $style['width'] ?>px; 
                                        height: <?= $style['height'] ?>px; 
                                        background-color: <?= $style['backgroundColor'] ?>; 
                                        border: 2px solid <?= $style['borderColor'] ?>;
                                        border-radius: <?= $style['borderRadius'] ?>;
                                        padding: 5px;
                                        overflow: hidden;
                                        word-wrap: break-word;
                                    ">
                                        <?= $note['content'] ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div id="locationsContainer" class="position-absolute" style="top: 0; left: 0; width: 100%;">
                            <?php foreach ($locations as $location): ?>
                                <?php $coordinates = json_decode($location['coordinates'], true); ?>
                                <img src="<?= base_url($location['image_path'])?>" style="position: absolute; left: 
                                <?= $coordinates['x'] ?>px; top: <?= $coordinates['y'] ?>px; width: <?= $location['size'] ?>; height: <?= $location['size'] ?>;" alt="<?= $location['name'] ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                <div id="toolMenu" class="card position-absolute bg-white shadow rounded" style="display: none; bottom: 10px; left: 10px; padding: 10px;">
                    <div class="d-flex flex-wrap">
                        <?php foreach ($icons as $icon): ?>
                            <button class="btn btn-light m-1 flex-grow-1" style="flex: 1 0 32%; max-width: 32%;" id="<?php echo $icon['id'] ?>">
                                <img src="<?php echo base_url($icon['image_path']); ?>" alt="<?php echo $icon['name']; ?>" style="width: 32px; height: 32px;">
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top: 10px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <label for="sizeSlider" style="margin-right: 10px;">Розмір іконки:</label>
                            <span id="sizeValue" style="min-width: 30px;">32</span>
                        </div>
                        <input type="range" id="sizeSlider" min="16" max="64" value="32" style="width: 150px; margin: 10px 0;">
                    </div>

                    <button id="deleteButton" class="btn btn-danger w-100 mt-2">Видалення</button>
                </div>
                <div id="notesMenu" class="position-absolute bg-white p-2 shadow rounded" style="display: none; bottom: 10px; left: 10px;">
                    <h5>Налаштування нотатки</h5>
                    <div class="form-group">
                        <label for="backgroundColor">Колір фону:</label>
                        <input type="color" id="backgroundColor" class="form-control" value="#ffffff">
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" id="toggleBackground" class="form-check-input" checked>
                        <label for="toggleBackground" class="form-check-label">Увімкнути фон</label>
                    </div>

                    <div class="form-group">
                        <label for="borderColor">Колір краю:</label>
                        <input type="color" id="borderColor" class="form-control">
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" id="toggleBorder" class="form-check-input" checked>
                        <label for="toggleBorder" class="form-check-label">Увімкнути край</label>
                    </div>

                    <div class="form-group">
                        <label for="borderRadius">Форма:</label>
                        <select id="borderRadius" class="form-control">
                            <option value="0">Квадратна</option>
                            <option value="10px">Заокруглена</option>
                        </select>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>

<footer>

</footer>

<script>
        const notes = document.querySelectorAll('#notesContainer .note');
        
        notes.forEach(noteElement => {
            function adjustSize() {
                const computed = window.getComputedStyle(noteElement);
                noteElement.style.height = 'auto';
                noteElement.style.width = 'auto';

                const width = noteElement.scrollWidth + parseInt(computed.paddingLeft) + parseInt(computed.paddingRight);
                const height = noteElement.scrollHeight + parseInt(computed.paddingTop) + parseInt(computed.paddingBottom);

                noteElement.style.width = `${width}px`;
                noteElement.style.height = `${height}px`;
            }

            noteElement.addEventListener('input', adjustSize);
            noteElement.addEventListener('click', function () {
                noteElement.focus();
            });

            adjustSize();
        });

    const mapImg = document.getElementById('mapImage');
    const downloadButton = document.getElementById('downloadButton');

    const notesContainer = document.getElementById('notesContainer');
    const notesButton = document.getElementById('notesButton');
    const notesMenu = document.getElementById('notesMenu');
    
    const locationContainer = document.getElementById('locationsContainer');
    const toolBarButton = document.getElementById('toolbarButton');
    const toolMenuButtons = document.querySelectorAll('#toolMenu button');
    const toolMenu = document.getElementById('toolMenu');
    const deleteButton = document.getElementById('deleteButton');
    const sizeSlider = document.getElementById('sizeSlider');
    const sizeValue = document.getElementById('sizeValue');

    let deletionMode = false;

    let selectedLocation = null;
    let currentMenu = null;
    
    const element = document.querySelector('#mapContainer');

    const panzoom = Panzoom(element, {
        contain: 'invert',
        maxScale: 4,
        minScale: 0.5,
    });


    element.parentElement.addEventListener('wheel', panzoom.zoomWithWheel);

    sizeSlider.addEventListener('input', function() {
        const newSize = this.value;
        sizeValue.textContent = newSize;
    });

    toolMenuButtons.forEach(button => {
        button.addEventListener('click', function () {
            const iconId = this.id;

            toolMenuButtons.forEach(btn => btn.classList.remove('active'));

            if (selectedLocation === iconId) {
                this.classList.remove('active');
                selectedLocation = null;
            } else {
                this.classList.add('active');
                selectedLocation = iconId;
            }
        });
    });

    function deleteElement(event) {
        if (event.target.parentElement === notesContainer || event.target.parentElement === locationsContainer) {
            event.target.parentElement.removeChild(event.target);
        }
    }

    deleteButton.addEventListener('click', function () {
        deletionMode = !deletionMode; 

        if (deletionMode) {
            mapContainer.addEventListener('click', deleteElement);
            alert('Режим видалення увімкнено. Натисніть на об\'єкт, щоб його видалити.');
        } else {
            mapContainer.removeEventListener('click', deleteElement);
            alert('Режим видалення вимкнено.');
        }
    });

    toolBarButton.addEventListener('click', function () {
        currentMenu = 'toolBar';
        notesMenu.style.display = 'none';
        if (toolMenu.style.display === 'none' || toolMenu.style.display === '') {
            toolMenu.style.display = 'block';
        } else {
            toolMenu.style.display = 'none';
        }
    });

    notesButton.addEventListener('click', function () {
        currentMenu = 'notes';
        toolMenu.style.display = 'none';
        if (notesMenu.style.display === 'none' || notesMenu.style.display === '') {
            notesMenu.style.display = 'block';
        } else {
            notesMenu.style.display = 'none';
        }
    });

    mapImg.addEventListener('click', function (event) {
        if (!mapImg.src || mapImg.src.endsWith("Вашу мапу вкрали гобліни")) {
            alert('Спочатку завантажте карту.');
            return;
        }
        if (currentMenu == 'toolBar') {
            const imag_path = document.getElementById(selectedLocation).querySelector('img').src;

            const rect = mapContainer.getBoundingClientRect();
            const scale = panzoom.getScale();

            console.log('Scale:', scale);
            const width = sizeSlider.value || 32;
            const height = sizeSlider.value || 32;

            const x = ((event.clientX - rect.left) / scale) - (width / 2);
            const y = ((event.clientY - rect.top) / scale) - (height / 2);

            const iconElement = document.createElement('img');
            iconElement.src = imag_path;
            iconElement.className = 'position-absolute';
            iconElement.style.left = `${x}px`;
            iconElement.style.top = `${y}px`;
            iconElement.style.width = `${width}px`;
            iconElement.style.height = `${height}px`;
            iconElement.dataset.iconId = selectedLocation;

            locationContainer.appendChild(iconElement);
        }
        else if (currentMenu == 'notes') {
        const backgroundColor = document.getElementById('backgroundColor').value;
        const isBackgroundEnabled = document.getElementById('toggleBackground').checked;
        const borderColor = document.getElementById('borderColor').value;
        const isBorderEnabled = document.getElementById('toggleBorder').checked;
        const borderRadius = document.getElementById('borderRadius').value;

        const rect = mapContainer.getBoundingClientRect();
        const scale = panzoom.getScale() || 1;
        const initWidth = 100;
        const initHeight = 50;
        const x = ((event.clientX - rect.left) / scale) - (initWidth / 2);
        const y = ((event.clientY - rect.top) / scale) - (initHeight / 2);

        const noteElement = document.createElement('div');
        noteElement.className = 'note';
        noteElement.contentEditable = true;
        noteElement.style.position = 'absolute';
        noteElement.style.left = `${x}px`;
        noteElement.style.top = `${y}px`;
        noteElement.style.minWidth = '50px';
        noteElement.style.minHeight = '20px';
        noteElement.style.backgroundColor = isBackgroundEnabled ? backgroundColor : 'transparent';
        noteElement.style.border = isBorderEnabled ? `2px solid ${borderColor}` : 'none';
        noteElement.style.borderRadius = `${borderRadius}px`;
        noteElement.style.padding = '5px';
        noteElement.style.overflow = 'auto';
        noteElement.style.resize = 'none';
        noteElement.style.wordWrap = 'break-word';

        function adjustSize() {
            const computed = window.getComputedStyle(noteElement);
            noteElement.style.height = 'auto';
            noteElement.style.width = 'auto';

            const width = noteElement.scrollWidth + parseInt(computed.paddingLeft) + parseInt(computed.paddingRight);
            const height = noteElement.scrollHeight + parseInt(computed.paddingTop) + parseInt(computed.paddingBottom);

            noteElement.style.width = `${width}px`;
            noteElement.style.height = `${height}px`;
        }

        noteElement.addEventListener('input', adjustSize);
        noteElement.addEventListener('click', function () {
            noteElement.focus();
        });



        notesContainer.appendChild(noteElement);
        noteElement.focus();
        adjustSize(); 
        }
    });

    document.getElementById('saveButton').addEventListener('click', function () {
        const mapImage = document.getElementById('mapImage');
        const mapId = mapImage.getAttribute('data-map-id');
        const mapName = mapImage.getAttribute('data-map-name');
        const canvas = document.createElement('canvas');
        canvas.width = mapImage.naturalWidth;
        canvas.height = mapImage.naturalHeight;
        const context = canvas.getContext('2d');
        context.drawImage(mapImage, 0, 0);
        const imageData = canvas.toDataURL('image/webp');

        const locations = Array.from(document.querySelectorAll('#locationsContainer img')).map(note => {
            return {
                icon_id: note.dataset.iconId,
                name: note.alt || 'Location',
                coordinates: {
                    x: parseInt(note.style.left, 10),
                    y: parseInt(note.style.top, 10),
                },
                size: note.style.width || 32,
            };
        });

        console.log(locations);

        const notes = Array.from(document.querySelectorAll('#notesContainer .note')).map(note => {
            return {
                content: note.innerHTML,
                style: {
                    x: parseInt(note.style.left, 10),
                    y: parseInt(note.style.top, 10),
                    width: parseInt(note.style.width, 10),
                    height: parseInt(note.style.height, 10),
                    backgroundColor: note.style.backgroundColor,
                    borderColor: note.style.border ? note.style.borderColor : 'none',
                    borderRadius: note.style.borderRadius
                }
            };
        });

        const mapData = {
            id: mapId,
            name: mapName,
            imageData: imageData,
            locations: locations,
            notes: notes
        };

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/map/update/' + mapId, true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                alert('Карта успішно збережена!');
            } else {
                alert('Не вдалося зберегти карту.');
            }
        };

        xhr.onerror = function () {
            alert('Сталася помилка мережі.');
        };

        xhr.send(JSON.stringify(mapData));
    });

    downloadButton.addEventListener('click', function () {
        const downloadButton = document.getElementById('downloadButton');
    const mapImage = document.getElementById('mapImage');
    const notesContainer = document.getElementById('notesContainer');
    const locationsContainer = document.getElementById('locationsContainer');

    downloadButton.addEventListener('click', function () {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        canvas.width = mapImage.naturalWidth;
        canvas.height = mapImage.naturalHeight;

        context.drawImage(mapImage, 0, 0);

        const locations = locationsContainer.querySelectorAll('img');
        locations.forEach(location => {
            const x = parseInt(location.style.left, 10);
            const y = parseInt(location.style.top, 10);
            context.drawImage(location, x, y, location.offsetWidth, location.offsetHeight);
        });

        const notes = notesContainer.querySelectorAll('div');
        notes.forEach(note => {
            const computedStyles = window.getComputedStyle(note);
            const x = parseInt(note.style.left, 10);
            const y = parseInt(note.style.top, 10);
            const width = parseInt(note.style.width, 10);
            const height = parseInt(note.style.height, 10);

            context.fillStyle = computedStyles.backgroundColor;
            context.fillRect(x, y, width, height);

            context.font = computedStyles.font;
            context.fillStyle = computedStyles.color;

            const text = note.textContent;
            const lines = text.split('\n');
            const lineHeight = 16;  

            lines.forEach((line, index) => {
                context.fillText(line.trim(), x + 5, y + (index + 1) * lineHeight); 
            });
        });

        const link = document.createElement('a');
        link.download = 'map_with_locations_and_notes.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
    });
</script>

</body>
</html>