import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
  const { phone, email, formTitle, submitText, recipientEmail } = attributes;
  const blockProps = useBlockProps({ className: 'cuf' });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Contact Information', 'ai-zippy-child')}>
          <TextControl
            label={__('Phone Number', 'ai-zippy-child')}
            value={phone}
            onChange={(val) => setAttributes({ phone: val })}
          />
          <TextControl
            label={__('Email Address', 'ai-zippy-child')}
            value={email}
            onChange={(val) => setAttributes({ email: val })}
          />
        </PanelBody>
        <PanelBody title={__('Form Settings', 'ai-zippy-child')}>
          <TextControl
            label={__('Form Title', 'ai-zippy-child')}
            value={formTitle}
            onChange={(val) => setAttributes({ formTitle: val })}
          />
          <TextControl
            label={__('Submit Button Text', 'ai-zippy-child')}
            value={submitText}
            onChange={(val) => setAttributes({ submitText: val })}
          />
          <TextControl
            label={__('Recipient Email', 'ai-zippy-child')}
            value={recipientEmail}
            onChange={(val) => setAttributes({ recipientEmail: val })}
            help={__('Form submissions will be sent to this email', 'ai-zippy-child')}
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="cuf__grid">
          <div className="cuf__info">
            <div className="cuf__card">
              <span className="cuf__card-icon">📞</span>
              <h4 className="cuf__card-title">Phone:</h4>
              <p className="cuf__card-text">{phone}</p>
            </div>
            <div className="cuf__card">
              <span className="cuf__card-icon">✉️</span>
              <h4 className="cuf__card-title">Email:</h4>
              <p className="cuf__card-text">{email}</p>
            </div>
          </div>
          <div className="cuf__form-wrap">
            <h3 className="cuf__form-title">{formTitle}</h3>
            <div className="cuf__form-preview">
              <p className="cuf__form-placeholder">Contact form preview (functional on frontend)</p>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
