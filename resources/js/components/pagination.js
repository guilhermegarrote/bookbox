import '../../css/components/_pagination.css';

export function bindPaginationForm() {
    const paginationForm = document.getElementById('paginationForm');
    if (!paginationForm) return;

    const perPageSelect = paginationForm.querySelector('#perPage');
    const pageInput = paginationForm.querySelector('#pageInput');

    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => {
            const params = {
                perPage: perPageSelect.value,
                page: 1,
            };

            paginationForm.dispatchEvent(new CustomEvent('filtersUpdated', {
                detail: params,
                bubbles: true,
            }));
        });
    }

    if (pageInput) {
        const getValidatedPage = () => {
            const max = parseInt(pageInput.getAttribute('max'));
            const min = parseInt(pageInput.getAttribute('min')) || 1;
            let page = parseInt(pageInput.value);

            if (isNaN(page)) page = 1;
            if (page < min) page = min;
            if (page > max) page = max;

            return page;
        };

        const emitPaginationChange = () => {
            const params = {
                page: getValidatedPage(),
                perPage: perPageSelect?.value || 10,
            };

            paginationForm.dispatchEvent(new CustomEvent('filtersUpdated', {
                detail: params,
                bubbles: true,
            }));
        };

        pageInput.addEventListener('change', emitPaginationChange);
        pageInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                emitPaginationChange();
            }
        });
    }

    const buttons = paginationForm.querySelectorAll('button.page-link');
    buttons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            if (button.disabled) return;

            const page = parseInt(button.dataset.page);
            const perPage = perPageSelect?.value || 10;

            if (page) {
                paginationForm.dispatchEvent(new CustomEvent('filtersUpdated', {
                    detail: { page, perPage },
                    bubbles: true,
                }));
            }
        });
    });
}
