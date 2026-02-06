import '@css/components/sidebar.css';

/**
 * Sets up sidebar resizing behavior.
 */
export function setupSidebarPage() {
    document.addEventListener('DOMContentLoaded', () => {
        resizeSidebarWrapper();
    });

    window.addEventListener('resize', resizeSidebarWrapper);
}

/**
 * Calculates total vertical space used by an element.
 *
 * @param {HTMLElement|null} element
 * @returns {number}
 */
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

/**
 * Resizes the sidebar wrapper height based on available screen space.
 */
function resizeSidebarWrapper() {
    const windowHeight = window.innerHeight;
    const header = document.querySelector('header');
    const title = document.querySelector('.page-title');
    const pagination = document.querySelector('.pagination-container');
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    const panel = document.querySelector('.panel');

    if (!sidebarWrapper) return;

    const headerSpace = getTotalVerticalSpace(header);
    const titleSpace = getTotalVerticalSpace(title);
    const paginationSpace = getTotalVerticalSpace(pagination);
    const extraSpacing = panel ? parseFloat(getComputedStyle(panel).marginRight) || 0 : 0;

    const availableHeight =
        windowHeight - headerSpace - titleSpace - paginationSpace - extraSpacing;

    sidebarWrapper.style.height = `${availableHeight}px`;
}
