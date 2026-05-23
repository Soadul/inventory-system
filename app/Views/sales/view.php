<div class="card-panel" style="max-width: 800px; margin: 0 auto;">
    
    <!-- Invoice Master Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:1px solid var(--border); padding-bottom:24px; margin-bottom:30px;">
        <div>
            <h2 style="font-size:1.4rem; font-weight:800; color:#ffffff; display:flex; align-items:center; gap:8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--accent);">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span>Sales Invoice #<?= $sale['id'] ?></span>
            </h2>
            <p style="color:var(--text-secondary); font-size:0.85rem; margin-top:6px;">Date Compiled: <strong style="color:#ffffff;"><?= $sale['sale_date'] ?></strong></p>
        </div>
        
        <div style="text-align: right;">
            <div class="profile-role" style="font-size:0.7rem; font-weight:700; margin-bottom:8px;">Audit Status</div>
            <div>
                <?php if ($sale['due_amount'] <= 0.0): ?>
                    <span class="badge badge-success" style="padding:6px 12px; font-size:0.75rem;">Fully Settled (Paid)</span>
                <?php else: ?>
                    <span class="badge badge-warning" style="padding:6px 12px; font-size:0.75rem;">Outstanding Credit Due</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="sales-row" style="grid-template-columns: 1fr 1fr; background:rgba(255,255,255,0.01); border-color:var(--border); padding:20px; border-radius:10px; margin-bottom:30px; gap:20px;">
        <div>
            <h5 style="color:var(--text-secondary); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">Sales Representative</h5>
            <p style="font-weight:600; color:#ffffff;"><?= htmlspecialchars($sale['salesman_name']) ?></p>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">OS-Inventory Certified Staff Account</p>
        </div>
        <div>
            <h5 style="color:var(--text-secondary); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">Payment Summary</h5>
            <p style="font-size:0.9rem; color:var(--text-secondary);">Method Used: <strong style="color:#ffffff;"><?= htmlspecialchars($sale['payment_method']) ?></strong></p>
            <?php if ($sale['due_amount'] > 0.0): ?>
                <p style="font-size:0.9rem; color:var(--text-secondary); margin-top:4px;">Pending Collections: <span style="color:var(--warning); font-weight:700;"><?= number_format($sale['due_amount'], 2) ?> TK</span></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Items list table -->
    <div class="table-responsive" style="margin-bottom: 40px;">
        <h4 style="font-size:1.05rem; font-weight:700; margin-bottom:16px;">Ordered Invoice Items</h4>
        <table class="table" style="font-size:0.9rem;">
            <thead>
                <tr style="background: rgba(255,255,255,0.015);">
                    <th>SKU Code</th>
                    <th>Product Name</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sale['items'] as $item): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($item['product_sku']) ?></code></td>
                        <td><strong><?= htmlspecialchars($item['product_name']) ?></strong></td>
                        <td style="text-align: right; color:var(--text-secondary);"><?= number_format($item['unit_price'], 2) ?> TK</td>
                        <td style="text-align: center; font-weight:600;"><?= $item['quantity'] ?> units</td>
                        <td style="text-align: right; font-weight:700;"><?= number_format($item['subtotal'], 2) ?> TK</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Invoice Totals Grid -->
    <div style="border-top:1px solid var(--border); padding-top:30px; display:flex; justify-content:space-between; align-items:flex-start;">
        <a href="<?= $this->getUrl('sales') ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            <span>Back to Invoices</span>
        </a>
        
        <div style="width:100%; max-width:320px; background:rgba(255,255,255,0.01); border:1px solid var(--border); border-radius:10px; padding:20px;">
            <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:var(--text-secondary); margin-bottom:8px;">
                <span>Total Invoice Value:</span>
                <span style="font-weight:700; color:#ffffff;"><?= number_format($sale['total_amount'], 2) ?> TK</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:var(--text-secondary); margin-bottom:12px; border-bottom:1px dashed var(--border); padding-bottom:8px;">
                <span>Collected Cash:</span>
                <span style="font-weight:700; color:var(--success);"><?= number_format($sale['paid_amount'], 2) ?> TK</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:1rem; color:var(--text-secondary); font-weight:700;">
                <span style="color:#ffffff;">Outstanding Due:</span>
                <span style="color:var(--warning); font-weight:800;"><?= number_format($sale['due_amount'], 2) ?> TK</span>
            </div>
        </div>
    </div>
</div>
