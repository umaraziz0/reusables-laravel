import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vite-plus/test';

import FieldFeedback from './FieldFeedback.vue';

describe('FieldFeedback', () => {
  it('renders nothing without a description, error, or slot', () => {
    const wrapper = mount(FieldFeedback);

    expect(wrapper.find('p').exists()).toBe(false);
  });

  it('renders the description with its id', () => {
    const wrapper = mount(FieldFeedback, {
      props: {
        description: 'Shown only once.',
        descriptionId: 'token-description',
      },
    });

    const feedback = wrapper.get('p');
    expect(feedback.text()).toBe('Shown only once.');
    expect(feedback.attributes('id')).toBe('token-description');
    expect(feedback.attributes('role')).toBeUndefined();
  });

  it('shows the error as an alert instead of the description', () => {
    const wrapper = mount(FieldFeedback, {
      props: {
        description: 'Shown only once.',
        descriptionId: 'token-description',
        error: 'Token is required.',
        errorId: 'token-error',
      },
    });

    const feedback = wrapper.get('p');
    expect(feedback.text()).toBe('Token is required.');
    expect(feedback.attributes('id')).toBe('token-error');
    expect(feedback.attributes('role')).toBe('alert');
  });

  it('renders slot content in place of the description', () => {
    const wrapper = mount(FieldFeedback, {
      props: { description: 'Plain text', descriptionId: 'email-description' },
      slots: { default: '<strong>Rich text</strong>' },
    });

    const feedback = wrapper.get('p');
    expect(feedback.html()).toContain('<strong>Rich text</strong>');
    expect(feedback.text()).not.toContain('Plain text');
  });

  it('renders slot content without a description', () => {
    const wrapper = mount(FieldFeedback, {
      slots: { default: 'Slot only' },
    });

    expect(wrapper.get('p').text()).toBe('Slot only');
  });
});
