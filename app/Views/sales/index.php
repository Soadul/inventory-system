<div class="card-panel">
    <div class="card-title">
        <span>📋 Daily Sales Transactions Log</span>
        <a href="<?= $this->getUrl('sales/create') ?>" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Input Daily Sells</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Sales Representative</th>
                    <th>Transaction Date</th>
                    <th>Total Value</th>
                    <th>Cash Paid</th>
                    <th>Credit Due</th>
                    <th>Payment Way</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:var(--text-muted);">No sales recorded in the system.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><strong>#<?= $sale['id'] ?></strong></td>
                            <td><?= htmlspecialchars($sale['salesman_name']) ?></td>
                            <td style="font-size:0.9rem; color:var(--text-secondary);"><?= $sale['sale_date'] ?></td>
                            <td style="font-weight:700;"><?= number_format($sale['total_amount'], 2) ?> TK</td>
                            <td style="color:var(--success);"><?= number_format($sale['paid_amount'], 2) ?> TK</td>
                            <td>
                                <?php if ($sale['due_amount'] > 0.0): ?>
                                    <span style="color:var(--warning); font-weight:700;"><?= number_format($sale['due_amount'], 2) ?> TK</span>
                                    <span class="badge badge-warning" style="font-size:0.6rem; padding:1px 4px; margin-left:4px;">Credit</span>
                                <?php else: ?>
                                    <span style="color:var(--success); font-weight:700;">0.00 TK</span>
                                    <span class="badge badge-success" style="font-size:0.6rem; padding:1px 4px; margin-left:4px;">Paid</span>
                                <?php endif; ?>
                            </td>
                            <td><span style="font-size:0.85rem; padding:2px 8px; background:rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:4px;"><?= htmlspecialchars($sale['payment_method']) ?></span></td>
                            <td style="text-align: center;">
                                <a href="<?= $this->getUrl("sales/view/{$sale['id']}") ?>" class="btn btn-secondary btn-sm">Inspect Invoice</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
