import { useBlockProps, InspectorControls, MediaUpload, PanelColorSettings, RichText } from '@wordpress/block-editor';
import { PanelBody, RangeControl, Button, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
  const { items, columns, gap, overlayColor, overlayTextColor, borderRadius } = attributes;

  const blockProps = useBlockProps({ className: 'eg' });

  const addItem = (media) => {
    const newItem = {
      id: media.id,
      url: media.url,
      alt: media.alt || '',
      caption: '',
      span: 'normal',
    };
    setAttributes({ items: [...items, newItem] });
  };

  const updateItem = (index, key, value) => {
    const updated = [...items];
    updated[index] = { ...updated[index], [key]: value };
    setAttributes({ items: updated });
  };

  const removeItem = (index) => {
    setAttributes({ items: items.filter((_, i) => i !== index) });
  };

  const moveItem = (index, direction) => {
    const updated = [...items];
    const target = index + direction;
    if (target < 0 || target >= updated.length) return;
    [updated[index], updated[target]] = [updated[target], updated[index]];
    setAttributes({ items: updated });
  };

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Grid Settings', 'ai-zippy-child')}>
          <RangeControl
            label={__('Columns', 'ai-zippy-child')}
            value={columns}
            onChange={(val) => setAttributes({ columns: val })}
            min={2}
            max={4}
          />
          <RangeControl
            label={__('Gap (px)', 'ai-zippy-child')}
            value={gap}
            onChange={(val) => setAttributes({ gap: val })}
            min={4}
            max={20}
          />
          <RangeControl
            label={__('Border Radius (px)', 'ai-zippy-child')}
            value={borderRadius}
            onChange={(val) => setAttributes({ borderRadius: val })}
            min={0}
            max={24}
          />
        </PanelBody>
        <PanelColorSettings
          title={__('Overlay Colors', 'ai-zippy-child')}
          colorSettings={[
            {
              value: overlayColor,
              onChange: (val) => setAttributes({ overlayColor: val || 'rgba(0,0,0,0.7)' }),
              label: __('Overlay Background', 'ai-zippy-child'),
            },
            {
              value: overlayTextColor,
              onChange: (val) => setAttributes({ overlayTextColor: val || '#ffffff' }),
              label: __('Overlay Text Color', 'ai-zippy-child'),
            },
          ]}
        />
      </InspectorControls>

      <div {...blockProps}>
        <div
          className="eg__grid"
          style={{
            '--eg-columns': columns,
            '--eg-gap': `${gap}px`,
            '--eg-radius': `${borderRadius}px`,
          }}
        >
          {items.map((item, index) => (
            <div key={item.id || index} className={`eg__item ${item.span === 'large' ? 'eg__item--large' : ''}`}>
              <div className="eg__image-wrap">
                <img src={item.url} alt={item.alt} className="eg__image" />
                <button
                  type="button"
                  className="eg__remove-btn"
                  onClick={() => removeItem(index)}
                  aria-label={__('Remove image', 'ai-zippy-child')}
                  title={__('Remove image', 'ai-zippy-child')}
                >
                  ×
                </button>
                <div className="eg__overlay" style={{ backgroundColor: overlayColor }}>
                  <RichText
                    tagName="span"
                    className="eg__caption"
                    style={{ color: overlayTextColor }}
                    value={item.caption}
                    onChange={(value) => {
                      const updated = [...items];
                      updated[index] = { ...updated[index], caption: value };
                      setAttributes({ items: updated });
                    }}
                    placeholder={__('Enter caption…', 'ai-zippy-child')}
                    allowedFormats={['core/bold', 'core/italic']}
                  />
                </div>
              </div>
              <div className="eg__item-controls">
                <SelectControl
                  value={item.span}
                  options={[
                    { label: __('Normal', 'ai-zippy-child'), value: 'normal' },
                    { label: __('Large (2×2)', 'ai-zippy-child'), value: 'large' },
                  ]}
                  onChange={(val) => updateItem(index, 'span', val)}
                />
                <div className="eg__item-actions">
                  <Button isSmall icon="arrow-up-alt" onClick={() => moveItem(index, -1)} disabled={index === 0} />
                  <Button isSmall icon="arrow-down-alt" onClick={() => moveItem(index, 1)} disabled={index === items.length - 1} />
                  <Button isSmall isDestructive icon="trash" onClick={() => removeItem(index)} />
                </div>
              </div>
            </div>
          ))}
        </div>

        <div className="eg__add-wrap">
          <MediaUpload
            onSelect={addItem}
            allowedTypes={['image']}
            render={({ open }) => (
              <Button variant="secondary" onClick={open} icon="plus-alt">
                {__('Add Image', 'ai-zippy-child')}
              </Button>
            )}
          />
        </div>
      </div>
    </>
  );
}
