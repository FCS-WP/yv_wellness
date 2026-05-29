import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
  PanelBody,
  RangeControl,
  ToggleControl,
  SelectControl,
} from '@wordpress/components';
import { PanelColorSettings } from '@wordpress/block-editor';

const SORT_OPTIONS = [
  { label: __('Latest First', 'ai-zippy-child'), value: 'date_DESC' },
  { label: __('Oldest First', 'ai-zippy-child'), value: 'date_ASC' },
  { label: __('A → Z', 'ai-zippy-child'), value: 'title_ASC' },
  { label: __('Z → A', 'ai-zippy-child'), value: 'title_DESC' },
];

export default function Edit({ attributes, setAttributes }) {
  const {
    columns = 3,
    postsPerPage = 6,
    excerptLines = 3,
    showFilter = true,
    showPostCount = true,
    orderBy = 'date',
    order = 'DESC',
    bgColor = '#FFFAF3',
    cardBgColor = '#ffffff',
  } = attributes;

  const blockProps = useBlockProps({
    className: 'bpg-editor',
    style: {
      '--bpg-columns': columns,
      '--bpg-bg': bgColor,
      '--bpg-card-bg': cardBgColor,
      '--bpg-excerpt-lines': excerptLines,
    },
  });

  const currentSort = `${orderBy}_${order}`;

  const onChangeSort = (value) => {
    const [nextOrderBy, nextOrder] = String(value || 'date_DESC').split('_');
    setAttributes({
      orderBy: nextOrderBy || 'date',
      order: nextOrder || 'DESC',
    });
  };

  // Build a static preview grid based on the configured number of cards.
  const previewCount = Math.max(1, Math.min(postsPerPage, columns * 2));
  const previewCards = Array.from({ length: previewCount });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Grid Settings', 'ai-zippy-child')} initialOpen={true}>
          <RangeControl
            label={__('Columns', 'ai-zippy-child')}
            value={columns}
            onChange={(value) => setAttributes({ columns: value })}
            min={1}
            max={4}
          />
          <RangeControl
            label={__('Posts Per Page', 'ai-zippy-child')}
            value={postsPerPage}
            onChange={(value) => setAttributes({ postsPerPage: value })}
            min={1}
            max={12}
          />
          <RangeControl
            label={__('Excerpt Lines', 'ai-zippy-child')}
            value={excerptLines}
            onChange={(value) => setAttributes({ excerptLines: value })}
            min={2}
            max={6}
          />
        </PanelBody>

        <PanelBody title={__('Display Options', 'ai-zippy-child')} initialOpen={false}>
          <ToggleControl
            label={__('Show Filter', 'ai-zippy-child')}
            checked={!!showFilter}
            onChange={(value) => setAttributes({ showFilter: value })}
          />
          <ToggleControl
            label={__('Show Post Count', 'ai-zippy-child')}
            checked={!!showPostCount}
            onChange={(value) => setAttributes({ showPostCount: value })}
          />
          <SelectControl
            label={__('Default Sort Order', 'ai-zippy-child')}
            value={currentSort}
            options={SORT_OPTIONS}
            onChange={onChangeSort}
          />
        </PanelBody>

        <PanelColorSettings
          title={__('Color Settings', 'ai-zippy-child')}
          initialOpen={false}
          colorSettings={[
            {
              value: bgColor,
              onChange: (value) => setAttributes({ bgColor: value || '#FFFAF3' }),
              label: __('Background Color', 'ai-zippy-child'),
            },
            {
              value: cardBgColor,
              onChange: (value) => setAttributes({ cardBgColor: value || '#ffffff' }),
              label: __('Card Background', 'ai-zippy-child'),
            },
          ]}
        />
      </InspectorControls>

      <div {...blockProps}>
        {(showPostCount || showFilter) && (
          <div className="bpg-editor__toolbar">
            {showPostCount ? (
              <div className="bpg-editor__count">
                {__('Showing posts (preview)', 'ai-zippy-child')}
              </div>
            ) : (
              <div className="bpg-editor__count" aria-hidden="true" />
            )}
            {showFilter && (
              <select
                className="bpg-editor__select"
                value={currentSort}
                onChange={(event) => onChangeSort(event.target.value)}
              >
                {SORT_OPTIONS.map((option) => (
                  <option key={option.value} value={option.value}>
                    {option.label}
                  </option>
                ))}
              </select>
            )}
          </div>
        )}

        <div className="bpg-editor__grid">
          {previewCards.map((_unused, index) => (
            <article className="bpg-editor__card" key={index}>
              <div className="bpg-editor__image" />
              <div className="bpg-editor__body">
                <div className="bpg-editor__line bpg-editor__line--title" />
                <div className="bpg-editor__line" />
                <div className="bpg-editor__line" />
                <div className="bpg-editor__line bpg-editor__line--short" />
              </div>
            </article>
          ))}
        </div>
      </div>
    </>
  );
}
