import type { InputTypeHTMLAttribute } from 'vue';

import { flushPromises, mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vite-plus/test';

import InputField from './InputField.vue';

const MISSPELLED_EMAIL = 'jane@gmial.com';

const CORRECTED_EMAIL = 'jane@gmail.com';

interface EmailFieldProps {
  modelValue?: string;
  description?: string;
  error?: string;
  type?: InputTypeHTMLAttribute;
}

function mountEmailField(props: EmailFieldProps = {}) {
  return mount(InputField, {
    props: { label: 'Email', name: 'email', type: 'email', ...props },
  });
}

async function blurWithValue(
  wrapper: ReturnType<typeof mountEmailField>,
  value: string,
): Promise<void> {
  const input = wrapper.get('input');
  await input.setValue(value);
  await input.trigger('blur');
}

describe('InputField', () => {
  it('links the label to the input using the name when no id is given', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Full name', name: 'full_name' },
    });

    expect(wrapper.get('input').attributes('id')).toBe('full_name');
    expect(wrapper.get('label').attributes('for')).toBe('full_name');
  });

  it('prefers an explicit id over the name', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Full name', name: 'full_name', id: 'profile-name' },
    });

    expect(wrapper.get('input').attributes('id')).toBe('profile-name');
    expect(wrapper.get('label').attributes('for')).toBe('profile-name');
  });

  it('visually hides the label when hideLabel is set', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Notes', name: 'notes', hideLabel: true },
    });

    expect(wrapper.get('label').classes()).toContain('sr-only');
  });

  it('marks the input required with a decorative asterisk', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Full name', name: 'full_name', required: true },
    });

    expect(wrapper.get('input').attributes()).toHaveProperty('required');
    expect(wrapper.get('label span').attributes('aria-hidden')).toBe('true');
  });

  it('passes extra attributes to the input rather than the wrapper', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Name', name: 'name' },
      attrs: { placeholder: 'Jane Doe', autocomplete: 'name' },
    });

    expect(wrapper.get('input').attributes('placeholder')).toBe('Jane Doe');
    expect(wrapper.get('input').attributes('autocomplete')).toBe('name');
    expect(wrapper.attributes('placeholder')).toBeUndefined();
  });

  it('describes the input with the description and any extra ids', () => {
    const wrapper = mount(InputField, {
      props: {
        label: 'API token',
        name: 'api_token',
        description: 'Shown only once.',
        ariaDescribedby: 'token-hint',
      },
    });

    expect(wrapper.get('input').attributes('aria-describedby')).toBe(
      'token-hint api_token-description',
    );
    expect(wrapper.get('#api_token-description').text()).toBe(
      'Shown only once.',
    );
  });

  it('describes the input with the error and marks it invalid', () => {
    const wrapper = mount(InputField, {
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

  it('omits aria-describedby when there is nothing to describe', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Name', name: 'name' },
    });

    expect(wrapper.get('input').attributes('aria-describedby')).toBeUndefined();
  });

  it('emits typed values through v-model', async () => {
    const wrapper = mount(InputField, {
      props: { label: 'Name', name: 'name', modelValue: '' },
    });

    await wrapper.get('input').setValue('Jane');

    expect(wrapper.emitted('update:modelValue')).toEqual([['Jane']]);
  });

  describe('email suggestion', () => {
    it('suggests a correction when a misspelled email loses focus', async () => {
      const wrapper = mountEmailField();

      await blurWithValue(wrapper, MISSPELLED_EMAIL);

      await vi.waitFor(() => {
        expect(wrapper.get('[role="status"]').text()).toBe(
          `Did you mean ${CORRECTED_EMAIL}?`,
        );
      });
      expect(wrapper.get('input').attributes('aria-describedby')).toBe(
        'email-description',
      );
    });

    it('emits the suggestion through v-model when clicked', async () => {
      const wrapper = mountEmailField({ modelValue: '' });
      await blurWithValue(wrapper, MISSPELLED_EMAIL);
      await vi.waitFor(() => wrapper.get('[role="status"] button'));

      await wrapper.get('[role="status"] button').trigger('click');

      expect(wrapper.emitted('update:modelValue')?.at(-1)).toEqual([
        CORRECTED_EMAIL,
      ]);
      expect(wrapper.find('[role="status"]').exists()).toBe(false);
    });

    it('applies the suggestion without v-model bound', async () => {
      const wrapper = mountEmailField();
      await blurWithValue(wrapper, MISSPELLED_EMAIL);
      await vi.waitFor(() => wrapper.get('[role="status"] button'));

      await wrapper.get('[role="status"] button').trigger('click');

      expect(wrapper.get('input').element.value).toBe(CORRECTED_EMAIL);
    });

    it('restores the description once the user types again', async () => {
      const wrapper = mountEmailField({ description: 'We never share it.' });
      await blurWithValue(wrapper, MISSPELLED_EMAIL);
      await vi.waitFor(() => wrapper.get('[role="status"]'));

      await wrapper.get('input').setValue('jane@');

      expect(wrapper.find('[role="status"]').exists()).toBe(false);
      expect(wrapper.get('#email-description').text()).toBe(
        'We never share it.',
      );
    });

    it('shows the error instead of the suggestion', async () => {
      const wrapper = mountEmailField({ error: 'Email is taken.' });

      await blurWithValue(wrapper, MISSPELLED_EMAIL);

      await vi.waitFor(() => {
        expect(wrapper.get('[role="alert"]').text()).toBe('Email is taken.');
      });
      expect(wrapper.find('[role="status"]').exists()).toBe(false);
    });

    it('does not suggest anything for a correctly spelled email', async () => {
      const wrapper = mountEmailField();

      await blurWithValue(wrapper, CORRECTED_EMAIL);
      await import('@zootools/email-spell-checker');
      await flushPromises();

      expect(wrapper.find('[role="status"]').exists()).toBe(false);
    });

    it('does not suggest anything for non-email inputs', async () => {
      const wrapper = mountEmailField({ type: 'text' });

      await blurWithValue(wrapper, MISSPELLED_EMAIL);
      await import('@zootools/email-spell-checker');
      await flushPromises();

      expect(wrapper.find('[role="status"]').exists()).toBe(false);
    });
  });
});
