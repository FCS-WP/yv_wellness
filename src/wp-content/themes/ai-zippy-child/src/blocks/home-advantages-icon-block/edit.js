import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from "@wordpress/block-editor";
import {
	PanelBody,
	TextControl,
	Button,
	ColorPalette,
	BaseControl,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";

const COLORS = [
	{ name: "Medium Brown", color: "#615245" },
	{ name: "Dark Brown", color: "#3B2715" },
	{ name: "Secondary Brown", color: "#7c4612" },
	{ name: "Light Cream", color: "#FFFAF3" },
	{ name: "White", color: "#ffffff" },
	{ name: "Black", color: "#000000" },
];

export default function Edit({ attributes, setAttributes }) {
	const { items = [], bgColor, bgImageUrl, bgImageId } = attributes;

	const wrapperStyle = {
		backgroundColor: bgColor || "#615245",
	};
	if (bgImageUrl) {
		wrapperStyle.backgroundImage = `url(${bgImageUrl})`;
		wrapperStyle.backgroundSize = "cover";
		wrapperStyle.backgroundPosition = "center";
	}

	const blockProps = useBlockProps({
		className: "haib",
		style: wrapperStyle,
	});

	const updateItem = (index, key, value) => {
		const next = items.map((item, i) =>
			i === index ? { ...item, [key]: value } : item
		);
		setAttributes({ items: next });
	};

	const setItemIcon = (index, media) => {
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
		setAttributes({
			items: [
				...items,
				{
					iconUrl: "",
					iconId: 0,
					subtitle: "",
					heading: "",
					text: "",
				},
			],
		});
	};

	const removeItem = (index) => {
		setAttributes({ items: items.filter((_, i) => i !== index) });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Background", "ai-zippy")} initialOpen={true}>
					<BaseControl label={__("Background Color", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={bgColor}
							onChange={(value) =>
								setAttributes({ bgColor: value || "#615245" })
							}
						/>
					</BaseControl>
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
				</PanelBody>

				<PanelBody title={__("Items", "ai-zippy")} initialOpen={false}>
					{items.map((item, index) => (
						<div
							key={index}
							style={{
								padding: "12px",
								marginBottom: "12px",
								border: "1px solid #ddd",
								borderRadius: "4px",
							}}
						>
							<strong>
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
															width: 40,
															height: 40,
															objectFit: "contain",
															background: "#615245",
															padding: 4,
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
							<TextControl
								label={__("Subtitle", "ai-zippy")}
								value={item.subtitle || ""}
								onChange={(value) => updateItem(index, "subtitle", value)}
							/>
							<TextControl
								label={__("Heading", "ai-zippy")}
								value={item.heading || ""}
								onChange={(value) => updateItem(index, "heading", value)}
							/>
							<TextControl
								label={__("Text", "ai-zippy")}
								value={item.text || ""}
								onChange={(value) => updateItem(index, "text", value)}
							/>
							<Button
								variant="link"
								isDestructive
								onClick={() => removeItem(index)}
							>
								{__("Remove Item", "ai-zippy")}
							</Button>
						</div>
					))}
					<Button variant="primary" onClick={addItem}>
						{__("Add Item", "ai-zippy")}
					</Button>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="haib__grid">
					{items.map((item, index) => (
						<div className="haib__item" key={index}>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) => setItemIcon(index, media)}
									allowedTypes={["image"]}
									value={item.iconId}
									render={({ open }) => (
										<div
											className={
												"haib__icon-wrap" +
												(!item.iconUrl ? " haib__icon-wrap--empty" : "")
											}
											onClick={open}
											role="button"
											tabIndex={0}
										>
											{item.iconUrl ? (
												<img
													className="haib__icon"
													src={item.iconUrl}
													alt={item.subtitle || ""}
												/>
											) : (
												<span
													style={{
														color: "rgba(255,255,255,0.7)",
														fontSize: "10px",
														textAlign: "center",
														padding: "4px",
													}}
												>
													{__("Add Icon", "ai-zippy")}
												</span>
											)}
										</div>
									)}
								/>
							</MediaUploadCheck>
							{item.subtitle && (
								<p className="haib__subtitle">{item.subtitle}</p>
							)}
							{item.heading && (
								<h3 className="haib__heading">{item.heading}</h3>
							)}
							{item.text && <p className="haib__text">{item.text}</p>}
						</div>
					))}
				</div>
			</section>
		</>
	);
}
