import { bindPaginationForm } from '../../components/pagination';
import { initStudentsModals } from './students-modals';

document.addEventListener('DOMContentLoaded', () => {
    bindPaginationForm();
    resizeTableWrapper();
    initStudentsModals();
});

window.addEventListener('resize', resizeTableWrapper);

function getTotalVerticalSpace(element) {
    if (!element) return 0;
    const style = getComputedStyle(element);
    return (
        element.offsetHeight +
        (parseFloat(style.marginTop) || 0) +
        (parseFloat(style.marginBottom) || 0) +
        (parseFloat(style.paddingTop) || 0) +
        (parseFloat(style.paddingBottom) || 0)
    );
}

function resizeTableWrapper() {
    const windowHeight = window.innerHeight;
    const header = document.querySelector('header');
    const title = document.querySelector('.page-title');
    const pagination = document.querySelector('.pagination-container');
    const tableWrapper = document.querySelector('.table-wrapper');
    if (!tableWrapper) return;
    const headerSpace = getTotalVerticalSpace(header);
    const titleSpace = getTotalVerticalSpace(title);
    const paginationSpace = getTotalVerticalSpace(pagination);
    const extraSpacing = 0;
    const availableHeight = windowHeight - headerSpace - titleSpace - paginationSpace - extraSpacing;
    tableWrapper.style.height = `${availableHeight}px`;
}
