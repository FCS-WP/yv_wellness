import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, Button } from "@wordpress/components";

const CheckmarkSVG = () => (
	<svg className="hp__check" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M5 13l4 4L19 7" stroke="#7c4612" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/>
	</svg>
);

export default function Edit({ attributes, setAttributes }) {
	const { subtitle, heading, description, features, imageId, imageUrl } = attributes;
	const blockProps = useBlockProps({ className: "hp" });

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
				<PanelBody title={__("Features", "ai-zippy")} initialOpen={false}>
					<p style={{ fontSize: "12px", color: "#757575" }}>
						{__("Features are configured via block attributes. Edit block.json defaults to change them.", "ai-zippy")}
					</p>
					{features && features.map((feature, index) => (
						<div key={index} style={{ marginBottom: "12px", padding: "8px", background: "#f0f0f0", borderRadius: "4px" }}>
							<strong>{feature.title}</strong>
							<p style={{ margin: "4px 0 0", fontSize: "12px" }}>{feature.text}</p>
						</div>
					))}
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
										<CheckmarkSVG />
										<div className="hp__feature-text">
											<strong>{feature.title}</strong>
											<span>{feature.text}</span>
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
