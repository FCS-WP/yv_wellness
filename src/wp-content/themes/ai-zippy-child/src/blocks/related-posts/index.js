import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { createElement } from '@wordpress/element';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: function () {
        const blockProps = useBlockProps({ className: 'single-post-related' });
        return createElement(
            'div',
            blockProps,
            createElement(
                'p',
                { style: { textAlign: 'center', padding: '2rem', color: '#615245' } },
                'Related Posts (rendered on frontend)'
            )
        );
    },
    save: function () {
        return null;
    },
});
