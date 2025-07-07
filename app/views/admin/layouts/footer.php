</div>
</main>
</div>
</div>
<script>
    window.APP_BASE_URL = '<?= BASE_URL ?>';
</script>

<script src="<?= BASE_URL ?>/js/admin.js?v=1.0"></script>

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