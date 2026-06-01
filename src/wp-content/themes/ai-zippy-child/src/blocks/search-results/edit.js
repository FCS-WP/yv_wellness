import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

export default function Edit() {
    const blockProps = useBlockProps({ className: 'search-results-editor-placeholder' });

    return (
        <div {...blockProps}>
            <div className="search-results-editor-placeholder__inner">
                <span className="search-results-editor-placeholder__kicker">
                    {__('Dynamic Block', 'ai-zippy-child')}
                </span>
                <h3 className="search-results-editor-placeholder__title">
                    {__('Search Results', 'ai-zippy-child')}
                </h3>
                <p className="search-results-editor-placeholder__desc">
                    {__(
                        'Displays categorized results (Products, Blog Posts, Pages) on the front-end search page.',
                        'ai-zippy-child'
                    )}
                </p>
            </div>
        </div>
    );
}
