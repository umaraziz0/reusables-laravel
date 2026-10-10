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

import SearchSelectField from './SearchSelectField.vue';

interface CountryFieldProps {
  label?: string;
  name?: string;
  options?: { value: string | number; label: string }[];
  placeholder?: string;
  description?: string;
  error?: string;
  emptyText?: string;
  hideLabel?: boolean;
  ariaDescribedby?: string;
}

const COUNTRY_OPTIONS = [
  { value: 'au', label: 'Australia' },
  { value: 'at', label: 'Austria' },
  { value: 'br', label: 'Brazil' },
  { value: 'ca', label: 'Canada' },
];

const ALL_COUNTRY_LABELS = ['Australia', 'Austria', 'Brazil', 'Canada'];

/**
 * Mounts a country field attached to the document, since Reka portals the
 * open list to the body.
 */
function mountSearchSelectField(
  props: CountryFieldProps = {},
  attrs: Record<string, string> = {},
): VueWrapper {
  return mount(SearchSelectField, {
    props: {
      label: 'Country',
      name: 'country',
      options: COUNTRY_OPTIONS,
      ...props,
    },
    attrs,
    attachTo: document.body,
  });
}

/**
 * Reka only renders the hidden input when the field sits in a form.
 */
async function mountInForm(fieldAttributes: string): Promise<VueWrapper> {
  const wrapper = mount({
    components: { SearchSelectField },
    setup: () => ({ options: COUNTRY_OPTIONS }),
    template: `
      <form>
        <SearchSelectField
          label="Country"
          name="country"
          :options="options"
          ${fieldAttributes}
        />
      </form>
    `,
  });

  await flushPromises();

  return wrapper;
}

/**
 * Returns the labels of the rendered options in the open list.
 */
function renderedLabels(): string[] {
  return [...document.body.querySelectorAll('[role="option"]')].map(
    (option) => option.textContent?.trim() ?? '',
  );
}

/**
 * Opens the list and returns the labels of the rendered options.
 */
async function openOptions(wrapper: VueWrapper): Promise<string[]> {
  await wrapper.get('button').trigger('click');
  await flushPromises();

  return renderedLabels();
}

/**
 * Types into the search input of the open list and returns the labels left.
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

  return renderedLabels();
}

/**
 * Clicks the rendered option with the given label.
 */
async function choose(label: string): Promise<void> {
  const option = [
    ...document.body.querySelectorAll<HTMLElement>('[role="option"]'),
  ].find((element) => element.textContent?.trim() === label);

  if (!option) {
    throw new Error(`No rendered option is labelled "${label}".`);
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
});

afterEach(() => {
  vi.restoreAllMocks();
});

describe('SearchSelectField', () => {
  it('links the label to the trigger and shows the placeholder', () => {
    const wrapper = mountSearchSelectField({
      placeholder: 'Choose a country…',
    });

    const trigger = wrapper.get('button');
    expect(trigger.attributes('id')).toBe('country');
    expect(trigger.attributes('aria-label')).toBeUndefined();
    expect(trigger.text()).toBe('Choose a country…');
    expect(wrapper.get('label').attributes('for')).toBe('country');
  });

  it('visually hides the label when hideLabel is set', () => {
    const wrapper = mountSearchSelectField({ hideLabel: true });

    expect(wrapper.get('label').classes()).toContain('sr-only');
  });

  it('passes extra attributes to the trigger rather than the wrapper', () => {
    const wrapper = mountSearchSelectField(
      {},
      { 'data-testid': 'country-select' },
    );

    expect(wrapper.get('button').attributes('data-testid')).toBe(
      'country-select',
    );
    expect(wrapper.attributes('data-testid')).toBeUndefined();
  });

  it('describes the trigger with the description and any extra ids', () => {
    const wrapper = mountSearchSelectField({
      description: 'Where you pay tax.',
      ariaDescribedby: 'country-hint',
    });

    expect(wrapper.get('button').attributes('aria-describedby')).toBe(
      'country-hint country-description',
    );
    expect(wrapper.get('#country-description').text()).toBe(
      'Where you pay tax.',
    );
  });

  it('describes the trigger with the error and marks it invalid', () => {
    const wrapper = mountSearchSelectField({
      description: 'Where you pay tax.',
      error: 'Choose a country.',
    });

    const trigger = wrapper.get('button');
    expect(trigger.attributes('aria-describedby')).toBe('country-error');
    expect(trigger.attributes('aria-invalid')).toBe('true');
    expect(wrapper.get('#country-error').text()).toBe('Choose a country.');
  });

  it('emits the chosen option through v-model and shows its label', async () => {
    const wrapper = mountSearchSelectField();

    await openOptions(wrapper);
    await choose('Brazil');

    expect(wrapper.emitted('update:modelValue')).toEqual([['br']]);
    expect(wrapper.get('button').text()).toBe('Brazil');
  });

  it('narrows the options to case-insensitive substring matches', async () => {
    const wrapper = mountSearchSelectField();

    await openOptions(wrapper);

    expect(await search('RA')).toEqual(['Australia', 'Brazil']);
  });

  it('shows the empty text when nothing matches', async () => {
    const wrapper = mountSearchSelectField({
      emptyText: 'No countries found.',
    });

    await openOptions(wrapper);

    expect(await search('zzz')).toEqual([]);
    expect(document.body.textContent).toContain('No countries found.');
  });

  it('lists every option again when reopened after a selection', async () => {
    const wrapper = mountSearchSelectField();

    await openOptions(wrapper);
    await search('RA');
    await choose('Brazil');

    expect(await openOptions(wrapper)).toEqual(ALL_COUNTRY_LABELS);
  });

  it('lists every option again when reopened after dismissing a search', async () => {
    const wrapper = mountSearchSelectField();

    await openOptions(wrapper);
    await search('RA');

    document.body
      .querySelector('input[role="combobox"]')
      ?.dispatchEvent(
        new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }),
      );
    await flushPromises();

    expect(renderedLabels()).toEqual([]);
    expect(await openOptions(wrapper)).toEqual(ALL_COUNTRY_LABELS);
  });

  it('mounts only a window of rows from a large list', async () => {
    const options = Array.from({ length: 10_000 }, (_, index) => ({
      value: index,
      label: `Item ${index}`,
    }));

    const wrapper = mountSearchSelectField({
      label: 'Item',
      name: 'item',
      options,
    });

    await openOptions(wrapper);
    const rendered = document.body.querySelectorAll('[role="option"]');

    // A 288px viewport of 32px rows shows 9, plus Reka's overscan.
    expect(rendered.length).toBeGreaterThanOrEqual(9);
    expect(rendered.length).toBeLessThan(50);
    expect(rendered[0].getAttribute('aria-setsize')).toBe('10000');
  });

  it('submits the value with the form under its name and marks it required', async () => {
    const wrapper = await mountInForm('model-value="ca" required');

    expect(
      new FormData(wrapper.get<HTMLFormElement>('form').element).get('country'),
    ).toBe('ca');
    expect(
      wrapper.get<HTMLInputElement>('input[name="country"]').element.required,
    ).toBe(true);
    expect(wrapper.get('label span').attributes('aria-hidden')).toBe('true');
  });

  it('shows and submits the default value when uncontrolled', async () => {
    const wrapper = await mountInForm('default-value="ca"');

    expect(wrapper.get('button').text()).toBe('Canada');
    expect(
      new FormData(wrapper.get<HTMLFormElement>('form').element).get('country'),
    ).toBe('ca');
  });

  it('disables both the trigger and the submitted value', async () => {
    const wrapper = await mountInForm('model-value="ca" disabled');

    expect(wrapper.get('button').attributes()).toHaveProperty('disabled');
    expect(
      wrapper.get<HTMLInputElement>('input[name="country"]').element.disabled,
    ).toBe(true);
  });
});
