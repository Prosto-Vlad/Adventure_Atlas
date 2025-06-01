
<?= view('header') ?>

<div class="container mt-4">
    <h1>Галерея ваших мап</h1>

    <?php foreach ($mapTypes as $type): ?>
        <?php
        $mapsOfType = array_filter($maps, function ($map) use ($type) {
            return $map['type_id'] === $type['id'];
        });
        ?>

        <h2><?= esc($type['name']) ?></h2>

        <?php if (empty($mapsOfType)): ?>
            <p>Ви ще не маєте мап такого типу.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($mapsOfType as $map): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card" style="height: 100%;">
                            <img src="<?= base_url($map['image_path']) ?>" class="card-img-top" alt="Preview">
                            <div class="card-body">
                                <h5 class="card-title "><?= esc($map['name']) ?></h5>
                                <a href="/map/view/<?= $map['id'] ?>" class="btn btn-secondary">Детальніше</a>
                                <a href="/map/delete/<?= $map['id'] ?>" class="btn btn-danger " onclick="return confirm('Ви впевнені, що хочете видалити цю мапу?')">Видалити</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<footer>

</footer>


</body>
</html>