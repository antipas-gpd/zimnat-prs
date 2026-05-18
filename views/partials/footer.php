    </div><!-- /page-content -->
</div><!-- /main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    const sb  = document.getElementById('sidebar');
    const ov  = document.getElementById('sidebar-overlay');
    const open = sb.classList.toggle('open');
    ov.style.display = open ? 'block' : 'none';
}

// Auto-dismiss alerts
document.querySelectorAll('.flash-alert').forEach(el => {
    setTimeout(() => el.classList.add('fade'), 4000);
});

// Confirm deletes
document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', e => {
        if (!confirm(btn.dataset.confirm)) e.preventDefault();
    });
});
</script>
</body>
</html>
