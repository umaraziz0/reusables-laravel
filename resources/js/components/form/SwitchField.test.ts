import { enableAutoUnmount, mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vite-plus/test';
import { h } from 'vue';

import SwitchField from './SwitchField.vue';

enableAutoUnmount(afterEach);

/**
 * Mounts the field inside a real form, because the switch only renders its
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
        h(SwitchField, { label: 'Dark mode', name: 'dark_mode', ...props }),
      ]),
    { attachTo: document.body },
  );

  return wrapper.get('form').element;
}

describe('SwitchField', () => {
  it('links the label to the switch using the name when no id is given', () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Dark mode', name: 'dark_mode' },
    });

    expect(wrapper.get('[role="switch"]').attributes('id')).toBe('dark_mode');
    expect(wrapper.get('label').attributes('for')).toBe('dark_mode');
  });

  it('turns the switch on and emits a boolean when the label is clicked', async () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Dark mode', name: 'dark_mode' },
      attachTo: document.body,
    });

    await wrapper.get('label').trigger('click');

    expect(wrapper.get('[role="switch"]').attributes('aria-checked')).toBe(
      'true',
    );
    expect(wrapper.emitted('update:modelValue')).toEqual([[true]]);
  });

  it('starts from the default value when no v-model is bound', () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Dark mode', name: 'dark_mode', defaultValue: true },
    });

    expect(wrapper.get('[role="switch"]').attributes('aria-checked')).toBe(
      'true',
    );
  });

  it('prefers an explicit id over the name', () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Dark mode', name: 'dark_mode', id: 'settings-dark' },
    });

    expect(wrapper.get('[role="switch"]').attributes('id')).toBe(
      'settings-dark',
    );
    expect(wrapper.get('label').attributes('for')).toBe('settings-dark');
  });

  it('visually hides the label when hideLabel is set', () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Enable row', name: 'row', hideLabel: true },
    });

    expect(wrapper.get('label').classes()).toContain('sr-only');
  });

  it('requires the switch with a decorative asterisk', () => {
    const form = mountInForm({ defaultValue: false, required: true });

    expect(
      form.querySelector<HTMLInputElement>('input[type="checkbox"]')?.required,
    ).toBe(true);
    expect(form.checkValidity()).toBe(false);
    expect(form.querySelector('label span')?.getAttribute('aria-hidden')).toBe(
      'true',
    );
  });

  it('passes extra attributes and switchClass to the switch rather than the wrapper', () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Dark mode', name: 'dark_mode', switchClass: 'ms-auto' },
      attrs: { 'data-test': 'dark-mode-switch' },
    });

    const switchElement = wrapper.get('[role="switch"]');
    expect(switchElement.attributes('data-test')).toBe('dark-mode-switch');
    expect(switchElement.classes()).toContain('ms-auto');
    expect(wrapper.attributes('data-test')).toBeUndefined();
  });

  it('disables the switch and marks the field disabled', () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Dark mode', name: 'dark_mode', disabled: true },
    });

    expect(wrapper.get('[role="switch"]').attributes()).toHaveProperty(
      'disabled',
    );
    expect(wrapper.attributes('data-disabled')).toBe('true');
  });

  it('describes the switch with the description and any extra ids', () => {
    const wrapper = mount(SwitchField, {
      props: {
        label: 'Dark mode',
        name: 'dark_mode',
        description: 'Easier on the eyes at night.',
        ariaDescribedby: 'dark-mode-hint',
      },
    });

    expect(wrapper.get('[role="switch"]').attributes('aria-describedby')).toBe(
      'dark-mode-hint dark_mode-description',
    );
    expect(wrapper.get('#dark_mode-description').text()).toBe(
      'Easier on the eyes at night.',
    );
  });

  it('describes the switch with the error and marks it invalid', () => {
    const wrapper = mount(SwitchField, {
      props: {
        label: 'Marketing emails',
        name: 'marketing',
        description: 'Sent monthly.',
        error: 'This setting cannot be changed right now.',
      },
    });

    const switchElement = wrapper.get('[role="switch"]');
    expect(switchElement.attributes('aria-describedby')).toBe(
      'marketing-error',
    );
    expect(switchElement.attributes('aria-invalid')).toBe('true');
    expect(wrapper.get('#marketing-error').text()).toBe(
      'This setting cannot be changed right now.',
    );
    expect(wrapper.attributes('data-invalid')).toBe('true');
  });

  it('omits aria-describedby when there is nothing to describe', () => {
    const wrapper = mount(SwitchField, {
      props: { label: 'Dark mode', name: 'dark_mode' },
    });

    expect(
      wrapper.get('[role="switch"]').attributes('aria-describedby'),
    ).toBeUndefined();
    expect(wrapper.attributes('data-invalid')).toBeUndefined();
  });

  describe('form submission', () => {
    it('submits "1" under its name when on', () => {
      const form = mountInForm({ defaultValue: true });

      expect(new FormData(form).get('dark_mode')).toBe('1');
    });

    it('submits nothing when off', () => {
      const form = mountInForm({ defaultValue: false });

      expect(new FormData(form).has('dark_mode')).toBe(false);
    });

    it('submits a custom value when one is given', () => {
      const form = mountInForm({ defaultValue: true, value: 'yes' });

      expect(new FormData(form).get('dark_mode')).toBe('yes');
    });
  });
});
