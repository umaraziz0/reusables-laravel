import { flushPromises, mount } from '@vue/test-utils';
import { describe, expect, it } from 'vite-plus/test';

import SelectField from './SelectField.vue';

const PLAN_OPTIONS = [
  { value: 'free', label: 'Free' },
  { value: 'pro', label: 'Pro' },
  { value: 'enterprise', label: 'Enterprise', disabled: true },
];

interface PlanFieldProps {
  modelValue?: string;
  placeholder?: string;
  required?: boolean;
}

/**
 * Mounts a plan select and waits for the closed content to register its
 * options, which happens one render after mount.
 */
async function mountSelectField(props: PlanFieldProps = {}) {
  const wrapper = mount(SelectField, {
    props: { label: 'Plan', name: 'plan', options: PLAN_OPTIONS, ...props },
  });

  await flushPromises();

  return wrapper;
}

/**
 * Reka only renders the hidden native select when the trigger sits in a form.
 */
async function mountInForm(fieldAttributes: string) {
  const wrapper = mount({
    components: { SelectField },
    setup: () => ({ options: PLAN_OPTIONS }),
    template: `
      <form>
        <SelectField
          label="Plan"
          name="plan"
          :options="options"
          model-value="pro"
          ${fieldAttributes}
        />
      </form>
    `,
  });

  await flushPromises();

  return wrapper;
}

describe('SelectField', () => {
  it('links the label to the trigger using the name when no id is given', () => {
    const wrapper = mount(SelectField, {
      props: { label: 'Plan', name: 'plan', options: PLAN_OPTIONS },
    });

    expect(wrapper.get('[role="combobox"]').attributes('id')).toBe('plan');
    expect(wrapper.get('label').attributes('for')).toBe('plan');
  });

  it('prefers an explicit id over the name', () => {
    const wrapper = mount(SelectField, {
      props: {
        label: 'Plan',
        name: 'plan',
        options: PLAN_OPTIONS,
        id: 'billing-plan',
      },
    });

    expect(wrapper.get('[role="combobox"]').attributes('id')).toBe(
      'billing-plan',
    );
    expect(wrapper.get('label').attributes('for')).toBe('billing-plan');
  });

  it('visually hides the label when hideLabel is set', () => {
    const wrapper = mount(SelectField, {
      props: {
        label: 'Plan',
        name: 'plan',
        options: PLAN_OPTIONS,
        hideLabel: true,
      },
    });

    expect(wrapper.get('label').classes()).toContain('sr-only');
  });

  it('marks the trigger required with a decorative asterisk', () => {
    const wrapper = mount(SelectField, {
      props: {
        label: 'Plan',
        name: 'plan',
        options: PLAN_OPTIONS,
        required: true,
      },
    });

    expect(wrapper.get('[role="combobox"]').attributes('aria-required')).toBe(
      'true',
    );
    expect(wrapper.get('label span').attributes('aria-hidden')).toBe('true');
  });

  it('passes extra attributes to the trigger rather than the wrapper', () => {
    const wrapper = mount(SelectField, {
      props: { label: 'Plan', name: 'plan', options: PLAN_OPTIONS },
      attrs: { 'data-testid': 'plan-select' },
    });

    expect(wrapper.get('[role="combobox"]').attributes('data-testid')).toBe(
      'plan-select',
    );
    expect(wrapper.attributes('data-testid')).toBeUndefined();
  });

  it('describes the trigger with the description and any extra ids', () => {
    const wrapper = mount(SelectField, {
      props: {
        label: 'Plan',
        name: 'plan',
        options: PLAN_OPTIONS,
        description: 'You can change it later.',
        ariaDescribedby: 'plan-hint',
      },
    });

    expect(
      wrapper.get('[role="combobox"]').attributes('aria-describedby'),
    ).toBe('plan-hint plan-description');
    expect(wrapper.get('#plan-description').text()).toBe(
      'You can change it later.',
    );
  });

  it('describes the trigger with the error and marks it invalid', () => {
    const wrapper = mount(SelectField, {
      props: {
        label: 'Plan',
        name: 'plan',
        options: PLAN_OPTIONS,
        description: 'You can change it later.',
        error: 'Choose a plan.',
      },
    });

    const trigger = wrapper.get('[role="combobox"]');
    expect(trigger.attributes('aria-describedby')).toBe('plan-error');
    expect(trigger.attributes('aria-invalid')).toBe('true');
    expect(wrapper.get('#plan-error').text()).toBe('Choose a plan.');
  });

  it('shows the placeholder until a value is chosen', () => {
    const wrapper = mount(SelectField, {
      props: {
        label: 'Plan',
        name: 'plan',
        options: PLAN_OPTIONS,
        placeholder: 'Choose a plan…',
      },
    });

    const trigger = wrapper.get('[role="combobox"]');
    expect(trigger.text()).toBe('Choose a plan…');
    expect(trigger.attributes()).toHaveProperty('data-placeholder');
  });

  it('shows the label of the selected option', async () => {
    const wrapper = await mountSelectField({
      placeholder: 'Choose a plan…',
      modelValue: 'pro',
    });

    expect(wrapper.get('[role="combobox"]').text()).toBe('Pro');
  });

  it('emits the chosen option through v-model', async () => {
    const wrapper = await mountSelectField({ modelValue: 'free' });

    await wrapper.get('[role="combobox"]').trigger('keydown', { key: 'Enter' });
    await flushPromises();

    const proOption = [
      ...document.body.querySelectorAll<HTMLElement>('[role="option"]'),
    ].find((option) => option.textContent?.trim() === 'Pro');

    expect(proOption).toBeDefined();

    proOption?.dispatchEvent(
      new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }),
    );
    await flushPromises();

    expect(wrapper.emitted('update:modelValue')).toEqual([['pro']]);
  });

  it('submits the value with the form under its name and marks it required', async () => {
    const wrapper = await mountInForm('required');

    expect(wrapper.get<HTMLSelectElement>('select').element.required).toBe(
      true,
    );
    expect(
      new FormData(wrapper.get<HTMLFormElement>('form').element).get('plan'),
    ).toBe('pro');
  });

  it('disables both the trigger and the submitted value', async () => {
    const wrapper = await mountInForm('disabled');

    expect(wrapper.get('[role="combobox"]').attributes()).toHaveProperty(
      'disabled',
    );
    // happy-dom's FormData still includes disabled selects, so assert the flag
    // browsers use to leave the control out of submission.
    expect(wrapper.get<HTMLSelectElement>('select').element.disabled).toBe(
      true,
    );
  });

  it('omits aria-describedby when there is nothing to describe', () => {
    const wrapper = mount(SelectField, {
      props: { label: 'Plan', name: 'plan', options: PLAN_OPTIONS },
    });

    expect(
      wrapper.get('[role="combobox"]').attributes('aria-describedby'),
    ).toBeUndefined();
  });
});
