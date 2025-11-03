function initCategoryTree() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.category-toggle')) {
            const toggle = e.target.closest('.category-toggle');
            const parentRow = toggle.closest('tr');
            const parentLevel = parseInt(parentRow.dataset.level);

            let currentRow = parentRow.nextElementSibling;
            const shouldShow = currentRow && currentRow.classList.contains('d-none');

            toggle.textContent = shouldShow ? '📂' : '📀';

            while (currentRow) {
                const currentLevel = parseInt(currentRow.dataset.level);

                if (currentLevel <= parentLevel) break;

                if (shouldShow) {
                    if (currentLevel === parentLevel + 1) {
                        currentRow.classList.remove('d-none');

                        const childToggle = currentRow.querySelector('.category-toggle');
                        if (childToggle) {
                            const nextRow = currentRow.nextElementSibling;
                            if (nextRow && nextRow.classList.contains('d-none')) {
                                childToggle.textContent = '📀';
                            } else {
                                childToggle.textContent = '📂';
                            }
                        }
                    }
                } else {
                    currentRow.classList.add('d-none');

                    const childToggle = currentRow.querySelector('.category-toggle');
                    if (childToggle) {
                        childToggle.textContent = '📀';
                    }
                }

                currentRow = currentRow.nextElementSibling;
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', initCategoryTree);