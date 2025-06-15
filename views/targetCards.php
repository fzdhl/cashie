<?php foreach ($targets as $target): ?>
    <div class="col">
        <div class="card shadow-sm p-3">
            <div class="d-flex justify-content-between">
                <h5 class="mb-2 fw-semibold"><?= htmlspecialchars($target['target']) ?></h5>
                <div>
                    <button
                        class="btn btn-sm btn-outline-success me-1 edit-btn"
                        data-id="<?= $target['target_id'] ?>"
                        data-name="<?= htmlspecialchars($target['target']) ?>"
                        data-amount="<?= $target['jumlah'] ?>">
                        Edit
                    </button>
                    <button 
                        class="btn btn-sm btn-outline-danger delete-btn"
                        data-id="<?=$target['target_id']?>">
                        Hapus
                    </button>
                </div>
            </div>
            <p class="text-muted mb-2">Target: Rp<?= number_format($target['jumlah'], 2, ',', '.') ?></p>
            <div class="progress mb-2" style="height: 15px;">
                <div class="progress-bar bg-success" style="width: 75%;">75%</div>
            </div>
            <small class="text-muted">
                Rp<?= number_format($target['jumlah'] * 0.75, 2, ',', '.') ?> dari Rp<?= number_format($target['jumlah'], 2, ',', '.') ?>
            </small>
        </div>
    </div>
<?php endforeach; ?>