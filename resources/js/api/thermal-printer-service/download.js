import { route } from 'ziggy-js';

/**
 * Downloads the thermal printer service installer ZIP file.
 *
 * @async
 * @function downloadPrinterServiceInstaller
 * @returns {Promise<void>}
 * @throws {Error} If the download fails.
 */
export async function downloadThermalPrinterService() {
    const response = await fetch(route('download.thermal-printer-service'), {
        method: 'GET',
        credentials: 'include'
    });

    if (response.ok) {
        const blob = await response.blob();

        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'thermal-printer-service.zip';
        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    }

    return response;
}
