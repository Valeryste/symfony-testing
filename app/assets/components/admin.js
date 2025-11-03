function initCategoryTree() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.category-toggle')) {
            const toggle = e.target.closest('.category-toggle');
            const parentRow = toggle.closest('tr');
            const parentLevel = parseInt(parentRow.dataset.level);

            let currentRow = parentRow.nextElementSibling;
            const shouldShow = currentRow && currentRow.classList.contains('d-none');

            while (currentRow) {
                const currentLevel = parseInt(currentRow.dataset.level);

                if (currentLevel <= parentLevel) break;

                currentRow.classList.toggle('d-none');

                currentRow = currentRow.nextElementSibling;
            }

            if (toggle) {
                toggle.textContent = shouldShow ? '📀' : '📂';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', initCategoryTree);