<?php
$role = $_SESSION['user_role'] ?? '';
?>

<div class="card-panel">
    <div class="card-title">
        <span>📦 Complete Inventory Catalog</span>
        <?php if ($role !== 'salesman'): ?>
            <a href="<?= $this->getUrl('products/create') ?>" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Add Product</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Selling Price</th>
                    <?php if ($role !== 'salesman'): ?>
                        <th>Purchasing Cost</th>
                    <?php endif; ?>
                    <th>Available Stock</th>
                    <th>Min Alert Level</th>
                    <?php if ($role !== 'salesman'): ?>
                        <th style="text-align: center;">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:var(--text-muted);">No products registered in catalog.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                        <tr style="<?= ($prod['stock_quantity'] <= $prod['min_stock_alert']) ? 'background: rgba(239, 68, 68, 0.015);' : '' ?>">
                            <td><code style="font-weight:700; color:var(--accent);"><?= htmlspecialchars($prod['sku']) ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($prod['name']) ?></strong>
                                <?php if ($prod['stock_quantity'] <= $prod['min_stock_alert']): ?>
                                    <span class="badge badge-danger" style="font-size:0.65rem; margin-left:6px; padding:2px 4px;">Low Stock</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($prod['category_name']) ?></td>
                            <td><?= number_format($prod['price'], 2) ?> TK</td>
                            <?php if ($role !== 'salesman'): ?>
                                <td style="color:var(--text-secondary);"><?= number_format($prod['cost'], 2) ?> TK</td>
                            <?php endif; ?>
                            <td>
                                <span style="font-weight:700; <?= ($prod['stock_quantity'] <= $prod['min_stock_alert']) ? 'color:var(--danger);' : 'color:var(--success);' ?>">
                                    <?= $prod['stock_quantity'] ?> units
                                </span>
                            </td>
                            <td><?= $prod['min_stock_alert'] ?> units</td>
                            <?php if ($role !== 'salesman'): ?>
                                <td style="text-align: center;">
                                    <div style="display:flex; justify-content:center; gap:8px;">
                                        <a href="<?= $this->getUrl("products/restock/{$prod['id']}") ?>" class="btn btn-secondary btn-sm" style="color:var(--success); border-color:rgba(16, 185, 129, 0.2);" title="Increase Stock">Restock</a>
                                        <a href="<?= $this->getUrl("products/edit/{$prod['id']}") ?>" class="btn btn-secondary btn-sm" style="color:var(--accent); border-color:var(--accent-glow);" title="Modify specifications">Edit</a>
                                        <a href="<?= $this->getUrl("products/delete/{$prod['id']}") ?>" class="btn btn-secondary btn-sm" style="color:var(--danger); border-color:rgba(239, 68, 68, 0.2);" onclick="return confirm('Terminate product catalog record? This is irreversible.')" title="Delete product">Delete</a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
