import {
  enableAutoUnmount,
  flushPromises,
  mount,
  type VueWrapper,
} from '@vue/test-utils';
import {
  afterEach,
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vite-plus/test';

import { type Country } from '@/lib/countries';

import PhoneNumberField from './PhoneNumberField.vue';

interface PhoneFieldProps {
  label?: string;
  name?: string;
  dialCode?: string;
  defaultDialCode?: string;
  countries?: Country[];
  description?: string;
  error?: string;
  emptyText?: string;
  ariaDescribedby?: string;
}

const COUNTRIES: Country[] = [
  { name: 'Canada', code: 'CA', dialCode: '+1', flag: '🇨🇦' },
  { name: 'Guernsey', code: 'GG', dialCode: '+44', flag: '🇬🇬' },
  { name: 'Indonesia', code: 'ID', dialCode: '+62', flag: '🇮🇩' },
  { name: 'Reunion', code: 'RE', dialCode: '+262', flag: '🇷🇪' },
  { name: 'United Kingdom', code: 'GB', dialCode: '+44', flag: '🇬🇧' },
  { name: 'United States', code: 'US', dialCode: '+1', flag: '🇺🇸' },
];

/**
 * Mounts a phone field attached to the document, since Reka portals the open
 * list to the body.
 */
function mountPhoneNumberField(
  props: PhoneFieldProps = {},
  attrs: Record<string, string> = {},
): VueWrapper {
  return mount(PhoneNumberField, {
    props: {
      label: 'Phone',
      name: 'phone',
      countries: COUNTRIES,
      ...props,
    },
    attrs,
    attachTo: document.body,
  });
}

async function mountInForm(fieldAttributes: string): Promise<VueWrapper> {
  const wrapper = mount({
    components: { PhoneNumberField },
    setup: () => ({ countries: COUNTRIES }),
    template: `
      <form>
        <PhoneNumberField
          label="Phone"
          name="phone"
          :countries="countries"
          ${fieldAttributes}
        />
      </form>
    `,
  });

  await flushPromises();

  return wrapper;
}

function submitted(wrapper: VueWrapper): FormData {
  return new FormData(wrapper.get<HTMLFormElement>('form').element);
}

function trigger(wrapper: VueWrapper) {
  return wrapper.get('[data-slot="phone-number-field-trigger"]');
}

/**
 * Returns the country names of the rendered options in the open list.
 */
function renderedNames(): string[] {
  return [...document.body.querySelectorAll('[role="option"]')].map(
    (option) => option.querySelector('.truncate')?.textContent?.trim() ?? '',
  );
}

async function openOptions(wrapper: VueWrapper): Promise<string[]> {
  await trigger(wrapper).trigger('click');
  await flushPromises();

  return renderedNames();
}

/**
 * Types into the search input of the open list and returns the names left.
 */
async function search(term: string): Promise<string[]> {
  const input = document.body.querySelector<HTMLInputElement>(
    'input[role="combobox"]',
  );

  if (!input) {
    throw new Error('The search input is not rendered; open the list first.');
  }

  input.value = term;
  input.dispatchEvent(new Event('input', { bubbles: true }));
  await flushPromises();

  return renderedNames();
}

async function choose(name: string): Promise<void> {
  const option = [
    ...document.body.querySelectorAll<HTMLElement>('[role="option"]'),
  ].find((element) => element.textContent?.includes(name));

  if (!option) {
    throw new Error(`No rendered option is named "${name}".`);
  }

  option.click();
  await flushPromises();
}

enableAutoUnmount(afterEach);

/**
 * happy-dom has no layout, so every element measures 0px tall and the
 * virtualizer renders no rows. Give the viewport and rows real heights.
 */
beforeEach(() => {
  vi.spyOn(HTMLElement.prototype, 'offsetHeight', 'get').mockImplementation(
    function (this: HTMLElement) {
      return this.dataset.slot === 'combobox-viewport' ? 288 : 32;
    },
  );
  vi.spyOn(HTMLElement.prototype, 'clientHeight', 'get').mockImplementation(
    function (this: HTMLElement) {
      return this.dataset.slot === 'combobox-viewport' ? 288 : 0;
    },
  );
});

afterEach(() => {
  vi.restoreAllMocks();
});

describe('PhoneNumberField', () => {
  it('links the label to the number input and defaults to Indonesia', () => {
    const wrapper = mountPhoneNumberField();

    const input = wrapper.get('input[type="tel"]');
    expect(input.attributes('id')).toBe('phone');
    expect(wrapper.get('label').attributes('for')).toBe('phone');
    expect(trigger(wrapper).text()).toContain('🇮🇩');
    expect(trigger(wrapper).text()).toContain('+62');
    expect(trigger(wrapper).attributes('aria-label')).toBe(
      'Country code: Indonesia +62',
    );
  });

  it('passes extra attributes to the number input rather than the wrapper', () => {
    const wrapper = mountPhoneNumberField(
      {},
      { placeholder: '812 3456 7890', autocomplete: 'tel' },
    );

    const input = wrapper.get('input[type="tel"]');
    expect(input.attributes('placeholder')).toBe('812 3456 7890');
    expect(input.attributes('autocomplete')).toBe('tel');
    expect(wrapper.attributes('placeholder')).toBeUndefined();
  });

  it('describes both controls with the error and marks them invalid', () => {
    const wrapper = mountPhoneNumberField({
      description: 'We text the code here.',
      error: 'Enter a valid phone number.',
    });

    for (const control of [
      wrapper.get('input[type="tel"]'),
      trigger(wrapper),
    ]) {
      expect(control.attributes('aria-describedby')).toBe('phone-error');
      expect(control.attributes('aria-invalid')).toBe('true');
    }

    expect(wrapper.get('#phone-error').text()).toBe(
      'Enter a valid phone number.',
    );
  });

  it('shows the primary country for a dial code several countries share', () => {
    const wrapper = mountPhoneNumberField({ dialCode: '+44' });

    expect(trigger(wrapper).attributes('aria-label')).toBe(
      'Country code: United Kingdom +44',
    );
  });

  it('emits the dial code and keeps the flag of the country the user picked', async () => {
    const wrapper = mountPhoneNumberField();

    await openOptions(wrapper);
    await choose('Canada');

    expect(wrapper.emitted('update:dialCode')).toEqual([['+1']]);
    expect(trigger(wrapper).text()).toContain('🇨🇦');
    expect(trigger(wrapper).attributes('aria-label')).toBe(
      'Country code: Canada +1',
    );
  });

  it.each([
    ['name', 'indo', ['Indonesia']],
    ['ISO code', 'gb', ['United Kingdom']],
    ['dial code', '+44', ['Guernsey', 'United Kingdom']],
    ['dial code without the plus', '62', ['Indonesia', 'Reunion']],
  ])('finds countries by %s', async (_, term, expected) => {
    const wrapper = mountPhoneNumberField();

    await openOptions(wrapper);

    expect(await search(term)).toEqual(expected);
  });

  it('shows the empty text when nothing matches', async () => {
    const wrapper = mountPhoneNumberField({ emptyText: 'Nothing here.' });

    await openOptions(wrapper);

    expect(await search('zzz')).toEqual([]);
    expect(document.body.textContent).toContain('Nothing here.');
  });

  it('submits the number and dial code and marks the number required', async () => {
    const wrapper = await mountInForm(
      'model-value="5550100" dial-code="+1" required',
    );

    expect(submitted(wrapper).get('phone')).toBe('5550100');
    expect(submitted(wrapper).get('phone_dial_code')).toBe('+1');
    expect(
      wrapper.get<HTMLInputElement>('input[type="tel"]').element.required,
    ).toBe(true);
  });

  it('submits the default dial code under a custom name when uncontrolled', async () => {
    const wrapper = await mountInForm(
      'default-dial-code="+44" dial-code-name="country_code"',
    );

    expect(submitted(wrapper).get('country_code')).toBe('+44');
    expect(submitted(wrapper).has('phone_dial_code')).toBe(false);
  });

  it('disables the trigger, the number and the submitted dial code', async () => {
    const wrapper = await mountInForm('model-value="812" disabled');

    expect(trigger(wrapper).attributes()).toHaveProperty('disabled');
    expect(
      wrapper.get<HTMLInputElement>('input[type="tel"]').element.disabled,
    ).toBe(true);
    expect(submitted(wrapper).has('phone')).toBe(false);
    expect(submitted(wrapper).has('phone_dial_code')).toBe(false);
  });
});
