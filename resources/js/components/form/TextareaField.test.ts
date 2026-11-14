import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vite-plus/test';
import { h, ref } from 'vue';

import TextareaField from './TextareaField.vue';

/**
 * Mounts the field inside a parent that owns the v-model, so the parent can
 * change the value the way a form reset would.
 */
function mountWithVModel(initialValue: string) {
  const content = ref(initialValue);

  const wrapper = mount(() =>
    h(TextareaField, {
      label: 'Bio',
      name: 'bio',
      maxlength: 10,
      modelValue: content.value,
      'onUpdate:modelValue': (value: string | number | undefined) => {
        content.value = String(value ?? '');
      },
    }),
  );

  return { wrapper, content };
}

describe('TextareaField', () => {
  it('links the label to the textarea using the name when no id is given', () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Bio', name: 'bio' },
    });

    expect(wrapper.get('textarea').attributes('id')).toBe('bio');
    expect(wrapper.get('label').attributes('for')).toBe('bio');
  });

  it('prefers an explicit id over the name', () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Bio', name: 'bio', id: 'profile-bio' },
    });

    expect(wrapper.get('textarea').attributes('id')).toBe('profile-bio');
    expect(wrapper.get('label').attributes('for')).toBe('profile-bio');
  });

  it('visually hides the label when hideLabel is set', () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Notes', name: 'notes', hideLabel: true },
    });

    expect(wrapper.get('label').classes()).toContain('sr-only');
  });

  it('marks the textarea required with a decorative asterisk', () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Bio', name: 'bio', required: true },
    });

    expect(wrapper.get('textarea').attributes()).toHaveProperty('required');
    expect(wrapper.get('label span').attributes('aria-hidden')).toBe('true');
  });

  it('passes extra attributes to the textarea rather than the wrapper', () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Bio', name: 'bio' },
      attrs: { placeholder: 'Tell us about yourself', rows: '6' },
    });

    expect(wrapper.get('textarea').attributes('placeholder')).toBe(
      'Tell us about yourself',
    );
    expect(wrapper.get('textarea').attributes('rows')).toBe('6');
    expect(wrapper.attributes('placeholder')).toBeUndefined();
  });

  it('describes the textarea with the description and any extra ids', () => {
    const wrapper = mount(TextareaField, {
      props: {
        label: 'Bio',
        name: 'bio',
        description: 'Shown on your public profile.',
        ariaDescribedby: 'bio-hint',
      },
    });

    expect(wrapper.get('textarea').attributes('aria-describedby')).toBe(
      'bio-hint bio-description',
    );
    expect(wrapper.get('#bio-description').text()).toBe(
      'Shown on your public profile.',
    );
  });

  it('describes the textarea with the error and marks it invalid', () => {
    const wrapper = mount(TextareaField, {
      props: {
        label: 'Bio',
        name: 'bio',
        description: 'Shown on your public profile.',
        error: 'Bio is required.',
      },
    });

    const textarea = wrapper.get('textarea');
    expect(textarea.attributes('aria-describedby')).toBe('bio-error');
    expect(textarea.attributes('aria-invalid')).toBe('true');
    expect(wrapper.get('#bio-error').text()).toBe('Bio is required.');
    expect(wrapper.attributes('data-invalid')).toBe('true');
  });

  it('omits aria-describedby when there is nothing to describe', () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Bio', name: 'bio' },
    });

    expect(
      wrapper.get('textarea').attributes('aria-describedby'),
    ).toBeUndefined();
    expect(wrapper.attributes('data-invalid')).toBeUndefined();
  });

  it('disables the textarea and marks the field disabled', () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Bio', name: 'bio', disabled: true },
    });

    expect(wrapper.get('textarea').attributes()).toHaveProperty('disabled');
    expect(wrapper.attributes('data-disabled')).toBe('true');
  });

  it('emits typed values through v-model', async () => {
    const wrapper = mount(TextareaField, {
      props: { label: 'Bio', name: 'bio', modelValue: '' },
    });

    await wrapper.get('textarea').setValue('Hello');

    expect(wrapper.emitted('update:modelValue')).toEqual([['Hello']]);
  });

  describe('character counter', () => {
    const counter = '[data-slot="field-counter"]';

    it('is not shown without a maxlength', () => {
      const wrapper = mount(TextareaField, {
        props: { label: 'Bio', name: 'bio' },
      });

      expect(wrapper.find(counter).exists()).toBe(false);
      expect(wrapper.get('textarea').attributes('maxlength')).toBeUndefined();
    });

    it('limits the textarea and counts what has been typed', async () => {
      const wrapper = mount(TextareaField, {
        props: { label: 'Bio', name: 'bio', maxlength: 10 },
      });

      expect(wrapper.get('textarea').attributes('maxlength')).toBe('10');
      expect(wrapper.get(counter).text()).toBe('0 / 10');

      await wrapper.get('textarea').setValue('Hello');

      expect(wrapper.get(counter).text()).toBe('5 / 10');
    });

    it('counts a value bound through v-model as it is typed', async () => {
      const { wrapper, content } = mountWithVModel('Hi');

      expect(wrapper.get(counter).text()).toBe('2 / 10');

      await wrapper.get('textarea').setValue('Hi there');

      expect(content.value).toBe('Hi there');
      expect(wrapper.get(counter).text()).toBe('8 / 10');
    });

    it('starts from the default value', () => {
      const wrapper = mount(TextareaField, {
        props: {
          label: 'Bio',
          name: 'bio',
          maxlength: 10,
          defaultValue: 'abc',
        },
      });

      expect(wrapper.get(counter).text()).toBe('3 / 10');
    });

    it('keeps the counter alongside an error', () => {
      const wrapper = mount(TextareaField, {
        props: {
          label: 'Bio',
          name: 'bio',
          maxlength: 10,
          error: 'Bio is required.',
        },
      });

      expect(wrapper.get('#bio-error').text()).toBe('Bio is required.');
      expect(wrapper.get(counter).text()).toBe('0 / 10');
    });

    it('announces the remaining characters once typing pauses', async () => {
      vi.useFakeTimers();

      try {
        const wrapper = mount(TextareaField, {
          props: { label: 'Bio', name: 'bio', maxlength: 10 },
        });

        const status = wrapper.get('[role="status"]');

        expect(status.text()).toBe('');

        await wrapper.get('textarea').setValue('Hel');
        await wrapper.get('textarea').setValue('Hello');

        expect(status.text()).toBe('');

        await vi.runAllTimersAsync();

        expect(status.text()).toBe('5 characters remaining');
      } finally {
        vi.useRealTimers();
      }
    });

    it('announces a single remaining character in the singular', async () => {
      vi.useFakeTimers();

      try {
        const wrapper = mount(TextareaField, {
          props: { label: 'Bio', name: 'bio', maxlength: 10 },
        });

        await wrapper.get('textarea').setValue('123456789');
        await vi.runAllTimersAsync();

        expect(wrapper.get('[role="status"]').text()).toBe(
          '1 character remaining',
        );
      } finally {
        vi.useRealTimers();
      }
    });

    it('does not announce when the value changes without typing', async () => {
      vi.useFakeTimers();

      try {
        const { wrapper, content } = mountWithVModel('Draft');

        content.value = '';
        await vi.runAllTimersAsync();

        expect(wrapper.get(counter).text()).toBe('0 / 10');
        expect(wrapper.get('[role="status"]').text()).toBe('');
      } finally {
        vi.useRealTimers();
      }
    });
  });
});
