<div class="card-panel">
    <div class="card-title">
        <span>👥 Active Staff & System User Accounts</span>
        <a href="<?= $this->getUrl('users/create') ?>" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 21 20 19 16 19 16 17 14 17 14 15 12 15 12 13 8 13 8 11 4 11 4 9 1 9"/>
                <rect x="8" y="2" width="14" height="6" rx="1"/>
            </svg>
            <span>Register New Staff</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username ID</th>
                    <th>Full Staff Name</th>
                    <th>Role Clearance</th>
                    <th>Account Created</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong>#<?= $u['id'] ?></strong></td>
                        <td><code style="font-weight:700; color:var(--accent);"><?= htmlspecialchars($u['username']) ?></code></td>
                        <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                        <td>
                            <?php
                            $role = $u['role'];
                            if ($role === 'super_admin') {
                                echo '<span class="badge badge-danger">Super Admin</span>';
                            } elseif ($role === 'admin') {
                                echo '<span class="badge badge-success">Admin</span>';
                            } else {
                                echo '<span class="badge badge-warning">Salesman</span>';
                            }
                            ?>
                        </td>
                        <td style="font-size:0.9rem; color:var(--text-secondary);"><?= $u['created_at'] ?></td>
                        <td style="text-align: center;">
                            <?php if ((int)$u['id'] === (int)$_SESSION['user_id']): ?>
                                <span style="font-size:0.8rem; color:var(--text-muted); font-style:italic;">Active Account</span>
                            <?php else: ?>
                                <a href="<?= $this->getUrl("users/delete/{$u['id']}") ?>" class="btn btn-secondary btn-sm" style="color:var(--danger); border-color:rgba(239, 68, 68, 0.2);" onclick="return confirm('Confirm user account termination? The staff member will lose system access immediately.')">Terminate Account</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
