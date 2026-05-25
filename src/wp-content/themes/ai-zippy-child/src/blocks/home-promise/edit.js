import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, Button, TextControl, ColorPalette } from "@wordpress/components";

const CheckmarkIcon = () => (
	<img className="hp__check" src="/wp-content/uploads/2026/05/brow-correct-tick.webp" alt="checkmark" width="24" height="24" />
);

const COLORS = [
	{ name: "Dark Brown", color: "#3B2715" },
	{ name: "Medium Brown", color: "#615245" },
	{ name: "Secondary Brown", color: "#7c4612" },
	{ name: "Primary Red", color: "#d50017" },
	{ name: "Black", color: "#000000" },
	{ name: "White", color: "#ffffff" },
];

export default function Edit({ attributes, setAttributes }) {
	const { subtitle, heading, description, features, imageId, imageUrl, featureTitleColor, featureTextColor } = attributes;
	const blockProps = useBlockProps({ className: "hp" });

	const updateFeature = (index, key, value) => {
		const updated = [...features];
		updated[index] = { ...updated[index], [key]: value };
		setAttributes({ features: updated });
	};

	const addFeature = () => {
		setAttributes({ features: [...features, { title: "New Feature", text: "Feature description" }] });
	};

	const removeFeature = (index) => {
		const updated = features.filter((_, i) => i !== index);
		setAttributes({ features: updated });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Image", "ai-zippy")} initialOpen={true}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) => setAttributes({ imageId: media.id, imageUrl: media.url })}
							allowedTypes={["image"]}
							value={imageId}
							render={({ open }) => (
								<div>
									{imageUrl && (
										<img src={imageUrl} alt="" style={{ width: "100%", marginBottom: "10px", borderRadius: "8px" }} />
									)}
									<Button variant="secondary" onClick={open} style={{ marginBottom: "8px", width: "100%" }}>
										{imageUrl ? __("Replace Image", "ai-zippy") : __("Select Image", "ai-zippy")}
									</Button>
									{imageUrl && (
										<Button variant="link" isDestructive onClick={() => setAttributes({ imageId: 0, imageUrl: "" })}>
											{__("Remove Image", "ai-zippy")}
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>
				<PanelBody title={__("Feature Colors", "ai-zippy")} initialOpen={false}>
					<p style={{ marginBottom: "8px", fontWeight: "600" }}>{__("Title Color", "ai-zippy")}</p>
					<ColorPalette
						colors={COLORS}
						value={featureTitleColor}
						onChange={(val) => setAttributes({ featureTitleColor: val })}
					/>
					<p style={{ marginBottom: "8px", fontWeight: "600" }}>{__("Text Color", "ai-zippy")}</p>
					<ColorPalette
						colors={COLORS}
						value={featureTextColor}
						onChange={(val) => setAttributes({ featureTextColor: val })}
					/>
				</PanelBody>
				<PanelBody title={__("Features", "ai-zippy")} initialOpen={false}>
					{features && features.map((feature, index) => (
						<div key={index} style={{ marginBottom: "16px", padding: "10px", background: "#f0f0f0", borderRadius: "4px" }}>
							<TextControl
								label={__("Title", "ai-zippy")}
								value={feature.title}
								onChange={(val) => updateFeature(index, "title", val)}
							/>
							<TextControl
								label={__("Text", "ai-zippy")}
								value={feature.text}
								onChange={(val) => updateFeature(index, "text", val)}
							/>
							<Button variant="link" isDestructive onClick={() => removeFeature(index)} style={{ marginTop: "4px" }}>
								{__("Remove", "ai-zippy")}
							</Button>
						</div>
					))}
					<Button variant="secondary" onClick={addFeature} style={{ width: "100%" }}>
						{__("+ Add Feature", "ai-zippy")}
					</Button>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="hp__grid">
					<div className="hp__content">
						<RichText
							tagName="p"
							className="hp__subtitle"
							value={subtitle}
							onChange={(val) => setAttributes({ subtitle: val })}
							placeholder={__("OUR PROMISE", "ai-zippy")}
						/>
						<RichText
							tagName="h2"
							className="hp__heading"
							value={heading}
							onChange={(val) => setAttributes({ heading: val })}
							placeholder={__("Enter heading...", "ai-zippy")}
						/>
						<RichText
							tagName="p"
							className="hp__desc"
							value={description}
							onChange={(val) => setAttributes({ description: val })}
							placeholder={__("Enter description...", "ai-zippy")}
						/>
						{features && features.length > 0 && (
							<ul className="hp__features">
								{features.map((feature, index) => (
									<li key={index} className="hp__feature">
										<CheckmarkIcon />
										<div className="hp__feature-text">
											<strong style={{ color: featureTitleColor }}>{feature.title}</strong>
											<span style={{ color: featureTextColor }}>{feature.text}</span>
										</div>
									</li>
								))}
							</ul>
						)}
					</div>
					<div className="hp__image-wrap">
						{imageUrl ? (
							<img className="hp__image" src={imageUrl} alt={subtitle || ""} />
						) : (
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) => setAttributes({ imageId: media.id, imageUrl: media.url })}
									allowedTypes={["image"]}
									value={imageId}
									render={({ open }) => (
										<Button variant="secondary" onClick={open} style={{ padding: "20px" }}>
											{__("Select Image", "ai-zippy")}
										</Button>
									)}
								/>
							</MediaUploadCheck>
						)}
					</div>
				</div>
			</section>
		</>
	);
}
