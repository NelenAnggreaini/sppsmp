
</main>
    </div>

    <footer class="global-footer">
        <span>&copy; 2026 Sistem Informasi Pembayaran SPP SMP | Version 1.0</span>
    </footer>

<script>
    // Auto hide alerts setelah 5 detik
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => alert.style.transition = 'opacity 0.5s');
        setTimeout(() => alerts.forEach(alert => alert.style.display = 'none'), 500);
    }, 5000);
</script>

</body>
</html>
