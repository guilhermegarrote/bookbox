import '../../css/components/sidebar.css';

export function setupSidebarPage() {
    document.addEventListener('DOMContentLoaded', () => {
        resizeSidebarWrapper();
    });

    window.addEventListener('resize', resizeSidebarWrapper);
}

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

function resizeSidebarWrapper() {
    const windowHeight = window.innerHeight;
    const header = document.querySelector('header');
    const title = document.querySelector('.page-title');
    const pagination = document.querySelector('.pagination-container');
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    if (!sidebarWrapper) return;
    const headerSpace = getTotalVerticalSpace(header);
    const titleSpace = getTotalVerticalSpace(title);
    const paginationSpace = getTotalVerticalSpace(pagination);
    const extraSpacing = 0;
    const availableHeight = windowHeight - headerSpace - titleSpace - paginationSpace - extraSpacing;
    sidebarWrapper.style.height = `${availableHeight}px`;
}