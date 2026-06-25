                <footer class="fw-app-footer">
                    &copy; <?= date('Y') ?> Sistem Informasi Laundry
                </footer>
            </div>
        </main>
    </div>
</div>
<script>
    (function () {
        var toggle = document.querySelector('[data-sidebar-toggle]');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            document.body.classList.toggle('fw-sidebar-open');
        });
    })();
</script>
</body>
</html>
