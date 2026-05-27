import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	ColorPalette,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Button,
	RangeControl,
	BaseControl,
} from '@wordpress/components';

const COLORS = [
	{ name: 'Primary', color: '#d50017' },
	{ name: 'Secondary', color: '#7c4612' },
	{ name: 'Accent', color: '#3B2715' },
	{ name: 'Light Cream', color: '#FFFAF3' },
	{ name: 'Light Pink', color: '#FFF2F2' },
	{ name: 'Medium Brown', color: '#615245' },
	{ name: 'White', color: '#ffffff' },
	{ name: 'Black', color: '#000000' },
];

/**
 * Convert a hex / rgb color + opacity (0-100) into rgba string.
 */
const buildOverlay = (color, opacity) => {
	const o = Math.max(0, Math.min(100, Number(opacity))) / 100;

	if (!color) {
		return `rgba(59,39,21,${o})`;
	}

	// rgba already provided — replace alpha.
	const rgbaMatch = color.match(/^rgba?\(([^)]+)\)$/i);
	if (rgbaMatch) {
		const parts = rgbaMatch[1].split(',').map((p) => p.trim());
		const [r, g, b] = parts;
		return `rgba(${r},${g},${b},${o})`;
	}

	// hex.
	const hex = color.replace('#', '');
	if (hex.length === 3 || hex.length === 6) {
		const full =
			hex.length === 3
				? hex
						.split('')
						.map((c) => c + c)
						.join('')
				: hex;
		const r = parseInt(full.slice(0, 2), 16);
		const g = parseInt(full.slice(2, 4), 16);
		const b = parseInt(full.slice(4, 6), 16);
		return `rgba(${r},${g},${b},${o})`;
	}

	return color;
};

const Edit = ({ attributes, setAttributes }) => {
	const {
		heading,
		headingColor,
		breadcrumb,
		breadcrumbColor,
		bgColor,
		bgImageUrl,
		bgImageId,
		overlayColor,
		overlayOpacity,
		minHeight,
	} = attributes;

	const blockProps = useBlockProps({
		className: 'bhp',
		style: {
			'--bhp-min-height': `${minHeight}px`,
			backgroundColor: bgImageUrl ? 'transparent' : bgColor,
			backgroundImage: bgImageUrl ? `url(${bgImageUrl})` : 'none',
		},
	});

	const overlayStyle = {
		backgroundColor: buildOverlay(overlayColor, overlayOpacity),
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__('Background Settings', 'ai-zippy')}
					initialOpen={true}
				>
					<BaseControl
						__nextHasNoMarginBottom
						label={__('Background Image', 'ai-zippy')}
					>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) =>
									setAttributes({
										bgImageUrl: media.url,
										bgImageId: media.id,
									})
								}
								allowedTypes={['image']}
								value={bgImageId}
								render={({ open }) => (
									<div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
										<Button
											variant={bgImageUrl ? 'secondary' : 'primary'}
											onClick={open}
										>
											{bgImageUrl
												? __('Replace Image', 'ai-zippy')
												: __('Select Image', 'ai-zippy')}
										</Button>
										{bgImageUrl && (
											<Button
												variant="tertiary"
												isDestructive
												onClick={() =>
													setAttributes({
														bgImageUrl: '',
														bgImageId: 0,
													})
												}
											>
												{__('Remove', 'ai-zippy')}
											</Button>
										)}
									</div>
								)}
							/>
						</MediaUploadCheck>
						{bgImageUrl && (
							<div
								style={{
									marginTop: '12px',
									borderRadius: '4px',
									overflow: 'hidden',
									border: '1px solid #ddd',
								}}
							>
								<img
									src={bgImageUrl}
									alt=""
									style={{
										width: '100%',
										display: 'block',
										maxHeight: '140px',
										objectFit: 'cover',
									}}
								/>
							</div>
						)}
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={__('Background Color (fallback)', 'ai-zippy')}
					>
						<ColorPalette
							colors={COLORS}
							value={bgColor}
							onChange={(value) =>
								setAttributes({ bgColor: value || '#3B2715' })
							}
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={__('Overlay Color', 'ai-zippy')}
					>
						<ColorPalette
							colors={COLORS}
							value={overlayColor}
							onChange={(value) =>
								setAttributes({
									overlayColor: value || 'rgba(59,39,21,0.65)',
								})
							}
						/>
					</BaseControl>

					<RangeControl
						label={__('Overlay Opacity (%)', 'ai-zippy')}
						value={overlayOpacity}
						onChange={(value) => setAttributes({ overlayOpacity: value })}
						min={0}
						max={100}
						step={1}
					/>

					<RangeControl
						label={__('Minimum Height (px)', 'ai-zippy')}
						value={minHeight}
						onChange={(value) => setAttributes({ minHeight: value })}
						min={150}
						max={500}
						step={10}
					/>
				</PanelBody>

				<PanelBody
					title={__('Typography Settings', 'ai-zippy')}
					initialOpen={false}
				>
					<BaseControl
						__nextHasNoMarginBottom
						label={__('Heading Color', 'ai-zippy')}
					>
						<ColorPalette
							colors={COLORS}
							value={headingColor}
							onChange={(value) =>
								setAttributes({ headingColor: value || '#ffffff' })
							}
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={__('Breadcrumb Color', 'ai-zippy')}
					>
						<ColorPalette
							colors={COLORS}
							value={breadcrumbColor}
							onChange={(value) =>
								setAttributes({
									breadcrumbColor: value || 'rgba(255,255,255,0.85)',
								})
							}
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<span className="bhp__overlay" style={overlayStyle} aria-hidden="true" />
				<div className="bhp__content">
					<span className="bhp__ornament" aria-hidden="true" />
					<RichText
						tagName="h1"
						className="bhp__heading"
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
						placeholder={__('Page Title', 'ai-zippy')}
						style={{ color: headingColor }}
						allowedFormats={['core/bold', 'core/italic']}
					/>
					<RichText
						tagName="p"
						className="bhp__breadcrumb"
						value={breadcrumb}
						onChange={(value) => setAttributes({ breadcrumb: value })}
						placeholder={__('Home / Page', 'ai-zippy')}
						style={{ color: breadcrumbColor }}
						allowedFormats={['core/bold', 'core/italic', 'core/link']}
					/>
				</div>
			</section>
		</>
	);
};

export default Edit;
