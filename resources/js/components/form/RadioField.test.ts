import { enableAutoUnmount, mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vite-plus/test';
import { h } from 'vue';

import RadioField from './RadioField.vue';

enableAutoUnmount(afterEach);

const planOptions = [
  { value: 'free', label: 'Free' },
  { value: 'pro', label: 'Pro' },
];

/**
 * Mounts the field inside a real form, because the radio group only renders
 * its native input when it sits in a form.
 */
function mountInForm(props: {
  defaultValue?: string;
  required?: boolean;
}): HTMLFormElement {
  const wrapper = mount(
    () =>
      h('form', [
        h(RadioField, {
          label: 'Plan',
          name: 'plan',
          options: planOptions,
          ...props,
        }),
      ]),
    { attachTo: document.body },
  );

  return wrapper.get('form').element;
}

describe('RadioField', () => {
  it('renders the label as a legend and links each option label to its radio', () => {
    const wrapper = mount(RadioField, {
      props: { label: 'Plan', name: 'plan', options: planOptions },
    });

    expect(wrapper.get('legend').text()).toBe('Plan');

    const radios = wrapper.findAll('[role="radio"]');
    expect(radios.map((radio) => radio.attributes('id'))).toEqual([
      'plan-free',
      'plan-pro',
    ]);
    expect(
      wrapper.findAll('label').map((label) => label.attributes('for')),
    ).toEqual(['plan-free', 'plan-pro']);
  });

  it('selects an option and emits its value when its label is clicked', async () => {
    const wrapper = mount(RadioField, {
      props: { label: 'Plan', name: 'plan', options: planOptions },
      attachTo: document.body,
    });

    await wrapper.get('label[for="plan-pro"]').trigger('click');

    expect(wrapper.get('#plan-pro').attributes('aria-checked')).toBe('true');
    expect(wrapper.emitted('update:modelValue')).toEqual([['pro']]);
  });

  it('prefers an explicit id over the name', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Plan',
        name: 'plan',
        id: 'billing-plan',
        options: planOptions,
      },
    });

    expect(wrapper.get('[role="radiogroup"]').attributes('id')).toBe(
      'billing-plan',
    );
    expect(wrapper.get('[role="radio"]').attributes('id')).toBe(
      'billing-plan-free',
    );
  });

  it('visually hides the legend when hideLabel is set', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Plan',
        name: 'plan',
        hideLabel: true,
        options: planOptions,
      },
    });

    expect(wrapper.get('legend').classes()).toContain('sr-only');
  });

  it('describes an option radio with the description under its label', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Delivery',
        name: 'delivery',
        options: [
          {
            value: 'express',
            label: 'Express',
            description: 'Next business day',
          },
          { value: 'pickup', label: 'Pickup' },
        ],
      },
    });

    expect(
      wrapper.get('#delivery-express').attributes('aria-describedby'),
    ).toBe('delivery-express-description');
    expect(
      wrapper.get('#delivery-pickup').attributes('aria-describedby'),
    ).toBeUndefined();
    expect(wrapper.get('#delivery-express-description').text()).toBe(
      'Next business day',
    );
  });

  it('lays the options out in a row when horizontal', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Plan',
        name: 'plan',
        orientation: 'horizontal',
        options: planOptions,
      },
    });

    expect(
      wrapper.get('[role="radiogroup"]').attributes('aria-orientation'),
    ).toBe('horizontal');
  });

  it('renders the default slot instead of the options', () => {
    const wrapper = mount(RadioField, {
      props: { label: 'Plan', name: 'plan', options: planOptions },
      slots: { default: '<p data-test="custom-options">Custom</p>' },
    });

    expect(wrapper.find('[data-test="custom-options"]').exists()).toBe(true);
    expect(wrapper.find('[role="radio"]').exists()).toBe(false);
  });

  it('passes extra attributes to the radio group rather than the wrapper', () => {
    const wrapper = mount(RadioField, {
      props: { label: 'Plan', name: 'plan', options: planOptions },
      attrs: { 'data-test': 'plan-group' },
    });

    expect(wrapper.get('[role="radiogroup"]').attributes('data-test')).toBe(
      'plan-group',
    );
    expect(wrapper.get('fieldset').attributes('data-test')).toBeUndefined();
  });

  it('disables every radio and dims the legend when disabled', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Plan',
        name: 'plan',
        disabled: true,
        options: planOptions,
      },
    });

    for (const radio of wrapper.findAll('[role="radio"]')) {
      expect(radio.attributes()).toHaveProperty('disabled');
    }

    expect(wrapper.get('fieldset').attributes('data-disabled')).toBe('true');
    expect(wrapper.get('legend').classes()).toContain('opacity-50');
  });

  it('disables only the options marked disabled', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Plan',
        name: 'plan',
        options: [
          ...planOptions,
          { value: 'enterprise', label: 'Enterprise', disabled: true },
        ],
      },
    });

    expect(wrapper.get('#plan-enterprise').attributes()).toHaveProperty(
      'disabled',
    );
    expect(wrapper.get('#plan-pro').attributes()).not.toHaveProperty(
      'disabled',
    );
  });

  it('describes the group with the description and any extra ids', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Plan',
        name: 'plan',
        description: 'You can change this later.',
        ariaDescribedby: 'plan-hint',
        options: planOptions,
      },
    });

    expect(
      wrapper.get('[role="radiogroup"]').attributes('aria-describedby'),
    ).toBe('plan-hint plan-description');
    expect(wrapper.get('#plan-description').text()).toBe(
      'You can change this later.',
    );
  });

  it('describes the group with the error and marks it invalid', () => {
    const wrapper = mount(RadioField, {
      props: {
        label: 'Plan',
        name: 'plan',
        description: 'You can change this later.',
        error: 'The plan field is required.',
        options: planOptions,
      },
    });

    const group = wrapper.get('[role="radiogroup"]');
    expect(group.attributes('aria-describedby')).toBe('plan-error');
    expect(group.attributes('aria-invalid')).toBe('true');
    expect(wrapper.get('#plan-error').text()).toBe(
      'The plan field is required.',
    );
    expect(wrapper.get('fieldset').attributes('data-invalid')).toBe('true');
    expect(wrapper.find('#plan-description').exists()).toBe(false);
  });

  it('omits aria-describedby when there is nothing to describe', () => {
    const wrapper = mount(RadioField, {
      props: { label: 'Plan', name: 'plan', options: planOptions },
    });

    expect(
      wrapper.get('[role="radiogroup"]').attributes('aria-describedby'),
    ).toBeUndefined();
    expect(wrapper.get('fieldset').attributes('data-invalid')).toBeUndefined();
  });

  describe('form submission', () => {
    it('requires a choice with a decorative asterisk', () => {
      const form = mountInForm({ required: true });

      expect(
        form
          .querySelector('[role="radiogroup"]')
          ?.getAttribute('aria-required'),
      ).toBe('true');
      expect(form.checkValidity()).toBe(false);
      expect(
        form.querySelector('legend span')?.getAttribute('aria-hidden'),
      ).toBe('true');
    });

    it('submits the chosen value under its name', () => {
      const form = mountInForm({ defaultValue: 'pro' });

      expect(new FormData(form).getAll('plan')).toEqual(['pro']);
    });

    it('submits an empty string when no option is chosen', () => {
      const form = mountInForm({});

      expect(new FormData(form).getAll('plan')).toEqual(['']);
    });
  });
});
