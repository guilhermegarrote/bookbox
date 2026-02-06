/**
 * Validates whether a string is a valid ISBN-10 or ISBN-13.
 *
 * @param {string} isbn - The ISBN string to validate
 * @returns {boolean} True if valid ISBN-10 or ISBN-13, false otherwise
 */
export function isValidISBN(isbn) {
    isbn = isbn.replace(/[-\s]/g, '');

    if (!/^\d{9}[\dX]$/.test(isbn) && !/^\d{13}$/.test(isbn)) return false;

    // ISBN-10 validation
    if (isbn.length === 10) {
        let sum = 0;
        for (let i = 0; i < 9; i++) sum += (i + 1) * parseInt(isbn[i], 10);
        const check = isbn[9].toUpperCase();
        sum += check === 'X' ? 10 * 10 : 10 * parseInt(check, 10);
        return sum % 11 === 0;
    }

    // ISBN-13 validation
    if (isbn.length === 13) {
        let sum = 0;
        for (let i = 0; i < 12; i++) {
            const digit = parseInt(isbn[i], 10);
            sum += i % 2 === 0 ? digit : digit * 3;
        }
        const checkDigit = (10 - (sum % 10)) % 10;
        return checkDigit === parseInt(isbn[12], 10);
    }

    return false;
}
