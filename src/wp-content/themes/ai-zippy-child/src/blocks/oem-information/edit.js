import {
	useBlockProps,
	RichText,
	InspectorControls,
	PanelColorSettings,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const {
		heading,
		headingColor,
		body,
		bodyColor,
		buttonText,
		buttonUrl,
		buttonTextColor,
		buttonBgColor,
		backgroundColor,
	} = attributes;

	const blockProps = useBlockProps({
		className: 'oem-info',
		style: { backgroundColor },
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Button Settings', 'ai-zippy-child')}>
					<TextControl
						label={__('Button Link URL', 'ai-zippy-child')}
						value={buttonUrl}
						onChange={(val) => setAttributes({ buttonUrl: val })}
						help={__('Use tel:NUMBER for phone links', 'ai-zippy-child')}
					/>
				</PanelBody>
				<PanelColorSettings
					title={__('Color Settings', 'ai-zippy-child')}
					colorSettings={[
						{
							value: headingColor,
							onChange: (val) => setAttributes({ headingColor: val }),
							label: __('Heading Color', 'ai-zippy-child'),
						},
						{
							value: bodyColor,
							onChange: (val) => setAttributes({ bodyColor: val }),
							label: __('Body Text Color', 'ai-zippy-child'),
						},
						{
							value: buttonTextColor,
							onChange: (val) => setAttributes({ buttonTextColor: val }),
							label: __('Button Text Color', 'ai-zippy-child'),
						},
						{
							value: buttonBgColor,
							onChange: (val) => setAttributes({ buttonBgColor: val }),
							label: __('Button Background Color', 'ai-zippy-child'),
						},
						{
							value: backgroundColor,
							onChange: (val) => setAttributes({ backgroundColor: val }),
							label: __('Background Color', 'ai-zippy-child'),
						},
					]}
				/>
			</InspectorControls>

			<div {...blockProps}>
				<div className="oem-info__container">
					<span className="oem-info__ornament" aria-hidden="true"></span>
					<RichText
						tagName="h2"
						className="oem-info__heading"
						value={heading}
						onChange={(val) => setAttributes({ heading: val })}
						placeholder={__('Enter heading…', 'ai-zippy-child')}
						style={{ color: headingColor }}
					/>
					<RichText
						tagName="p"
						className="oem-info__body"
						value={body}
						onChange={(val) => setAttributes({ body: val })}
						placeholder={__('Enter description…', 'ai-zippy-child')}
						style={{ color: bodyColor }}
					/>
					<div className="oem-info__btn-wrap">
						<RichText
							tagName="span"
							className="oem-info__btn"
							value={buttonText}
							onChange={(val) => setAttributes({ buttonText: val })}
							placeholder={__('Button text…', 'ai-zippy-child')}
							style={{ color: buttonTextColor, backgroundColor: buttonBgColor }}
						/>
					</div>
				</div>
			</div>
		</>
	);
}
