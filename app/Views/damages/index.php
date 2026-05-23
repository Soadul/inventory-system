<div class="card-panel">
    <div class="card-title">
        <span>📋 Recorded Stock Damage & Waste Register</span>
        <a href="<?= $this->getUrl('damages/create') ?>" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Log Product Damage</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Product SKU</th>
                    <th>Product Name</th>
                    <th>Deducted Quantity</th>
                    <th>Waste Reason</th>
                    <th>Recorded By Staff</th>
                    <th>Audit Log Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($damages)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:var(--text-muted);">No stock damages logged in the system.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($damages as $dmg): ?>
                        <tr>
                            <td><strong>#<?= $dmg['id'] ?></strong></td>
                            <td><code><?= htmlspecialchars($dmg['product_sku']) ?></code></td>
                            <td><strong><?= htmlspecialchars($dmg['product_name']) ?></strong></td>
                            <td><span style="color:var(--danger); font-weight:700;"><?= $dmg['quantity'] ?> units</span></td>
                            <td>
                                <?php
                                $r = $dmg['reason'];
                                if ($r === 'Expired') $badgeClass = 'badge-danger';
                                elseif ($r === 'Broken') $badgeClass = 'badge-warning';
                                else $badgeClass = 'badge-success'; // Spoiled / Lost
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($r) ?></span>
                            </td>
                            <td><?= htmlspecialchars($dmg['recorder_name']) ?></td>
                            <td style="font-size:0.9rem; color:var(--text-secondary);"><?= $dmg['record_date'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
