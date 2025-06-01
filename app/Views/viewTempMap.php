<?= view('header') ?>

<?php
$isLoggedIn = session()->get('logged_in');
$userName = $isLoggedIn ? session()->get('username') : 'Увійти або зареєструватись';
?>

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
                        <?php if ($isLoggedIn): ?>
                            <li class="nav-item">
                                <button class="btn btn-secondary w-100 my-2" id="saveButton" data-toggle="modal" data-target="#nameModal">Зберегти</button>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <button id="downloadButton" class="btn btn-success w-100 my-2">Скачати</button>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col d-flex flex-column flex-grow-1">
                <div id="canvasContainer" style="overflow: auto; position: relative; width: 100%; height: 100%;">
                    <div id="mapContainer" class="panzoom" style="width: 100%; height: 100%; position: relative;">
                        <img id="mapImage" src="<?= esc($imageDataUrl) ?>" alt="Вашу мапу вкрали гобліни" style="max-width: none; height: auto; display: block;" data-map-type-id="<?= esc($type_id) ?>">

                        <div id="notesContainer" class="position-absolute" style="top: 0; left: 0; width: 100%;">
                            
                        </div>
                        <div id="locationsContainer" class="position-absolute" style="top: 0; left: 0; width: 100%;">
                            
                        </div>
                    </div>
                </div>
                <div id="toolMenu" class="position-absolute bg-white p-2 shadow rounded" style="display: none; bottom: 10px; left: 10px;">
                <div class="container">
                    <div class="row no-gutters">
                        <?php foreach ($icons as $icon): ?>
                            <div class="col-4 d-flex justify-content-center mb-2">
                                <button class="btn btn-light p-0" id="<?php echo $icon['id'] ?>" style="width: 32px; height: 32px;">
                                    <img src="<?php echo base_url($icon['image_path']); ?>" alt="<?php echo $icon['name']; ?>" style="width: 32px; height: 32px;">
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 10px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <label for="sizeSlider" style="margin-right: 10px;">Розмір іконки:</label>
                            <span id="sizeValue" style="min-width: 30px;">32</span>
                        </div>
                        <input type="range" id="sizeSlider" min="16" max="64" value="32" style="width: auto; margin: 10px 0;">
                    </div>

                    <button id="deleteButton" class="btn btn-danger w-100 mt-2">Видалення</button>
                </div>
                </div>
                <div id="notesMenu" class="position-absolute bg-white p-2 shadow rounded" style="display: none; bottom: 10px; left: 10px;">
                    <h5>Налаштування нотатки</h5>
                    <div class="form-group">
                        <label for="backgroundColor">Колір фону:</label>
                        <input type="color" id="backgroundColor" class="form-control">
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

<div class="modal fade" id="nameModal" tabindex="-1" role="dialog" aria-labelledby="nameModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nameModalLabel">Введіть назву мапи</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="nameForm">
                    <div class="form-group">
                        <label for="mapName">Назва:</label>
                        <input type="text" class="form-control" id="mapName" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Закрити</button>
                <button type="button" class="btn btn-secondary" id="saveMapButton">Зберегти</button>
            </div>
        </div>
    </div>
</div>

<footer>

</footer>

<script>
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
        console.log('Notes button clicked');
        console.log('Current menu:', currentMenu);
        console.log('Notes menu display:', notesMenu.style.display);
        if (notesMenu.style.display === 'none' || notesMenu.style.display === '') {
            notesMenu.style.display = 'block';
            console.log('Notes menu displayed');
        } else {
            notesMenu.style.display = 'none';
            console.log('Notes menu hidden');
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

    document.getElementById('saveMapButton').addEventListener('click', function () {
        const mapImage = document.getElementById('mapImage');
        const mapId = mapImage.getAttribute('data-map-id');
        const mapName = document.getElementById('mapName').value || 'Нова карта';
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
            name: mapName,
            type_id: mapImage.getAttribute('data-map-type-id'),
            imageData: imageData,
            locations: locations,
            notes: notes
        };

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/map/save', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                window.location.href = '/gallery';
            } else {
                alert('Не вдалося зберегти карту.');
            }
        };

        xhr.onerror = function () {
            alert('Сталася помилка мережі.');
        };

        xhr.send(JSON.stringify(mapData));
    });
</script>

</body>
</html>