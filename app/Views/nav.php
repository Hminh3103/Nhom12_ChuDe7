<nav class="lab-nav" aria-label="Chọn demo">
    <a href="<?= htmlspecialchars($base) ?>/" <?= ($activeLab ?? 'prompt') === 'prompt' ? 'aria-current="page"' : '' ?>>01 · Prompt Injection</a>
    <a href="<?= htmlspecialchars($base) ?>/sql-injection" <?= ($activeLab ?? 'prompt') === 'sql' ? 'aria-current="page"' : '' ?>>02 · SQL Injection</a>
</nav>
