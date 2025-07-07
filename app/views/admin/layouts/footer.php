</div> <!-- /.content-container -->
</main>
</div> <!-- /.admin-main-container -->
</div> <!-- /.admin-wrapper -->

<!-- Script for layout interactivity (e.g., dropdown menu) -->
<script>
    function layoutData() {
        return {
            isProfileMenuOpen: false,
            toggleProfileMenu() {
                this.isProfileMenuOpen = !this.isProfileMenuOpen;
            },
            closeProfileMenu() {
                this.isProfileMenuOpen = false;
            },
        }
    }
</script>
</body>

</html>