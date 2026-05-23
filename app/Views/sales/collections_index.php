<div class="card-panel">
    <div class="card-title">
        <span>📋 Recorded Credit Accounts Collections</span>
        <a href="<?= $this->getUrl('sales/collections/create') ?>" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Log Cash Collection</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Collection ID</th>
                    <th>Recorded By</th>
                    <th>Customer / Store Name</th>
                    <th>Cash Collected</th>
                    <th>Collection Date</th>
                    <th>Description / Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($collections)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-muted);">No credit collections logged in system database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($collections as $coll): ?>
                        <tr>
                            <td><strong>#<?= $coll['id'] ?></strong></td>
                            <td><?= htmlspecialchars($coll['salesman_name']) ?></td>
                            <td><strong><?= htmlspecialchars($coll['customer_name']) ?></strong></td>
                            <td style="color:var(--success); font-weight:700;"><?= number_format($coll['amount'], 2) ?> TK</td>
                            <td style="font-size:0.9rem; color:var(--text-secondary);"><?= $coll['payment_date'] ?></td>
                            <td style="font-size:0.85rem; color:var(--text-secondary);"><?= htmlspecialchars($coll['notes'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
