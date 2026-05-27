import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	ColorPalette,
} from "@wordpress/block-editor";
import {
	PanelBody,
	Button,
	RangeControl,
	BaseControl,
	__experimentalDivider as Divider,
} from "@wordpress/components";

const COLORS = [
	{ name: "Primary", color: "#d50017" },
	{ name: "Secondary", color: "#7c4612" },
	{ name: "Accent", color: "#3B2715" },
	{ name: "Light Cream", color: "#FFFAF3" },
	{ name: "Light Pink", color: "#FFF2F2" },
	{ name: "Medium Brown", color: "#615245" },
	{ name: "White", color: "#ffffff" },
	{ name: "Black", color: "#000000" },
];

const DEFAULT_ITEM = {
	iconUrl: "",
	iconId: 0,
	heading: "New Item",
	text: "Describe this value or feature in a sentence or two.",
	iconBgColor: "#FFFAF3",
	headingColor: "#3B2715",
	textColor: "#615245",
};

export default function Edit({ attributes, setAttributes }) {
	const {
		items = [],
		columns = 3,
		bgColor,
		bgImageUrl,
		bgImageId,
		bgOverlayColor,
		sectionHeading,
		sectionHeadingColor,
	} = attributes;

	const wrapperStyle = {
		backgroundColor: bgColor || "#ffffff",
		"--aitb-columns": columns,
		"--aitb-overlay": bgOverlayColor || "rgba(255,250,243,0.85)",
	};
	if (bgImageUrl) {
		wrapperStyle.backgroundImage = `url(${bgImageUrl})`;
		wrapperStyle.backgroundSize = "cover";
		wrapperStyle.backgroundPosition = "center";
	}

	const blockProps = useBlockProps({
		className: "aitb" + (bgImageUrl ? " aitb--has-bg" : ""),
		style: wrapperStyle,
	});

	const updateItem = (index, key, value) => {
		const next = items.map((item, i) =>
			i === index ? { ...item, [key]: value } : item
		);
		setAttributes({ items: next });
	};

	const setItemIcon = (index, media) => {
		updateItem(index, "iconUrl", media?.url || "");
		const next = items.map((item, i) =>
			i === index
				? { ...item, iconUrl: media?.url || "", iconId: media?.id || 0 }
				: item
		);
		setAttributes({ items: next });
	};

	const removeItemIcon = (index) => {
		const next = items.map((item, i) =>
			i === index ? { ...item, iconUrl: "", iconId: 0 } : item
		);
		setAttributes({ items: next });
	};

	const addItem = () => {
		setAttributes({ items: [...items, { ...DEFAULT_ITEM }] });
	};

	const removeItem = (index) => {
		setAttributes({ items: items.filter((_, i) => i !== index) });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Background Settings", "ai-zippy")} initialOpen={true}>
					<BaseControl label={__("Background Image", "ai-zippy")}>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) =>
									setAttributes({
										bgImageUrl: media?.url || "",
										bgImageId: media?.id || 0,
									})
								}
								allowedTypes={["image"]}
								value={bgImageId}
								render={({ open }) => (
									<div style={{ display: "flex", gap: "8px", flexWrap: "wrap" }}>
										{bgImageUrl && (
											<img
												src={bgImageUrl}
												alt=""
												style={{
													width: "100%",
													height: 90,
													objectFit: "cover",
													borderRadius: 6,
													marginBottom: 8,
												}}
											/>
										)}
										<Button variant="secondary" onClick={open}>
											{bgImageUrl
												? __("Replace Image", "ai-zippy")
												: __("Select Image", "ai-zippy")}
										</Button>
										{bgImageUrl && (
											<Button
												variant="link"
												isDestructive
												onClick={() =>
													setAttributes({ bgImageUrl: "", bgImageId: 0 })
												}
											>
												{__("Remove", "ai-zippy")}
											</Button>
										)}
									</div>
								)}
							/>
						</MediaUploadCheck>
					</BaseControl>
					<BaseControl label={__("Background Color", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={bgColor}
							onChange={(value) => setAttributes({ bgColor: value || "#ffffff" })}
						/>
					</BaseControl>
					<BaseControl label={__("Overlay Color (when image used)", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={bgOverlayColor}
							onChange={(value) =>
								setAttributes({ bgOverlayColor: value || "rgba(255,250,243,0.85)" })
							}
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody title={__("Layout Settings", "ai-zippy")} initialOpen={false}>
					<RangeControl
						label={__("Desktop Columns", "ai-zippy")}
						value={columns}
						onChange={(value) => setAttributes({ columns: value || 3 })}
						min={1}
						max={6}
					/>
				</PanelBody>

				<PanelBody title={__("Section Heading", "ai-zippy")} initialOpen={false}>
					<BaseControl label={__("Heading Color", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={sectionHeadingColor}
							onChange={(value) =>
								setAttributes({ sectionHeadingColor: value || "#3B2715" })
							}
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody title={__("Items", "ai-zippy")} initialOpen={false}>
					{items.map((item, index) => (
						<div
							key={index}
							style={{
								padding: "12px",
								marginBottom: "12px",
								border: "1px solid #ddd",
								borderRadius: "6px",
								background: "#fafafa",
							}}
						>
							<strong style={{ display: "block", marginBottom: 8 }}>
								{__("Item", "ai-zippy")} #{index + 1}
							</strong>
							<BaseControl label={__("Icon", "ai-zippy")}>
								<MediaUploadCheck>
									<MediaUpload
										onSelect={(media) => setItemIcon(index, media)}
										allowedTypes={["image"]}
										value={item.iconId}
										render={({ open }) => (
											<div
												style={{
													display: "flex",
													gap: "8px",
													flexWrap: "wrap",
													alignItems: "center",
												}}
											>
												{item.iconUrl && (
													<img
														src={item.iconUrl}
														alt=""
														style={{
															width: 44,
															height: 44,
															objectFit: "contain",
															background: item.iconBgColor || "#FFFAF3",
															padding: 6,
															borderRadius: "50%",
														}}
													/>
												)}
												<Button variant="secondary" onClick={open}>
													{item.iconUrl
														? __("Replace", "ai-zippy")
														: __("Select", "ai-zippy")}
												</Button>
												{item.iconUrl && (
													<Button
														variant="link"
														isDestructive
														onClick={() => removeItemIcon(index)}
													>
														{__("Remove", "ai-zippy")}
													</Button>
												)}
											</div>
										)}
									/>
								</MediaUploadCheck>
							</BaseControl>
							<BaseControl label={__("Icon Background", "ai-zippy")}>
								<ColorPalette
									colors={COLORS}
									value={item.iconBgColor}
									onChange={(value) =>
										updateItem(index, "iconBgColor", value || "#FFFAF3")
									}
								/>
							</BaseControl>
							<BaseControl label={__("Heading Color", "ai-zippy")}>
								<ColorPalette
									colors={COLORS}
									value={item.headingColor}
									onChange={(value) =>
										updateItem(index, "headingColor", value || "#3B2715")
									}
								/>
							</BaseControl>
							<BaseControl label={__("Text Color", "ai-zippy")}>
								<ColorPalette
									colors={COLORS}
									value={item.textColor}
									onChange={(value) =>
										updateItem(index, "textColor", value || "#615245")
									}
								/>
							</BaseControl>
							<Divider />
							<Button
								variant="link"
								isDestructive
								onClick={() => removeItem(index)}
								style={{ marginTop: 8 }}
							>
								{__("Remove Item", "ai-zippy")}
							</Button>
						</div>
					))}
					<Button variant="primary" onClick={addItem} style={{ width: "100%" }}>
						{__("+ Add Item", "ai-zippy")}
					</Button>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				{bgImageUrl && <span className="aitb__bg-overlay" aria-hidden="true" />}
				<div className="aitb__inner">
					<RichText
						tagName="h2"
						className="aitb__section-heading"
						value={sectionHeading}
						onChange={(val) => setAttributes({ sectionHeading: val })}
						placeholder={__("Optional section heading…", "ai-zippy")}
						style={{ color: sectionHeadingColor }}
					/>
					<div className="aitb__grid">
						{items.map((item, index) => (
							<article className="aitb__item" key={index}>
								<MediaUploadCheck>
									<MediaUpload
										onSelect={(media) => setItemIcon(index, media)}
										allowedTypes={["image"]}
										value={item.iconId}
										render={({ open }) => (
											<div
												className={
													"aitb__icon-wrap" +
													(!item.iconUrl ? " aitb__icon-wrap--empty" : "")
												}
												style={{ backgroundColor: item.iconBgColor || "#FFFAF3" }}
												onClick={open}
												role="button"
												tabIndex={0}
											>
												<span className="aitb__icon-ring" aria-hidden="true" />
												{item.iconUrl ? (
													<img
														className="aitb__icon"
														src={item.iconUrl}
														alt=""
													/>
												) : (
													<span className="aitb__icon-placeholder">
														{__("Add Icon", "ai-zippy")}
													</span>
												)}
											</div>
										)}
									/>
								</MediaUploadCheck>
								<RichText
									tagName="h3"
									className="aitb__heading"
									value={item.heading}
									onChange={(val) => updateItem(index, "heading", val)}
									placeholder={__("Heading…", "ai-zippy")}
									style={{ color: item.headingColor }}
								/>
								<span
									className="aitb__divider"
									aria-hidden="true"
									style={{ backgroundColor: item.headingColor }}
								/>
								<RichText
									tagName="p"
									className="aitb__text"
									value={item.text}
									onChange={(val) => updateItem(index, "text", val)}
									placeholder={__("Description…", "ai-zippy")}
									style={{ color: item.textColor }}
								/>
							</article>
						))}
					</div>
				</div>
			</section>
		</>
	);
}
