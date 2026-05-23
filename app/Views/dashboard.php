<?php
$role = $_SESSION['user_role'] ?? '';
$fullName = $_SESSION['user_fullname'] ?? 'User';
?>

<!-- Dynamic Statistics Grid -->
<div class="stats-grid">
    <?php if ($role === 'salesman'): ?>
        <!-- Salesman Indicators -->
        <div class="stat-card">
            <div class="stat-details">
                <h4>Your Sales (Invoice Value)</h4>
                <div class="stat-value"><?= number_format($stats['total_sales'] ?? 0, 2) ?> TK</div>
            </div>
            <div class="stat-icon" style="color:var(--accent); background:rgba(99, 102, 241, 0.08);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-details">
                <h4>Your Collections (Cash)</h4>
                <div class="stat-value" style="color:var(--success);"><?= number_format($stats['total_collected_cash'] ?? 0, 2) ?> TK</div>
            </div>
            <div class="stat-icon" style="color:var(--success); background:rgba(16, 185, 129, 0.08);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-details">
                <h4>Your Customer Dues</h4>
                <div class="stat-value" style="color:var(--warning);"><?= number_format($stats['total_dues'] ?? 0, 2) ?> TK</div>
            </div>
            <div class="stat-icon" style="color:var(--warning); background:rgba(245, 158, 11, 0.08);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
            </div>
        </div>
    <?php else: ?>
        <!-- Admin/Super Admin Indicators -->
        <div class="stat-card">
            <div class="stat-details">
                <h4>Total Sales</h4>
                <div class="stat-value"><?= number_format($stats['total_sales'] ?? 0, 2) ?> TK</div>
            </div>
            <div class="stat-icon" style="color:var(--accent); background:rgba(99, 102, 241, 0.08);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-details">
                <h4>Collected Cash</h4>
                <div class="stat-value" style="color:var(--success);"><?= number_format($stats['total_collected'] ?? 0, 2) ?> TK</div>
            </div>
            <div class="stat-icon" style="color:var(--success); background:rgba(16, 185, 129, 0.08);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <line x1="12" y1="4" x2="12" y2="20"/>
                </svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-details">
                <h4>Pending Dues</h4>
                <div class="stat-value" style="color:var(--warning);"><?= number_format($stats['total_dues'] ?? 0, 2) ?> TK</div>
            </div>
            <div class="stat-icon" style="color:var(--warning); background:rgba(245, 158, 11, 0.08);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-details">
                <h4>Stock Quantity</h4>
                <div class="stat-value" style="color:#ffffff;"><?= $stats['total_stock'] ?? 0 ?> units</div>
            </div>
            <div class="stat-icon" style="color:#ffffff; background:rgba(255,255,255,0.03);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Main Workspace Panel Grid -->
<div class="dashboard-grid">
    
    <!-- Column 1: Recent Invoices -->
    <div class="card-panel">
        <div class="card-title">
            <span>📡 Recent Sales Transactions</span>
            <a href="<?= $this->getUrl('sales/create') ?>" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Input Sales</span>
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Salesman</th>
                        <th>Total Value</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentSales)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--text-muted);">No sales recorded yet today.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentSales as $sale): ?>
                            <tr>
                                <td><strong>#<?= $sale['id'] ?></strong></td>
                                <td><?= htmlspecialchars($sale['salesman_name']) ?></td>
                                <td><?= number_format($sale['total_amount'], 2) ?> TK</td>
                                <td>
                                    <?php if ($sale['due_amount'] <= 0.0): ?>
                                        <span class="badge badge-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Due</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= $this->getUrl("sales/view/{$sale['id']}") ?>" class="btn btn-secondary btn-sm">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Column 2: Alerts & Waste -->
    <div class="dashboard-col-side">
        
        <!-- Low Stock Warning Panel (Restricted to Admin Roles) -->
        <?php if ($role !== 'salesman'): ?>
            <div class="card-panel">
                <div class="card-title" style="color:var(--danger);">
                    <span>⚠️ Low Stock Alerts</span>
                    <span class="badge badge-danger"><?= count($lowStock) ?> Alert(s)</span>
                </div>
                <div class="low-stock-list">
                    <?php if (empty($lowStock)): ?>
                        <p style="color:var(--text-muted); font-size:0.9rem; text-align:center;">All stock levels within optimal bounds.</p>
                    <?php else: ?>
                        <?php foreach ($lowStock as $prod): ?>
                            <div class="low-stock-item">
                                <div class="low-stock-details">
                                    <h5><?= htmlspecialchars($prod['name']) ?></h5>
                                    <span>SKU: <?= htmlspecialchars($prod['sku']) ?></span>
                                </div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="low-stock-badge"><?= $prod['stock_quantity'] ?> left</div>
                                    <a href="<?= $this->getUrl("products/restock/{$prod['id']}") ?>" class="btn btn-primary btn-sm" style="padding:4px 8px; background:var(--success);">Restock</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Recent Damages (Waste Log) -->
        <div class="card-panel">
            <div class="card-title">
                <span>🗑️ Recent Damages & Waste</span>
                <a href="<?= $this->getUrl('damages/create') ?>" class="btn btn-secondary btn-sm" style="color:var(--danger); border-color:rgba(239, 68, 68, 0.2);">Log Waste</a>
            </div>
            
            <div class="table-responsive">
                <table class="table" style="font-size:0.85rem;">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentDamages)): ?>
                            <tr>
                                <td colspan="3" style="text-align:center; color:var(--text-muted);">No waste records.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentDamages as $dmg): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($dmg['product_name']) ?></strong></td>
                                    <td><?= $dmg['quantity'] ?> units</td>
                                    <td><span class="badge badge-danger"><?= htmlspecialchars($dmg['reason']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
