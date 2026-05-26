import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
} from "@wordpress/block-editor";
import {
	PanelBody,
	RangeControl,
	TextControl,
	Button,
	ColorPalette,
} from "@wordpress/components";

export default function Edit({ attributes, setAttributes }) {
	const {
		heading,
		body,
		buttonText,
		buttonUrl,
		bgImageId,
		bgImageUrl,
		bgColor,
		overlayColor,
		overlayOpacity,
	} = attributes;

	const blockProps = useBlockProps({
		className: "hbh",
		style: {
			backgroundColor: bgColor,
			backgroundImage: bgImageUrl ? `url(${bgImageUrl})` : undefined,
		},
	});

	const bgPalette = [
		{ name: "Cream", color: "#FFFAF3" },
		{ name: "Warm Beige", color: "#F5EBDD" },
		{ name: "Soft Sand", color: "#EFE3D2" },
		{ name: "Dark Brown", color: "#3B2715" },
		{ name: "White", color: "#FFFFFF" },
	];

	const overlayPalette = [
		{ name: "Cream Veil", color: "rgba(255, 250, 243, 0.6)" },
		{ name: "Dark Veil", color: "rgba(59, 39, 21, 0.4)" },
		{ name: "Black Veil", color: "rgba(0, 0, 0, 0.4)" },
		{ name: "Transparent", color: "rgba(0, 0, 0, 0)" },
	];

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__("Background Settings", "ai-zippy")}
					initialOpen={true}
				>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) =>
								setAttributes({
									bgImageId: media.id,
									bgImageUrl: media.url,
								})
							}
							allowedTypes={["image"]}
							value={bgImageId}
							render={({ open }) => (
								<div style={{ marginBottom: "16px" }}>
									<p style={{ marginBottom: "4px" }}>
										<strong>
											{__("Background Image", "ai-zippy")}
										</strong>
									</p>
									{bgImageUrl && (
										<img
											src={bgImageUrl}
											alt=""
											style={{
												maxWidth: "100%",
												marginBottom: "8px",
												borderRadius: "8px",
											}}
										/>
									)}
									<Button
										onClick={open}
										variant="secondary"
									>
										{bgImageUrl
											? __("Replace", "ai-zippy")
											: __("Select Image", "ai-zippy")}
									</Button>
									{bgImageUrl && (
										<Button
											onClick={() =>
												setAttributes({
													bgImageId: 0,
													bgImageUrl: "",
												})
											}
											variant="link"
											isDestructive
											style={{ marginLeft: "8px" }}
										>
											{__("Remove", "ai-zippy")}
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>

					<p style={{ marginBottom: "4px" }}>
						<strong>{__("Background Color", "ai-zippy")}</strong>
					</p>
					<ColorPalette
						value={bgColor}
						colors={bgPalette}
						onChange={(val) =>
							setAttributes({ bgColor: val || "#FFFAF3" })
						}
					/>
				</PanelBody>

				<PanelBody
					title={__("Overlay Settings", "ai-zippy")}
					initialOpen={false}
				>
					<p style={{ marginBottom: "4px" }}>
						<strong>{__("Overlay Color", "ai-zippy")}</strong>
					</p>
					<ColorPalette
						value={overlayColor}
						colors={overlayPalette}
						onChange={(val) =>
							setAttributes({
								overlayColor:
									val || "rgba(255, 250, 243, 0.6)",
							})
						}
					/>
					<RangeControl
						label={__("Overlay Opacity", "ai-zippy")}
						value={overlayOpacity}
						onChange={(val) =>
							setAttributes({ overlayOpacity: val })
						}
						min={0}
						max={100}
					/>
				</PanelBody>

				<PanelBody
					title={__("Button Settings", "ai-zippy")}
					initialOpen={false}
				>
					<TextControl
						label={__("Button URL", "ai-zippy")}
						value={buttonUrl}
						onChange={(val) =>
							setAttributes({ buttonUrl: val })
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div
					className="hbh__overlay"
					style={{
						opacity: overlayOpacity / 100,
						backgroundColor: overlayColor,
					}}
				/>
				<div className="hbh__container">
					<div className="hbh__content">
						<RichText
							tagName="h1"
							className="hbh__heading"
							value={heading}
							onChange={(val) =>
								setAttributes({ heading: val })
							}
							placeholder={__(
								"Transform Beauty, Confidence & Wellness",
								"ai-zippy",
							)}
						/>
						<RichText
							tagName="p"
							className="hbh__body"
							value={body}
							onChange={(val) => setAttributes({ body: val })}
							placeholder={__(
								"Advanced beauty and wellness technology…",
								"ai-zippy",
							)}
						/>
						<RichText
							tagName="span"
							className="hbh__btn"
							value={buttonText}
							onChange={(val) =>
								setAttributes({ buttonText: val })
							}
							placeholder={__("SHOP NOW", "ai-zippy")}
						/>
					</div>
				</div>
			</div>
		</>
	);
}
