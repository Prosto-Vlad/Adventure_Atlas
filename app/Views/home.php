<?= view('header') ?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              



<section class="flex-grow-1">
    <div class="container mt-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="d-flex justify-content-center mt-4">
                    <img src="/images/home/landscape.svg" class="center-image" alt="Мапа світу" style="width: 50%; ">
                </div>
                <div class="card-body">
                    <h5 class="card-title">Мапа світу</h5>
                    <form id="generateWorldForm">
                        <div class="form-group">
                            <label for="worlWidth">Ширина:</label>
                            <input type="number" class="form-control" id="worlWidth" required>
                        </div>
                        <div class="form-group">
                            <label for="worldHeight">Висота:</label>
                            <input type="number" class="form-control" id="worldHeight" required>
                        </div>
                        <button type="button" class="btn btn-secondary btn-block" id="worldGenerateButton">Згенерувати</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="d-flex justify-content-center mt-4">
                    <img src="/images/home/castle.svg" class="card-img-top" alt="Мапа міста" style="width: 50%; ">
                </div>
                <div class="card-body">
                    <h5 class="card-title">Мапа міста</h5>
                    <form id="generateCityForm">
                        <div class="form-group">
                            <label for="cityWidth">Ширина:</label>
                            <input type="number" class="form-control" id="cityWidth" required>
                        </div>
                        <div class="form-group">
                            <label for="cityHeight">Висота:</label>
                            <input type="number" class="form-control" id="cityHeight" required>
                        </div>
                        <div class="form-group">
                            <label for="pointCount">Кількість точок для розбиття:</label>
                            <input type="number" class="form-control" id="pointCount" value="20" required>
                        </div>
                        <div class="form-group">
                            <label for="roadWidth">Ширина доріг:</label>
                            <input type="number" class="form-control" id="roadWidth" value="2" required>
                        </div>
                        <div class="form-group">
                            <label for="wallWidth">Ширина стін:</label>
                            <input type="number" class="form-control" id="wallWidth" value="5" required>
                        </div>
                        <div class="form-group">
                            <label for="towerRadius">Радіус башт:</label>
                            <input type="number" class="form-control" id="towerRadius" value="10" required>
                        </div>
                        <button type="button" class="btn btn-secondary btn-block" id="cityGenerateButton">Згенерувати</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="d-flex justify-content-center mt-4">
                    <img src="/images/home/door.svg" class="card-img-top" alt="Мапа локації" style="width: 50%; ">
                </div>
                <div class="card-body">
                    <h5 class="card-title">Мапа локації</h5>
                    <form id="generateLocationForm">
                        <div class="form-group">
                            <label for="locationWidth">Ширина:</label>
                            <input type="number" class="form-control" id="locationWidth" required>
                        </div>
                        <div class="form-group">
                            <label for="locationHeight">Висота:</label>
                            <input type="number" class="form-control" id="locationHeight" required>
                        </div>
                        <div class="form-group">
                            <label for="locationGrid">Розмір сітки:</label>
                            <input type="number" class="form-control" id="locationGrid" value="25" required>
                        </div>
                        <div class="form-group">
                            <label for="locationMaxRoom">Максимальна кількість кімнат:</label>
                            <input type="number" class="form-control" id="locationMaxRoom" value="20"  required>
                        </div>
                        <div class="form-group">
                            <label for="locationMaxRoomSize">Максимальний розмір кімнати:</label>
                            <input type="number" class="form-control" id="locationMaxRoomSize" value="10" required>
                        </div>
                        <div class="form-group">
                            <label for="locationMinRoomSize">Мінімальний розмір кімнати:</label>
                            <input type="number" class="form-control" id="locationMinRoomSize" value="5"  required>
                        </div>
                        <button type="button" class="btn btn-secondary btn-block" id="locationGenerateButton">Згенерувати</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<div class="modal" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body d-flex justify-content-center align-items-center">
                <div class="spinner-border text-dark" role="status" id="spinner" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Завантаження...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>

</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const worldGenerateButton = document.getElementById('worldGenerateButton');
        const cityGenerateButton = document.getElementById('cityGenerateButton');
        const locationGenerateButton = document.getElementById('locationGenerateButton');

        const worldWidthInput = document.getElementById('worlWidth');
        const worldHeightInput = document.getElementById('worldHeight');

        const cityWidthInput = document.getElementById('cityWidth');
        const cityHeightInput = document.getElementById('cityHeight');
        const pointCountInput = document.getElementById('pointCount');
        const roadWidthInput = document.getElementById('roadWidth');
        const wallWidthInput = document.getElementById('wallWidth');
        const towerRadiusInput = document.getElementById('towerRadius');

        const locationWidthInput = document.getElementById('locationWidth');
        const locationHeightInput = document.getElementById('locationHeight');
        const locationGridInput = document.getElementById('locationGrid');
        const locationMaxRoomInput = document.getElementById('locationMaxRoom');
        const locationMaxRoomSizeInput = document.getElementById('locationMaxRoomSize');
        const locationMinRoomSizeInput = document.getElementById('locationMinRoomSize');

        const spinner = document.getElementById('spinner');

            worldGenerateButton.addEventListener('click', function () {
                const width = worldWidthInput.value;
                const height = worldHeightInput.value;

                if(!width || !height) {
                    alert('Будь ласка, введіть ширину та висоту.');
                    return;
                }
                if(width < 100 || height < 100) {
                    alert('Ширина та висота повинні бути не менше 100.');
                    return;
                }
                if(width > 1000 || height > 1000) {
                    alert('Ширина та висота повинні бути не більше 1000.');
                    return;
                }


                $('#loadingModal').modal('show');

                if (width && height) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'generate-world-map', true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.onload = function() {
                        $('#loadingModal').modal('hide');
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    window.location.href = response.redirect;
                                } else {
                                    alert(response.message || 'Не вдалося згенерувати мапу');
                                }
                            } catch (e) {
                                alert('Сталася помилка під час обробки відповіді сервера.');
                            }
                        } else {
                            alert('Помилка при генерації мапи');
                        }
                    };
                    xhr.onerror = function() {
                        alert('Сталася помилка мережі');
                    };
                    console.log(`Generating world map with width: ${width}, height: ${height}`);
                    xhr.send(JSON.stringify({ width: parseInt(width, 10), height: parseInt(height, 10) }));
                } else {
                    alert('Будь ласка, введіть ширину та висоту.');
                }
            });

            cityGenerateButton.addEventListener('click', function () {
                const width = cityWidthInput.value;
                const height = cityHeightInput.value;
                const pointCount = pointCountInput.value;
                const roadWidth = roadWidthInput.value;
                const wallWidth = wallWidthInput.value;
                const towerRadius = towerRadiusInput.value;

                $('#loadingModal').modal('show');

                if (width && height) {

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'generate-city-map', true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.onload = function() {
                        $('#loadingModal').modal('hide');
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    window.location.href = response.redirect;
                                } else {
                                    alert(response.message || 'Не вдалося згенерувати мапу');
                                }
                            } catch (e) {
                                alert('Сталася помилка під час обробки відповіді сервера.');
                            }
                        } else {
                            alert('Помилка при генерації мапи');
                        }
                    };
                    xhr.onerror = function() {
                        alert('Сталася помилка мережі');
                    };
                    console.log(`Generating world map with width: ${width}, height: ${height}`);
                    xhr.send(JSON.stringify({ 
                        width: parseInt(width, 10),
                        height: parseInt(height, 10),
                        pointCount: parseInt(pointCount, 10),
                        roadWidth: parseInt(roadWidth, 10),
                        wallWidth: parseInt(wallWidth, 10),
                        towerRadius: parseInt(towerRadius, 10)
                     }));
                } else {
                    alert('Будь ласка, введіть ширину та висоту.');
                }
            });

            locationGenerateButton.addEventListener('click', function () {
                const width = locationWidthInput.value;
                const height = locationHeightInput.value;
                const gridSize = locationGridInput.value;
                const maxRoom = locationMaxRoomInput.value;
                const maxRoomSize = locationMaxRoomSizeInput.value;
                const minRoomSize = locationMinRoomSizeInput.value;

                $('#loadingModal').modal('show');

                if (width && height) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'generate-location-map', true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.onload = function() {
                        $('#loadingModal').modal('hide');
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    window.location.href = response.redirect;
                                } else {
                                    alert(response.message || 'Не вдалося згенерувати мапу');
                                }
                            } catch (e) {
                                alert('Сталася помилка під час обробки відповіді сервера.');
                            }
                        } else {
                            alert('Помилка при генерації мапи');
                        }
                    };
                    xhr.onerror = function() {
                        alert('Сталася помилка мережі');
                    };
                    console.log(`Generating world map with width: ${width}, height: ${height}`);
                    xhr.send(JSON.stringify({ 
                        width: parseInt(width, 10), 
                        height: parseInt(height, 10), 
                        gridSize: parseInt(gridSize, 10),
                        maxRoom: parseInt(maxRoom, 10),
                        maxRoomSize: parseInt(maxRoomSize, 10),
                        minRoomSize: parseInt(minRoomSize, 10)}));
                } else {
                    alert('Будь ласка, введіть ширину та висоту.');
                }
            });
        });
</script>

</body>
</html>