import { enableAutoUnmount, mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vite-plus/test';
import { h } from 'vue';

import CheckboxField from './CheckboxField.vue';

enableAutoUnmount(afterEach);

/**
 * Mounts the field inside a real form, because the checkbox only renders its
 * native input when it sits in a form.
 */
function mountInForm(props: {
  defaultValue: boolean;
  value?: string;
  required?: boolean;
}): HTMLFormElement {
  const wrapper = mount(
    () =>
      h('form', [
        h(CheckboxField, { label: 'Newsletter', name: 'newsletter', ...props }),
      ]),
    { attachTo: document.body },
  );

  return wrapper.get('form').element;
}

describe('CheckboxField', () => {
  it('links the label to the checkbox using the name when no id is given', () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Newsletter', name: 'newsletter' },
    });

    expect(wrapper.get('[role="checkbox"]').attributes('id')).toBe(
      'newsletter',
    );
    expect(wrapper.get('label').attributes('for')).toBe('newsletter');
  });

  it('checks the box and emits a boolean when the label is clicked', async () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Newsletter', name: 'newsletter' },
      attachTo: document.body,
    });

    await wrapper.get('label').trigger('click');

    expect(wrapper.get('[role="checkbox"]').attributes('aria-checked')).toBe(
      'true',
    );
    expect(wrapper.emitted('update:modelValue')).toEqual([[true]]);
  });

  it('starts from the default value when no v-model is bound', () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Newsletter', name: 'newsletter', defaultValue: true },
    });

    expect(wrapper.get('[role="checkbox"]').attributes('aria-checked')).toBe(
      'true',
    );
  });

  it('prefers an explicit id over the name', () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Newsletter', name: 'newsletter', id: 'signup-news' },
    });

    expect(wrapper.get('[role="checkbox"]').attributes('id')).toBe(
      'signup-news',
    );
    expect(wrapper.get('label').attributes('for')).toBe('signup-news');
  });

  it('visually hides the label when hideLabel is set', () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Select row', name: 'row', hideLabel: true },
    });

    expect(wrapper.get('label').classes()).toContain('sr-only');
  });

  it('requires the checkbox with a decorative asterisk', () => {
    const form = mountInForm({ defaultValue: false, required: true });

    expect(
      form.querySelector('[role="checkbox"]')?.getAttribute('aria-required'),
    ).toBe('true');
    expect(
      form.querySelector<HTMLInputElement>('input[type="checkbox"]')?.required,
    ).toBe(true);
    expect(form.checkValidity()).toBe(false);
    expect(form.querySelector('label span')?.getAttribute('aria-hidden')).toBe(
      'true',
    );
  });

  it('passes extra attributes to the checkbox rather than the wrapper', () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Newsletter', name: 'newsletter' },
      attrs: { 'data-test': 'newsletter-checkbox' },
    });

    expect(wrapper.get('[role="checkbox"]').attributes('data-test')).toBe(
      'newsletter-checkbox',
    );
    expect(wrapper.attributes('data-test')).toBeUndefined();
  });

  it('disables the checkbox and marks the field disabled', () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Newsletter', name: 'newsletter', disabled: true },
    });

    expect(wrapper.get('[role="checkbox"]').attributes()).toHaveProperty(
      'disabled',
    );
    expect(wrapper.attributes('data-disabled')).toBe('true');
  });

  it('describes the checkbox with the description and any extra ids', () => {
    const wrapper = mount(CheckboxField, {
      props: {
        label: 'Newsletter',
        name: 'newsletter',
        description: 'At most one email per month.',
        ariaDescribedby: 'newsletter-hint',
      },
    });

    expect(
      wrapper.get('[role="checkbox"]').attributes('aria-describedby'),
    ).toBe('newsletter-hint newsletter-description');
    expect(wrapper.get('#newsletter-description').text()).toBe(
      'At most one email per month.',
    );
  });

  it('describes the checkbox with the error and marks it invalid', () => {
    const wrapper = mount(CheckboxField, {
      props: {
        label: 'I accept the terms',
        name: 'terms',
        description: 'Read them first.',
        error: 'You must accept the terms.',
      },
    });

    const checkbox = wrapper.get('[role="checkbox"]');
    expect(checkbox.attributes('aria-describedby')).toBe('terms-error');
    expect(checkbox.attributes('aria-invalid')).toBe('true');
    expect(wrapper.get('#terms-error').text()).toBe(
      'You must accept the terms.',
    );
    expect(wrapper.attributes('data-invalid')).toBe('true');
  });

  it('omits aria-describedby when there is nothing to describe', () => {
    const wrapper = mount(CheckboxField, {
      props: { label: 'Newsletter', name: 'newsletter' },
    });

    expect(
      wrapper.get('[role="checkbox"]').attributes('aria-describedby'),
    ).toBeUndefined();
    expect(wrapper.attributes('data-invalid')).toBeUndefined();
  });

  describe('form submission', () => {
    it('submits "1" under its name when checked', () => {
      const form = mountInForm({ defaultValue: true });

      expect(new FormData(form).get('newsletter')).toBe('1');
    });

    it('submits nothing when unchecked', () => {
      const form = mountInForm({ defaultValue: false });

      expect(new FormData(form).has('newsletter')).toBe(false);
    });

    it('submits a custom value when one is given', () => {
      const form = mountInForm({ defaultValue: true, value: 'yes' });

      expect(new FormData(form).get('newsletter')).toBe('yes');
    });
  });
});
