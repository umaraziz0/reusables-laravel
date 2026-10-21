import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vite-plus/test';

import InputGroupField from './InputGroupField.vue';

describe('InputGroupField', () => {
  it('links the label to the input using the name when no id is given', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Website', name: 'website' },
    });

    expect(wrapper.get('input').attributes('id')).toBe('website');
    expect(wrapper.get('label').attributes('for')).toBe('website');
  });

  it('prefers an explicit id over the name', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Website', name: 'website', id: 'profile-website' },
    });

    expect(wrapper.get('input').attributes('id')).toBe('profile-website');
    expect(wrapper.get('label').attributes('for')).toBe('profile-website');
  });

  it('visually hides the label when hideLabel is set', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Search', name: 'search', hideLabel: true },
    });

    expect(wrapper.get('label').classes()).toContain('sr-only');
  });

  it('marks the input required with a decorative asterisk', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Email', name: 'email', required: true },
    });

    expect(wrapper.get('input').attributes()).toHaveProperty('required');
    expect(wrapper.get('label span').attributes('aria-hidden')).toBe('true');
  });

  it('passes extra attributes to the input rather than the wrapper', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Website', name: 'website' },
      attrs: { placeholder: 'example', autocomplete: 'off' },
    });

    expect(wrapper.get('input').attributes('placeholder')).toBe('example');
    expect(wrapper.get('input').attributes('autocomplete')).toBe('off');
    expect(wrapper.attributes('placeholder')).toBeUndefined();
  });

  it('describes the input with the description and any extra ids', () => {
    const wrapper = mount(InputGroupField, {
      props: {
        label: 'API key',
        name: 'api_key',
        description: 'Shown only once.',
        ariaDescribedby: 'key-hint',
      },
    });

    expect(wrapper.get('input').attributes('aria-describedby')).toBe(
      'key-hint api_key-description',
    );
    expect(wrapper.get('#api_key-description').text()).toBe('Shown only once.');
  });

  it('describes the input with the error and marks it invalid', () => {
    const wrapper = mount(InputGroupField, {
      props: {
        label: 'Username',
        name: 'username',
        description: '3–30 characters.',
        error: 'Username must be at least 3 characters.',
      },
    });

    const input = wrapper.get('input');
    expect(input.attributes('aria-describedby')).toBe('username-error');
    expect(input.attributes('aria-invalid')).toBe('true');
    expect(wrapper.get('#username-error').text()).toBe(
      'Username must be at least 3 characters.',
    );
  });

  it('emits typed values through v-model', async () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Search', name: 'search', modelValue: '' },
    });

    await wrapper.get('input').setValue('invoices');

    expect(wrapper.emitted('update:modelValue')).toEqual([['invoices']]);
  });

  it('renders each slot in an addon aligned to its side', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Website', name: 'website' },
      slots: {
        start: 'https://',
        end: '.com',
        'block-start': 'Header',
        'block-end': 'Footer',
      },
    });

    const addonTextByAlign = Object.fromEntries(
      wrapper
        .findAll('[data-slot="input-group-addon"]')
        .map((addon) => [addon.attributes('data-align'), addon.text()]),
    );

    expect(addonTextByAlign).toEqual({
      'inline-start': 'https://',
      'inline-end': '.com',
      'block-start': 'Header',
      'block-end': 'Footer',
    });
  });

  it('renders no addons when no slots are given', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Website', name: 'website' },
    });

    expect(wrapper.find('[data-slot="input-group-addon"]').exists()).toBe(
      false,
    );
  });

  it('disables the input and marks the group disabled', () => {
    const wrapper = mount(InputGroupField, {
      props: { label: 'Account ID', name: 'account_id', disabled: true },
    });

    expect(wrapper.get('input').attributes()).toHaveProperty('disabled');
    expect(
      wrapper.get('[data-slot="input-group"]').attributes('data-disabled'),
    ).toBe('true');
  });
});
