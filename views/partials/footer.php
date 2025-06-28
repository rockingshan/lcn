<?php 
/*
 * views/partials/footer.php
 *
 * Footer partial for all pages.
 * - Renders copyright and footer.
 * - Uses Tailwind CSS for styling.
 */
</main> <!-- closes the main tag from the header -->

<footer class="bg-gray-800 text-white text-center p-4 fixed bottom-0 w-full">
    <p>&copy; 2012-<?php echo date("Y"); ?> All Rights with Meghbela Digital</p>
</footer>

<script>
    // Mobile menu toggle
    const btn = document.querySelector('button.mobile-menu-button');
    const menu = document.querySelector('.mobile-menu');

    btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });

    // Live search functionality
    function searchTable() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toUpperCase();
        const table = document.getElementById("channelTable");
        const tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
            let rowVisible = false;
            let tds = tr[i].getElementsByTagName("td");
            for (let j = 0; j < tds.length; j++) {
                if (tds[j]) {
                    if (tds[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                        rowVisible = true;
                        break;
                    }
                }
            }
            if (rowVisible) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    // Table sorting functionality
    const sortDirections = {};
    function sortTable(columnIndex) {
        const table = document.getElementById("channelTable");
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.getElementsByTagName("tr"));
        const dir = sortDirections[columnIndex] === 'asc' ? 'desc' : 'asc';
        sortDirections[columnIndex] = dir;
        
        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].textContent.trim();
            const bText = b.cells[columnIndex].textContent.trim();
            
            const aVal = isNaN(parseFloat(aText)) ? aText : parseFloat(aText);
            const bVal = isNaN(parseFloat(bText)) ? bText : parseFloat(bText);

            if (aVal < bVal) return dir === 'asc' ? -1 : 1;
            if (aVal > bVal) return dir === 'asc' ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tbody.appendChild(row));
    }
</script>

</body>
</html> 