import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaUpload,
	MediaUploadCheck,
} from "@wordpress/block-editor";
import {
	PanelBody,
	TextControl,
	ColorPalette,
	Button,
	BaseControl,
} from "@wordpress/components";

const COLORS = [
	{ name: "Secondary Brown", color: "#7c4612" },
	{ name: "Dark Brown", color: "#3B2715" },
	{ name: "Medium Brown", color: "#615245" },
	{ name: "Primary Red", color: "#d50017" },
	{ name: "Black", color: "#000000" },
	{ name: "White", color: "#ffffff" },
];

export default function Edit({ attributes, setAttributes }) {
	const { title, titleColor, images } = attributes;
	const blockProps = useBlockProps({ className: "hg" });

	const onSelectImages = (media) => {
		const next = (Array.isArray(media) ? media : [media]).map((m) => ({
			id: m.id,
			url: m.url,
			alt: m.alt || "",
		}));
		setAttributes({ images: next });
	};

	const removeImage = (id) => {
		setAttributes({
			images: images.filter((img) => img.id !== id),
		});
	};

	const hasImages = Array.isArray(images) && images.length > 0;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Title Settings", "ai-zippy")} initialOpen={true}>
					<TextControl
						label={__("Title", "ai-zippy")}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
					/>
					<BaseControl
						label={__("Title Color", "ai-zippy")}
						id="hg-title-color"
					>
						<ColorPalette
							colors={COLORS}
							value={titleColor}
							onChange={(value) =>
								setAttributes({ titleColor: value || "#7c4612" })
							}
							disableCustomColors={false}
							clearable={false}
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody
					title={__("Gallery Images", "ai-zippy")}
					initialOpen={true}
				>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={onSelectImages}
							allowedTypes={["image"]}
							gallery={true}
							multiple={true}
							value={images.map((img) => img.id)}
							render={({ open }) => (
								<Button
									variant="primary"
									onClick={open}
									style={{ marginBottom: "1rem" }}
								>
									{hasImages
										? __("Replace Images", "ai-zippy")
										: __("Select Images", "ai-zippy")}
								</Button>
							)}
						/>
					</MediaUploadCheck>

					{hasImages && (
						<ul
							style={{
								listStyle: "none",
								margin: 0,
								padding: 0,
								display: "flex",
								flexDirection: "column",
								gap: "0.5rem",
							}}
						>
							{images.map((img) => (
								<li
									key={img.id}
									style={{
										display: "flex",
										alignItems: "center",
										gap: "0.5rem",
										padding: "0.25rem",
										border: "1px solid #ddd",
										borderRadius: "4px",
									}}
								>
									<img
										src={img.url}
										alt={img.alt}
										style={{
											width: 48,
											height: 48,
											objectFit: "cover",
											borderRadius: "4px",
										}}
									/>
									<span
										style={{
											flex: 1,
											fontSize: "12px",
											overflow: "hidden",
											textOverflow: "ellipsis",
											whiteSpace: "nowrap",
										}}
									>
										{img.alt || `Image #${img.id}`}
									</span>
									<Button
										isDestructive
										variant="tertiary"
										onClick={() => removeImage(img.id)}
									>
										{__("Remove", "ai-zippy")}
									</Button>
								</li>
							))}
						</ul>
					)}
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<RichText
					tagName="h2"
					className="hg__title"
					value={title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__("Our Customer's Journey", "ai-zippy")}
					style={{ color: titleColor }}
					allowedFormats={["core/italic", "core/bold"]}
				/>

				{hasImages ? (
					<div className="hg__track">
						{images.map((img) => (
							<div className="hg__slide" key={img.id}>
								<img
									className="hg__image"
									src={img.url}
									alt={img.alt}
									loading="lazy"
								/>
							</div>
						))}
					</div>
				) : (
					<p className="hg__empty">
						{__(
							"No images selected. Add images in the block settings.",
							"ai-zippy"
						)}
					</p>
				)}
			</section>
		</>
	);
}
