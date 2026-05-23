<div class="card-panel" style="max-width: 550px; margin: 0 auto;">
    <div class="card-title">
        <span>👥 Create New Staff User Account</span>
        <a href="<?= $this->getUrl('users') ?>" class="btn btn-secondary btn-sm">Cancel</a>
    </div>

    <form action="<?= $this->getUrl('users/create') ?>" method="POST">
        <div class="form-group">
            <label for="full_name">Staff Full Name *</label>
            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. Rafiqul Islam" required autofocus>
        </div>

        <div class="form-group">
            <label for="username">Username ID *</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="e.g. rafiqul" required>
            <small style="color:var(--text-muted); display:block; margin-top:4px;">* Must be unique and contains only alphanumeric characters.</small>
        </div>

        <div class="form-group">
            <label for="password">Security Password *</label>
            <input type="password" id="password" name="password" minlength="6" class="form-control" placeholder="••••••••" required>
            <small style="color:var(--text-muted); display:block; margin-top:4px;">* Must be at least 6 characters in length.</small>
        </div>

        <div class="form-group">
            <label for="role">Role Clearance Level *</label>
            <select id="role" name="role" class="form-control" required>
                <option value="" disabled selected>-- Select Role Clearance --</option>
                <option value="admin">Administrator (Inventory & Damaged CRUD)</option>
                <option value="salesman">Sales Representative (Sales, Collections, Damage log inputs)</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
            <span>Register Staff Account</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/>
                <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
        </button>
    </form>
</div>
