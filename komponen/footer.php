<?php
// komponen/footer.php
?>
    </main>
    <footer class="bg-white border-t border-gray-200 py-6 mt-auto dark:bg-gray-800 dark:border-gray-700 transition-colors duration-300">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Dibuat untuk Praktik Kerja Lapangan.</p>
        </div>
    </footer>

    <script>
        // --- LOGIKA DARK MODE ---
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleBtn = document.getElementById('theme-toggle');

        // Set ikon awal
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });

        // --- LOGIKA DROPDOWN PROFIL ---
        const dropdownBtn = document.getElementById('dropdownProfileButton');
        const dropdownMenu = document.getElementById('dropdownProfile');

        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Mencegah klik tertutup langsung
            dropdownMenu.classList.toggle('hidden');
        });

        // Tutup dropdown jika klik di luar area
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>