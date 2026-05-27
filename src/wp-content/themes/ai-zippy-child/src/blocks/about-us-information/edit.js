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

export default function Edit({ attributes, setAttributes }) {
	const {
		subHeading,
		subHeadingColor,
		heading,
		headingColor,
		description,
		descriptionColor,
		imageUrl,
		imageId,
		imageAlt,
		bgColor,
		bgImageUrl,
		bgImageId,
	} = attributes;

	const wrapperStyle = {
		backgroundColor: bgColor || "#ffffff",
	};
	if (bgImageUrl) {
		wrapperStyle.backgroundImage = `url(${bgImageUrl})`;
		wrapperStyle.backgroundSize = "cover";
		wrapperStyle.backgroundPosition = "center";
	}

	const blockProps = useBlockProps({
		className: "aui" + (bgImageUrl ? " aui--has-bg" : ""),
		style: wrapperStyle,
	});

	const onSelectImage = (media) => {
		setAttributes({
			imageUrl: media?.url || "",
			imageId: media?.id || 0,
			imageAlt: media?.alt || "",
		});
	};

	const onRemoveImage = () => {
		setAttributes({ imageUrl: "", imageId: 0, imageAlt: "" });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Background Settings", "ai-zippy")}
					initialOpen={true}
				>
					<BaseControl label={__("Background Color", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={bgColor}
							onChange={(value) =>
								setAttributes({ bgColor: value || "#ffffff" })
							}
						/>
					</BaseControl>
					<BaseControl label={__("Background Image (optional)", "ai-zippy")}>
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
									<div
										style={{
											display: "flex",
											gap: "8px",
											flexWrap: "wrap",
										}}
									>
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
													setAttributes({
														bgImageUrl: "",
														bgImageId: 0,
													})
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

				<PanelBody
					title={__("Typography Colors", "ai-zippy")}
					initialOpen={false}
				>
					<BaseControl label={__("Sub-heading Color", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={subHeadingColor}
							onChange={(value) =>
								setAttributes({ subHeadingColor: value || "#d50017" })
							}
						/>
					</BaseControl>
					<BaseControl label={__("Heading Color", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={headingColor}
							onChange={(value) =>
								setAttributes({ headingColor: value || "#3B2715" })
							}
						/>
					</BaseControl>
					<BaseControl label={__("Description Color", "ai-zippy")}>
						<ColorPalette
							colors={COLORS}
							value={descriptionColor}
							onChange={(value) =>
								setAttributes({ descriptionColor: value || "#615245" })
							}
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody title={__("Image", "ai-zippy")} initialOpen={false}>
					<BaseControl label={__("Content Image", "ai-zippy")}>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectImage}
								allowedTypes={["image"]}
								value={imageId}
								render={({ open }) => (
									<div
										style={{
											display: "flex",
											gap: "8px",
											flexWrap: "wrap",
										}}
									>
										{imageUrl && (
											<img
												src={imageUrl}
												alt=""
												style={{
													width: "100%",
													height: 140,
													objectFit: "cover",
													borderRadius: 8,
													marginBottom: 8,
												}}
											/>
										)}
										<Button variant="secondary" onClick={open}>
											{imageUrl
												? __("Replace Image", "ai-zippy")
												: __("Select Image", "ai-zippy")}
										</Button>
										{imageUrl && (
											<Button
												variant="link"
												isDestructive
												onClick={onRemoveImage}
											>
												{__("Remove", "ai-zippy")}
											</Button>
										)}
									</div>
								)}
							/>
						</MediaUploadCheck>
					</BaseControl>
					<Divider />
					<BaseControl label={__("Image Alt Text", "ai-zippy")}>
						<input
							type="text"
							className="components-text-control__input"
							value={imageAlt || ""}
							onChange={(e) =>
								setAttributes({ imageAlt: e.target.value })
							}
							placeholder={__("Describe the image…", "ai-zippy")}
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				{bgImageUrl && <span className="aui__bg-overlay" aria-hidden="true" />}
				<div className="aui__wrapper">
					<div className="aui__content">
						<RichText
							tagName="p"
							className="aui__sub-heading"
							value={subHeading}
							onChange={(val) => setAttributes({ subHeading: val })}
							placeholder={__("Sub-heading…", "ai-zippy")}
							style={{ color: subHeadingColor }}
							allowedFormats={["core/bold", "core/italic"]}
						/>
						<RichText
							tagName="h2"
							className="aui__heading"
							value={heading}
							onChange={(val) => setAttributes({ heading: val })}
							placeholder={__("Section heading…", "ai-zippy")}
							style={{ color: headingColor }}
							allowedFormats={[
								"core/bold",
								"core/italic",
								"core/underline",
							]}
						/>
						<RichText
							tagName="div"
							className="aui__description"
							multiline="p"
							value={description}
							onChange={(val) => setAttributes({ description: val })}
							placeholder={__("Description paragraphs…", "ai-zippy")}
							style={{ color: descriptionColor }}
						/>
					</div>
					<div className="aui__media">
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectImage}
								allowedTypes={["image"]}
								value={imageId}
								render={({ open }) => (
									<div
										className={
											"aui__media-frame" +
											(!imageUrl ? " aui__media-frame--empty" : "")
										}
										onClick={open}
										role="button"
										tabIndex={0}
									>
										<span className="aui__media-stamp" aria-hidden="true" />
										{imageUrl ? (
											<img
												className="aui__image"
												src={imageUrl}
												alt={imageAlt || ""}
											/>
										) : (
											<span className="aui__media-placeholder">
												{__("Click to add image", "ai-zippy")}
											</span>
										)}
									</div>
								)}
							/>
						</MediaUploadCheck>
					</div>
				</div>
			</section>
		</>
	);
}
