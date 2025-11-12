import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function toMoney(amount: number) {
    return  new Intl.NumberFormat('en', {
        style: 'currency',
        currency: 'MAD',
        trailingZeroDisplay: 'stripIfInteger'
    }).format(amount)
}

/**
 * Converts a number into a readable, abbreviated string (e.g., 1000 -> 1k).
 * Handles numbers up to Trillions (T).
 * * @param num The number to format.
 * @returns The abbreviated and formatted string.
 */
export function formatNumber(num: number | string): string {
    // Ensure the input is treated as a number
    const number = typeof num === 'string' ? parseFloat(num.replace(/,/g, '')) : num;

    if (isNaN(number)) {
        return String(num); // Return original value if it's not a valid number
    }

    // Define magnitude tiers
    const units = [
        { value: 1e12, symbol: "T" },
        { value: 1e9, symbol: "B" }, // Billion
        { value: 1e6, symbol: "M" }, // Million
        { value: 1e3, symbol: "K" }, // Thousand
    ];

    // Check against the tiers
    for (let i = 0; i < units.length; i++) {
        const unit = units[i];
        if (number >= unit.value) {
            // Format to 1 decimal place if the result is not a whole number
            return (number / unit.value).toFixed(1).replace(/\.0$/, '') + unit.symbol;
        }
    }

    // Handle small numbers or numbers less than 1000 without abbreviation
    // Use Intl.NumberFormat for standard formatting (e.g., 999 or 123.45)
    return new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 }).format(number);
}
