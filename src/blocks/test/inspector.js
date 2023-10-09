/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl, BaseControl, ColorPalette, DropdownMenu } =
	wp.components;
const { useState } = wp.element;

const Inspector = ({ attributes, setAttributes }) => {
	const { content, textColor } = attributes;
	const [device, setDevice] = useState('desktop');

	return (
		<InspectorControls>
			<PanelBody title={__('Test Block Settings', 'boilerplate')}>
				<BaseControl label={__('Direction', 'boilerplate')} id="test">
					<DropdownMenu
						icon={device}
						controls={[
							{
								icon: 'desktop',
								onClick: () => setDevice('desktop'),
							},
							{
								icon: 'tablet',
								onClick: () => setDevice('tablet'),
							},
							{
								icon: 'smartphone',
								onClick: () => setDevice('smartphone'),
							},
						]}
					/>
				</BaseControl>
				<TextControl
					label={__('Content')}
					value={content}
					onChange={(v) => setAttributes({ content: v })}
				/>
				<BaseControl label={__('Color', 'boilerplate')} id="color">
					<ColorPalette
						colors={[
							{ name: 'red', color: '#f00' },
							{ name: 'white', color: '#fff' },
							{ name: 'blue', color: '#00f' },
						]}
						value={textColor}
						onChange={(v) => setAttributes({ textColor: v })}
					/>
				</BaseControl>
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;
