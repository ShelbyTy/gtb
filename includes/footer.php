    <!-- Bootstrap pour le menu responsive et les composants -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lance les notifications Bootstrap quand la page est chargée
        document.querySelectorAll('.toast').forEach(function(toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        });
    </script>
    </body>

    </html>