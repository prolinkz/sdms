<?php
// admin_footer.php
// Assumes BASE_URL is available from init.php
?>
            </div><!-- End page-content -->
            <footer class="global-footer">
                &copy; <?= date('Y') ?> Student Database Management System. All rights reserved.
                <!-- You can add 'Designed by Prolinkz' if that's a credit you want to include -->
            </footer>
        </div><!-- End main-content -->
    </div><!-- End wrapper -->

    <!-- Global JavaScript for layout functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown functionality for settings menu
            const settingsDropdown = document.getElementById('settingsDropdown');
            if (settingsDropdown) {
                const userInfoToggle = settingsDropdown.querySelector('.user-info-toggle'); // The new toggle area
                
                userInfoToggle.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent document click from closing it immediately
                    settingsDropdown.classList.toggle('open');
                });

                // Close dropdown if clicked outside
                window.addEventListener('click', function(e) {
                    if (!settingsDropdown.contains(e.target)) {
                        settingsDropdown.classList.remove('open');
                    }
                });
            }

            // Sidebar Toggle (main toggle button in sidebar header)
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-collapsed');
                });
            }

            // Mobile Menu Toggle (hamburger icon in top-navbar for small screens)
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            if (mobileMenuToggle && sidebar) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });

                // Close sidebar if clicking outside when open (for mobile)
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768 && sidebar.classList.contains('open') && !sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                });
            }

            // Dark Mode Toggle
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                // Check local storage for dark mode preference on load
                const isDarkMode = localStorage.getItem('darkMode') === 'true';
                if (isDarkMode) {
                    document.body.classList.add('dark-mode');
                    darkModeToggle.checked = true;
                }

                darkModeToggle.addEventListener('change', function() {
                    if (this.checked) {
                        document.body.classList.add('dark-mode');
                        localStorage.setItem('darkMode', 'true');
                    } else {
                        document.body.classList.remove('dark-mode');
                        localStorage.setItem('darkMode', 'false');
                    }
                });
            }
        });
    </script>
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>