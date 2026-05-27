import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from "@wordpress/block-editor";
import {
	PanelBody,
	TextControl,
	TextareaControl,
	ColorPalette,
	RangeControl,
	SelectControl,
	Button,
} from "@wordpress/components";

const COLORS = [
	{ name: "Light Pink", color: "#FFF2F2" },
	{ name: "Light Cream", color: "#FFFAF3" },
	{ name: "Dark Brown", color: "#3B2715" },
	{ name: "Medium Brown", color: "#615245" },
	{ name: "Secondary Brown", color: "#7c4612" },
	{ name: "White", color: "#ffffff" },
	{ name: "Black", color: "#000000" },
];

const ORDER_BY_OPTIONS = [
	{ label: "Popularity (Best Selling)", value: "popularity" },
	{ label: "Newest", value: "date" },
	{ label: "Rating", value: "rating" },
	{ label: "Price (Low to High)", value: "price" },
	{ label: "Random", value: "rand" },
];

export default function Edit({ attributes, setAttributes }) {
	const {
		title,
		titleColor,
		bgColor,
		bgImageUrl,
		bgImageId,
		productIds,
		columns,
		rows,
		orderBy,
	} = attributes;

	const productIdsString = Array.isArray(productIds) ? productIds.join(", ") : "";

	const handleProductIdsChange = (value) => {
		if (!value || !value.trim()) {
			setAttributes({ productIds: [] });
			return;
		}
		const ids = value
			.split(",")
			.map((id) => parseInt(id.trim(), 10))
			.filter((id) => !isNaN(id) && id > 0);
		setAttributes({ productIds: ids });
	};

	const wrapperStyle = {
		backgroundColor: bgColor,
	};
	if (bgImageUrl) {
		wrapperStyle.backgroundImage = `url(${bgImageUrl})`;
		wrapperStyle.backgroundSize = "cover";
		wrapperStyle.backgroundPosition = "center";
	}

	const blockProps = useBlockProps({
		className: "hpg",
		style: wrapperStyle,
	});

	const previewCount = Math.max(1, Math.min(columns * rows, 24));
	const previewCards = Array.from({ length: previewCount });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Background", "ai-zippy")} initialOpen={true}>
					<p style={{ marginBottom: "8px", fontWeight: 500 }}>
						{__("Background Color", "ai-zippy")}
					</p>
					<ColorPalette
						colors={COLORS}
						value={bgColor}
						onChange={(value) => setAttributes({ bgColor: value || "#FFF2F2" })}
					/>
					<p style={{ marginTop: "16px", marginBottom: "8px", fontWeight: 500 }}>
						{__("Background Image", "ai-zippy")}
					</p>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									bgImageUrl: media.url,
									bgImageId: media.id,
								})
							}
							allowedTypes={["image"]}
							value={bgImageId}
							render={({ open }) => (
								<div>
									<Button variant="secondary" onClick={open}>
										{bgImageUrl
											? __("Replace Image", "ai-zippy")
											: __("Upload Image", "ai-zippy")}
									</Button>
									{bgImageUrl && (
										<Button
											variant="link"
											isDestructive
											onClick={() =>
												setAttributes({ bgImageUrl: "", bgImageId: 0 })
											}
											style={{ marginLeft: "8px" }}
										>
											{__("Remove", "ai-zippy")}
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>

				<PanelBody title={__("Title", "ai-zippy")} initialOpen={false}>
					<TextControl
						label={__("Title Text", "ai-zippy")}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
					/>
					<p style={{ marginTop: "16px", marginBottom: "8px", fontWeight: 500 }}>
						{__("Title Color", "ai-zippy")}
					</p>
					<ColorPalette
						colors={COLORS}
						value={titleColor}
						onChange={(value) => setAttributes({ titleColor: value || "#3B2715" })}
					/>
				</PanelBody>

				<PanelBody title={__("Grid Settings", "ai-zippy")} initialOpen={false}>
					<RangeControl
						label={__("Columns (Desktop)", "ai-zippy")}
						value={columns}
						onChange={(value) => setAttributes({ columns: value })}
						min={1}
						max={6}
					/>
					<RangeControl
						label={__("Rows", "ai-zippy")}
						value={rows}
						onChange={(value) => setAttributes({ rows: value })}
						min={1}
						max={4}
					/>
					<SelectControl
						label={__("Order By", "ai-zippy")}
						value={orderBy}
						options={ORDER_BY_OPTIONS}
						onChange={(value) => setAttributes({ orderBy: value })}
					/>
				</PanelBody>

				<PanelBody title={__("Products (Optional)", "ai-zippy")} initialOpen={false}>
					<TextareaControl
						label={__("About Product Selection", "ai-zippy")}
						help={__(
							"Enter specific product IDs separated by commas (e.g. 12, 34, 56). When left empty, products will be loaded using the 'Order By' strategy.",
							"ai-zippy"
						)}
						value=""
						onChange={() => {}}
						rows={2}
						disabled
					/>
					<TextControl
						label={__("Product IDs", "ai-zippy")}
						placeholder="12, 34, 56"
						value={productIdsString}
						onChange={handleProductIdsChange}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{title && (
					<h2 className="hpg__title" style={{ color: titleColor }}>
						{title}
					</h2>
				)}
				<div
					className="hpg__preview-grid"
					style={{ "--hpg-columns": columns }}
				>
					{previewCards.map((_, index) => (
						<div key={index} className="hpg__preview-card" />
					))}
				</div>
				<p
					style={{
						textAlign: "center",
						marginTop: "1.5rem",
						color: "#615245",
						fontStyle: "italic",
						fontSize: "0.875rem",
					}}
				>
					{__(
						"Products will be loaded on the frontend.",
						"ai-zippy"
					)}
				</p>
			</div>
		</>
	);
}
