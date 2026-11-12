import countryCodes from '../../data/country-codes.json';

// --- Types --- //

export interface Country {
  name: string;
  /** ISO 3166-1 alpha-2, e.g. `ID`. */
  code: string;
  /** International prefix including the plus, e.g. `+62`. */
  dialCode: string;
  flag: string;
}

// --- Constants --- //

/**
 * Regional indicator symbol for the letter A. A flag emoji is the pair of
 * indicators for the country's two-letter code.
 */
const REGIONAL_INDICATOR_A = 0x1f1e6;

/**
 * The country shown for a dial code shared by several, when only the dial
 * code is known (initial value, old input, or a value set by the parent).
 */
export const PRIMARY_COUNTRY_BY_DIAL_CODE = new Map([
  ['+1', 'US'],
  ['+44', 'GB'],
  ['+47', 'NO'],
  ['+61', 'AU'],
  ['+262', 'RE'],
  ['+358', 'FI'],
  ['+500', 'FK'],
  ['+590', 'GP'],
  ['+672', 'NF'],
]);

/**
 * Shared with the backend's `App\Data\CountryCodes`, which reads the same
 * JSON file.
 */
export const COUNTRIES: readonly Country[] = countryCodes.map((country) => ({
  name: country.name,
  code: country.code,
  dialCode: country.dial_code,
  flag: flagFor(country.code),
}));

// --- Methods --- //

function flagFor(code: string): string {
  return String.fromCodePoint(
    ...code
      .toUpperCase()
      .split('')
      .map((letter) => REGIONAL_INDICATOR_A + letter.charCodeAt(0) - 65),
  );
}
